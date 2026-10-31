<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hymn extends Model
{
    use HasFactory;

    protected $fillable = [
        'hymnal_name',
        'hymnal_code',
        'number',
        'title',
        'author',
        'tune',
        'meter',
        'scripture_ref',
        'category',
        'lyrics',
        'lyrics_status',
        'lyrics_source',
        'lyrics_updated_by',
    ];

    protected $attributes = [
        'lyrics_status' => 'available',
    ];

    public function lyricsEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lyrics_updated_by');
    }

    public function hasLyrics(): bool
    {
        return $this->lyrics_status === 'available' && trim((string) $this->lyrics) !== '';
    }

    public function scopeMissingLyrics($query)
    {
        return $query->where('lyrics_status', 'not_entered');
    }

    /**
     * Get a formatted display label like "UMH 57: O For a Thousand Tongues to Sing"
     */
    public function getFullTitleAttribute(): string
    {
        return "{$this->hymnal_code} {$this->number}: {$this->title}";
    }
}
