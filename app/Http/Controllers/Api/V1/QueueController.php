<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QueueEntry;
use App\Models\Shop;
use App\Services\QueueManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(protected QueueManagementService $queueService) {}

    public function checkIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'party_size' => 'integer|min:1|max:5',
            'service_id' => 'nullable|exists:services,id',
            'stylist_preference' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $shop = Shop::findOrFail($validated['shop_id']);

        if (!$shop->queue_enabled) {
            return response()->json(['message' => 'Queue is not enabled for this location.'], 422);
        }

        if ($shop->waitingQueue()->count() >= $shop->queue_capacity) {
            return response()->json(['message' => 'Queue is full. Please try again later.'], 422);
        }

        $entry = $this->queueService->checkIn($shop, $validated);

        return response()->json([
            'message' => 'Successfully checked in!',
            'queue_entry' => $entry,
            'queue_number' => $entry->queue_number,
            'position' => $entry->position,
            'estimated_wait_minutes' => $entry->estimated_wait_minutes,
        ], 201);
    }

    public function status(string $queueNumber): JsonResponse
    {
        $entry = QueueEntry::where('queue_number', $queueNumber)
            ->whereDate('created_at', today())
            ->with(['shop', 'staff', 'service'])
            ->firstOrFail();

        $peopleAhead = QueueEntry::where('shop_id', $entry->shop_id)
            ->where('status', 'waiting')
            ->where('position', '<', $entry->position)
            ->count();

        return response()->json([
            'queue_entry' => $entry,
            'people_ahead' => $entry->status === 'waiting' ? $peopleAhead : 0,
            'estimated_wait_minutes' => $entry->estimated_wait_minutes,
        ]);
    }

    public function cancel(string $queueNumber): JsonResponse
    {
        $entry = QueueEntry::where('queue_number', $queueNumber)
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'called'])
            ->firstOrFail();

        $this->queueService->cancel($entry);

        return response()->json(['message' => 'Check-in cancelled.']);
    }

    public function shopStatus(Shop $shop): JsonResponse
    {
        return response()->json($this->queueService->getQueueStatus($shop));
    }
}
