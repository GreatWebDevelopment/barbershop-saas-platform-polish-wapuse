<?php

namespace App\Observers;

use App\Models\Appointment;

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
    }
}
