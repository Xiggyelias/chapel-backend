<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;

class ClashCheckController extends Controller
{
    /**
     * Innovation: Smart Conflict & Capacity AI Resolver
     * Detects clashes and automatically calculates free alternative venues and time slots.
     */
    public function check(Request $request)
    {
        $venueId = $request->input('venue_id');
        $date = $request->input('event_date');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $expectedAttendance = (int) $request->input('expected_attendance', 50);
        $ignoreRequestId = $request->input('request_id');

        if (!$venueId || !$date || !$startTime || !$endTime) {
            return response()->json(['has_clash' => false, 'clashes' => [], 'suggestions' => []]);
        }

        $clashes = Event::where('venue_id', $venueId)
            ->whereDate('event_date', $date)
            ->where('status', '!=', 'cancelled')
            ->when($ignoreRequestId, function ($q) use ($ignoreRequestId) {
                $q->where('request_id', '!=', $ignoreRequestId);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                      ->orWhereBetween('end_time', [$startTime, $endTime])
                      ->orWhere(function ($q) use ($startTime, $endTime) {
                          $q->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                      });
            })
            ->with(['club', 'venue'])
            ->get();

        $suggestions = [];

        if ($clashes->isNotEmpty()) {
            // 1. Find alternative venues free at that exact date and time window with sufficient capacity
            $bookedVenueIds = Event::whereDate('event_date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime])
                          ->orWhereBetween('end_time', [$startTime, $endTime])
                          ->orWhere(function ($q) use ($startTime, $endTime) {
                              $q->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                          });
                })
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

            // 2. Find alternate time slot at same venue (e.g. after the conflicting event)
            $lastClash = $clashes->sortByDesc('end_time')->first();
            if ($lastClash) {
                $altStart = date('H:i', strtotime($lastClash->end_time . ' + 15 minutes'));
                $durationMinutes = (strtotime($endTime) - strtotime($startTime)) / 60;
                $altEnd = date('H:i', strtotime($altStart . " + {$durationMinutes} minutes"));

                // If before 22:00 curfew
                if (strtotime($altEnd) <= strtotime('22:00')) {
                    $suggestions[] = [
                        'type' => 'alternative_time',
                        'venue_id' => $venueId,
                        'venue_name' => $clashes->first()->venue->name,
                        'start_time' => $altStart,
                        'end_time' => $altEnd,
                        'message' => "Shift time slot to {$altStart} - {$altEnd} (immediately following {$lastClash->club->name}'s event)",
                    ];
                }
            }
        }

        return response()->json([
            'has_clash' => $clashes->isNotEmpty(),
            'clashes' => $clashes->map(function ($ev) {
                return [
                    'title' => $ev->title,
                    'club' => $ev->club->name,
                    'start_time' => $ev->start_time,
                    'end_time' => $ev->end_time,
                    'venue' => $ev->venue->name,
                ];
            }),
            'suggestions' => $suggestions,
        ]);
    }
}
