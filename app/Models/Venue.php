<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'booking_rules',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }
}
