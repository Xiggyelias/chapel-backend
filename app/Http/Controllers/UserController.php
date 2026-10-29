<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Club;
use App\Models\LeadershipTerm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the official Staff & User Directory for Chaplaincy Administration.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isOffice()) {
            abort(403, 'Unauthorized access to the Chaplaincy User Registry.');
        }

        $query = User::with('club');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('position', 'like', "%{$term}%");
            });
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15);
        $clubs = Club::where('status', 'active')->orderBy('name')->get();

        $stats = [
            'total_users' => User::count(),
            'chapel_staff' => User::whereIn('role', ['chaplain', 'chapel_assistant', 'chapel_secretary'])->count(),
            'club_leaders' => User::whereIn('role', ['club_president', 'club_executive'])->count(),
            'campus_life' => User::where('role', 'campus_life_src')->count(),
        ];

        return view('users.index', compact('users', 'clubs', 'stats', 'currentUser'));
    }

    /**
     * Register and provision a new user (Staff Minister, Assistant, Administrator, or Club Executive).
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isChapelSecretary() && !$currentUser->isChaplain()) {
            abort(403, 'Only the Chapel Secretary or Chaplain can register and provision official portal accounts.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:chaplain,chapel_assistant,chapel_secretary,club_president,club_executive,campus_life_src'],
            'club_id' => ['nullable', 'required_if:role,club_president,club_executive', 'exists:clubs,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $rawPassword = (!empty($validated['password'])) ? $validated['password'] : 'Chapel@2026!';

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($rawPassword),
            'role' => $validated['role'],
            'club_id' => in_array($validated['role'], ['club_president', 'club_executive']) ? $validated['club_id'] : null,
            'position' => $validated['position'] ?? $this->defaultPosition($validated['role']),
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // If club president, record leadership term
        if ($user->isClubPresident() && $user->club_id) {
            $club = Club::find($user->club_id);
            if ($club) {
                $club->update(['current_president' => $user->name]);

                LeadershipTerm::create([
                    'club_id' => $club->id,
                    'user_id' => $user->id,
                    'person_name' => $user->name,
                    'position' => 'President',
                    'official_email' => $user->email,
                    'personal_phone' => $user->phone,
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addYear()->toDateString(),
                    'is_active' => true,
                    'notes' => 'Registered administratively by Chaplaincy Office on ' . now()->toFormattedDateString(),
                ]);
            }
        }

        AuditLog::create([
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->name,
            'action' => 'user_registered',
            'notes' => "Registered new {$user->getRoleDisplayName()} account for {$user->name} ({$user->email}).",
        ]);

        return redirect()->route('users.index')->with('success', "Account successfully registered for <strong>{$user->name}</strong> ({$user->getRoleDisplayName()})! Credentials: Email <code>{$user->email}</code>, Password <code>{$rawPassword}</code>.");
    }

    /**
     * Reset user password to default.
     */
    public function resetPassword(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isChapelSecretary() && !$currentUser->isChaplain()) {
            abort(403, 'Unauthorized.');
        }

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make('Chapel@2026!'),
        ]);

        AuditLog::create([
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->name,
            'action' => 'user_password_reset',
            'notes' => "Reset portal password for {$user->name} ({$user->email}) to default.",
        ]);

        return redirect()->back()->with('success', "Password for {$user->name} was reset to default: <code>Chapel@2026!</code>.");
    }

    /**
     * Toggle active/suspended status.
     */
    public function toggleStatus(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isChapelSecretary() && !$currentUser->isChaplain()) {
            abort(403, 'Unauthorized.');
        }

        $user = User::findOrFail($id);
        if ($user->id === $currentUser->id) {
            return redirect()->back()->with('error', 'You cannot suspend your own active administrator account.');
        }

        $newStatus = ($user->status === 'active') ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        AuditLog::create([
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->name,
            'action' => 'user_status_changed',
            'notes' => "Updated status of {$user->name} to {$newStatus}.",
        ]);

        return redirect()->back()->with('info', "Account status for {$user->name} changed to {$newStatus}.");
    }

    private function defaultPosition(string $role): string
    {
        return match ($role) {
            'chaplain' => 'University Chaplain',
            'chapel_assistant' => 'Assistant Chaplain & Worship Coordinator',
            'chapel_secretary' => 'Chapel Secretary & Administrator',
            'club_president' => 'President',
            'club_executive' => 'Executive Officer',
            'campus_life_src' => 'Campus Life Observer',
            default => 'Staff',
        };
    }
}
