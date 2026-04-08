<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Clear stale route cache that may contain absolute paths from build environment
$routeCache = dirname(__DIR__).'/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    @unlink($routeCache);
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // health: '/up', - disabled, custom route in web.php
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $except = ['webhook/*'];

        // Disable CSRF in testing environment
        if ($_ENV['APP_ENV'] ?? null === 'testing') {
            $except[] = '*';
        }

        $middleware->validateCsrfTokens(except: $except);

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
