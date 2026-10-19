<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'club_member_id',
        'meeting_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(ClubMember::class, 'club_member_id');
    }
}
