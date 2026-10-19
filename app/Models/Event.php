<?php

namespace App\Models;

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
}
