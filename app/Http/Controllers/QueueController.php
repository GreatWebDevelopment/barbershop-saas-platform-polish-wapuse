<?php

namespace App\Http\Controllers;

use App\Models\QueueEntry;
use App\Models\Shop;
use App\Services\QueueManagementService;
use Inertia\Inertia;

class QueueController extends Controller
{
    public function __construct(protected QueueManagementService $queueService) {}

    public function checkIn(Shop $shop)
    {
        $shop->load(['services', 'activeStaff']);

        return Inertia::render('Queue/CheckIn', [
            'shop' => $shop,
        ]);
    }

    public function status(string $queueNumber)
    {
        $entry = QueueEntry::where('queue_number', $queueNumber)
            ->whereDate('created_at', today())
            ->with(['shop', 'staff', 'service'])
            ->firstOrFail();

        return Inertia::render('Queue/Status', [
            'entry' => $entry,
            'shopId' => $entry->shop_id,
        ]);
    }

    public function display(Shop $shop)
    {
        $queueStatus = $this->queueService->getQueueStatus($shop);

        return Inertia::render('Queue/Display', [
            'shop' => $shop,
            'queueStatus' => $queueStatus,
            'reverbKey' => config('broadcasting.connections.reverb.key'),
            'reverbHost' => config('broadcasting.connections.reverb.host', 'localhost'),
            'reverbPort' => config('broadcasting.connections.reverb.port', 8080),
        ]);
    }
}
