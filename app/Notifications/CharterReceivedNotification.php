<?php

namespace App\Notifications;

use App\Models\Club;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Receipt for a public fellowship charter application, sent to the founding president.
 */
class CharterReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public Club $club)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Charter application received: {$this->club->name}")
            ->greeting('Hello ' . ($this->club->founding_president_name ?: 'there') . ',')
            ->line("Your application to charter {$this->club->name} ({$this->club->code}) has been received by the Africa University Chaplaincy Office.")
            ->line("Tracking reference: {$this->club->tracking_code}")
            ->action('Check application status', route('clubs.receipt', ['tracking' => $this->club->tracking_code]))
            ->line('Once the charter is approved, the founding president receives portal sign-in details by email.')
            ->salutation("Kwang-Lim Chapel\nAfrica University Chaplaincy Office");
    }
}
