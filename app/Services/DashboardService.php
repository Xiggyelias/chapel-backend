<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Document;
use App\Models\EquipmentRequest;
use App\Models\Event;
use App\Models\Handover;
use App\Models\Report;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds each role's dashboard around what that person is responsible for:
 * an action inbox ("needs you"), the figures they manage, and role-specific panels.
 *
 * Every task is ['title', 'meta', 'url', 'tone' => critical|warning|info, 'cta'].
 */
class DashboardService
{
    private Carbon $today;

    public function __construct()
    {
        $this->today = Carbon::today();
    }

    public function forUser(User $user): array
    {
        return match (true) {
            $user->isChaplain() => $this->chaplain(),
            $user->isChapelAssistant() => $this->assistant(),
            $user->isChapelSecretary() => $this->secretary(),
            $user->isClubLeader() => $this->clubLeader($user),
            default => $this->observer(),
        };
    }

    /**
     * University Chaplain: final decisions, VC liaison, preaching ministry, pastoral oversight of reports.
     */
    private function chaplain(): array
    {
        $pending = $this->pendingDecisions();
        $reportsToReview = Report::with(['club', 'event'])->where('status', 'submitted')->latest()->get();
        $handoversToSeal = Handover::with('club')->where('status', 'meeting_scheduled')->get();

        $tasks = collect()
            ->merge($this->decisionTasks($pending))
            ->merge($handoversToSeal->map(fn ($h) => [
                'title' => "Seal leadership handover: {$h->club?->name}",
                'meta' => "Meeting recorded • transfer to {$h->incoming_president_name}",
                'url' => route('handover.show', $h->id),
                'tone' => 'info',
                'cta' => 'Complete',
            ]))
            ->merge($reportsToReview->take(5)->map(fn ($r) => [
                'title' => "Review report: {$r->title}",
                'meta' => ($r->club?->name ?? 'Club') . " • {$r->attendance} attended • spent \${$r->spending}",
                'url' => route('reports.show', $r->id),
                'tone' => 'info',
                'cta' => 'Add remarks',
            ]))
            ->merge($this->worshipTasks());

        $monthStart = $this->today->copy()->startOfMonth();

        return [
            'stats' => [
                $this->stat('Awaiting your decision', $pending->count(), 'Submitted & under review', route('requests.index', ['status' => 'submitted']), $pending->isNotEmpty() ? 'critical' : 'neutral'),
                $this->stat('Need VC go-ahead', $pending->where('vc_go_ahead_needed', true)->where('vc_go_ahead_status', '!=', 'received')->count(), 'Outings & missions', route('requests.index', ['category' => 'outing']), 'warning'),
                $this->stat('Reports to review', $reportsToReview->count(), 'Post-activity (§7.1.2)', route('reports.index'), 'info'),
                $this->stat('Activities this month', Event::where('status', '!=', 'cancelled')->whereBetween('event_date', [$monthStart, $monthStart->copy()->endOfMonth()])->count(), 'On the Master Calendar', route('calendar.index'), 'neutral'),
            ],
            'tasks' => $this->sortTasks($tasks),
            'decisionQueue' => $pending->take(6),
            'services' => $this->upcomingServices(3),
            'upcomingEvents' => $this->upcomingEvents(7),
            'pulse' => $this->ministryPulse(),
        ];
    }

