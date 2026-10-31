<?php

namespace App\Services;

use App\Models\Club;
use App\Models\User;
use App\Notifications\AccountCredentialsNotification;
use App\Notifications\CharterReceivedNotification;
use App\Notifications\PortalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Routes alerts to the people responsible (never to the person who triggered them),
 * in the portal inbox and by email when SMTP is configured.
 * Delivery failures are logged and never block the workflow action itself.
 */
class PortalNotifier
{
    /**
     * Email goes out only when a real mail transport is configured and the switch is on.
     */
    public static function emailEnabled(): bool
    {
        return (bool) config('chapel.email.enabled')
            && !in_array(config('mail.default'), ['log', 'array', null], true);
    }

    public static function clubLeaders(?int $clubId, string $title, string $message, string $url, string $tone = 'info'): void
    {
        if (!$clubId) {
            return;
        }

        self::send(
            User::where('club_id', $clubId)->whereIn('role', ['club_president', 'club_executive']),
            $title, $message, $url, $tone
        );
    }

    public static function approvers(string $title, string $message, string $url, string $tone = 'info'): void
    {
        self::send(User::whereIn('role', ['chaplain', 'chapel_assistant']), $title, $message, $url, $tone);
    }

    public static function office(string $title, string $message, string $url, string $tone = 'info'): void
    {
        self::send(User::whereIn('role', ['chaplain', 'chapel_assistant', 'chapel_secretary']), $title, $message, $url, $tone);
    }

    /**
     * Email a one-time password to the account holder. Returns true when an email was queued.
     */
    public static function credentials(User $user, string $temporaryPassword, string $reason = 'created'): bool
    {
        if (!self::emailEnabled()) {
            return false;
        }

        try {
            $user->notify(new AccountCredentialsNotification($temporaryPassword, $reason));
            return true;
        } catch (\Throwable $e) {
            Log::warning("Credentials email to {$user->email} failed: " . $e->getMessage());
            return false;
        }
    }

    public static function charterReceived(Club $club): void
    {
        if (!self::emailEnabled() || !$club->founding_president_email) {
            return;
        }

        try {
            Notification::route('mail', $club->founding_president_email)->notify(new CharterReceivedNotification($club));
        } catch (\Throwable $e) {
            Log::warning("Charter receipt email for {$club->code} failed: " . $e->getMessage());
        }
    }

    private static function send($query, string $title, string $message, string $url, string $tone): void
    {
        try {
            $recipients = $query->where('status', 'active')
                ->when(Auth::id(), fn ($q) => $q->where('id', '!=', Auth::id()))
                ->get();

            Notification::send($recipients, new PortalNotification($title, $message, $url, $tone));
        } catch (\Throwable $e) {
            Log::warning('Portal notification failed: ' . $e->getMessage());
        }
    }
}
