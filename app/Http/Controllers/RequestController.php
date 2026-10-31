<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\Venue;
use App\Services\PrivateFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    /**
     * Display a listing of the requests.
     * Enforces club isolation: Club execs see only own club's requests.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Campus Life / SRC cannot view requests
        if ($user->isCampusLifeSrc()) {
            return redirect()->route('calendar.index')->with('warning', 'Campus Life & SRC roles have access to the Master Calendar only.');
        }

        $query = ChapelRequest::with(['club', 'requester', 'venue', 'decidedBy']);

        // Data isolation
        if (!$user->isOffice()) {
            $query->where('club_id', $user->club_id);
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('club_id') && $user->isOffice()) {
            $query->where('club_id', $request->club_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $clubs = Club::active()->get();
        $venues = Venue::where('is_active', true)->get();

        return view('requests.index', compact('requests', 'clubs', 'venues'));
    }

    /**
     * Show form to create new request
     */
    public function create()
    {
        $user = Auth::user();
        $this->ensureCanSubmit($user);

        $clubs = $user->isOffice() ? Club::active()->get() : Club::active()->where('id', $user->club_id)->get();
        $venues = Venue::where('is_active', true)->get();

        return view('requests.create', compact('clubs', 'venues', 'user'));
    }

    /**
     * Store a newly created request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->ensureCanSubmit($user);

        // Club leaders may only submit for their own club
        if ($user->isClubLeader()) {
            $request->merge(['club_id' => $user->club_id]);
        }

        $validated = $this->validateProposal($request);

        $isLate = ChapelRequest::checkIsLate($validated['event_date']);

        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = PrivateFileService::store($file, 'request_attachments');
        }

        $chapelRequest = DB::transaction(function () use ($validated, $user, $request, $isLate, $attachmentPath, $attachmentName) {
            $chapelRequest = ChapelRequest::create([
                // Temporary unique value; replaced by the id-based reference below
                'reference_no' => 'PENDING-' . uniqid(),
                'club_id' => $validated['club_id'],
                'user_id' => $user->id,
                'category' => $validated['category'],
                'title' => $validated['title'],
                'purpose' => $validated['purpose'],
                'event_date' => $validated['event_date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'venue_id' => $validated['venue_id'] ?? null,
                'venue_other' => $validated['venue_other'] ?? null,
                'expected_attendance' => $validated['expected_attendance'] ?? 0,
                'budget' => $validated['budget'] ?? 0,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
                'sign_interpreter_required' => $request->boolean('sign_interpreter_required'),
                'sign_interpreter_name' => $request->input('sign_interpreter_name'),
                'status' => 'submitted',
                'is_late_submission' => $isLate,
                'vc_go_ahead_needed' => $validated['category'] === 'outing', // Outings automatically flag for VC go-ahead
            ]);

            // Derived from the primary key so concurrent submissions can never share a reference
            $chapelRequest->update([
                'reference_no' => sprintf('REQ-%s-%04d', now()->year, $chapelRequest->id),
            ]);

            return $chapelRequest;
        });

        AuditLog::record(
            'submitted',
            $isLate ? 'Submitted with late notice (< 14 days before event date).' : 'Initial submission received.',
            $chapelRequest,
            null,
            'submitted'
        );

        $notice = $isLate
            ? "Request {$chapelRequest->reference_no} submitted successfully. NOTE: Flagged as late submission (< 14 days before activity per Chapel Manual §4.1)."
            : "Request {$chapelRequest->reference_no} submitted successfully.";

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', $notice);
    }

    /**
     * Display a specific request
     */
    public function show($id)
    {
        $user = Auth::user();
        $requestItem = ChapelRequest::with(['club', 'requester', 'venue', 'decidedBy', 'event', 'auditLogs.user'])->findOrFail($id);
        $this->ensureCanView($user, $requestItem);

        $clashes = $requestItem->findClashes();

        return view('requests.show', compact('requestItem', 'clashes', 'user'));
    }

    /**
     * Edit a proposal that is still awaiting a decision or was returned for changes.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $requestItem = ChapelRequest::with(['club', 'venue'])->findOrFail($id);
        $this->ensureCanEdit($user, $requestItem);

        $venues = Venue::where('is_active', true)->get();

        return view('requests.edit', compact('requestItem', 'venues', 'user'));
    }

    /**
     * Save edits and (re)submit the proposal for review.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $chapelRequest = ChapelRequest::findOrFail($id);
        $this->ensureCanEdit($user, $chapelRequest);

        // The sponsoring club of an existing proposal cannot be changed
        $request->merge(['club_id' => $chapelRequest->club_id]);
        $validated = $this->validateProposal($request);

        $oldStatus = $chapelRequest->status;
        $isLate = ChapelRequest::checkIsLate($validated['event_date'], $chapelRequest->created_at->copy()->startOfDay());

        $updates = [
            'category' => $validated['category'],
            'title' => $validated['title'],
            'purpose' => $validated['purpose'],
            'event_date' => $validated['event_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'venue_id' => $validated['venue_id'] ?? null,
            'venue_other' => $validated['venue_other'] ?? null,
            'expected_attendance' => $validated['expected_attendance'] ?? 0,
            'budget' => $validated['budget'] ?? 0,
            'sign_interpreter_required' => $request->boolean('sign_interpreter_required'),
            'sign_interpreter_name' => $request->input('sign_interpreter_name'),
            'is_late_submission' => $isLate,
            'status' => 'submitted',
        ];

        if ($validated['category'] === 'outing') {
            $updates['vc_go_ahead_needed'] = true;
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $updates['attachment_name'] = $file->getClientOriginalName();
            $updates['attachment_path'] = PrivateFileService::store($file, 'request_attachments');
        }

        $chapelRequest->update($updates);

        AuditLog::record(
            $oldStatus === 'needs_changes' ? 'resubmitted' : 'edited',
            $oldStatus === 'needs_changes' ? 'Proposal revised and resubmitted for review.' : 'Proposal details updated before review.',
            $chapelRequest,
            $oldStatus,
            'submitted'
        );

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', "Request {$chapelRequest->reference_no} updated and submitted for review.");
    }

    public function downloadAttachment($id)
    {
        $requestItem = ChapelRequest::findOrFail($id);
        $this->ensureCanView(Auth::user(), $requestItem);

        return PrivateFileService::download($requestItem->attachment_path, $requestItem->attachment_name);
    }

    public function downloadVcAttachment($id)
    {
        $requestItem = ChapelRequest::findOrFail($id);
        $this->ensureCanView(Auth::user(), $requestItem);

        return PrivateFileService::download($requestItem->vc_go_ahead_attachment, "VC-Clearance-{$requestItem->reference_no}");
    }

    /**
     * Review / Approve a request (Chaplain / Assistant Chaplain)
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveRequests()) {
            abort(403, 'Only the Chaplain or Assistant Chaplain may approve requests.');
        }

        $chapelRequest = ChapelRequest::with(['club', 'venue', 'event'])->findOrFail($id);

        if (!$chapelRequest->isPendingDecision()) {
            return back()->with('error', 'Only proposals awaiting a decision can be approved. Proposals returned for changes must be resubmitted by the club first.');
        }

        if ($chapelRequest->vc_go_ahead_needed && $chapelRequest->vc_go_ahead_status !== 'received') {
            return back()->with('error', 'Cannot approve: This request requires Vice-Chancellor go-ahead clearance to be recorded first.');
        }

        $clashes = $chapelRequest->findClashes();
        if ($clashes->isNotEmpty() && !$request->boolean('override_clash')) {
            $titles = $clashes->pluck('title')->implode(', ');
            return back()->with('error', "Cannot approve: the venue is already booked at this time ({$titles}). Ask the club to change venue or time, or tick the override box to double-book deliberately.");
        }

        $oldStatus = $chapelRequest->status;
        $notes = $request->input('notes') ?: 'Approved by ' . $user->getRoleDisplayName();

        DB::transaction(function () use ($chapelRequest, $user, $notes) {
            $chapelRequest->update([
                'status' => 'approved',
                'decision_reason' => $notes,
                'decided_by' => $user->id,
                'decided_at' => now(),
            ]);

            // Approval publishes the activity on the Master Calendar
            Event::updateOrCreate(
                ['request_id' => $chapelRequest->id],
                [
                    'club_id' => $chapelRequest->club_id,
                    'venue_id' => $chapelRequest->venue_id,
                    'venue_name' => $chapelRequest->venue ? $chapelRequest->venue->name : $chapelRequest->venue_other,
                    'title' => $chapelRequest->title,
                    'description' => $chapelRequest->purpose,
                    'event_date' => $chapelRequest->event_date,
                    'start_time' => $chapelRequest->start_time,
                    'end_time' => $chapelRequest->end_time,
                    'expected_attendance' => $chapelRequest->expected_attendance,
                    'status' => 'scheduled',
                    'semester' => Event::semesterFor($chapelRequest->event_date),
                ]
            );
        });

        $auditNotes = $clashes->isNotEmpty() ? "{$notes} (Venue clash overridden by approver.)" : $notes;
        AuditLog::record('approved', $auditNotes, $chapelRequest, $oldStatus, 'approved');

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', 'Request approved successfully. Master Calendar event generated.');
    }

    /**
     * Request changes (Chaplain / Assistant Chaplain)
     */
    public function needsChanges(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveRequests()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        $chapelRequest = ChapelRequest::with('event')->findOrFail($id);
        if ($chapelRequest->status === 'declined') {
            return back()->with('error', 'This proposal has already been declined.');
        }

        $oldStatus = $chapelRequest->status;

        $chapelRequest->update([
            'status' => 'needs_changes',
            'decision_reason' => $request->notes,
            'decided_by' => $user->id,
            'decided_at' => now(),
        ]);

        // A previously approved activity comes off the calendar until it is approved again
        $chapelRequest->event?->update(['status' => 'cancelled']);

        AuditLog::record('needs_changes', $request->notes, $chapelRequest, $oldStatus, 'needs_changes');

        return redirect()->route('requests.show', $chapelRequest->id)->with('info', 'Request flagged as Needs Changes. The club can now revise and resubmit it.');
    }

    /**
     * Decline a request (reason mandatory per spec)
     */
    public function decline(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveRequests()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $chapelRequest = ChapelRequest::with('event')->findOrFail($id);
        if ($chapelRequest->status === 'declined') {
            return back()->with('error', 'This proposal has already been declined.');
        }

        $oldStatus = $chapelRequest->status;

        $chapelRequest->update([
            'status' => 'declined',
            'decision_reason' => $request->reason,
            'decided_by' => $user->id,
            'decided_at' => now(),
        ]);

        $chapelRequest->event?->update(['status' => 'cancelled']);

        AuditLog::record('declined', "Declined with mandatory reason: {$request->reason}", $chapelRequest, $oldStatus, 'declined');

        return redirect()->route('requests.show', $chapelRequest->id)->with('info', 'Request declined. Reason recorded in official audit log.');
    }

    /**
     * Record Vice-Chancellor Go-Ahead Clearance
     */
    public function recordVcGoAhead(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveRequests()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'vc_go_ahead_note' => ['required', 'string', 'max:2000'],
            'vc_attachment' => ['nullable', 'file', 'mimes:pdf,docx,png,jpg,jpeg', 'max:5120'],
        ]);

        $chapelRequest = ChapelRequest::findOrFail($id);

        $attachmentPath = $chapelRequest->vc_go_ahead_attachment;
        if ($request->hasFile('vc_attachment')) {
            $attachmentPath = PrivateFileService::store($request->file('vc_attachment'), 'vc_clearance_docs');
        }

        $chapelRequest->update([
            'vc_go_ahead_needed' => true,
            'vc_go_ahead_status' => 'received',
            'vc_go_ahead_note' => $request->vc_go_ahead_note,
            'vc_go_ahead_attachment' => $attachmentPath,
        ]);

        AuditLog::record('vc_note_added', "VC Go-Ahead clearance recorded: {$request->vc_go_ahead_note}", $chapelRequest, $chapelRequest->status, $chapelRequest->status);

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', 'Vice-Chancellor clearance recorded. Request is ready for final approval.');
    }

    private function validateProposal(Request $request): array
    {
        // Browsers and PostgreSQL may supply seconds (HH:MM:SS); bookings are minute-precision
        foreach (['start_time', 'end_time'] as $field) {
            if (is_string($request->input($field)) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $request->input($field))) {
                $request->merge([$field => substr($request->input($field), 0, 5)]);
            }
        }

        return $request->validate([
            'club_id' => ['required', Rule::exists('clubs', 'id')->where('status', 'active')],
            'category' => ['required', 'in:event,venue,poster,equipment,outing'],
            'title' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:5000'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'venue_id' => ['nullable', 'required_without:venue_other', Rule::exists('venues', 'id')->where('is_active', true)],
            'venue_other' => ['nullable', 'required_without:venue_id', 'string', 'max:255'],
            'expected_attendance' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'budget' => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,docx,png,jpg,jpeg', 'max:10240'],
            'sign_interpreter_required' => ['nullable', 'boolean'],
            'sign_interpreter_name' => ['nullable', 'string', 'max:255'],
        ], [
            'club_id.exists' => 'Proposals can only be submitted for an active, chartered fellowship.',
            'venue_id.required_without' => 'Choose a venue or describe an alternative location.',
            'venue_other.required_without' => 'Choose a venue or describe an alternative location.',
        ]);
    }

    private function ensureCanSubmit($user): void
    {
        if (!$user->isOffice() && !$user->isClubLeader()) {
            abort(403, 'Unauthorized.');
        }
    }

    private function ensureCanView($user, ChapelRequest $requestItem): void
    {
        if (!$user->canAccessClub($requestItem->club_id)) {
            abort(403, 'Club Data Isolation: You may only view requests submitted by your own club.');
        }
    }

    private function ensureCanEdit($user, ChapelRequest $requestItem): void
    {
        $this->ensureCanView($user, $requestItem);

        if ($user->isCampusLifeSrc() || !$requestItem->isEditableByClub()) {
            abort(403, 'Only proposals awaiting review or returned for changes can be edited.');
        }
    }
}
