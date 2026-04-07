<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PayPalConnectController extends Controller
{
    private function baseUrl(): string
    {
        return config('services.paypal.mode') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    private function getAccessToken(): ?string
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (!$clientId || !$secret) {
            return null;
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post("{$this->baseUrl()}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        return $response->json('access_token');
    }

    public function redirect(Request $request)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return redirect()->route('settings.payments')
                ->with('error', 'Failed to authenticate with PayPal. Please check your configuration.');
        }

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl()}/v2/customer/partner-referrals", [
                'tracking_id' => 'user_' . $request->user()->id . '_' . time(),
                'operations' => [[
                    'operation' => 'API_INTEGRATION',
                    'api_integration_preference' => [
                        'rest_api_integration' => [
                            'integration_method' => 'PAYPAL',
                            'integration_type' => 'THIRD_PARTY',
                            'third_party_details' => [
                                'features' => ['PAYMENT', 'REFUND'],
                            ],
                        ],
                    ],
                ]],
                'products' => ['EXPRESS_CHECKOUT'],
                'legal_consents' => [[
                    'type' => 'SHARE_DATA_CONSENT',
                    'granted' => true,
                ]],
                'partner_config_override' => [
                    'return_url' => route('settings.payments.paypal.callback'),
                ],
            ]);

        if ($response->failed()) {
            return redirect()->route('settings.payments')
                ->with('error', 'Failed to create PayPal partner referral. Please try again.');
        }

        $links = $response->json('links', []);
        $actionUrl = collect($links)->firstWhere('rel', 'action_url')['href'] ?? null;

        if (!$actionUrl) {
            return redirect()->route('settings.payments')
                ->with('error', 'Failed to get PayPal onboarding URL.');
        }

        return redirect($actionUrl);
    }

    public function callback(Request $request)
    {
        $merchantId = $request->query('merchantIdInPayPal') ?? $request->query('merchantId');
        $permissionsGranted = $request->query('permissionsGranted', 'false') === 'true';

        if (!$merchantId) {
            return redirect()->route('settings.payments')
                ->with('error', 'PayPal onboarding was not completed.');
        }

        $paymentsReceivable = false;

        $accessToken = $this->getAccessToken();
        if ($accessToken) {
            $partnerId = config('services.paypal.partner_id');
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl()}/v1/customer/partners/{$partnerId}/merchant-integrations/{$merchantId}");

            if ($response->successful()) {
                $paymentsReceivable = $response->json('payments_receivable', false);
            }
        }

        $request->user()->update([
            'paypal_merchant_id' => $merchantId,
            'paypal_connected_at' => now(),
            'paypal_payments_receivable' => $permissionsGranted || $paymentsReceivable,
        ]);

        return redirect()->route('settings.payments')
            ->with('success', 'PayPal account connected successfully!');
    }

    public function disconnect(Request $request)
    {
        $request->user()->update([
            'paypal_merchant_id' => null,
            'paypal_connected_at' => null,
            'paypal_payments_receivable' => false,
        ]);

        return redirect()->route('settings.payments')
            ->with('success', 'PayPal account disconnected.');
    }
}
