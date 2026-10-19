<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'title',
        'message',
        'audience',
        'target_club_id',
        'is_guideline',
        'is_pinned',
        'published_date',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_guideline' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function targetClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'target_club_id');
    }
}
