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
use Illuminate\Support\Facades\Hash;

class HandoverController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = Handover::with(['club', 'outgoingPresident', 'verifier', 'completer']);

        if ($user->isClubExecutive() || $user->isClubPresident()) {
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
            'club_id' => ['required', 'exists:clubs,id'],
            'academic_year' => ['required', 'string'],
            'incoming_president_name' => ['required', 'string', 'max:255'],
            'incoming_president_email' => ['required', 'email'],
            'incoming_president_phone' => ['nullable', 'string'],
        ]);

        $club = Club::findOrFail($validated['club_id']);
        $outgoingLeader = User::where('club_id', $club->id)->whereIn('role', ['club_president', 'club_executive'])->first();

        $handover = Handover::create([
            'club_id' => $club->id,
            'academic_year' => $validated['academic_year'],
            'outgoing_president_id' => $outgoingLeader?->id,
            'outgoing_president_name' => $outgoingLeader ? $outgoingLeader->name : ($club->current_president ?: 'Current Executive'),
            'incoming_president_name' => $validated['incoming_president_name'],
            'incoming_president_email' => $validated['incoming_president_email'],
            'incoming_president_phone' => $validated['incoming_president_phone'],
            'status' => 'initiated',
        ]);

        return redirect()->route('handover.show', $handover->id)->with('success', "Handover process initiated for {$club->name}. Outgoing executive must now complete checklist.");
    }

    public function show($id)
    {
        $user = Auth::user();
        $handover = Handover::with(['club', 'outgoingPresident', 'verifier', 'completer'])->findOrFail($id);

        if (($user->isClubExecutive() || $user->isClubPresident()) && $user->club_id !== $handover->club_id) {
            abort(403, 'Unauthorized.');
        }

        // Vault completeness check
        $vaultCount = Document::where('club_id', $handover->club_id)->where('is_archived', false)->count();

        return view('handover.show', compact('handover', 'vaultCount', 'user'));
    }

    /**
     * Step 2: The outgoing executive completes the checklist and note
     */
    public function updateChecklist(Request $request, $id)
    {
        $user = Auth::user();
        $handover = Handover::findOrFail($id);

        if (($user->isClubExecutive() || $user->isClubPresident()) && $user->club_id !== $handover->club_id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'checklist_running_activities' => ['required', 'string'],
            'checklist_pending_items' => ['required', 'string'],
            'checklist_key_contacts' => ['required', 'string'],
            'checklist_yearly_calendar' => ['required', 'string'],
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

        $validated = $request->validate([
            'meeting_date' => ['required', 'date'],
            'meeting_notes' => ['required', 'string'],
        ]);

        $handover = Handover::findOrFail($id);
        $handover->update(array_merge($validated, [
            'status' => 'meeting_scheduled',
        ]));

        return redirect()->route('handover.show', $handover->id)->with('success', 'Handover meeting details recorded.');
    }

    /**
     * Step 5: Administrator moves access and official email to the new executive.
     * Past leaders stay in history.
     */
    public function completeTransfer(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isChapelSecretary() && !$user->isChaplain()) {
            abort(403, 'Only the Chapel Secretary or Chaplain can transfer executive credentials.');
        }

        $handover = Handover::with('club')->findOrFail($id);
        $club = $handover->club;

        // Archive previous active leadership terms for this club
        LeadershipTerm::where('club_id', $club->id)->where('is_active', true)->update([
            'is_active' => false,
            'end_date' => now()->toDateString(),
        ]);

        // Find or create incoming president account
        $newLeader = User::updateOrCreate(
            ['email' => $handover->incoming_president_email],
            [
                'name' => $handover->incoming_president_name,
                'password' => Hash::make('Chapel@2026!'),
                'role' => 'club_president',
                'club_id' => $club->id,
                'phone' => $handover->incoming_president_phone,
                'position' => 'President',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Record new leadership term in permanent history
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

        // Update club president name
        $club->update([
            'current_president' => $handover->incoming_president_name,
        ]);

        // Mark handover completed
        $handover->update([
            'status' => 'completed',
            'completed_by' => $user->id,
            'completed_at' => now(),
        ]);

        return redirect()->route('handover.show', $handover->id)->with('success', "Credentials and official email successfully transferred to incoming president: {$handover->incoming_president_name}. Historical records preserved.");
    }

    /**
     * Display printable official Leadership Handover Certificate
     */
    public function certificate($id)
    {
        $user = Auth::user();
        $handover = Handover::with(['club', 'outgoingPresident', 'verifier', 'completer'])->findOrFail($id);

        if (($user->isClubExecutive() || $user->isClubPresident()) && $user->club_id !== $handover->club_id) {
            abort(403, 'Unauthorized.');
        }

        $health = $handover->club->getArchiveHealthScore();

        return view('handover.certificate', compact('handover', 'health', 'user'));
    }
}
