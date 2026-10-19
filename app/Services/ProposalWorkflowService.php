<?php

namespace App\Services;

use App\Models\Request as ChapelRequest;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProposalWorkflowService
{
    /**
     * Determine if a date violates the mandatory 14-day advance rule.
     */
    public function isWithinFourteenDays(string|Carbon $eventDate): bool
    {
        $target = $eventDate instanceof Carbon ? $eventDate : Carbon::parse($eventDate);
        return $target->lessThanOrEqualTo(Carbon::today()->addDays(14));
    }

    /**
     * Determine if a proposal requires Vice Chancellor (VC) concurrence.
     */
    public function requiresVcConcurrence(array $data): bool
    {
        $hasVip = !empty($data['has_external_speaker']) || !empty($data['has_vip_guest']);
        $highImpact = ($data['expected_attendance'] ?? 0) >= 300;
        $offCampus = !empty($data['is_off_campus']);

        return $hasVip || $highImpact || $offCampus;
    }

    /**
     * Generate an unforgeable digital clearance pass tracking code.
     */
    public function generateClearancePassCode(): string
    {
        return 'AU-PASS-' . date('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
