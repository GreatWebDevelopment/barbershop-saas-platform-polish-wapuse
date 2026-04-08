<?php

namespace App\Events;

use App\Models\Shop;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Shop $shop) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('queue.' . $this->shop->id),
        ];
    }

    public function broadcastWith(): array
    {
        $service = app(\App\Services\QueueManagementService::class);
        return $service->getQueueStatus($this->shop);
    }
}
