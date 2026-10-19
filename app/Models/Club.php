<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'tracking_code',
        'type',
        'description',
        'meeting_schedule',
        'venue',
        'official_email',
        'contact_phone',
        'current_president',
        'founding_president_name',
        'student_id',
        'founding_president_email',
        'advisor_name',
        'advisor_email',
        'constitution_path',
        'color_hex',
        'status',
        'charter_notes',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function leadershipTerms(): HasMany
    {
        return $this->hasMany(LeadershipTerm::class)->orderBy('start_date', 'desc');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Innovation: Vault Sentinel - Archive Health Score (0 - 100%)
     * Evaluates whether the club's permanent institutional memory is intact.
     */
    public function getArchiveHealthScore(): array
    {
        $currentYear = date('Y');
        $checks = [
            'constitution' => [
                'name' => 'Approved Constitution & Byelaws',
                'weight' => 20,
                'satisfied' => $this->documents()->where('folder', 'constitution')->where('is_archived', false)->exists(),
            ],
            'executive_contacts' => [
                'name' => "Executive & Committee Contacts ({$currentYear})",
                'weight' => 20,
                'satisfied' => $this->documents()->where('folder', 'executive_contacts')->where('is_archived', false)->exists() || $this->leadershipTerms()->where('is_active', true)->exists(),
            ],
            'minutes' => [
                'name' => 'Executive & Fellowship Meeting Minutes',
                'weight' => 20,
                'satisfied' => $this->documents()->where('folder', 'minutes')->where('is_archived', false)->exists(),
            ],
            'reports' => [
                'name' => 'Ministry & Post-Activity Reports',
                'weight' => 20,
                'satisfied' => $this->documents()->where('folder', 'reports')->where('is_archived', false)->exists() || $this->reports()->exists(),
            ],
            'finance_documents' => [
                'name' => 'Financial Budgets & Verified Receipts',
                'weight' => 20,
                'satisfied' => $this->documents()->where('folder', 'finance_documents')->where('is_archived', false)->exists(),
            ],
        ];

        $score = 0;
        $missing = [];
        $completed = [];

        foreach ($checks as $key => $check) {
            if ($check['satisfied']) {
                $score += $check['weight'];
                $completed[] = $check['name'];
            } else {
                $missing[] = $check['name'];
            }
        }

        $grade = match(true) {
            $score >= 90 => 'Distinction (Gold Tier)',
            $score >= 70 => 'Healthy (Silver Tier)',
            $score >= 50 => 'Moderate (Bronze Tier)',
            default => 'At Risk (Incomplete Memory)',
        };

        $color = match(true) {
            $score >= 90 => '#10B981',
            $score >= 70 => '#3B82F6',
            $score >= 50 => '#F59E0B',
            default => '#EF4444',
        };

        return [
            'score' => $score,
            'grade' => $grade,
            'color' => $color,
            'missing' => $missing,
            'completed' => $completed,
        ];
    }

    /**
     * 14-Day Advance Notice Compliance Rate
     */
    public function getFourteenDayComplianceRate(): float
    {
        $total = $this->requests()->count();
        if ($total === 0) return 100.0;
        $onTime = $this->requests()->where('is_late_submission', false)->count();
        return round(($onTime / $total) * 100, 1);
    }
}
