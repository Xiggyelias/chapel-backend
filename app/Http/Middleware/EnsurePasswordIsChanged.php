<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Accounts holding a temporary or default password may only reach the
 * change-password screen (and sign out) until they set their own.
 */
class EnsurePasswordIsChanged
{
    private const ALLOWED_ROUTES = ['profile.index', 'profile.password', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->mustChangePassword() && !$request->routeIs(...self::ALLOWED_ROUTES)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Please set a new password before continuing.'], 403);
            }

            return redirect()->route('profile.index')
                ->with('warning', 'For security, please replace your temporary password before using the portal.');
        }

        return $next($request);
    }
}
