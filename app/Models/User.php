<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    // Mirrors the column default so freshly created models report the same status as stored rows
    protected $attributes = [
        'status' => 'active',
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
        return (bool) $this->must_change_password;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * One-time password for newly provisioned or reset accounts.
     * The holder is forced to replace it on first sign-in.
     */
    public static function generateTemporaryPassword(): string
    {
        return 'AU-' . Str::upper(Str::random(4)) . '-' . random_int(1000, 9999) . '-' . Str::lower(Str::random(4));
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

    public function isAssistantChaplain(): bool
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

    public function isClubLeader(): bool
    {
        return $this->isClubExecutive() || $this->isClubPresident();
    }

    public function canManageUsers(): bool
    {
        return $this->isChaplain() || $this->isChapelSecretary();
    }

    /**
     * Club data isolation: the Office sees every club, club leaders only their own, observers none.
     */
    public function canAccessClub(?int $clubId): bool
    {
        if ($this->isOffice()) {
            return true;
        }

        return $this->isClubLeader() && $clubId !== null && (int) $this->club_id === (int) $clubId;
    }

    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'chaplain' => 'University Chaplain',
            'chapel_assistant' => 'Assistant Chaplain',
            'chapel_secretary' => 'Chapel Secretary (Administrator)',
            'club_executive' => 'Club Executive',
            'club_president' => 'Club President (Chapel Committee)',
            'campus_life_src' => 'Campus Life & SRC Observer',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }
}