    /**
     * Assistant Chaplain: day-to-day scheduling, venue logistics, equipment desk and liturgy coordination.
     */
    private function assistant(): array
    {
        $pending = $this->pendingDecisions();
        $equipmentToAction = EquipmentRequest::with('club')->whereIn('status', ['requested', 'approved'])->orderBy('needed_date')->get();
        $overdueReturns = EquipmentRequest::with('club')->where('status', 'issued')->whereNotNull('return_date')->whereDate('return_date', '<', $this->today)->get();
        $clashes = $this->upcomingClashes(14);

        $tasks = collect()
            ->merge($this->decisionTasks($pending))
            ->merge($clashes->map(fn ($pair) => [
                'title' => "Venue clash: {$pair[0]->title} / {$pair[1]->title}",
                'meta' => $pair[0]->event_date->format('D M j') . ' • ' . ($pair[0]->venue?->name ?? 'Venue'),
                'url' => route('calendar.index', ['month' => $pair[0]->event_date->month, 'year' => $pair[0]->event_date->year]),
                'tone' => 'critical',
                'cta' => 'Resolve',
            ]))
            ->merge($equipmentToAction->map(fn ($e) => [
                'title' => ($e->status === 'requested' ? 'Approve' : 'Issue') . " equipment: {$e->quantity}× {$e->item_name}",
                'meta' => ($e->club?->name ?? 'Club') . ' • needed ' . $e->needed_date->format('D M j'),
                'url' => route('equipment.index', ['status' => $e->status]),
                'tone' => $e->needed_date->lte($this->today->copy()->addDays(2)) ? 'warning' : 'info',
                'cta' => $e->status === 'requested' ? 'Review' : 'Issue',
            ]))
            ->merge($overdueReturns->map(fn ($e) => [
                'title' => "Overdue return: {$e->quantity}× {$e->item_name}",
                'meta' => ($e->club?->name ?? 'Club') . ' • was due ' . $e->return_date->format('M j'),
                'url' => route('equipment.index', ['status' => 'issued']),
                'tone' => 'critical',
                'cta' => 'Follow up',
            ]))
            ->merge($this->worshipTasks());

        return [
            'stats' => [
                $this->stat('Awaiting decision', $pending->count(), 'Review queue', route('requests.index', ['status' => 'submitted']), $pending->isNotEmpty() ? 'critical' : 'neutral'),
                $this->stat("Today's activities", Event::where('status', '!=', 'cancelled')->whereDate('event_date', $this->today)->count(), 'Across all venues', route('calendar.index'), 'neutral'),
                $this->stat('Equipment to action', $equipmentToAction->count() + $overdueReturns->count(), $overdueReturns->count() . ' overdue returns', route('equipment.index'), $overdueReturns->isNotEmpty() ? 'critical' : 'info'),
                $this->stat('Venue clashes (14 days)', $clashes->count(), 'Double bookings', route('calendar.index'), $clashes->isNotEmpty() ? 'critical' : 'neutral'),
            ],
            'tasks' => $this->sortTasks($tasks),
            'decisionQueue' => $pending->take(6),
            'todaysSchedule' => Event::with(['club', 'venue'])->where('status', '!=', 'cancelled')->whereDate('event_date', $this->today)->orderBy('start_time')->get(),
            'weekByDay' => $this->upcomingEvents(7)->groupBy(fn ($e) => $e->event_date->toDateString()),
            'services' => $this->upcomingServices(3),
        ];
    }

    /**
     * Chapel Secretary: charters, accounts, records vault compliance and institutional memory.
     */
    private function secretary(): array
    {
        $charters = Club::pending()->latest()->get();
        $foldersToVerify = Handover::with('club')->where('status', 'checklist_completed')->get();
        $reportsToReview = Report::with('club')->where('status', 'submitted')->latest()->get();

        $vaultLeague = Club::active()->orderBy('name')->get()
            ->map(fn ($club) => ['club' => $club, 'health' => $club->getArchiveHealthScore()])
            ->sortBy(fn ($row) => $row['health']['score'])
            ->values();

        $suspended = User::where('status', '!=', 'active')->count();
        $awaitingFirstLogin = User::where('must_change_password', true)->count();

        $tasks = collect()
            ->merge($charters->map(fn ($c) => [
                'title' => "Charter application: {$c->name}",
                'meta' => "{$c->code} • {$c->founding_president_name} • lodged " . $c->created_at->diffForHumans(),
                'url' => route('clubs.index', ['status' => 'pending']),
                'tone' => 'warning',
                'cta' => 'Process',
            ]))
            ->merge($foldersToVerify->map(fn ($h) => [
                'title' => "Verify vault folders for handover: {$h->club?->name}",
                'meta' => "Checklist completed • {$h->academic_year}",
                'url' => route('handover.show', $h->id),
                'tone' => 'warning',
                'cta' => 'Verify',
            ]))
            ->merge($vaultLeague->filter(fn ($row) => $row['health']['score'] < 60)->map(fn ($row) => [
                'title' => "Vault at risk: {$row['club']->name} ({$row['health']['score']}%)",
                'meta' => 'Missing: ' . implode(', ', array_slice($row['health']['missing'], 0, 2)),
                'url' => route('vault.index', ['club_id' => $row['club']->id]),
                'tone' => 'info',
                'cta' => 'Open vault',
            ]))
            ->merge($reportsToReview->take(5)->map(fn ($r) => [
                'title' => "File report remarks: {$r->title}",
                'meta' => $r->club?->name ?? 'Club',
                'url' => route('reports.show', $r->id),
                'tone' => 'info',
                'cta' => 'Review',
            ]));

        return [
            'stats' => [
                $this->stat('Charter applications', $charters->count(), 'Awaiting registration', route('clubs.index', ['status' => 'pending']), $charters->isNotEmpty() ? 'warning' : 'neutral'),
                $this->stat('Avg. vault compliance', round($vaultLeague->avg(fn ($row) => $row['health']['score']) ?? 0) . '%', $vaultLeague->count() . ' active fellowships', route('vault.index'), 'info'),
                $this->stat('Accounts pending first login', $awaitingFirstLogin, $suspended . ' suspended', route('users.index'), 'neutral'),
                $this->stat('Records in vault', Document::where('is_archived', false)->count(), 'Active documents', route('vault.index'), 'neutral'),
            ],
            'tasks' => $this->sortTasks($tasks),
            'charters' => $charters,
            'vaultLeague' => $vaultLeague,
            'recentDocuments' => Document::with(['club', 'uploader'])->latest()->take(6)->get(),
            'auditLogs' => AuditLog::with('user')->latest()->take(8)->get(),
        ];
    }

