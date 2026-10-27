<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all upstream proxies (Dokploy Traefik / Docker ingress) for proper HTTPS & IP resolution
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'login',
            'demo-switch',
            'demo-switch/*',
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle CSRF Token Mismatch gracefully so user never sees raw 419 Page Expired
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your session has expired. Please refresh the page and try again.'], 419);
            }
            return redirect()->route('login')->with('warning', 'Your session expired. Please sign in to continue.');
        });

        // Handle generic 419 HTTP exceptions
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Your session has expired. Please refresh the page and try again.'], 419);
                }
                return redirect()->route('login')->with('warning', 'Your session expired. Please sign in to continue.');
            }
        });
    })->create();
