<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'full_name',
        'student_id',
        'email',
        'phone',
        'role_in_club',
        'status',
        'joined_date',
    ];

    protected $casts = [
        'joined_date' => 'date',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
