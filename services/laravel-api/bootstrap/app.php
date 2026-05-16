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
        // Force Accept: application/json on api/* before route matching (covers 404s too)
        $middleware->prepend(\App\Http\Middleware\ForceJsonAcceptHeader::class);

        // No login route — return null so auth failure becomes a JSON 401, not a redirect
        $middleware->redirectGuestsTo(fn (\Illuminate\Http\Request $request) =>
            $request->expectsJson() ? null : null
        );

        $middleware->alias([
            'set.auth.user' => \App\Http\Middleware\SetAuthenticatedUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // expectsJson() is true when ForceJsonAcceptHeader ran; is('api/*') catches 404s before routing
        $exceptions->shouldRenderJsonWhen(fn ($request) => $request->expectsJson() || $request->is('api/*'));
    })->create();
