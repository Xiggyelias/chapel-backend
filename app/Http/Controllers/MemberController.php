<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Club;
use App\Models\ClubMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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

        $selectedClub = $this->resolveClub($user, $request->input('club_id'));
        $clubs = $user->isOffice() ? Club::active()->get() : collect([$selectedClub]);

        $members = ClubMember::where('club_id', $selectedClub->id)
            ->withCount(['attendances as present_count' => function ($q) {
                $q->where('status', 'present');
            }])
            ->orderBy('full_name', 'asc')
            ->paginate(15)
            ->withQueryString();

        // Recent attendance meeting dates for this club
        $recentMeetings = Attendance::where('club_id', $selectedClub->id)
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

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_in_club' => ['required', 'string', 'max:100'],
        ]);

        $this->ensureCanManage($user, (int) $validated['club_id']);

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

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'meeting_date' => ['required', 'date', 'before_or_equal:today'],
            'attendance' => ['required', 'array'], // [member_id => 'present'|'absent'|'excused']
            'attendance.*' => ['required', Rule::in(['present', 'absent', 'excused'])],
        ]);

        $clubId = (int) $validated['club_id'];
        $this->ensureCanManage($user, $clubId);

        // Ignore any member ids that do not belong to this club's roster
        $rosterIds = ClubMember::where('club_id', $clubId)
            ->whereIn('id', array_keys($validated['attendance']))
            ->pluck('id')
            ->all();

        foreach ($rosterIds as $memberId) {
            Attendance::updateOrCreate(
                [
                    'club_id' => $clubId,
                    'club_member_id' => $memberId,
                    'meeting_date' => $validated['meeting_date'],
                ],
                ['status' => $validated['attendance'][$memberId]]
            );
        }

        $count = count($rosterIds);

        return back()->with('success', "Meeting attendance register recorded for {$count} members on {$validated['meeting_date']}.");
    }

    /**
     * QR Attendance Kiosk: displays meeting QR code for student check-in
     */
    public function qrKiosk(Request $request, $clubId = null)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        $club = $this->resolveClub($user, $clubId);
        $today = Carbon::today()->toDateString();
        $token = $club->checkinToken($today);

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
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC Observer Mode: Authorized to view University Master Calendar and public feeds only.');
        }

        $club = $this->resolveClub($user, $clubId);
        $members = ClubMember::where('club_id', $club->id)->get();
        $today = Carbon::today()->toDateString();

        return view('members.scanner', compact('club', 'members', 'today'));
    }

    /**
     * Leader scanner API: record a scanned student ID / email for the leader's club
     */
    public function processQrCheckin(Request $request)
    {
        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'student_id_or_email' => ['required', 'string', 'max:255'],
            'meeting_date' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $clubId = (int) $validated['club_id'];
        $this->ensureCanManage(Auth::user(), $clubId);

        return response()->json($this->checkIn(
            $clubId,
            trim($validated['student_id_or_email']),
            $validated['meeting_date'] ?? Carbon::today()->toDateString()
        ));
    }

    /**
     * Student Self Check-in view (when scanning kiosk QR)
     */
    public function selfCheckin(Request $request, $token)
    {
        $today = Carbon::today()->toDateString();
        $matchingClub = Club::findByCheckinToken($token, $today);

        return view('members.self_checkin', compact('matchingClub', 'token', 'today'));
    }

    /**
     * Public self check-in submission, valid only with today's kiosk token
     */
    public function processSelfCheckin(Request $request, $token)
    {
        $today = Carbon::today()->toDateString();
        $club = Club::findByCheckinToken($token, $today);

        if (!$club) {
            return response()->json([
                'success' => false,
                'message' => 'This check-in code has expired or is invalid. Please scan today\'s kiosk QR code.',
            ], 422);
        }

        $validated = $request->validate([
            'student_id_or_email' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        return response()->json($this->checkIn($club->id, trim($validated['student_id_or_email']), $today));
    }

    /**
     * Mark a person present, auto-enrolling first-time attendees as visitors.
     */
    private function checkIn(int $clubId, string $input, string $meetingDate): array
    {
        $isEmail = str_contains($input, '@');

        $member = ClubMember::where('club_id', $clubId)
            ->where(function ($q) use ($input, $isEmail) {
                if ($isEmail) {
                    $q->whereRaw('LOWER(email) = ?', [mb_strtolower($input)]);
                } else {
                    $q->where('student_id', $input)
                      ->orWhereRaw('LOWER(full_name) = ?', [mb_strtolower($input)]);
                }
            })
            ->first();

        if (!$member) {
            $member = ClubMember::create([
                'club_id' => $clubId,
                'full_name' => $input,
                'email' => $isEmail ? $input : null,
                'student_id' => $isEmail ? null : $input,
                'role_in_club' => 'Attendee / Visitor',
                'status' => 'active',
                'joined_date' => $meetingDate,
            ]);
        }

        Attendance::updateOrCreate(
            [
                'club_id' => $clubId,
                'club_member_id' => $member->id,
                'meeting_date' => $meetingDate,
            ],
            ['status' => 'present']
        );

        $totalPresent = Attendance::where('club_id', $clubId)
            ->where('meeting_date', $meetingDate)
            ->where('status', 'present')
            ->count();

        return [
            'success' => true,
            'member_name' => $member->full_name,
            'role' => $member->role_in_club,
            'total_present' => $totalPresent,
            'message' => "Checked in: {$member->full_name}",
        ];
    }

    /**
     * Club leaders are locked to their own club; the Office may pick any club (defaulting to the first active one).
     */
    private function resolveClub($user, $requestedClubId): Club
    {
        if ($user->isOffice()) {
            $clubId = $requestedClubId ?: Club::active()->orderBy('name')->value('id');
            abort_if(!$clubId, 404, 'No active fellowships are registered yet.');

            return Club::findOrFail($clubId);
        }

        abort_unless($user->isClubLeader() && $user->club_id, 403, 'Your account is not linked to a fellowship.');

        return Club::findOrFail($user->club_id);
    }

    private function ensureCanManage($user, int $clubId): void
    {
        if (!$user->canAccessClub($clubId)) {
            abort(403, 'Club Data Isolation: You may only manage your own club\'s roster.');
        }
    }
}
