<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user profile and change password screen.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'password.different' => 'Your new password must be different from your current default password.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Your new password must be at least 8 characters in length.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided current password is incorrect.',
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('profile.index')->with('success', 'Your password has been successfully updated! You can now use your new password to sign in.');
    }

    /**
     * Update contact details (phone, etc.)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')->with('success', 'Profile details updated successfully.');
    }
}
