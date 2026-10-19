<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ChapelRemindersCommand extends Command
{
    protected $signature = 'chapel:reminders';
    protected $description = 'Process automated Chaplaincy reminders for worship services, 14-day rule notices, and post-event reports';

    public function handle(): int
    {
        $today = Carbon::today();
        $this->info("Executing Chaplaincy Policy Reminder Check for {$today->toDateString()}...");

        // 1. Worship Planning Reminders (Policy 4.3)
        $upcomingServices = ServiceSheet::where('service_date', '>=', $today)
            ->where('service_date', '<=', $today->copy()->addDays(7))
            ->get();

        foreach ($upcomingServices as $service) {
            $daysUntil = $today->diffInDays($service->service_date, false);

            if ($daysUntil === 3 && !$service->preacher_submitted_materials) {
                $msg = "[Worship Reminder - 3 Days]: Preacher materials (scripture, theme, hymns) due today for {$service->service_type} on {$service->service_date->toDateString()}.";
                $this->warn($msg);
                Log::info($msg);
            }

            if ($daysUntil === 2 && !$service->media_notified) {
                $msg = "[Worship Reminder - 2 Days]: Media team dispatch required for {$service->service_type} on {$service->service_date->toDateString()}.";
                $this->warn($msg);
                Log::info($msg);
            }

            if ($daysUntil === 1) {
                $msg = "[Worship Reminder - 1 Day]: Final Praise and Worship team coordination for {$service->service_type} tomorrow.";
                $this->line($msg);
                Log::info($msg);
            }
        }

        // 2. 14-Day Advance Notice Decision Checks
        $pendingRequests = ChapelRequest::whereIn('status', ['submitted', 'under_review'])
            ->where('event_date', '>=', $today)
            ->get();

        foreach ($pendingRequests as $req) {
            $daysUntil = $today->diffInDays($req->event_date, false);
            if ($daysUntil <= 14) {
                $msg = "[14-Day Decision Urgent Alert]: Request {$req->reference_no} ('{$req->title}') is {$daysUntil} days away. Immediate decision required per Chapel Manual §4.1.";
                $this->error($msg);
                Log::warning($msg);
            }
        }

        // 3. Post-Activity Report Prompts
        $yesterdayEvents = Event::where('event_date', $today->copy()->subDays(1))
            ->whereDoesntHave('report')
            ->with('club')
            ->get();

        foreach ($yesterdayEvents as $ev) {
            $msg = "[Post-Activity Report Prompt]: Event '{$ev->title}' concluded yesterday. Prompt {$ev->club->name} to submit report per Chapel Manual §7.1.2.";
            $this->info($msg);
            Log::info($msg);
        }

        $this->info("Chapel automated reminder check completed.");
        return 0;
    }
}
