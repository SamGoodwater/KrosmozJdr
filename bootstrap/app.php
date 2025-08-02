<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configuration des middlewares web
        $webMiddleware = [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ];

        // En mode test, ne pas inclure le CSRF et utiliser des sessions array
        if (($_ENV['APP_ENV'] ?? 'local') === 'testing') {
            $middleware->web(append: $webMiddleware);
            // Désactiver complètement le CSRF en mode test
            config(['session.driver' => 'array']);
            config(['session.verify_csrf_token' => false]);
        } else {
            // En mode normal, inclure tous les middlewares web par défaut
            $middleware->web(append: array_merge([
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            ], $webMiddleware));
        }
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
