<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\PortalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Routes in-portal alerts to the people responsible, never to the person who triggered them.
 * Delivery failures are logged and never block the workflow action itself.
 */
class PortalNotifier
{
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
