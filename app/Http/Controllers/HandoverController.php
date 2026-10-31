<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Document;
use App\Models\Handover;
use App\Models\LeadershipTerm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class HandoverController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = Handover::with(['club', 'outgoingPresident', 'verifier', 'completer']);

        if (!$user->isOffice()) {
            $query->where('club_id', $user->club_id);
        }

        $handovers = $query->orderBy('created_at', 'desc')->paginate(10);
        $clubs = Club::active()->get();

        return view('handover.index', compact('handovers', 'clubs', 'user'));
    }

    /**
     * Step 1: The Office initiates a handover for the club
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Only the Chaplaincy Office can initiate an official leadership handover.');
        }

        $validated = $request->validate([
            'club_id' => ['required', Rule::exists('clubs', 'id')->where('status', 'active')],
            'academic_year' => ['required', 'string', 'max:20'],
            'incoming_president_name' => ['required', 'string', 'max:255'],
            'incoming_president_email' => ['required', 'email', 'max:255'],
            'incoming_president_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $club = Club::findOrFail($validated['club_id']);

        $openHandover = Handover::where('club_id', $club->id)->where('status', '!=', 'completed')->first();
        if ($openHandover) {
            return redirect()->route('handover.show', $openHandover->id)
                ->with('warning', "{$club->name} already has a handover in progress. Complete it before starting another.");
        }

        if ($error = $this->incomingEmailConflict(strtolower(trim($validated['incoming_president_email'])), $club)) {
            return back()->withInput()->withErrors(['incoming_president_email' => $error]);
        }

        $outgoingLeader = User::where('club_id', $club->id)
            ->where('status', 'active')
            ->orderByRaw("CASE role WHEN 'club_president' THEN 1 ELSE 2 END")
            ->whereIn('role', ['club_president', 'club_executive'])
            ->first();

        $handover = Handover::create([
            'club_id' => $club->id,
            'academic_year' => $validated['academic_year'],
            'outgoing_president_id' => $outgoingLeader?->id,
            'outgoing_president_name' => $outgoingLeader ? $outgoingLeader->name : ($club->current_president ?: 'Current Executive'),
            'incoming_president_name' => $validated['incoming_president_name'],
            'incoming_president_email' => strtolower(trim($validated['incoming_president_email'])),
            'incoming_president_phone' => $validated['incoming_president_phone'],
            'status' => 'initiated',
        ]);

        AuditLog::record('handover_initiated', "Initiated {$validated['academic_year']} leadership handover for {$club->name} to {$handover->incoming_president_name}.");

        return redirect()->route('handover.show', $handover->id)->with('success', "Handover process initiated for {$club->name}. Outgoing executive must now complete checklist.");
    }

    public function show($id)
    {
        $handover = Handover::with(['club', 'outgoingPresident', 'verifier', 'completer'])->findOrFail($id);
        $user = Auth::user();
        $this->ensureCanAccess($user, $handover);

        // Vault completeness check
        $vaultCount = Document::where('club_id', $handover->club_id)->where('is_archived', false)->count();

        return view('handover.show', compact('handover', 'vaultCount', 'user'));
    }

    /**
     * Step 2: The outgoing executive completes the checklist and note
     */
    public function updateChecklist(Request $request, $id)
    {
        $handover = Handover::findOrFail($id);
        $this->ensureCanAccess(Auth::user(), $handover);

        if (!in_array($handover->status, ['initiated', 'checklist_completed'], true)) {
            return back()->with('error', 'The checklist is locked once the Office has verified the club folders.');
        }

        $validated = $request->validate([
            'checklist_running_activities' => ['required', 'string', 'max:5000'],
            'checklist_pending_items' => ['required', 'string', 'max:5000'],
            'checklist_key_contacts' => ['required', 'string', 'max:5000'],
            'checklist_yearly_calendar' => ['required', 'string', 'max:5000'],
        ]);

        $handover->update(array_merge($validated, [
            'status' => 'checklist_completed',
        ]));

        return redirect()->route('handover.show', $handover->id)->with('success', 'Handover checklist and status notes recorded successfully.');
    }

    /**
     * Step 3: The Office confirms the club's folders are complete
     */
    public function verifyFolders(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Only the Chaplaincy Office can confirm folder completeness.');
        }

        $handover = Handover::findOrFail($id);
        if ($handover->status !== 'checklist_completed') {
            return back()->with('error', 'The outgoing executive must complete the handover checklist before folders can be verified.');
        }

        $handover->update([
            'folders_verified' => true,
            'folders_verified_by' => $user->id,
            'folders_verified_at' => now(),
            'status' => 'folders_verified',
        ]);

        return redirect()->route('handover.show', $handover->id)->with('success', 'Club Vault folders confirmed complete by Chaplaincy Office.');
    }

    /**
     * Step 4: Record meeting between old & new executives with the Office
     */
    public function recordMeeting(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOffice()) {
            abort(403, 'Only the Chaplaincy Office can record handover meetings.');
        }

        $handover = Handover::findOrFail($id);
        if (!in_array($handover->status, ['folders_verified', 'meeting_scheduled'], true)) {
            return back()->with('error', 'Club folders must be verified before the handover meeting is recorded.');
        }

        $validated = $request->validate([
            'meeting_date' => ['required', 'date'],
            'meeting_notes' => ['required', 'string', 'max:5000'],
        ]);

        $handover->update(array_merge($validated, [
            'status' => 'meeting_scheduled',
        ]));

        return redirect()->route('handover.show', $handover->id)->with('success', 'Handover meeting details recorded.');
    }

    /**
     * Step 5: Administrator moves access and official email to the new executive.
     * Past leaders stay in history; the outgoing president's portal access is retired.
     */
    public function completeTransfer(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canManageUsers()) {
            abort(403, 'Only the Chapel Secretary or Chaplain can transfer executive credentials.');
        }

        $handover = Handover::with('club')->findOrFail($id);
        $club = $handover->club;

        if ($handover->status !== 'meeting_scheduled') {
            return back()->with('error', 'All earlier handover steps (checklist, folder verification, meeting) must be completed before credentials are transferred.');
        }

        $incomingEmail = strtolower(trim($handover->incoming_president_email));
        if ($error = $this->incomingEmailConflict($incomingEmail, $club)) {
            return back()->with('error', $error);
        }

        $credentialNote = DB::transaction(function () use ($handover, $club, $user, $incomingEmail) {
            // Archive previous active leadership terms for this club
            LeadershipTerm::where('club_id', $club->id)->where('is_active', true)->update([
                'is_active' => false,
                'end_date' => now()->toDateString(),
            ]);

            $newLeader = User::where('email', $incomingEmail)->first();
            $temporaryPassword = null;

            if ($newLeader) {
                // Existing club account (e.g. a club executive being promoted): keep their own password
                $newLeader->update([
                    'role' => 'club_president',
                    'club_id' => $club->id,
                    'position' => 'President',
                    'status' => 'active',
                    'phone' => $handover->incoming_president_phone ?: $newLeader->phone,
                ]);
            } else {
                $temporaryPassword = User::generateTemporaryPassword();
                $newLeader = User::create([
                    'name' => $handover->incoming_president_name,
                    'email' => $incomingEmail,
                    'password' => Hash::make($temporaryPassword),
                    'must_change_password' => true,
                    'role' => 'club_president',
                    'club_id' => $club->id,
                    'phone' => $handover->incoming_president_phone,
                    'position' => 'President',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);
            }

            // Retire the outgoing president's portal access
            if ($handover->outgoing_president_id && $handover->outgoing_president_id !== $newLeader->id) {
                User::where('id', $handover->outgoing_president_id)
                    ->where('club_id', $club->id)
                    ->where('role', 'club_president')
                    ->update(['status' => 'suspended']);
            }

            LeadershipTerm::create([
                'club_id' => $club->id,
                'user_id' => $newLeader->id,
                'person_name' => $handover->incoming_president_name,
                'position' => 'President',
                'official_email' => $club->official_email,
                'personal_phone' => $handover->incoming_president_phone,
                'start_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => "Official handover completed for {$handover->academic_year}.",
            ]);

            $club->update([
                'current_president' => $handover->incoming_president_name,
            ]);

            $handover->update([
                'status' => 'completed',
                'completed_by' => $user->id,
                'completed_at' => now(),
            ]);

            return $temporaryPassword
                ? " Sign-in: {$incomingEmail} / temporary password {$temporaryPassword} (must be changed at first sign-in; share it privately)."
                : " {$incomingEmail} keeps their existing password.";
        });

        AuditLog::record('handover_completed', "Completed {$handover->academic_year} handover for {$club->name}; executive access transferred to {$handover->incoming_president_name}.");

        return redirect()->route('handover.show', $handover->id)->with('success', "Credentials transferred to incoming president {$handover->incoming_president_name}. Historical records preserved; the outgoing president's portal access has been retired.{$credentialNote}");
    }

    /**
     * Display printable official Leadership Handover Certificate
     */
    public function certificate($id)
    {
        $handover = Handover::with(['club', 'outgoingPresident', 'verifier', 'completer'])->findOrFail($id);
        $user = Auth::user();
        $this->ensureCanAccess($user, $handover);

        if ($handover->status !== 'completed') {
            return redirect()->route('handover.show', $handover->id)->with('error', 'The stewardship certificate is issued once the handover has been completed.');
        }

        $health = $handover->club->getArchiveHealthScore();

        return view('handover.certificate', compact('handover', 'health', 'user'));
    }

    private function ensureCanAccess($user, Handover $handover): void
    {
        if (!$user->canAccessClub($handover->club_id)) {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * The incoming president's email must not belong to staff, observers or another club's leaders.
     */
    private function incomingEmailConflict(string $email, Club $club): ?string
    {
        $existing = User::where('email', $email)->first();

        if (!$existing) {
            return null;
        }

        if (!$existing->isClubLeader()) {
            return "{$email} belongs to a {$existing->getRoleDisplayName()} account and cannot be assigned as a club president.";
        }

        if ($existing->club_id && (int) $existing->club_id !== (int) $club->id) {
            return "{$email} is already a leader of another fellowship.";
        }

        return null;
    }
}
