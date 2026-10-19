<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Club;
use App\Models\ClubMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display club membership roster.
     * Enforces privacy: Visible ONLY to that club's leaders and Chaplaincy Office.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Unauthorized.');
        }

        $clubId = $user->isOffice() 
            ? ($request->input('club_id') ?? Club::active()->first()?->id)
            : $user->club_id;

        $selectedClub = Club::findOrFail($clubId);
        $clubs = $user->isOffice() ? Club::active()->get() : collect([$selectedClub]);

        $members = ClubMember::where('club_id', $clubId)
            ->withCount(['attendances as present_count' => function ($q) {
                $q->where('status', 'present');
            }])
            ->orderBy('full_name', 'asc')
            ->paginate(15);

        // Recent attendance meeting dates for this club
        $recentMeetings = Attendance::where('club_id', $clubId)
            ->select('meeting_date')
            ->distinct()
            ->orderBy('meeting_date', 'desc')
            ->take(5)
            ->pluck('meeting_date');

        return view('members.index', compact('members', 'selectedClub', 'clubs', 'recentMeetings', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'role_in_club' => ['required', 'string'],
        ]);

        if (($user->isClubExecutive() || $user->isClubPresident()) && $validated['club_id'] != $user->club_id) {
            abort(403, 'Unauthorized.');
        }

        ClubMember::create(array_merge($validated, [
            'status' => 'active',
            'joined_date' => now()->toDateString(),
        ]));

        return back()->with('success', "Member '{$validated['full_name']}' added to roster.");
    }

    /**
     * Take / Record meeting attendance register
     */
    public function recordAttendance(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'meeting_date' => ['required', 'date'],
            'attendance' => ['required', 'array'], // [member_id => 'present'|'absent'|'excused']
        ]);

        if (($user->isClubExecutive() || $user->isClubPresident()) && $validated['club_id'] != $user->club_id) {
            abort(403, 'Unauthorized.');
        }

        $count = 0;
        foreach ($validated['attendance'] as $memberId => $status) {
            Attendance::updateOrCreate(
                [
                    'club_id' => $validated['club_id'],
                    'club_member_id' => $memberId,
                    'meeting_date' => $validated['meeting_date'],
                ],
                [
                    'status' => $status,
                ]
            );
            $count++;
        }

        return back()->with('success', "Meeting attendance register recorded for {$count} members on {$validated['meeting_date']}.");
    }

    /**
     * QR Attendance Kiosk: displays meeting QR code for student check-in
     */
    public function qrKiosk(Request $request, $clubId = null)
    {
        $user = Auth::user();
        if ($user && $user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        $targetClubId = ($user && ($user->isClubExecutive() || $user->isClubPresident()))
            ? $user->club_id
            : ($clubId ?: ($user?->club_id ?: Club::active()->first()?->id));

        $club = Club::findOrFail($targetClubId);
        $today = Carbon::today()->toDateString();

        // Generate attendance checkin token for today's meeting
        $token = strtoupper(substr(hash('sha256', "AU_MEETING_{$club->id}_{$today}"), 0, 12));

        $attendeesToday = Attendance::with('member')
            ->where('club_id', $club->id)
            ->where('meeting_date', $today)
            ->where('status', 'present')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('members.qr_kiosk', compact('club', 'today', 'token', 'attendeesToday'));
    }

    /**
     * In-browser live camera scanner for leaders
     */
    public function scanner(Request $request, $clubId = null)
    {
        $user = Auth::user();
        if ($user && $user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        $targetClubId = ($user && ($user->isClubExecutive() || $user->isClubPresident()))
            ? $user->club_id
            : ($clubId ?: ($user?->club_id ?: Club::active()->first()?->id));

        $club = Club::findOrFail($targetClubId);
        $members = ClubMember::where('club_id', $club->id)->get();
        $today = Carbon::today()->toDateString();

        return view('members.scanner', compact('club', 'members', 'today'));
    }

    /**
     * API to record scanned QR check-in
     */
    public function processQrCheckin(Request $request)
    {
        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'student_id_or_email' => ['required', 'string'],
            'meeting_date' => ['nullable', 'date'],
        ]);

        $meetingDate = $validated['meeting_date'] ?: Carbon::today()->toDateString();
        $input = trim($validated['student_id_or_email']);

        // Find member by student_id or email or full_name
        $member = ClubMember::where('club_id', $validated['club_id'])
            ->where(function ($q) use ($input) {
                $q->where('student_id', $input)
                  ->orWhere('email', $input)
                  ->orWhere('full_name', 'like', "%{$input}%");
            })
            ->first();

        if (!$member) {
            // Auto-enroll attendee if not previously on roster
            $member = ClubMember::create([
                'club_id' => $validated['club_id'],
                'full_name' => $input,
                'email' => str_contains($input, '@') ? $input : null,
                'student_id' => !str_contains($input, '@') ? $input : null,
                'role_in_club' => 'Attendee / Visitor',
                'status' => 'active',
                'joined_date' => $meetingDate,
            ]);
        }

        Attendance::updateOrCreate(
            [
                'club_id' => $validated['club_id'],
                'club_member_id' => $member->id,
                'meeting_date' => $meetingDate,
            ],
            ['status' => 'present']
        );

        $totalPresent = Attendance::where('club_id', $validated['club_id'])
            ->where('meeting_date', $meetingDate)
            ->where('status', 'present')
            ->count();

        return response()->json([
            'success' => true,
            'member_name' => $member->full_name,
            'role' => $member->role_in_club,
            'total_present' => $totalPresent,
            'message' => "Checked in: {$member->full_name}",
        ]);
    }

    /**
     * Student Self Check-in view (when scanning kiosk QR)
     */
    public function selfCheckin(Request $request, $token)
    {
        $today = Carbon::today()->toDateString();
        $clubs = Club::active()->get();
        $matchingClub = null;
        foreach ($clubs as $c) {
            $expectedToken = strtoupper(substr(hash('sha256', "AU_MEETING_{$c->id}_{$today}"), 0, 12));
            if ($expectedToken === $token) {
                $matchingClub = $c;
                break;
            }
        }

        if (!$matchingClub) {
            $matchingClub = Club::active()->first();
        }

        return view('members.self_checkin', compact('matchingClub', 'token', 'today'));
    }
}
