<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Handover extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'academic_year',
        'outgoing_president_id',
        'outgoing_president_name',
        'incoming_president_name',
        'incoming_president_email',
        'incoming_president_phone',
        'checklist_running_activities',
        'checklist_pending_items',
        'checklist_key_contacts',
        'checklist_yearly_calendar',
        'folders_verified',
        'folders_verified_by',
        'folders_verified_at',
        'meeting_date',
        'meeting_notes',
        'status',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'folders_verified' => 'boolean',
        'folders_verified_at' => 'datetime',
        'meeting_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function outgoingPresident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'outgoing_president_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'folders_verified_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
