<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    /**
     * Interactive Master Calendar view
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Month and Year navigation
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Event::with(['club', 'venue', 'request'])
            ->whereBetween('event_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '!=', 'cancelled');

        // Optional filters
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }

        $events = $query->orderBy('event_date', 'asc')->orderBy('start_time', 'asc')->get();

        // Detect clashes on calendar days
        $clashDates = [];
        $dayVenueCount = [];
        foreach ($events as $ev) {
            $key = $ev->event_date->toDateString() . '_' . $ev->venue_id;
            if ($ev->venue_id) {
                if (!isset($dayVenueCount[$key])) {
                    $dayVenueCount[$key] = [];
                }
                $dayVenueCount[$key][] = $ev;
            }
        }

        foreach ($dayVenueCount as $group) {
            if (count($group) > 1) {
                // Check time overlap
                for ($i = 0; $i < count($group); $i++) {
                    for ($j = $i + 1; $j < count($group); $j++) {
                        $e1 = $group[$i];
                        $e2 = $group[$j];
                        if (max($e1->start_time, $e2->start_time) < min($e1->end_time, $e2->end_time)) {
                            $clashDates[$e1->event_date->toDateString()] = true;
                            $e1->is_clashing = true;
                            $e2->is_clashing = true;
                        }
                    }
                }
            }
        }

        $clubs = Club::active()->get();
        $venues = Venue::where('is_active', true)->get();

        // Calendar view mode: month grid or agenda list
        $viewMode = $request->input('view') === 'list' ? 'list' : 'month';

        // "Coming up" rail: the next few activities from today, whatever month is on screen
        $upcoming = Event::with(['club', 'venue'])
            ->where('event_date', '>=', Carbon::today()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        return view('calendar.index', compact(
            'events',
            'clubs',
            'venues',
            'startDate',
            'endDate',
            'month',
            'year',
            'viewMode',
            'clashDates',
            'upcoming',
            'user'
        ));
    }

    /**
     * Shareable Read-Only View for Campus Life & SRC and external feeds
     */
    public function publicView(Request $request)
    {
        $today = Carbon::today();
        $events = Event::with(['club', 'venue'])
            ->where('event_date', '>=', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $clubs = Club::active()->get();
        $venues = Venue::where('is_active', true)->get();

        return view('calendar.public', compact('events', 'clubs', 'venues', 'today'));
    }

    /**
     * JSON feed for calendar events or external calendars
     */
    public function jsonFeed(Request $request)
    {
        try {
            $start = Carbon::parse($request->input('start', Carbon::now()->startOfMonth()->toDateString()))->toDateString();
            $end = Carbon::parse($request->input('end', Carbon::now()->addMonths(2)->endOfMonth()->toDateString()))->toDateString();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid date range.'], 422);
        }

        $events = Event::with(['club', 'venue'])
            ->whereBetween('event_date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'club' => $event->club?->name,
                    'club_code' => $event->club?->code,
                    'color' => $event->club?->color_hex ?? '#002147',
                    'venue' => $event->venue ? $event->venue->name : $event->venue_name,
                    'start' => $event->event_date->toDateString() . 'T' . $event->start_time,
                    'end' => $event->event_date->toDateString() . 'T' . $event->end_time,
                    'attendance' => $event->expected_attendance,
                    'status' => $event->status,
                ];
            });

        return response()->json($events);
    }

    /**
     * RFC 5545 iCalendar (.ics) subscription feed for Google Calendar, Outlook and Apple Calendar.
     * People subscribe to it once (one tap from the calendar page) and their own calendar app
     * keeps itself up to date; nobody has to download or import a file.
     */
    public function icsFeed(Request $request)
    {
        $events = Event::with(['club', 'venue'])
            ->where('event_date', '>=', Carbon::now()->subMonths(1)->startOfMonth())
            ->where('status', '!=', 'cancelled')
            ->orderBy('event_date', 'asc')
            ->get();

        $lines = self::icsHeader([
            'X-WR-CALNAME:Africa University Kwang-Lim Chapel',
            'X-WR-CALDESC:Official Kwang-Lim Chapel Fellowships and Ministry Master Calendar',
            // Ask subscribing apps to check for changes hourly (Apple/Outlook honour this)
            'REFRESH-INTERVAL;VALUE=DURATION:PT1H',
            'X-PUBLISHED-TTL:PT1H',
        ]);
        foreach ($events as $event) {
            array_push($lines, ...self::icsEvent($event));
        }
        $lines[] = 'END:VCALENDAR';

        return response(implode("\r\n", array_map(self::icsFold(...), $lines)) . "\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="kwang-lim-chapel.ics"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * A single activity as an .ics file: the "Apple / other calendar" option on an event.
     * On iPhone and Android this opens straight into the phone's calendar.
     */
    public function eventIcs(Event $event)
    {
        abort_if($event->status === 'cancelled', 404);
        $event->loadMissing(['club', 'venue']);

        $lines = self::icsHeader();
        array_push($lines, ...self::icsEvent($event));
        $lines[] = 'END:VCALENDAR';

        $filename = (\Illuminate\Support\Str::slug($event->title) ?: 'chapel-activity') . '.ics';

        return response(implode("\r\n", array_map(self::icsFold(...), $lines)) . "\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * VCALENDAR preamble with the campus time zone (CAT, UTC+2, no daylight saving).
     */
    private static function icsHeader(array $extra = []): array
    {
        return [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Africa University Chaplaincy//Kwang-Lim Chapel System//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-TIMEZONE:Africa/Harare',
            ...$extra,
            'BEGIN:VTIMEZONE',
            'TZID:Africa/Harare',
            'BEGIN:STANDARD',
            'DTSTART:19700101T000000',
            'TZOFFSETFROM:+0200',
            'TZOFFSETTO:+0200',
            'TZNAME:CAT',
            'END:STANDARD',
            'END:VTIMEZONE',
        ];
    }

    private static function icsEvent(Event $event): array
    {
        $dateStr = $event->event_date->format('Ymd');
        $startFormatted = str_replace(':', '', substr($event->start_time, 0, 5)) . '00';
        $endFormatted = str_replace(':', '', substr($event->end_time, 0, 5)) . '00';

        $clubName = $event->club?->name ?? 'Kwang-Lim Chapel';
        $summary = self::icsEscape("{$event->title} ({$clubName})");
        $location = self::icsEscape($event->venue ? $event->venue->name : ($event->venue_name ?: 'Kwang-Lim Chapel'));
        $desc = self::icsEscape(trim(($event->description ? $event->description . "\n\n" : '') . "Fellowship Activity: {$event->title} by {$clubName}. Cleared by Africa University Chaplaincy Office."));

        return [
            'BEGIN:VEVENT',
            "UID:chapel-event-{$event->id}@africau.edu",
            'DTSTAMP:' . gmdate('Ymd\THis\Z'),
            "DTSTART;TZID=Africa/Harare:{$dateStr}T{$startFormatted}",
            "DTEND;TZID=Africa/Harare:{$dateStr}T{$endFormatted}",
            "SUMMARY:{$summary}",
            "LOCATION:{$location}",
            "DESCRIPTION:{$desc}",
            'STATUS:CONFIRMED',
            'TRANSP:OPAQUE',
            'END:VEVENT',
        ];
    }

    /**
     * RFC 5545 text escaping (backslash, comma, semicolon, newlines).
     */
    private static function icsEscape(string $text): string
    {
        return str_replace(['\\', ',', ';', "\r\n", "\n", "\r"], ['\\\\', '\,', '\;', '\n', '\n', ''], $text);
    }

    /**
     * RFC 5545 §3.1 line folding: content lines longer than 75 octets continue on
     * the next line after a single space. Never splits a multi-byte character.
     */
    private static function icsFold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }
        $out = [];
        $current = '';
        foreach (mb_str_split($line) as $char) {
            $limit = $out ? 74 : 75; // continuation lines start with a space
            if (strlen($current) + strlen($char) > $limit) {
                $out[] = $current;
                $current = '';
            }
            $current .= $char;
        }
        $out[] = $current;

        return implode("\r\n ", $out);
    }
}
