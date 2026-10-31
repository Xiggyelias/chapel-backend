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
