<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Application-level hardening headers (nginx adds frame/nosniff/referrer headers at the edge).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        // The attendance scanner needs this site's camera; nothing else needs device features
        $response->headers->set('Permissions-Policy', 'camera=(self), microphone=(), geolocation=(), payment=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Signed-in pages hold personal and club data: never let browsers or proxies keep a copy
        if ($request->user() && !$response->headers->has('Content-Disposition')) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }
}
