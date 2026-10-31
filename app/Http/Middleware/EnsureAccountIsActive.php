<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Signs out any session whose account has been suspended by the Office,
 * so suspension takes effect immediately rather than at the next login.
 */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'This account has been suspended.'], 403);
            }

            return redirect()->route('login')->with('error', 'Your portal account has been suspended. Please contact the Chaplaincy Office.');
        }

        return $next($request);
    }
}
