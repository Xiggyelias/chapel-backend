<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $demoUsers = User::with('club')->get();
        return view('auth.login', compact('demoUsers'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our Chaplaincy records.',
        ])->onlyInput('email');
    }

    /**
     * Demo role switcher for instant evaluator convenience (1-click access)
     */
    public function switchDemoUser(Request $request, $userId = null)
    {
        if (!app()->environment('local')) {
            abort(404);
        }

        $id = $userId ?: $request->input('user_id');
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Evaluator user profile not found.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo && !str_contains($redirectTo, 'login') && !str_contains($redirectTo, 'demo-switch')) {
            return redirect($redirectTo)->with('success', "Switched active session to: {$user->name} ({$user->getRoleDisplayName()})");
        }

        return redirect()->route('dashboard')->with('success', "Switched active session to: {$user->name} ({$user->getRoleDisplayName()})");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been safely signed out of the Chaplaincy portal.');
    }
}
