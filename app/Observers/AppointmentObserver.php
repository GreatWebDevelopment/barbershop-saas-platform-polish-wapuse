<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Notifications\AppointmentCompletedNotification;

class AppointmentObserver
{
    public function updated(Appointment $appointment): void
    {
        if (!$appointment->wasChanged('status')) {
            return;
        }

        if ($appointment->status !== 'completed') {
            return;
        }

        // Increment service booking count
        if ($appointment->service) {
            $appointment->service->increment('booking_count');
        }

        $customer = $appointment->customer;
        if (!$customer) {
            return;
        }

        $price = (float) $appointment->price;

        $customer->increment('visit_count');
        $customer->increment('total_spent', $price);
        $customer->update(['last_visit_at' => now()]);

        $pointsPerDollar = $customer->shop?->loyalty_points_per_dollar ?? 1;
        $pointsEarned = (int) floor($price * $pointsPerDollar);

        if ($pointsEarned > 0) {
            $customer->increment('loyalty_points', $pointsEarned);
        }

        // Award referral bonus points if this is the customer's first visit and they were referred
        if ($customer->visit_count === 1 && $customer->referred_by_id) {
            $referrer = $customer->referrer;
            $bonusPoints = $customer->shop?->referral_bonus_points ?? 50;

            if ($referrer && $bonusPoints > 0) {
                $referrer->increment('loyalty_points', $bonusPoints);
                $customer->increment('loyalty_points', $bonusPoints);
            }
        }

        // Schedule review request email 2 hours after completion
        if ($customer->email) {
            $customer->notify(
                (new AppointmentCompletedNotification($appointment))->delay(now()->addHours(2))
            );
        }
    }
}
