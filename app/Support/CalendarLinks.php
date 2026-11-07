<?php

namespace App\Support;

use App\Models\Event;

/**
 * One-tap "add to my calendar" links for a single chapel activity, so people
 * never have to download and import a calendar file by hand.
 *
 * All chapel times are local to the campus (Africa/Harare, UTC+2, no DST).
 */
class CalendarLinks
{
    public const TIMEZONE = 'Africa/Harare';
    public const UTC_OFFSET = '+02:00';

    /**
     * JSON for the calendar's event panel (data-event="…" on each clickable activity).
     */
    public static function payload(Event $event, ?string $proposalUrl = null): string
    {
        $color = $event->club?->color_hex ?: '#DC2626';

        return json_encode([
            'title' => $event->title,
            'club' => $event->club?->name ?? 'Chaplaincy Office',
            'date' => $event->event_date->format('l, j F Y'),
            'time' => substr((string) $event->start_time, 0, 5) . ' – ' . substr((string) $event->end_time, 0, 5),
            'venue' => $event->venue?->name ?? ($event->venue_name ?: 'Venue to be confirmed'),
            'desc' => $event->description,
            'color' => preg_match('/^#[0-9a-fA-F]{3,8}$/', $color) ? $color : '#DC2626',
            'attendance' => $event->expected_attendance,
            'clash' => (bool) ($event->is_clashing ?? false),
            'links' => self::for($event),
            'proposal' => $proposalUrl,
        ]);
    }

    /**
     * @return array{google: string, outlook: string, outlookPersonal: string, ics: string, whatsapp: string, text: string}
     */
    public static function for(Event $event): array
    {
        $date = $event->event_date->format('Y-m-d');
        $start = substr((string) $event->start_time, 0, 5) ?: '00:00';
        $end = substr((string) $event->end_time, 0, 5) ?: $start;

        $club = $event->club?->name ?? 'Kwang-Lim Chapel';
        $venue = $event->venue?->name ?? ($event->venue_name ?: 'Kwang-Lim Chapel, Africa University');
        $details = trim(($event->description ? $event->description . "\n\n" : '')
            . "Hosted by {$club}. Cleared by the Africa University Chaplaincy.\n"
            . route('calendar.public'));

        $compact = fn (string $t) => str_replace([':', '-'], '', $t);
        $googleDates = $compact($date) . 'T' . $compact($start) . '00/' . $compact($date) . 'T' . $compact($end) . '00';

        $outlookQuery = http_build_query([
            'path' => '/calendar/action/compose',
            'rru' => 'addevent',
            'subject' => $event->title,
            'startdt' => "{$date}T{$start}:00" . self::UTC_OFFSET,
            'enddt' => "{$date}T{$end}:00" . self::UTC_OFFSET,
            'location' => $venue,
            'body' => $details,
        ], '', '&', PHP_QUERY_RFC3986);

        $text = "*{$event->title}*\n"
            . $event->event_date->format('l, j F Y') . " · {$start}–{$end}\n"
            . "{$venue}\n"
            . "Hosted by {$club}\n\n"
            . 'Chapel calendar: ' . route('calendar.public');

        return [
            'google' => 'https://calendar.google.com/calendar/render?' . http_build_query([
                'action' => 'TEMPLATE',
                'text' => $event->title,
                'dates' => $googleDates,
                'ctz' => self::TIMEZONE,
                'details' => $details,
                'location' => $venue,
            ], '', '&', PHP_QUERY_RFC3986),
            'outlook' => 'https://outlook.office.com/calendar/0/deeplink/compose?' . $outlookQuery,
            'outlookPersonal' => 'https://outlook.live.com/calendar/0/deeplink/compose?' . $outlookQuery,
            'ics' => route('calendar.event_ics', $event->id),
            'whatsapp' => 'https://wa.me/?text=' . rawurlencode($text),
            'text' => str_replace('*', '', $text),
        ];
    }
}
