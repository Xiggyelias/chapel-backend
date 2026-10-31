<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route group to the Office of the Chaplain (Chaplain, Assistant Chaplain, Secretary).
 */
class EnsureOfficeStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->isOffice()) {
            abort(403, 'This area is restricted to the Office of the Chaplain.');
        }

        return $next($request);
    }
}
