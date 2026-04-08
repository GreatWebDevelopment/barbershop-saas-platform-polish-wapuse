<?php

namespace App\Services;

use App\Events\QueueUpdated;
use App\Models\QueueEntry;
use App\Models\Shop;
use App\Models\Staff;
use Carbon\Carbon;

class QueueManagementService
{
    public function checkIn(Shop $shop, array $data): QueueEntry
    {
        $queueNumber = QueueEntry::generateQueueNumber($shop->id);
        $position = $shop->waitingQueue()->count() + 1;
        $estimatedWait = $this->calculateWaitTime($shop, $position);

        $entry = QueueEntry::create([
            'shop_id' => $shop->id,
            'queue_number' => $queueNumber,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'party_size' => $data['party_size'] ?? 1,
            'service_id' => $data['service_id'] ?? null,
            'stylist_preference' => $data['stylist_preference'] ?? 'first_available',
            'staff_id' => ($data['stylist_preference'] ?? 'first_available') !== 'first_available'
                ? $data['stylist_preference'] : null,
            'status' => 'waiting',
            'position' => $position,
            'estimated_wait_minutes' => $estimatedWait,
            'checked_in_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        $entry->load(['shop', 'staff', 'service']);
        QueueUpdated::dispatch($shop);

        return $entry;
    }

    public function calculateWaitTime(Shop $shop, ?int $position = null): int
    {
        $position = $position ?? $shop->waitingQueue()->count();
        $activeStaff = $shop->activeStaff()->count();

        if ($activeStaff === 0) {
            return $position * 20; // fallback: 20 min per person
        }

        // Average service time from completed entries today
        $avgServiceTime = QueueEntry::where('shop_id', $shop->id)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->whereNotNull('service_started_at')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, service_started_at, completed_at)) as avg_time')
            ->value('avg_time');

        $avgServiceTime = $avgServiceTime ?: 18; // default 18 minutes

        // Peak hour buffer (1.2x during 11-1pm and 5-7pm)
        $hour = now()->hour;
        $peakMultiplier = (($hour >= 11 && $hour <= 13) || ($hour >= 17 && $hour <= 19)) ? 1.2 : 1.0;

        return (int) ceil(($position * $avgServiceTime * $peakMultiplier) / $activeStaff);
    }

    public function callNext(Shop $shop, Staff $staff): ?QueueEntry
    {
        $entry = $shop->waitingQueue()
            ->when($staff, function ($q) use ($staff) {
                // Prefer entries that specifically requested this stylist
                $q->orderByRaw("CASE WHEN staff_id = ? THEN 0 ELSE 1 END", [$staff->id]);
            })
            ->first();

        if (!$entry) {
            return null;
        }

        $entry->update([
            'status' => 'called',
            'staff_id' => $staff->id,
            'called_at' => now(),
        ]);

        $staff->update(['current_queue_entry_id' => $entry->id]);

        $this->recalculatePositions($shop);
        QueueUpdated::dispatch($shop);

        return $entry->fresh(['staff', 'service']);
    }

    public function startService(QueueEntry $entry): QueueEntry
    {
        $entry->update([
            'status' => 'in_service',
            'service_started_at' => now(),
        ]);

        QueueUpdated::dispatch($entry->shop);

        return $entry->fresh();
    }

    public function completeService(QueueEntry $entry): QueueEntry
    {
        $entry->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        if ($entry->staff) {
            $entry->staff->update(['current_queue_entry_id' => null]);
        }

        $this->recalculatePositions($entry->shop);
        QueueUpdated::dispatch($entry->shop);

        return $entry->fresh();
    }

    public function cancel(QueueEntry $entry): QueueEntry
    {
        $entry->update(['status' => 'cancelled']);

        if ($entry->staff) {
            $entry->staff->update(['current_queue_entry_id' => null]);
        }

        $this->recalculatePositions($entry->shop);
        QueueUpdated::dispatch($entry->shop);

        return $entry->fresh();
    }

    public function markNoShow(QueueEntry $entry): QueueEntry
    {
        $entry->update(['status' => 'no_show']);

        if ($entry->staff) {
            $entry->staff->update(['current_queue_entry_id' => null]);
        }

        $this->recalculatePositions($entry->shop);
        QueueUpdated::dispatch($entry->shop);

        return $entry->fresh();
    }

    public function checkNoShows(Shop $shop): void
    {
        $entries = $shop->queueEntries()
            ->where('status', 'called')
            ->where('called_at', '<', now()->subMinutes(10))
            ->get();

        foreach ($entries as $entry) {
            $this->markNoShow($entry);
        }
    }

    protected function recalculatePositions(Shop $shop): void
    {
        $waiting = $shop->queueEntries()
            ->where('status', 'waiting')
            ->orderBy('checked_in_at')
            ->get();

        $activeStaff = $shop->activeStaff()->count();

        foreach ($waiting as $i => $entry) {
            $position = $i + 1;
            $wait = $this->calculateWaitTime($shop, $position);
            $entry->update([
                'position' => $position,
                'estimated_wait_minutes' => $wait,
            ]);
        }
    }

    public function getQueueStatus(Shop $shop): array
    {
        $waiting = $shop->queueEntries()->where('status', 'waiting')->orderBy('position')->get();
        $inService = $shop->queueEntries()->where('status', 'in_service')->with('staff')->get();
        $called = $shop->queueEntries()->where('status', 'called')->with('staff')->get();
        $activeStaff = $shop->activeStaff()->get();

        return [
            'shop' => $shop->only(['id', 'name', 'address', 'city', 'state', 'queue_enabled']),
            'waiting' => $waiting,
            'in_service' => $inService,
            'called' => $called,
            'active_staff' => $activeStaff,
            'wait_time' => $this->calculateWaitTime($shop),
            'total_waiting' => $waiting->count(),
            'total_in_service' => $inService->count(),
        ];
    }
}
