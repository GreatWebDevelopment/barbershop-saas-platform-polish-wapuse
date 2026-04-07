<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentSettingsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $shop = Shop::first();

        return Inertia::render('Settings/Payments', [
            'stripe' => [
                'connected' => (bool) $user->stripe_account_id,
                'account_id' => $user->stripe_account_id,
                'connected_at' => $user->stripe_connected_at?->toDateTimeString(),
                'livemode' => (bool) $user->stripe_livemode,
            ],
            'paypal' => [
                'connected' => (bool) $user->paypal_merchant_id,
                'merchant_id' => $user->paypal_merchant_id,
                'connected_at' => $user->paypal_connected_at?->toDateTimeString(),
                'payments_receivable' => (bool) $user->paypal_payments_receivable,
            ],
            'shop' => $shop ? $shop->only(['id', 'payment_methods']) : ['id' => null, 'payment_methods' => []],
        ]);
    }

    public function updatePaymentMethods(Request $request)
    {
        $validated = $request->validate([
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'string|in:cash,card,paypal',
        ]);

        $shop = Shop::firstOrFail();
        $shop->update($validated);

        return redirect()->route('settings.payments')->with('success', 'Payment methods updated.');
    }
}
