<?php

namespace App\Jobs;

use App\Models\QueueEntry;
use App\Models\QueueNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class QueueNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public QueueEntry $entry,
        public string $event,
    ) {}

    public function handle(): void
    {
        $messages = [
            'checked_in' => "You're checked in! Queue #{$this->entry->queue_number}. Estimated wait: {$this->entry->estimated_wait_minutes} min.",
            'almost_ready' => "Almost your turn! Queue #{$this->entry->queue_number} — you're next. Please head to the shop.",
            'your_turn' => "It's your turn! Queue #{$this->entry->queue_number}. Please check in at the front desk.",
            'no_show' => "You were marked as a no-show for queue #{$this->entry->queue_number}. Please check in again if you're still here.",
        ];

        $message = $messages[$this->event] ?? "Queue #{$this->entry->queue_number} update.";

        $notification = QueueNotification::create([
            'queue_entry_id' => $this->entry->id,
            'type' => 'sms',
            'event' => $this->event,
            'message' => $message,
            'recipient' => $this->entry->customer_phone,
            'status' => 'pending',
        ]);

        // Twilio SMS (if configured)
        if (config('services.twilio.sid')) {
            try {
                // Would integrate Twilio here
                // $twilio = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
                // $twilio->messages->create($this->entry->customer_phone, [
                //     'from' => config('services.twilio.from'),
                //     'body' => $message,
                // ]);
                $notification->update(['status' => 'sent', 'sent_at' => now()]);
            } catch (\Exception $e) {
                Log::error('Queue SMS failed', ['error' => $e->getMessage(), 'entry' => $this->entry->id]);
                $notification->update(['status' => 'failed']);
            }
        } else {
            // Mark as sent (no SMS provider configured)
            $notification->update(['status' => 'sent', 'sent_at' => now()]);
            Log::info('Queue notification (no SMS configured)', ['event' => $this->event, 'entry' => $this->entry->id]);
        }
    }
}
