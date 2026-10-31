<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Emails a newly issued one-time password to the account holder.
 * The password only works until the holder sets their own at first sign-in.
 */
class AccountCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $temporaryPassword,
        public string $reason = 'created',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $intro = $this->reason === 'reset'
            ? 'The Chaplaincy Office has issued you a new temporary password for the Kwang-Lim Chapel portal.'
            : 'A Kwang-Lim Chapel portal account has been created for you by the Africa University Chaplaincy Office.';

        return (new MailMessage)
            ->subject('Your Kwang-Lim Chapel portal sign-in')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($intro)
            ->line('Email: ' . $notifiable->email)
            ->line('Temporary password: ' . $this->temporaryPassword)
            ->action('Sign in', route('login'))
            ->line('You will be asked to choose your own password straight after signing in. If you did not expect this email, contact the Chaplaincy Office.')
            ->salutation("Kwang-Lim Chapel\nAfrica University Chaplaincy Office");
    }
}
