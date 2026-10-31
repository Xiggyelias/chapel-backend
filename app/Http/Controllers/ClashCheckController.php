<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClashCheckController extends Controller
{
    /**
     * Smart Conflict & Capacity Resolver
     * Detects clashes and calculates free alternative venues and time slots.
     */
    public function check(Request $request)
    {
        $empty = ['has_clash' => false, 'clashes' => [], 'suggestions' => []];

        $validator = Validator::make($request->all(), [
            'venue_id' => ['required', 'integer'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'expected_attendance' => ['nullable', 'integer', 'min:0'],
            'request_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json($empty);
        }

        $venueId = (int) $request->input('venue_id');
        $date = $request->input('event_date');
        $startTime = substr($request->input('start_time'), 0, 5);
        $endTime = substr($request->input('end_time'), 0, 5);
        $expectedAttendance = (int) $request->input('expected_attendance', 50);
        $ignoreRequestId = $request->input('request_id');

        if ($startTime >= $endTime) {
            return response()->json($empty);
        }

        $clashes = Event::where('venue_id', $venueId)
            ->overlapping($date, $startTime, $endTime)
            ->when($ignoreRequestId, function ($q) use ($ignoreRequestId) {
                $q->where(function ($inner) use ($ignoreRequestId) {
                    $inner->whereNull('request_id')->orWhere('request_id', '!=', $ignoreRequestId);
                });
            })
            ->with(['club', 'venue'])
            ->get();

        $suggestions = [];

        if ($clashes->isNotEmpty()) {
            // 1. Alternative venues free in the same window with sufficient capacity
            $bookedVenueIds = Event::overlapping($date, $startTime, $endTime)
                ->pluck('venue_id')
                ->filter();

            $freeVenues = Venue::where('is_active', true)
                ->whereNotIn('id', $bookedVenueIds)
                ->where('id', '!=', $venueId)
                ->where('capacity', '>=', $expectedAttendance)
                ->orderBy('capacity', 'asc')
                ->get();

            foreach ($freeVenues as $fv) {
                $suggestions[] = [
                    'type' => 'alternative_venue',
                    'venue_id' => $fv->id,
                    'venue_name' => $fv->name,
                    'capacity' => $fv->capacity,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'message' => "Switch to {$fv->name} (Holds {$fv->capacity} people, free at {$startTime} - {$endTime})",
                ];
            }

            // 2. Next slot at the same venue, starting 15 minutes after the last clashing booking
            $lastClash = $clashes->sortByDesc('end_time')->first();
            $altStart = date('H:i', strtotime($lastClash->end_time . ' + 15 minutes'));
            $durationMinutes = (strtotime($endTime) - strtotime($startTime)) / 60;
            $altEnd = date('H:i', strtotime($altStart . " + {$durationMinutes} minutes"));

            // Must still finish before the 22:00 curfew, on the same day, and be genuinely free
            $slotIsFree = $altStart < $altEnd
                && !Event::where('venue_id', $venueId)->overlapping($date, $altStart, $altEnd)->exists();

            if ($altEnd <= '22:00' && $slotIsFree) {
                $suggestions[] = [
                    'type' => 'alternative_time',
                    'venue_id' => $venueId,
                    'venue_name' => $lastClash->venue?->name ?? 'Same venue',
                    'start_time' => $altStart,
                    'end_time' => $altEnd,
                    'message' => "Shift time slot to {$altStart} - {$altEnd} (immediately following " . ($lastClash->club?->name ?? 'the existing booking') . "'s event)",
                ];
            }
        }

        return response()->json([
            'has_clash' => $clashes->isNotEmpty(),
            'clashes' => $clashes->map(function ($ev) {
                return [
                    'title' => $ev->title,
                    'club' => $ev->club?->name,
                    'start_time' => substr($ev->start_time, 0, 5),
                    'end_time' => substr($ev->end_time, 0, 5),
                    'venue' => $ev->venue?->name ?? $ev->venue_name,
                ];
            })->values(),
            'suggestions' => $suggestions,
        ]);
    }
}
