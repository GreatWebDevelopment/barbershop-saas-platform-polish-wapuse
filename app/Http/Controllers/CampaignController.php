<?php

namespace App\Http\Controllers;

use App\Jobs\SendCampaignJob;
use App\Models\Customer;
use App\Models\EmailCampaign;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $campaigns = EmailCampaign::where('shop_id', $request->user()->shop_id)
            ->latest()
            ->paginate(20);

        return Inertia::render('Marketing/Campaigns', [
            'campaigns' => $campaigns,
        ]);
    }

    public function create(Request $request)
    {
        $shopId = $request->user()->shop_id;

        $segmentCounts = [
            'all' => Customer::where('shop_id', $shopId)->whereNotNull('email')->count(),
            'regulars' => Customer::where('shop_id', $shopId)->whereNotNull('email')->where('visit_count', '>=', 5)->count(),
            'lapsed' => Customer::where('shop_id', $shopId)->whereNotNull('email')
                ->where(fn ($q) => $q->where('last_visit_at', '<', now()->subDays(60))->orWhereNull('last_visit_at'))
                ->where('visit_count', '>', 0)->count(),
            'new' => Customer::where('shop_id', $shopId)->whereNotNull('email')
                ->where('created_at', '>=', now()->subDays(30))
                ->where('visit_count', '<=', 1)->count(),
        ];

        return Inertia::render('Marketing/CreateCampaign', [
            'segmentCounts' => $segmentCounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'segment' => 'required|in:all,regulars,lapsed,new',
        ]);

        $validated['shop_id'] = $request->user()->shop_id;

        $campaign = EmailCampaign::create($validated);

        return redirect()->route('marketing.campaigns.index')
            ->with('success', 'Campaign saved as draft.');
    }

    public function send(Request $request, EmailCampaign $campaign)
    {
        if ($campaign->shop_id !== $request->user()->shop_id) {
            abort(403);
        }

        if ($campaign->status === 'sent') {
            return back()->with('error', 'Campaign already sent.');
        }

        SendCampaignJob::dispatch($campaign);

        return back()->with('success', 'Campaign queued for sending.');
    }
}
