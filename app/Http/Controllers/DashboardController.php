<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Document;
use App\Models\EquipmentRequest;
use App\Models\Event;
use App\Models\Handover;
use App\Models\Request as ChapelRequest;
use App\Models\ServiceSheet;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Common announcements for everyone
        $announcements = Announcement::with('sender')
            ->where(function ($query) use ($user) {
                $query->where('audience', 'all')
                      ->orWhere('target_club_id', $user->club_id);
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_date', 'desc')
            ->take(4)
            ->get();

        // Role-tailored data pack
        $roleData = [];

        if ($user->isChaplain()) {
            // 1. REV. DR. MUCHANYEREYI - UNIVERSITY CHAPLAIN (Supreme Liturgical & Executive Governance)
            $roleData['pending_vc_clearance'] = ChapelRequest::where('vc_go_ahead_needed', true)
                ->whereIn('status', ['submitted', 'under_review'])
                ->count();
            $roleData['high_impact_reviews'] = ChapelRequest::whereIn('status', ['submitted', 'under_review'])
                ->where(function ($q) {
                    $q->where('vc_go_ahead_needed', true)
                      ->orWhere('expected_attendance', '>=', 300);
                })->count();
            $roleData['pending_handovers_count'] = Handover::where('status', '!=', 'completed')->count();
            $roleData['late_waiver_count'] = ChapelRequest::where('is_late_submission', true)
                ->whereIn('status', ['submitted', 'under_review'])
                ->count();

            // Next Sunday Convocational Service Liturgy
            $roleData['next_sunday_service'] = ServiceSheet::where('service_date', '>=', $today)
                ->where('service_type', 'like', '%Sunday%')
                ->orderBy('service_date', 'asc')
                ->first();

            // High priority requests requiring Chaplain's final seal
            $roleData['executiveRequests'] = ChapelRequest::with(['club', 'requester', 'venue'])
                ->where(function ($q) {
                    $q->where('vc_go_ahead_needed', true)
                      ->orWhere('expected_attendance', '>=', 300)
                      ->orWhere('is_late_submission', true);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Handover certificates awaiting Chaplain's signature
            $roleData['pendingHandovers'] = Handover::with(['club', 'outgoingPresident'])
                ->where('status', '!=', 'completed')
                ->take(3)
                ->get();

        } elseif ($user->isChapelAssistant()) {
            // 2. PASTOR MICAH DAVID - ASSISTANT CHAPLAIN (Master Operational Scheduler & Liturgy Coordinator)
            $roleData['operational_pending'] = ChapelRequest::whereIn('status', ['submitted', 'under_review'])->count();
            $roleData['todays_events_count'] = Event::where('event_date', $today)->count();
            $roleData['pending_equipment_count'] = EquipmentRequest::where('status', 'requested')->count();
            $roleData['late_submissions_count'] = ChapelRequest::where('is_late_submission', true)
                ->whereIn('status', ['submitted', 'under_review'])->count();

            // Operational queue for review
            $roleData['operationalRequests'] = ChapelRequest::with(['club', 'requester', 'venue'])
                ->whereIn('status', ['submitted', 'under_review'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            // Today's schedule across venues
            $roleData['todaysSchedule'] = Event::with(['club', 'venue'])
                ->where('event_date', $today)
                ->orderBy('start_time', 'asc')
                ->get();

            // Equipment requests needing dispatch/approval
            $roleData['pendingEquipment'] = EquipmentRequest::with(['club', 'requester'])
                ->where('status', 'requested')
                ->take(4)
                ->get();

            // Liturgy milestones (Wednesday and Sunday)
            $roleData['upcomingServices'] = ServiceSheet::where('service_date', '>=', $today)
                ->orderBy('service_date', 'asc')
                ->take(2)
                ->get();

        } elseif ($user->isChapelSecretary()) {
            // 3. MRS. CONSTANCE MARUME - CHAPEL SECRETARY (Chief Registrar & Records Sentinel)
            $roleData['pending_club_charters'] = Club::where('status', 'pending_approval')->count();
            $roleData['active_chartered_clubs'] = Club::where('status', 'active')->count();
            $roleData['total_vault_documents'] = Document::count();
            
            $activeClubs = Club::active()->get();
            $roleData['vault_compliance_avg'] = round($activeClubs->avg(function ($club) {
                return $club->getArchiveHealthScore()['score'];
            }) ?? 85);

            // Pending fellowship charters to process
            $roleData['pendingCharters'] = Club::where('status', 'pending_approval')
                ->orderBy('created_at', 'desc')
                ->get();

            // Recent vault uploads
            $roleData['recentDocuments'] = Document::with(['club', 'uploader'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            // System audit trail
            $roleData['auditLogs'] = AuditLog::with('user')
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

        } elseif ($user->isClubExecutive() || $user->isClubPresident()) {
            // 4. CLUB LEADERS (KUDZAI / TINASHE) - "LITTLE THINGS" LIGHTWEIGHT DASHBOARD
            $clubId = $user->club_id;
            $myClub = $user->club;

            $roleData['myClub'] = $myClub;
            $roleData['my_members_count'] = ClubMember::where('club_id', $clubId)->where('status', 'active')->count();
            $roleData['my_pending_proposals'] = ChapelRequest::where('club_id', $clubId)
                ->whereIn('status', ['submitted', 'under_review'])->count();
            $roleData['my_approved_proposals'] = ChapelRequest::where('club_id', $clubId)
                ->where('status', 'approved')->count();
            $roleData['my_vault_health'] = $myClub ? $myClub->getArchiveHealthScore() : ['score' => 100, 'rating' => 'A'];

            // My Proposals list
            $roleData['myRequests'] = ChapelRequest::with(['venue'])
                ->where('club_id', $clubId)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();

            // Next approved fellowship event
            $roleData['myNextEvent'] = Event::where('club_id', $clubId)
                ->where('event_date', '>=', $today)
                ->orderBy('event_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->first();

            // Recent members
            $roleData['myMembers'] = ClubMember::where('club_id', $clubId)
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get();

            // My equipment requests
            $roleData['myEquipment'] = EquipmentRequest::where('club_id', $clubId)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();

        } else {
            // 5. CAMPUS LIFE & SRC - OBSERVER CALENDAR & CONVOCATIONS
            $roleData['approved_month_count'] = Event::whereBetween('event_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])->count();
            $roleData['total_active_clubs'] = Club::where('status', 'active')->count();

            // Today's campus events
            $roleData['todaysCampusEvents'] = Event::with(['club', 'venue'])
                ->where('event_date', $today)
                ->orderBy('start_time', 'asc')
                ->get();

            // Upcoming university calendar
            $roleData['upcomingCampusEvents'] = Event::with(['club', 'venue'])
                ->where('event_date', '>=', $today)
                ->orderBy('event_date', 'asc')
                ->orderBy('start_time', 'asc')
                ->take(8)
                ->get();
        }

        // Common metrics & upcoming events for generic fallbacks
        $metrics = [
            'pending_requests' => ChapelRequest::whereIn('status', ['submitted', 'under_review'])->count(),
            'approved_events' => Event::where('event_date', '>=', $today)->count(),
            'needs_vc_go_ahead' => ChapelRequest::where('vc_go_ahead_needed', true)->where('status', 'under_review')->count(),
            'late_submissions' => ChapelRequest::where('is_late_submission', true)->whereIn('status', ['submitted', 'under_review'])->count(),
            'total_clubs' => Club::active()->count(),
            'vault_documents' => Document::count(),
        ];

        $upcomingEvents = Event::with(['club', 'venue'])
            ->where('event_date', '>=', $today)
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'announcements',
            'roleData',
            'metrics',
            'upcomingEvents'
        ));
    }

    /**
     * Institutional Guidelines & Policy Manual
     */
    public function guidelines()
    {
        $user = Auth::user();
        return view('guidelines.index', compact('user'));
    }

    /**
     * Institutional Audit Trail & Activity Sentinel
     */
    public function audit(Request $request)
    {
        $user = Auth::user();
        $actionFilter = $request->input('action');
        $logs = AuditLog::with(['user', 'request.club'])
            ->when($actionFilter, fn($q) => $q->where('action', 'like', "%{$actionFilter}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $totalLogsCount = AuditLog::count();
        $approvalActionsCount = AuditLog::where('action', 'like', '%approve%')->count();
        $submissionActionsCount = AuditLog::where('action', 'like', '%submit%')->count();

        return view('audit.index', compact(
            'user',
            'logs',
            'actionFilter',
            'totalLogsCount',
            'approvalActionsCount',
            'submissionActionsCount'
        ));
    }

    /**
     * Instant Command Palette Search API (Ctrl + K)
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $user = Auth::user();

        // Requests matching title, purpose, reference_no (club leaders: own club only; observers: none)
        $requests = ChapelRequest::with('club')
            ->when(!$user->isOffice(), function ($q) use ($user) {
                $user->isClubLeader() ? $q->where('club_id', $user->club_id) : $q->whereRaw('1 = 0');
            })
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('reference_no', 'like', "%{$query}%")
                  ->orWhere('purpose', 'like', "%{$query}%");
            })
            ->take(5)
            ->get()
            ->map(function ($r) {
                return [
                    'category' => 'Proposal',
                    'title' => $r->title,
                    'subtitle' => "Ref: {$r->reference_no} • " . ($r->club?->name ?? 'Unknown club') . ' (' . str_replace('_', ' ', $r->status) . ')',
                    'url' => route('requests.show', $r->id),
                ];
            });

        // Active clubs matching name or code (vault link only where the user may open it)
        $clubs = Club::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")->orWhere('code', 'like', "%{$query}%");
            })
            ->take(4)
            ->get()
            ->map(function ($c) use ($user) {
                return [
                    'category' => 'Fellowship',
                    'title' => $c->name,
                    'subtitle' => "{$c->type} • {$c->meeting_schedule}",
                    'url' => $user->canAccessClub($c->id)
                        ? route('vault.index', ['club_id' => $c->id])
                        : route('calendar.index', ['club_id' => $c->id]),
                ];
            });

        // Events matching title
        $events = Event::with('venue')
            ->where('title', 'like', "%{$query}%")
            ->where('status', '!=', 'cancelled')
            ->take(4)
            ->get()
            ->map(function ($e) {
                return [
                    'category' => 'Calendar Event',
                    'title' => $e->title,
                    'subtitle' => $e->event_date->format('M d, Y') . ' • ' . ($e->venue?->name ?? $e->venue_name ?? 'Venue TBC'),
                    'url' => route('calendar.index', ['month' => $e->event_date->month, 'year' => $e->event_date->year]),
                ];
            });

        return response()->json([
            'results' => $requests->concat($clubs)->concat($events)->values(),
        ]);
    }
}
