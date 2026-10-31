<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\Venue;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboards)
    {
        $user = Auth::user();

        $announcements = Announcement::with('sender')
            ->where(function ($query) use ($user) {
                $query->where('audience', 'all')
                      ->when($user->club_id, fn ($q) => $q->orWhere('target_club_id', $user->club_id));
            })
            ->orderBy('is_pinned', 'desc')
            ->orderBy('published_date', 'desc')
            ->take(4)
            ->get();

        $dash = $dashboards->forUser($user);

        return view('dashboard.index', compact('user', 'announcements', 'dash'));
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
