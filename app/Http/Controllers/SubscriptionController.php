<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    private const PLANS = [
        'starter' => ['monthly' => 4900, 'annual' => 3900, 'name' => 'Starter'],
        'professional' => ['monthly' => 9900, 'annual' => 7900, 'name' => 'Professional'],
        'enterprise' => ['monthly' => 19900, 'annual' => 15900, 'name' => 'Enterprise'],
    ];

    public function checkout(Request $request)
    {
        $plan = $request->query('plan', 'starter');
        $billing = $request->query('billing', 'monthly');

        if (!isset(self::PLANS[$plan])) {
            $plan = 'starter';
        }
        if (!in_array($billing, ['monthly', 'annual'])) {
            $billing = 'monthly';
        }

        $planData = self::PLANS[$plan];
        $price = $planData[$billing];

        return Inertia::render('Checkout', [
            'plan' => $plan,
            'planName' => $planData['name'],
            'billing' => $billing,
            'price' => $price,
            'stripeKey' => config('services.stripe.key', ''),
        ]);
    }

    public function createStripeSession(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|string|in:starter,professional,enterprise',
            'billing' => 'required|string|in:monthly,annual',
        ]);

        $plan = $validated['plan'];
        $billing = $validated['billing'];
        $planData = self::PLANS[$plan];
        $price = $planData[$billing];

        $stripeKey = config('services.stripe.secret');

        if (!$stripeKey) {
            return back()->withErrors(['stripe' => 'Stripe is not configured. Please contact support.']);
        }

        $stripe = new \Stripe\StripeClient($stripeKey);

        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "BarberPro {$planData['name']} Plan ({$billing})",
                    ],
                    'unit_amount' => $price,
                    'recurring' => [
                        'interval' => $billing === 'annual' ? 'year' : 'month',
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pricing'),
            'metadata' => [
                'user_id' => $request->user()->id,
                'plan' => $plan,
                'billing' => $billing,
            ],
        ];

        // Use connected Stripe account for destination charges if available
        $connectedAccountId = $request->user()->stripe_account_id;
        if ($connectedAccountId) {
            $sessionParams['payment_intent_data'] = [
                'transfer_data' => [
                    'destination' => $connectedAccountId,
                ],
            ];
        }

        $session = $stripe->checkout->sessions->create($sessionParams);

        Subscription::create([
            'user_id' => $request->user()->id,
            'plan' => $plan,
            'billing_cycle' => $billing,
            'price' => $price,
            'status' => 'pending',
            'payment_provider' => 'stripe',
            'stripe_session_id' => $session->id,
        ]);

        return Inertia::location($session->url);
    }

    public function createPaypalOrder(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|string|in:starter,professional,enterprise',
            'billing' => 'required|string|in:monthly,annual',
        ]);

        $plan = $validated['plan'];
        $billing = $validated['billing'];
        $planData = self::PLANS[$plan];
        $price = $planData[$billing];

        $subscription = Subscription::create([
            'user_id' => $request->user()->id,
            'plan' => $plan,
            'billing_cycle' => $billing,
            'price' => $price,
            'status' => 'pending',
            'payment_provider' => 'paypal',
        ]);

        $responseData = [
            'subscription_id' => $subscription->id,
            'amount' => number_format($price / 100, 2, '.', ''),
            'description' => "BarberPro {$planData['name']} Plan ({$billing})",
        ];

        // Include connected PayPal merchant as payee if available
        $merchantId = $request->user()->paypal_merchant_id;
        if ($merchantId) {
            $responseData['payee'] = ['merchant_id' => $merchantId];
        }

        return response()->json($responseData);
    }

    public function capturePaypalOrder(Request $request)
    {
        $validated = $request->validate([
            'subscription_id' => 'required|integer',
            'paypal_order_id' => 'required|string',
        ]);

        $subscription = Subscription::where('id', $validated['subscription_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $subscription->update([
            'status' => 'active',
            'paypal_order_id' => $validated['paypal_order_id'],
            'current_period_start' => now(),
            'current_period_end' => $subscription->billing_cycle === 'annual'
                ? now()->addYear()
                : now()->addMonth(),
        ]);

        return response()->json(['status' => 'captured']);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if ($sessionId) {
            $subscription = Subscription::where('stripe_session_id', $sessionId)->first();
            if ($subscription && $subscription->status === 'pending') {
                $subscription->update([
                    'status' => 'active',
                    'current_period_start' => now(),
                    'current_period_end' => $subscription->billing_cycle === 'annual'
                        ? now()->addYear()
                        : now()->addMonth(),
                ]);
            }
        }

        return Inertia::render('PaymentSuccess', [
            'plan' => $request->query('plan', 'starter'),
        ]);
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if (!$event || !isset($event['type'])) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        switch ($event['type']) {
            case 'checkout.session.completed':
                $sessionId = $event['data']['object']['id'] ?? null;
                if ($sessionId) {
                    $subscription = Subscription::where('stripe_session_id', $sessionId)->first();
                    if ($subscription) {
                        $subscription->update([
                            'status' => 'active',
                            'stripe_subscription_id' => $event['data']['object']['subscription'] ?? null,
                            'current_period_start' => now(),
                            'current_period_end' => $subscription->billing_cycle === 'annual'
                                ? now()->addYear()
                                : now()->addMonth(),
                        ]);
                    }
                }
                break;

            case 'customer.subscription.deleted':
                $stripeSubId = $event['data']['object']['id'] ?? null;
                if ($stripeSubId) {
                    Subscription::where('stripe_subscription_id', $stripeSubId)
                        ->update(['status' => 'canceled']);
                }
                break;
        }

        return response()->json(['status' => 'ok']);
    }
}
