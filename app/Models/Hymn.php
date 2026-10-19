<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Get a formatted display label like "UMH 57: O For a Thousand Tongues to Sing"
     */
    public function getFullTitleAttribute(): string
    {
        return "{$this->hymnal_code} {$this->number}: {$this->title}";
    }
}
