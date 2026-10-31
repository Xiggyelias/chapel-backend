<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\LeadershipTerm;
use App\Models\User;
use App\Models\Venue;
use App\Services\PrivateFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\PortalNotifier;

class ClubController extends Controller
{
    /**
     * Display the registry of all clubs, pending charter applications, and archives.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->input('status', 'all');

        $clubsQuery = Club::withCount(['members', 'requests', 'documents', 'events']);

        if ($statusFilter === 'active') {
            $clubsQuery->where('status', 'active');
        } elseif ($statusFilter === 'pending') {
            $clubsQuery->where('status', 'pending_approval');
        } elseif ($statusFilter === 'archived') {
            $clubsQuery->where('status', 'archived');
        }

        $clubs = $clubsQuery->orderBy('status', 'asc')->orderBy('name', 'asc')->paginate(15)->withQueryString();

        $activeCount = Club::where('status', 'active')->count();
        $pendingCount = Club::where('status', 'pending_approval')->count();
        $archivedCount = Club::where('status', 'archived')->count();

        return view('clubs.index', compact('clubs', 'activeCount', 'pendingCount', 'archivedCount', 'statusFilter', 'user'));
    }

    /**
     * Show the fellowship chartering and registration application form.
     */
    public function create()
    {
        $user = Auth::user();
        $venues = Venue::where('is_active', true)->get();

        // If logged in as Chaplaincy Office staff, show internal registration form within admin shell
        if ($user && $user->isOffice()) {
            return view('clubs.register', compact('venues', 'user'));
        }

        // For outside applicants or students, show isolated secure charter application portal
        return view('clubs.public_register', compact('venues', 'user'));
    }

    /**
     * Store a new club registration / chartering application.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:clubs,name'],
            'code' => ['required', 'alpha_dash', 'max:15', 'unique:clubs,code'],
            'type' => ['required', 'in:fellowship,prayer group,choir or worship team,service'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'meeting_schedule' => ['required', 'string', 'max:255'],
            'venue' => ['required', 'string', 'max:255'],
            'official_email' => ['required', 'email', 'unique:clubs,official_email'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'founding_president_name' => ['required', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'founding_president_email' => ['required', 'email'],
            'advisor_name' => ['nullable', 'string', 'max:255'],
            'advisor_email' => ['nullable', 'email', 'max:255'],
            'constitution' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $constitutionPath = null;
        if ($request->hasFile('constitution')) {
            $constitutionPath = PrivateFileService::store($request->file('constitution'), 'charters');
        }

        // Palette selector based on type
        $colorHex = match($validated['type']) {
            'fellowship' => '#DC2626',
            'prayer group' => '#EF4444',
            'choir or worship team' => '#2563EB',
            'service' => '#059669',
            default => '#DC2626',
        };

        // Generate a unique tracking reference code
        $trackingCode = 'AU-CHTR-' . date('Y') . '-' . strtoupper(Str::random(5));

        // Determine initial status: Office can charter directly, outside applicants are strictly quarantined
        $initialStatus = ($user && $user->isOffice()) ? 'active' : 'pending_approval';

        $club = Club::create([
            'name' => $validated['name'],
            'code' => strtoupper(trim($validated['code'])),
            'tracking_code' => $trackingCode,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'meeting_schedule' => $validated['meeting_schedule'],
            'venue' => $validated['venue'],
            'official_email' => strtolower(trim($validated['official_email'])),
            'contact_phone' => $validated['contact_phone'],
            'current_president' => $validated['founding_president_name'],
            'founding_president_name' => $validated['founding_president_name'],
            'student_id' => $validated['student_id'] ?? null,
            'founding_president_email' => strtolower(trim($validated['founding_president_email'])),
            'advisor_name' => $validated['advisor_name'] ?? null,
            'advisor_email' => $validated['advisor_email'] ?? null,
            'constitution_path' => $constitutionPath,
            'color_hex' => $colorHex,
            'status' => $initialStatus,
        ]);

        // Audit log
        AuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? $validated['founding_president_name'],
            'action' => 'club_registration_submitted',
            'notes' => "Submitted official fellowship charter application for '{$club->name}' ({$club->code}). Tracking: {$trackingCode}. Status: {$initialStatus}",
        ]);

        if ($initialStatus === 'active') {
            // If office chartered directly, provision leader account
            $credentials = $this->provisionLeaderAccount($club);
            return redirect()->route('clubs.index')->with('success', "Fellowship '{$club->name}' has been officially registered and chartered under Chaplaincy oversight. {$credentials}");
        }

        PortalNotifier::charterReceived($club);

        PortalNotifier::office(
            "Charter application: {$club->name}",
            "{$club->code} • {$club->founding_president_name} • tracking {$trackingCode}",
            route('clubs.index', ['status' => 'pending']),
            'warning'
        );

        // For public/student applications: NEVER redirect to dashboard or grant internal access!
        // Show secure tracking receipt page
        return redirect()->route('clubs.receipt', ['tracking' => $trackingCode])
            ->with('tracking_code', $trackingCode)
            ->with('success', "Charter application lodged successfully. Tracking reference: {$trackingCode}");
    }

    /**
     * Display the secure confirmation and tracking receipt for a submitted charter application.
     */
    public function receipt(Request $request)
    {
        $tracking = $request->query('tracking') ?? session('tracking_code');
        $club = null;
        if ($tracking) {
            $club = Club::where('tracking_code', $tracking)->first();
        }

        return view('clubs.receipt', compact('club', 'tracking'));
    }

