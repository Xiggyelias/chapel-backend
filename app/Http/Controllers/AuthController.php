<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * "Forgot your password?" Accounts are issued by the Chapel Office, so a reset is a
     * request to the office: they issue a temporary password from the user registry and
     * the portal emails it to the account holder.
     */
    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('profile.index');
        }

        return view('auth.forgot-password');
    }

    public function requestPasswordHelp(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);
        $email = Str::lower(trim($data['email']));

        // Generous enough for a genuine person, useless for flooding the office
        $throttleKey = 'pw-help|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $minutes = (int) ceil(RateLimiter::availableIn($throttleKey) / 60);
            throw ValidationException::withMessages([
                'email' => "You have already sent a few requests. Please try again in {$minutes} minute" . ($minutes === 1 ? '' : 's') . ', or visit the Chaplaincy Office.',
            ]);
        }
        RateLimiter::hit($throttleKey, 3600);

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if ($user && $user->isActive()) {
            \App\Services\PortalNotifier::office(
                'Password reset requested',
                "{$user->name} ({$user->email}) asked for a new temporary password from the sign-in page. Issue one from the Staff & User Registry.",
                route('users.index', ['search' => $user->email]),
                'warning'
            );
        }

        // Same answer whether or not the account exists, so the form cannot be used to probe emails
        return back()->with('status', 'Thank you. If that address belongs to a portal account, the Chapel Office has been asked to issue a new temporary password. It will arrive by email, usually within one working day.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many sign-in attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        if (!Auth::validate($credentials)) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'email' => 'The provided credentials do not match our Chaplaincy records.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !$user->isActive()) {
            return back()->withErrors([
                'email' => 'This portal account has been suspended. Please contact the Chaplaincy Office.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Demo role switcher for evaluator convenience (1-click access).
     * Only available in local environments with DEMO_LOGIN_ENABLED=true.
     */
    public function switchDemoUser(Request $request, $userId = null)
    {
        if (!app()->environment('local') || !config('app.demo_login')) {
            abort(404);
        }

        $user = User::find($userId ?: $request->input('user_id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'Evaluator user profile not found.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $message = "Switched active session to: {$user->name} ({$user->getRoleDisplayName()})";

        // Only follow same-site relative paths to avoid open redirects
        $redirectTo = (string) $request->input('redirect_to', '');
        if (str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, '//')
            && !str_contains($redirectTo, 'login') && !str_contains($redirectTo, 'demo-switch')) {
            return redirect($redirectTo)->with('success', $message);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been safely signed out of the Chaplaincy portal.');
    }
}
