<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\QueueManagementService;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function __construct(protected QueueManagementService $queueService) {}

    public function index()
    {
        $shops = Shop::withCount(['queueEntries as waiting_count' => fn($q) => $q->where('status', 'waiting')])
            ->with('activeStaff')
            ->whereNotNull('latitude')
            ->get()
            ->map(function ($shop) {
                $shop->wait_time = $this->queueService->calculateWaitTime($shop);
                return $shop;
            });

        return Inertia::render('Locations/Index', [
            'locations' => $shops,
            'googleMapsKey' => config('services.google.maps_key'),
        ]);
    }

    public function show(Shop $shop)
    {
        $shop->load(['services', 'activeStaff']);
        $shop->loadCount(['queueEntries as waiting_count' => fn($q) => $q->where('status', 'waiting')]);
        $queueStatus = $this->queueService->getQueueStatus($shop);

        return Inertia::render('Locations/Show', [
            'location' => $shop,
            'queueStatus' => $queueStatus,
        ]);
    }
}
