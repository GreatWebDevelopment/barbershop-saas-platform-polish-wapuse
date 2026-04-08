<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyReward;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoyaltySettingsController extends Controller
{
    public function edit(Request $request)
    {
        $shop = $request->user()->shop;
        $rewards = LoyaltyReward::where('shop_id', $shop->id)
            ->orderBy('points_required')
            ->get();

        return Inertia::render('Settings/Loyalty', [
            'shop' => $shop->only([
                'id', 'loyalty_enabled', 'loyalty_points_per_dollar',
                'loyalty_redemption_value', 'referral_bonus_points',
            ]),
            'rewards' => $rewards,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'loyalty_enabled' => 'boolean',
            'loyalty_points_per_dollar' => 'integer|min:1|max:100',
            'loyalty_redemption_value' => 'numeric|min:0.01|max:1000',
            'referral_bonus_points' => 'integer|min:0|max:10000',
        ]);

        $request->user()->shop->update($validated);

        return back()->with('success', 'Loyalty settings updated.');
    }

    public function storeReward(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'points_required' => 'required|integer|min:1',
            'discount_amount' => 'required|numeric|min:0.01',
            'discount_type' => 'required|in:fixed,percentage',
        ]);

        $validated['shop_id'] = $request->user()->shop_id;
        LoyaltyReward::create($validated);

        return back()->with('success', 'Reward tier added.');
    }

    public function destroyReward(Request $request, LoyaltyReward $reward)
    {
        if ($reward->shop_id !== $request->user()->shop_id) {
            abort(403);
        }

        $reward->delete();

        return back()->with('success', 'Reward tier removed.');
    }
}
