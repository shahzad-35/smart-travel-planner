<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TripShare;

class ShareTokenExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TripShare $share)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Trip share link expired')
            ->greeting('Hello!')
            ->line('A trip share link has expired.')
            ->line('Destination: ' . ($this->share->trip->destination ?? 'Trip'))
            ->line('You can generate a new share link if needed.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'trip_id' => $this->share->trip_id,
            'token' => $this->share->token,
            'expired_at' => now()->toIso8601String(),
        ];
    }
}