    /**
     * Club President / Executive: run the fellowship, propose activities, report back, keep the vault complete.
     */
    private function clubLeader(User $user): array
    {
        $club = $user->club;
        if (!$club) {
            return ['stats' => [], 'tasks' => collect(), 'club' => null];
        }

        $myRequests = ChapelRequest::with('venue')->where('club_id', $club->id)->latest()->get();
        $needsChanges = $myRequests->where('status', 'needs_changes');
        $reportsDue = Event::where('club_id', $club->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('event_date', '<', $this->today)
            ->whereDoesntHave('report')
            ->orderByDesc('event_date')
            ->get();
        $upcoming = Event::with(['venue', 'request'])
            ->where('club_id', $club->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('event_date', '>=', $this->today)
            ->orderBy('event_date')->orderBy('start_time')
            ->get();
        $health = $club->getArchiveHealthScore();
        $openHandover = Handover::where('club_id', $club->id)->whereIn('status', ['initiated'])->first();
        $equipmentDue = EquipmentRequest::where('club_id', $club->id)->where('status', 'issued')->whereNotNull('return_date')
            ->whereDate('return_date', '<=', $this->today->copy()->addDays(2))->get();

        $tasks = collect()
            ->merge($needsChanges->map(fn ($r) => [
                'title' => "Revise & resubmit: {$r->title}",
                'meta' => 'Chaplaincy: ' . \Illuminate\Support\Str::limit((string) $r->decision_reason, 70),
                'url' => route('requests.edit', $r->id),
                'tone' => 'critical',
                'cta' => 'Revise',
            ]))
            ->merge($reportsDue->map(fn ($e) => [
                'title' => "Submit post-activity report: {$e->title}",
                'meta' => 'Held ' . $e->event_date->format('M j') . ' • required within 7 days (§7.1.2)',
                'url' => route('reports.create', ['event_id' => $e->id]),
                'tone' => $e->event_date->lt($this->today->copy()->subDays(7)) ? 'critical' : 'warning',
                'cta' => 'Report',
            ]))
            ->merge($openHandover ? [[
                'title' => 'Complete your leadership handover checklist',
                'meta' => "Handover to {$openHandover->incoming_president_name} ({$openHandover->academic_year})",
                'url' => route('handover.show', $openHandover->id),
                'tone' => 'warning',
                'cta' => 'Open',
            ]] : [])
            ->merge($equipmentDue->map(fn ($e) => [
                'title' => "Return equipment: {$e->quantity}× {$e->item_name}",
                'meta' => 'Due back ' . $e->return_date->format('D M j'),
                'url' => route('equipment.index'),
                'tone' => $e->return_date->lt($this->today) ? 'critical' : 'warning',
                'cta' => 'View',
            ]))
            ->merge($upcoming->filter(fn ($e) => $e->request_id && $e->event_date->lte($this->today->copy()->addDays(7)))->map(fn ($e) => [
                'title' => "Print clearance pass: {$e->title}",
                'meta' => $e->event_date->format('D M j') . ' • show at the venue gate',
                'url' => route('requests.clearance_pass', $e->request_id),
                'tone' => 'info',
                'cta' => 'Open pass',
            ]))
            ->merge(collect($health['missing'])->take(3)->map(fn ($folder) => [
                'title' => "Upload to vault: {$folder}",
                'meta' => "Vault health {$health['score']}%",
                'url' => route('vault.index'),
                'tone' => 'info',
                'cta' => 'Upload',
            ]));

        $data = [
            'club' => $club,
            'stats' => [
                $this->stat('Awaiting decision', $myRequests->whereIn('status', ['submitted', 'under_review'])->count(), 'Proposals with the Chaplaincy', route('requests.index'), 'info'),
                $this->stat('Need your changes', $needsChanges->count(), 'Returned proposals', route('requests.index', ['status' => 'needs_changes']), $needsChanges->isNotEmpty() ? 'critical' : 'neutral'),
                $this->stat('Reports due', $reportsDue->count(), 'Past activities', route('reports.create'), $reportsDue->isNotEmpty() ? 'warning' : 'neutral'),
                $this->stat('Active members', ClubMember::where('club_id', $club->id)->where('status', 'active')->count(), 'On the roster', route('members.index'), 'neutral'),
            ],
            'tasks' => $this->sortTasks($tasks),
            'myRequests' => $myRequests->take(5),
            'upcoming' => $upcoming->take(4),
            'health' => $health,
            'lastMeetingAttendance' => \App\Models\Attendance::where('club_id', $club->id)->where('status', 'present')
                ->selectRaw('meeting_date, count(*) as present')->groupBy('meeting_date')->orderByDesc('meeting_date')->take(6)->get()->reverse()->values(),
        ];

        // Club presidents sit on the Chapel Committee and help plan worship
        if ($user->isClubPresident()) {
            $data['services'] = $this->upcomingServices(2);
        }

        return $data;
    }

    /**
     * Campus Life & SRC: read-only awareness of activities that affect campus logistics.
     */
    private function observer(): array
    {
        $upcoming = $this->upcomingEvents(14);
        $large = $upcoming->filter(fn ($e) => (int) $e->expected_attendance >= 300);
        $lateNight = $upcoming->filter(fn ($e) => substr((string) $e->end_time, 0, 5) > '21:00' || substr((string) $e->end_time, 0, 5) < substr((string) $e->start_time, 0, 5));

        return [
            'stats' => [
                $this->stat("Today's activities", $upcoming->filter(fn ($e) => $e->event_date->isSameDay($this->today))->count(), 'Cleared by Chaplaincy', route('calendar.index'), 'neutral'),
                $this->stat('Next 14 days', $upcoming->count(), 'Approved activities', route('calendar.index'), 'neutral'),
                $this->stat('Large gatherings', $large->count(), '300+ expected', route('calendar.index'), $large->isNotEmpty() ? 'warning' : 'neutral'),
                $this->stat('Late-evening events', $lateNight->count(), 'Ending after 21:00', route('calendar.index'), 'info'),
            ],
            'tasks' => $this->sortTasks($large->merge($lateNight)->unique('id')->map(fn ($e) => [
                'title' => "Logistics notice: {$e->title}",
                'meta' => $e->event_date->format('D M j') . ' • ' . substr($e->start_time, 0, 5) . '–' . substr($e->end_time, 0, 5) . ' • ' . ($e->venue?->name ?? $e->venue_name) . ' • ' . (int) $e->expected_attendance . ' expected',
                'url' => route('calendar.index', ['month' => $e->event_date->month, 'year' => $e->event_date->year]),
                'tone' => (int) $e->expected_attendance >= 300 ? 'warning' : 'info',
                'cta' => 'View',
            ])),
            'weekByDay' => $upcoming->groupBy(fn ($e) => $e->event_date->toDateString()),
        ];
    }

    private function pendingDecisions(): Collection
    {
        return ChapelRequest::with(['club', 'venue'])
            ->whereIn('status', ['submitted', 'under_review'])
            ->orderBy('event_date')
            ->get();
    }

    private function decisionTasks(Collection $pending): Collection
    {
        return $pending->map(function ($r) {
            $days = (int) $this->today->diffInDays($r->event_date, false);
            $flags = array_filter([
                $r->vc_go_ahead_needed && $r->vc_go_ahead_status !== 'received' ? 'needs VC go-ahead' : null,
                $r->is_late_submission ? 'late notice' : null,
                (int) $r->expected_attendance >= 300 ? (int) $r->expected_attendance . ' expected' : null,
            ]);

            return [
                'title' => "Decide: {$r->title}",
                'meta' => ($r->club?->name ?? 'Club') . ' • ' . ($days <= 0 ? 'happens today' : "in {$days} days") . ($flags ? ' • ' . implode(' • ', $flags) : ''),
                'url' => route('requests.show', $r->id),
                'tone' => $days <= 7 ? 'critical' : ($days <= 14 ? 'warning' : 'info'),
                'cta' => 'Review',
            ];
        });
    }

    private function worshipTasks(): Collection
    {
        return ServiceSheet::whereBetween('service_date', [$this->today, $this->today->copy()->addDays(7)])
            ->orderBy('service_date')
            ->get()
            ->flatMap(function ($s) {
                $days = (int) $this->today->diffInDays($s->service_date, false);
                $label = $s->service_type . ' (' . $s->service_date->format('D M j') . ')';
                $items = [];

                if (!$s->preacher_submitted_materials && $days <= 3) {
                    $items[] = ['title' => "Preacher materials outstanding: {$label}", 'meta' => ($s->preacher ?: 'Preacher not assigned') . ' • due 3 days before', 'url' => route('worship.edit', $s->id), 'tone' => 'critical', 'cta' => 'Update'];
                }
                if (!$s->media_notified && $days <= 2) {
                    $items[] = ['title' => "Send order of service to media: {$label}", 'meta' => 'Due 2 days before the service', 'url' => route('worship.show', $s->id), 'tone' => 'warning', 'cta' => 'Dispatch'];
                }
                if ($s->status === 'draft') {
                    $items[] = ['title' => "Finalise service sheet: {$label}", 'meta' => 'Still in draft', 'url' => route('worship.edit', $s->id), 'tone' => $days <= 2 ? 'warning' : 'info', 'cta' => 'Edit'];
                }

                return $items;
            });
    }

    private function upcomingServices(int $limit): Collection
    {
        return ServiceSheet::whereDate('service_date', '>=', $this->today)->orderBy('service_date')->take($limit)->get();
    }

    private function upcomingEvents(int $days): Collection
    {
        return Event::with(['club', 'venue'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('event_date', [$this->today->toDateString(), $this->today->copy()->addDays($days)->toDateString()])
            ->orderBy('event_date')->orderBy('start_time')
            ->get();
    }

    /**
     * Pairs of live bookings at the same venue whose hours overlap.
     */
    private function upcomingClashes(int $days): Collection
    {
        $pairs = collect();

        $this->upcomingEvents($days)->whereNotNull('venue_id')
            ->groupBy(fn ($e) => $e->event_date->toDateString() . '|' . $e->venue_id)
            ->each(function ($group) use ($pairs) {
                $events = $group->values();
                for ($i = 0; $i < $events->count(); $i++) {
                    for ($j = $i + 1; $j < $events->count(); $j++) {
                        if ($events[$i]->start_time < $events[$j]->end_time && $events[$i]->end_time > $events[$j]->start_time) {
                            $pairs->push([$events[$i], $events[$j]]);
                        }
                    }
                }
            });

        return $pairs;
    }

    private function ministryPulse(): array
    {
        $yearStart = $this->today->copy()->startOfYear();

        $budget = (float) ChapelRequest::where('status', 'approved')->whereDate('event_date', '>=', $yearStart)->sum('budget');
        $spent = (float) Report::whereHas('request', fn ($q) => $q->where('status', 'approved')->whereDate('event_date', '>=', $yearStart))->sum('spending');

        return [
            'activities' => Event::where('status', '!=', 'cancelled')->whereDate('event_date', '>=', $yearStart)->whereDate('event_date', '<', $this->today)->count(),
            'attendance' => (int) Report::whereDate('created_at', '>=', $yearStart)->sum('attendance'),
            'reportRate' => $this->reportRate($yearStart),
            'budget' => $budget,
            'spent' => $spent,
            'activeClubs' => Club::active()->count(),
        ];
    }

    private function reportRate(Carbon $since): int
    {
        $held = Event::where('status', '!=', 'cancelled')->whereDate('event_date', '>=', $since)->whereDate('event_date', '<', $this->today);
        $total = (clone $held)->count();

        return $total === 0 ? 100 : (int) round((clone $held)->has('report')->count() / $total * 100);
    }

    private function stat(string $label, int|string $value, string $hint, string $url, string $tone): array
    {
        return compact('label', 'value', 'hint', 'url', 'tone');
    }

    private function sortTasks(Collection $tasks): Collection
    {
        $order = ['critical' => 0, 'warning' => 1, 'info' => 2];

        return $tasks->values()->sortBy(fn ($t) => $order[$t['tone']] ?? 3)->values();
    }
}
