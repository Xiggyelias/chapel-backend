<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'must_change_password',
        'role',
        'club_id',
        'phone',
        'position',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function mustChangePassword(): bool
    {
        return (bool) $this->must_change_password || \Illuminate\Support\Facades\Hash::check('Chapel@2026!', $this->password);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function leadershipTerms(): HasMany
    {
        return $this->hasMany(LeadershipTerm::class);
    }

    // Role helpers
    public function isChaplain(): bool
    {
        return $this->role === 'chaplain';
    }

    public function isChapelAssistant(): bool
    {
        return $this->role === 'chapel_assistant';
    }

    public function isChapelSecretary(): bool
    {
        return $this->role === 'chapel_secretary';
    }

    public function isOffice(): bool
    {
        return in_array($this->role, ['chaplain', 'chapel_assistant', 'chapel_secretary']);
    }

    public function canApproveRequests(): bool
    {
        return in_array($this->role, ['chaplain', 'chapel_assistant']);
    }

    public function isClubExecutive(): bool
    {
        return $this->role === 'club_executive';
    }

    public function isClubPresident(): bool
    {
        return $this->role === 'club_president';
    }

    public function isCampusLifeSrc(): bool
    {
        return $this->role === 'campus_life_src';
    }

    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'chaplain' => 'University Chaplain',
            'chapel_assistant' => 'Chapel Assistant',
            'chapel_secretary' => 'Chapel Secretary (Administrator)',
            'club_executive' => 'Club Executive',
            'club_president' => 'Club President (Chapel Committee)',
            'campus_life_src' => 'Campus Life & SRC Observer',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }
}
