<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\Event;
use App\Models\Request as ChapelRequest;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        if ($user->isClubExecutive() || $user->isClubPresident()) {
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
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $clubs = $user->isOffice() ? Club::active()->get() : Club::where('id', $user->club_id)->get();
        $venues = Venue::where('is_active', true)->get();

        return view('requests.create', compact('clubs', 'venues', 'user'));
    }

    /**
     * Store a newly created request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'club_id' => ['required', 'exists:clubs,id'],
            'category' => ['required', 'in:event,venue,poster,equipment,outing'],
            'title' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string'],
            'event_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'end_time' => ['required', 'after:start_time'],
            'venue_id' => ['nullable', 'exists:venues,id'],
            'venue_other' => ['nullable', 'string', 'max:255'],
            'expected_attendance' => ['nullable', 'integer', 'min:1'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,docx,png,jpg,jpeg', 'max:10240'],
            'sign_interpreter_required' => ['nullable', 'boolean'],
            'sign_interpreter_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Club executive can only submit for their own club
        if ($user->isClubExecutive() || $user->isClubPresident()) {
            $validated['club_id'] = $user->club_id;
        }

        // Generate sequential reference number
        $year = date('Y');
        $count = ChapelRequest::whereYear('created_at', $year)->count() + 1;
        $referenceNo = sprintf('REQ-%s-%04d', $year, $count);

        // Check 14-day rule
        $isLate = ChapelRequest::checkIsLate($validated['event_date']);

        // Handle attachment upload
        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('request_attachments', 'public');
        }

        $chapelRequest = ChapelRequest::create([
            'reference_no' => $referenceNo,
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
            'vc_go_ahead_needed' => in_array($validated['category'], ['outing']), // Outings automatically flag for VC go-ahead
        ]);

        // Audit Trail
        AuditLog::create([
            'request_id' => $chapelRequest->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'submitted',
            'old_status' => null,
            'new_status' => 'submitted',
            'notes' => $isLate ? 'Submitted with late notice (< 14 days before event date).' : 'Initial submission received.',
            'ip_address' => $request->ip(),
        ]);

        $notice = $isLate 
            ? "Request {$referenceNo} submitted successfully. NOTE: Flagged as late submission (< 14 days before activity per Chapel Manual §4.1)."
            : "Request {$referenceNo} submitted successfully.";

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', $notice);
    }

    /**
     * Display a specific request
     */
    public function show($id)
    {
        $user = Auth::user();
        if ($user->isCampusLifeSrc()) {
            abort(403, 'Unauthorized.');
        }

        $requestItem = ChapelRequest::with(['club', 'requester', 'venue', 'decidedBy', 'event', 'auditLogs.user'])->findOrFail($id);

        // Security data isolation check
        if (($user->isClubExecutive() || $user->isClubPresident()) && $requestItem->club_id !== $user->club_id) {
            abort(403, 'Club Data Isolation: You may only view requests submitted by your own club.');
        }

        // Check for venue clashes
        $clashes = $requestItem->findClashes();

        return view('requests.show', compact('requestItem', 'clashes', 'user'));
    }

    /**
     * Review / Approve a request (Chaplain / Chapel Assistant)
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveRequests()) {
            abort(403, 'Only the Chaplain or Assistant Chaplain may approve requests.');
        }

        $chapelRequest = ChapelRequest::with(['club', 'venue'])->findOrFail($id);

        // Check VC go-ahead if needed
        if ($chapelRequest->vc_go_ahead_needed && $chapelRequest->vc_go_ahead_status !== 'received') {
            return back()->with('error', 'Cannot approve: This request requires Vice-Chancellor go-ahead clearance to be recorded first.');
        }

        $notes = $request->input('notes', 'Approved by ' . $user->getRoleDisplayName());

        $chapelRequest->update([
            'status' => 'approved',
            'decision_reason' => $notes,
            'decided_by' => $user->id,
            'decided_at' => now(),
        ]);

        // Automatically create or update calendar event!
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
                'semester' => 'Semester 1 2026/2027',
            ]
        );

        // Audit Trail
        AuditLog::create([
            'request_id' => $chapelRequest->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'approved',
            'old_status' => $chapelRequest->getOriginal('status'),
            'new_status' => 'approved',
            'notes' => $notes,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', 'Request approved successfully. Master Calendar event generated.');
    }

    /**
     * Request changes (Chaplain / Chapel Assistant)
     */
    public function needsChanges(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canApproveRequests()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'notes' => ['required', 'string'],
        ]);

        $chapelRequest = ChapelRequest::findOrFail($id);
        $oldStatus = $chapelRequest->status;

        $chapelRequest->update([
            'status' => 'needs_changes',
            'decision_reason' => $request->notes,
            'decided_by' => $user->id,
            'decided_at' => now(),
        ]);

        AuditLog::create([
            'request_id' => $chapelRequest->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'needs_changes',
            'old_status' => $oldStatus,
            'new_status' => 'needs_changes',
            'notes' => $request->notes,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('requests.show', $chapelRequest->id)->with('info', 'Request flagged as Needs Changes. The club executive has been notified.');
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
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $chapelRequest = ChapelRequest::findOrFail($id);
        $oldStatus = $chapelRequest->status;

        $chapelRequest->update([
            'status' => 'declined',
            'decision_reason' => $request->reason,
            'decided_by' => $user->id,
            'decided_at' => now(),
        ]);

        // If an event existed previously, cancel it
        if ($chapelRequest->event) {
            $chapelRequest->event->update(['status' => 'cancelled']);
        }

        AuditLog::create([
            'request_id' => $chapelRequest->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'declined',
            'old_status' => $oldStatus,
            'new_status' => 'declined',
            'notes' => "Declined with mandatory reason: {$request->reason}",
            'ip_address' => $request->ip(),
        ]);

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
            'vc_go_ahead_note' => ['required', 'string'],
            'vc_attachment' => ['nullable', 'file', 'mimes:pdf,docx,png,jpg', 'max:5120'],
        ]);

        $chapelRequest = ChapelRequest::findOrFail($id);

        $attachmentPath = $chapelRequest->vc_go_ahead_attachment;
        if ($request->hasFile('vc_attachment')) {
            $attachmentPath = $request->file('vc_attachment')->store('vc_clearance_docs', 'public');
        }

        $chapelRequest->update([
            'vc_go_ahead_needed' => true,
            'vc_go_ahead_status' => 'received',
            'vc_go_ahead_note' => $request->vc_go_ahead_note,
            'vc_go_ahead_attachment' => $attachmentPath,
        ]);

        AuditLog::create([
            'request_id' => $chapelRequest->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'vc_note_added',
            'old_status' => $chapelRequest->status,
            'new_status' => $chapelRequest->status,
            'notes' => "VC Go-Ahead clearance recorded: {$request->vc_go_ahead_note}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('requests.show', $chapelRequest->id)->with('success', 'Vice-Chancellor clearance recorded. Request is ready for final approval.');
    }
}
