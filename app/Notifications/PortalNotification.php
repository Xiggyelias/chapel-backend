<?php

namespace App\Notifications;

use App\Services\PortalNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A single alert ("your proposal was approved", "a new proposal needs review"),
 * shown in the portal inbox immediately and also emailed when SMTP is configured.
 * tone: success | warning | critical | info
 */
class PortalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public string $title,
        public string $message,
        public string $url,
        public string $tone = 'info',
    ) {
    }

    public function via(object $notifiable): array
    {
        return PortalNotifier::emailEnabled() && !empty($notifiable->email)
            ? ['database', 'mail']
            : ['database'];
    }

    /**
     * The inbox entry is written straight away; only the email waits on the queue,
     * so a slow or unavailable mail server never delays the person taking the action.
     */
    public function viaConnections(): array
    {
        return ['database' => 'sync'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title . ' | Kwang-Lim Chapel')
            ->greeting('Hello ' . ($notifiable->name ?? '') . ',')
            ->line($this->title)
            ->line($this->message)
            ->action('Open in the portal', $this->url)
            ->salutation("Kwang-Lim Chapel\nAfrica University Chaplaincy Office");

        return $this->tone === 'critical' ? $mail->error() : $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'tone' => $this->tone,
        ];
    }
}
