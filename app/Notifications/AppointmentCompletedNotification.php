<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Appointment $appointment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $shop = $this->appointment->shop;
        $staff = $this->appointment->staff;
        $service = $this->appointment->service;

        return (new MailMessage)
            ->subject('How was your visit? — ' . $shop->name)
            ->greeting('Hi ' . $notifiable->first_name . '!')
            ->line('Thank you for visiting **' . $shop->name . '** today.')
            ->line('**Service:** ' . ($service?->name ?? 'N/A'))
            ->line('**Stylist:** ' . ($staff?->name ?? 'N/A'))
            ->line('We\'d love to hear about your experience! Please take a moment to leave us a review.')
            ->action('Leave a Google Review', 'https://www.google.com/search?q=' . urlencode($shop->name . ' ' . ($shop->city ?? '')))
            ->line('Your feedback helps us improve and helps others discover our shop.')
            ->salutation('— ' . $shop->name);
    }
}
