<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,paypal',
        ]);

        $appointment->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => $validated['payment_method'] === 'cash' ? 'paid' : 'processing',
        ]);

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $event = json_decode($payload, true);

        if (isset($event['type']) && $event['type'] === 'payment_intent.succeeded') {
            $paymentIntentId = $event['data']['object']['id'] ?? null;
            if ($paymentIntentId) {
                Appointment::where('payment_intent_id', $paymentIntentId)
                    ->update(['payment_status' => 'paid']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function paypalCapture(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
        ]);

        $appointment->update([
            'payment_method' => 'paypal',
            'payment_status' => 'paid',
            'payment_intent_id' => $validated['order_id'],
        ]);

        return response()->json(['status' => 'captured']);
    }
}
