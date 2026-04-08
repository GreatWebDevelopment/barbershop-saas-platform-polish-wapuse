<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected EmailCampaign $campaign,
    ) {}

    public function handle(): void
    {
        $query = Customer::where('shop_id', $this->campaign->shop_id)
            ->whereNotNull('email');

        switch ($this->campaign->segment) {
            case 'regulars':
                $query->where('visit_count', '>=', 5);
                break;
            case 'lapsed':
                $query->where(function ($q) {
                    $q->where('last_visit_at', '<', now()->subDays(60))
                      ->orWhereNull('last_visit_at');
                })->where('visit_count', '>', 0);
                break;
            case 'new':
                $query->where('created_at', '>=', now()->subDays(30))
                    ->where('visit_count', '<=', 1);
                break;
        }

        $customers = $query->get();

        foreach ($customers as $customer) {
            Mail::html($this->campaign->body_html, function ($message) use ($customer) {
                $message->to($customer->email, $customer->full_name)
                    ->subject($this->campaign->subject);
            });
        }

        $this->campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipient_count' => $customers->count(),
        ]);
    }
}
