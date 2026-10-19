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
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

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

        // Current calendar view mode: month, week, or list
        $viewMode = $request->input('view', 'month');

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
        $start = $request->input('start', Carbon::now()->startOfMonth()->toDateString());
        $end = $request->input('end', Carbon::now()->addMonths(2)->endOfMonth()->toDateString());

        $events = Event::with(['club', 'venue'])
            ->whereBetween('event_date', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'club' => $event->club->name,
                    'club_code' => $event->club->code,
                    'color' => $event->club->color_hex ?? '#002147',
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
     * RFC 5545 iCalendar (.ics) Feed for Google Calendar, Outlook, and Apple Calendar
     * Allows staff and Campus Life to subscribe directly in their calendar app.
     */
    public function icsFeed(Request $request)
    {
        $events = Event::with(['club', 'venue'])
            ->where('event_date', '>=', Carbon::now()->subMonths(1)->startOfMonth())
            ->where('status', '!=', 'cancelled')
            ->orderBy('event_date', 'asc')
            ->get();

        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'PRODID:-//Africa University Chaplaincy//Kwang-Lim Chapel System//EN';
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:PUBLISH';
        $lines[] = 'X-WR-CALNAME:Africa University Kwang-Lim Chapel';
        $lines[] = 'X-WR-TIMEZONE:Africa/Harare';
        $lines[] = 'X-WR-CALDESC:Official Kwang-Lim Chapel Fellowships and Ministry Master Calendar';

        foreach ($events as $event) {
            $dateStr = $event->event_date->format('Ymd');
            $startFormatted = str_replace(':', '', substr($event->start_time, 0, 5)) . '00';
            $endFormatted = str_replace(':', '', substr($event->end_time, 0, 5)) . '00';

            $dtStart = "{$dateStr}T{$startFormatted}";
            $dtEnd = "{$dateStr}T{$endFormatted}";
            $dtStamp = gmdate('Ymd\THis\Z');
            $uid = "chapel-event-{$event->id}@africau.edu";

            $summary = str_replace([',', ';'], ['\,', '\;'], "{$event->title} ({$event->club->name})");
            $location = str_replace([',', ';'], ['\,', '\;'], $event->venue ? $event->venue->name : ($event->venue_name ?: 'Kwang-Lim Chapel'));
            $desc = str_replace(["\r", "\n", ',', ';'], [' ', ' ', '\,', '\;'], "Fellowship Activity: {$event->title} by {$event->club->name}. Cleared by Africa University Chaplaincy Office.");

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = "UID:{$uid}";
            $lines[] = "DTSTAMP:{$dtStamp}";
            $lines[] = "DTSTART;TZID=Africa/Harare:{$dtStart}";
            $lines[] = "DTEND;TZID=Africa/Harare:{$dtEnd}";
            $lines[] = "SUMMARY:{$summary}";
            $lines[] = "LOCATION:{$location}";
            $lines[] = "DESCRIPTION:{$desc}";
            $lines[] = 'STATUS:CONFIRMED';
            $lines[] = 'TRANSP:OPAQUE';
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';
        $content = implode("\r\n", $lines);

        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="kwang-lim-chapel.ics"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
