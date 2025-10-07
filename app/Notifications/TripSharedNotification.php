<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TripShare;

class TripSharedNotification extends Notification implements ShouldQueue
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
            ->subject('A trip was shared with you')
            ->greeting('Hello!')
            ->line('A trip has been shared with your email address.')
            ->line('Destination: ' . ($this->share->trip->destination ?? 'Trip'))
            ->action('View Trip', url('/share/' . $this->share->token))
            ->line('This link may expire on ' . optional($this->share->expires_at)->toDateTimeString());
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'trip_id' => $this->share->trip_id,
            'token' => $this->share->token,
            'shared_with_email' => $this->share->shared_with_email,
            'expires_at' => optional($this->share->expires_at)?->toIso8601String(),
        ];
    }
}