    /**
     * Office action: open the constitution submitted with a charter application.
     */
    public function downloadConstitution($id)
    {
        $club = Club::findOrFail($id);

        return PrivateFileService::download($club->constitution_path, "{$club->code}-Constitution");
    }

    /**
     * Office action: Officially charter & activate a pending (or restore an archived) fellowship.
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $club = Club::findOrFail($id);

        if ($club->status === 'active') {
            return redirect()->back()->with('info', "Fellowship '{$club->name}' is already chartered and active.");
        }

        $request->validate(['charter_notes' => ['nullable', 'string', 'max:2000']]);

        $club->update([
            'status' => 'active',
            'charter_notes' => $request->input('charter_notes', 'Charter approved and ratified by Chaplaincy Office on ' . now()->toFormattedDateString()),
        ]);

        $credentials = $this->provisionLeaderAccount($club);

        AuditLog::record('club_charter_approved', "Officially chartered and ratified '{$club->name}' ({$club->code}) under Africa University Chaplaincy oversight.");

        return redirect()->back()->with('success', "Fellowship '{$club->name}' has been chartered! {$credentials}");
    }

    /**
     * Office action: Archive or retire a club.
     */
    public function archive(Request $request, $id)
    {
        $club = Club::findOrFail($id);
        $club->update(['status' => 'archived']);

        AuditLog::record('club_archived', "Archived records for fellowship '{$club->name}'.");

        return redirect()->back()->with('info', "Fellowship '{$club->name}' records archived per university retention policy.");
    }

    /**
     * Provision leader portal account for new club and describe the outcome for the Office.
     */
    private function provisionLeaderAccount(Club $club): string
    {
        $email = $club->founding_president_email ?: $club->official_email;
        $name = $club->current_president ?: $club->founding_president_name;

        if (!$email) {
            return 'No leader email was supplied, so no portal account was created.';
        }

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return "An existing portal account ({$email}) was found, so no new account was created. Link it to this club from Users if appropriate.";
        }

        $temporaryPassword = User::generateTemporaryPassword();

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
            'role' => 'club_president',
            'club_id' => $club->id,
            'position' => 'President',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        LeadershipTerm::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'person_name' => $name,
            'position' => 'President',
            'official_email' => $email,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'is_active' => true,
            'notes' => 'Founding President (Chartered ' . now()->format('Y') . ')',
        ]);

        $delivery = PortalNotifier::credentials($user, $temporaryPassword)
            ? "Sign-in details were emailed to {$email}."
            : 'Share it privately.';

        return "Leader account created for {$name}: {$email} / temporary password {$temporaryPassword} (must be changed at first sign-in). {$delivery}";
    }
}
