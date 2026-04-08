<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\QueueEntry;
use App\Models\Staff;
use App\Services\QueueManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffQueueController extends Controller
{
    public function __construct(protected QueueManagementService $queueService) {}

    public function queue(Request $request): JsonResponse
    {
        $user = $request->user();
        $shop = $user->shop;

        if (!$shop) {
            return response()->json(['message' => 'No shop assigned.'], 403);
        }

        return response()->json($this->queueService->getQueueStatus($shop));
    }

    public function callNext(Request $request, QueueEntry $entry): JsonResponse
    {
        $user = $request->user();
        $staff = Staff::where('shop_id', $user->shop_id)
            ->where('email', $user->email)
            ->firstOrFail();

        if ($entry->status !== 'waiting') {
            return response()->json(['message' => 'Entry is not in waiting status.'], 422);
        }

        $entry->update([
            'status' => 'called',
            'staff_id' => $staff->id,
            'called_at' => now(),
        ]);
        $staff->update(['current_queue_entry_id' => $entry->id]);

        $this->queueService->checkNoShows($entry->shop);
        \App\Events\QueueUpdated::dispatch($entry->shop);

        return response()->json(['message' => 'Customer called.', 'entry' => $entry->fresh(['staff'])]);
    }

    public function startService(Request $request, QueueEntry $entry): JsonResponse
    {
        if ($entry->status !== 'called') {
            return response()->json(['message' => 'Entry must be in called status.'], 422);
        }

        $entry = $this->queueService->startService($entry);

        return response()->json(['message' => 'Service started.', 'entry' => $entry]);
    }

    public function completeService(Request $request, QueueEntry $entry): JsonResponse
    {
        if ($entry->status !== 'in_service') {
            return response()->json(['message' => 'Entry must be in service.'], 422);
        }

        $entry = $this->queueService->completeService($entry);

        return response()->json(['message' => 'Service completed.', 'entry' => $entry]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,on_break,off_duty',
        ]);

        $user = $request->user();
        $staff = Staff::where('shop_id', $user->shop_id)
            ->where('email', $user->email)
            ->firstOrFail();

        $staff->update(['queue_status' => $validated['status']]);
        \App\Events\QueueUpdated::dispatch($staff->shop);

        return response()->json(['message' => 'Status updated.', 'staff' => $staff->fresh()]);
    }
}
