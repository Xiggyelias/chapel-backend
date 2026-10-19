<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'club_id',
        'user_id',
        'category',
        'title',
        'purpose',
        'event_date',
        'start_time',
        'end_time',
        'venue_id',
        'venue_other',
        'expected_attendance',
        'budget',
        'attachment_path',
        'attachment_name',
        'status',
        'decision_reason',
        'vc_go_ahead_needed',
        'vc_go_ahead_status',
        'vc_go_ahead_note',
        'vc_go_ahead_attachment',
        'decided_by',
        'decided_at',
        'is_late_submission',
        'sign_interpreter_required',
        'sign_interpreter_name',
    ];

    protected $casts = [
        'event_date' => 'date',
        'budget' => 'decimal:2',
        'vc_go_ahead_needed' => 'boolean',
        'is_late_submission' => 'boolean',
        'sign_interpreter_required' => 'boolean',
        'decided_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function event(): HasOne
    {
        return $this->hasOne(Event::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if request is submitted at least 14 days before the event
     */
    public static function checkIsLate(string $eventDate, ?Carbon $submissionDate = null): bool
    {
        $subDate = $submissionDate ?: Carbon::today();
        $targetDate = Carbon::parse($eventDate);
        return $subDate->diffInDays($targetDate, false) < 14;
    }

    /**
     * Check clashes against approved calendar events and active requests with the same venue
     */
    public function findClashes()
    {
        if (!$this->venue_id || !$this->event_date) {
            return collect();
        }

        // Find existing approved events on the same date and overlapping time
        return Event::where('venue_id', $this->venue_id)
            ->where('event_date', $this->event_date)
            ->where(function ($query) {
                $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                      ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                      ->orWhere(function ($q) {
                          $q->where('start_time', '<=', $this->start_time)
                            ->where('end_time', '>=', $this->end_time);
                      });
            })
            ->when($this->event, function ($q) {
                $q->where('id', '!=', $this->event->id);
            })
            ->with(['club', 'venue'])
            ->get();
    }
}
