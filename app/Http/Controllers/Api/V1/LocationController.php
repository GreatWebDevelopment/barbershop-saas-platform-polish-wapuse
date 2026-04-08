<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\QueueManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(protected QueueManagementService $queueService) {}

    public function nearby(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:1|max:100',
        ]);

        $shops = Shop::nearby(
            $validated['lat'],
            $validated['lng'],
            $validated['radius'] ?? 25
        )
            ->with('activeStaff')
            ->withCount(['queueEntries as waiting_count' => fn($q) => $q->where('status', 'waiting')])
            ->get()
            ->map(function ($shop) {
                $shop->wait_time = $this->queueService->calculateWaitTime($shop);
                return $shop;
            });

        return response()->json(['locations' => $shops]);
    }

    public function show(Shop $shop): JsonResponse
    {
        $shop->load(['services', 'activeStaff']);
        $shop->loadCount(['queueEntries as waiting_count' => fn($q) => $q->where('status', 'waiting')]);

        return response()->json([
            'location' => $shop,
            'wait_time' => $this->queueService->calculateWaitTime($shop),
            'queue' => $this->queueService->getQueueStatus($shop),
        ]);
    }
}
