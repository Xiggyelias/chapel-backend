<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'request_id',
        'club_id',
        'submitted_by',
        'title',
        'attendance',
        'outcome',
        'spending',
        'photos',
        'status',
        'chaplaincy_remarks',
    ];

    protected $casts = [
        'spending' => 'decimal:2',
        'attendance' => 'integer',
        'photos' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
