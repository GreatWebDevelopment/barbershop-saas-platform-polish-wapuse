<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StripeConnectController extends Controller
{
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('stripe_oauth_state', $state);

        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.stripe.client_id'),
            'scope' => 'read_write',
            'redirect_uri' => route('settings.payments.stripe.callback'),
            'state' => $state,
        ]);

        return redirect("https://connect.stripe.com/oauth/authorize?{$params}");
    }

    public function callback(Request $request)
    {
        if ($request->query('error')) {
            return redirect()->route('settings.payments')
                ->with('error', 'Stripe connection was denied: ' . $request->query('error_description', 'Unknown error'));
        }

        $savedState = $request->session()->pull('stripe_oauth_state');
        if (!$savedState || $savedState !== $request->query('state')) {
            return redirect()->route('settings.payments')
                ->with('error', 'Invalid state parameter. Please try again.');
        }

        $response = Http::asForm()->post('https://connect.stripe.com/oauth/token', [
            'client_secret' => config('services.stripe.secret'),
            'code' => $request->query('code'),
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed() || !$response->json('stripe_user_id')) {
            return redirect()->route('settings.payments')
                ->with('error', 'Failed to connect Stripe account. Please try again.');
        }

        $data = $response->json();

        $request->user()->update([
            'stripe_account_id' => $data['stripe_user_id'],
            'stripe_connected_at' => now(),
            'stripe_livemode' => $data['livemode'] ?? false,
        ]);

        return redirect()->route('settings.payments')
            ->with('success', 'Stripe account connected successfully!');
    }

    public function disconnect(Request $request)
    {
        $user = $request->user();

        if ($user->stripe_account_id) {
            try {
                Http::asForm()->post('https://connect.stripe.com/oauth/deauthorize', [
                    'client_id' => config('services.stripe.client_id'),
                    'stripe_user_id' => $user->stripe_account_id,
                ]);
            } catch (\Exception $e) {
                // Continue with local disconnect even if API call fails
            }

            $user->update([
                'stripe_account_id' => null,
                'stripe_connected_at' => null,
                'stripe_livemode' => false,
            ]);
        }

        return redirect()->route('settings.payments')
            ->with('success', 'Stripe account disconnected.');
    }
}
