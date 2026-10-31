<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'club_id',
        'venue_id',
        'venue_name',
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'expected_attendance',
        'status',
        'is_semester_plan',
        'semester',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_semester_plan' => 'boolean',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString())->orderBy('event_date')->orderBy('start_time');
    }

    /**
     * Live bookings that overlap a time window. Back-to-back slots (one ends as the next starts) do not clash.
     */
    public function scopeOverlapping($query, string $date, string $startTime, string $endTime)
    {
        return $query->whereDate('event_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);
    }

    /**
     * Africa University academic calendar: Semester 1 runs August-December, Semester 2 January-June.
     */
    public static function semesterFor(Carbon $date): string
    {
        $year = $date->year;

        if ($date->month >= 8) {
            return "Semester 1 {$year}/" . ($year + 1);
        }

        if ($date->month <= 6) {
            return 'Semester 2 ' . ($year - 1) . "/{$year}";
        }

        return 'Vacation ' . ($year - 1) . "/{$year}";
    }
}
