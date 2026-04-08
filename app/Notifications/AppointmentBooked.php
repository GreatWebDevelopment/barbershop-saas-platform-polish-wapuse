<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Shop;
use App\Models\Staff;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBooked extends Notification
{
    use Queueable;

    public function __construct(
        protected Appointment $appointment,
        protected Shop $shop,
        protected Staff $staff,
        protected Service $service,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment Confirmed — ' . $this->shop->name)
            ->greeting('Hello ' . $notifiable->first_name . '!')
            ->line('Your appointment has been confirmed.')
            ->line('**Service:** ' . $this->service->name)
            ->line('**Stylist:** ' . $this->staff->name)
            ->line('**Date:** ' . $this->appointment->starts_at->format('l, F j, Y'))
            ->line('**Time:** ' . $this->appointment->starts_at->format('g:i A') . ' — ' . $this->appointment->ends_at->format('g:i A'))
            ->line('**Total:** $' . number_format($this->appointment->price, 2))
            ->line('We look forward to seeing you!')
            ->salutation('— ' . $this->shop->name);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'shop_name' => $this->shop->name,
            'service_name' => $this->service->name,
            'starts_at' => $this->appointment->starts_at->toIso8601String(),
        ];
    }
}
