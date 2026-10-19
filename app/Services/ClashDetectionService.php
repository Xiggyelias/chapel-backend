<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClashDetectionService
{
    /**
     * Determine if a venue booking has any overlapping conflicts.
     */
    public function checkClash(int $venueId, string $eventDate, string $startTime, string $endTime, ?int $excludeRequestId = null): array
    {
        $venue = Venue::find($venueId);
        $venueName = $venue ? $venue->name : 'Requested Venue';

        // Check scheduled calendar events
        $eventClash = Event::where('venue_id', $venueId)
            ->where('event_date', $eventDate)
            ->where('status', 'scheduled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->first();

        // Check approved/pending requests
        $requestQuery = ChapelRequest::where('venue_id', $venueId)
            ->where('event_date', $eventDate)
            ->whereIn('status', ['approved', 'in_review', 'pending'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeRequestId) {
            $requestQuery->where('id', '!=', $excludeRequestId);
        }

        $requestClash = $requestQuery->first();

        $hasClash = ($eventClash !== null || $requestClash !== null);

        $conflictingItem = $eventClash ?: $requestClash;
        $conflictDetails = null;

        if ($conflictingItem) {
            $conflictDetails = [
                'title' => $conflictingItem->title ?? 'Reserved Activity',
                'start_time' => $conflictingItem->start_time,
                'end_time' => $conflictingItem->end_time,
                'venue_name' => $venueName,
            ];
        }

        $suggestions = [];
        if ($hasClash) {
            $suggestions = $this->findAlternativeVenues($venueId, $eventDate, $startTime, $endTime);
        }

        return [
            'has_clash' => $hasClash,
            'venue' => $venueName,
            'conflict' => $conflictDetails,
            'suggestions' => $suggestions,
        ];
    }

    /**
     * Find alternative venues that are completely free during the requested time slot.
     */
    public function findAlternativeVenues(int $conflictedVenueId, string $eventDate, string $startTime, string $endTime): array
    {
        $otherVenues = Venue::where('id', '!=', $conflictedVenueId)
            ->where('is_active', true)
            ->get();

        $available = [];

        foreach ($otherVenues as $v) {
            $hasOverlap = Event::where('venue_id', $v->id)
                ->where('event_date', $eventDate)
                ->where('status', 'scheduled')
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();

            if (!$hasOverlap) {
                $hasReqOverlap = ChapelRequest::where('venue_id', $v->id)
                    ->where('event_date', $eventDate)
                    ->whereIn('status', ['approved', 'in_review'])
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->exists();

                if (!$hasReqOverlap) {
                    $available[] = [
                        'venue_id' => $v->id,
                        'name' => $v->name,
                        'capacity' => $v->capacity,
                    ];
                }
            }
        }

        return $available;
    }
}
