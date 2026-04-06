<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $shop = Shop::firstOrFail();

        return Inertia::render('Settings/Payments', [
            'shop' => $shop->only([
                'id', 'stripe_account_id', 'stripe_enabled',
                'paypal_email', 'paypal_client_id', 'paypal_enabled',
                'payment_methods',
            ]),
        ]);
    }

    public function updateStripe(Request $request)
    {
        $validated = $request->validate([
            'stripe_account_id' => 'nullable|string|max:255',
            'stripe_enabled' => 'boolean',
        ]);

        $shop = Shop::firstOrFail();
        $shop->update($validated);

        return redirect()->route('settings.payments')->with('success', 'Stripe settings updated.');
    }

    public function updatePaypal(Request $request)
    {
        $validated = $request->validate([
            'paypal_email' => 'nullable|email|max:255',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_enabled' => 'boolean',
        ]);

        $shop = Shop::firstOrFail();
        $shop->update($validated);

        return redirect()->route('settings.payments')->with('success', 'PayPal settings updated.');
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
