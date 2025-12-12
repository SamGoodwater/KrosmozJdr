<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Enregistrer le middleware CheckRole avec l'alias 'role'
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Gestion des redirections "intended" en contexte Inertia (SPA).
         *
         * @description
         * Quand un utilisateur non connecté tente d'accéder à une route protégée (`auth`),
         * Laravel déclenche une AuthenticationException. Sur une requête Inertia, on veut :
         * - mémoriser l'URL demandée (`url.intended`) pour pouvoir y revenir après login/register,
         * - rediriger vers la page de login via un "full reload" (Inertia::location),
         *   afin de rester compatible avec le protocole Inertia.
         *
         * @example
         * // GET /user/edit (protégé) -> redirect /login
         * // après login -> redirect vers /user/edit
         */
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            // On ne mémorise l'URL "intended" que pour les GET (navigation).
            // Cela évite de rediriger après login vers une URL de POST/PUT/DELETE.
            if ($request->isMethod('GET') && ! $request->routeIs([
                'login',
                'register',
                'password.*',
                'verification.*',
            ])) {
                redirect()->setIntendedUrl($request->fullUrl());
            }

            $loginUrl = route('login', absolute: false);

            if ($request->header('X-Inertia')) {
                return Inertia::location($loginUrl);
            }

            /**
             * Requêtes AJAX (axios, etc.)
             *
             * @description
             * Sur une requête XHR, Laravel redirige traditionnellement vers /login (302),
             * mais axios suit la redirection et se retrouve avec un HTML 200, ce qui rend
             * la détection côté client fragile. Ici, on renvoie un 401 JSON + une URL de
             * redirection, et on tente de mémoriser la page "courante" via le Referer.
             */
            if ($request->ajax()) {
                $referer = $request->headers->get('referer');
                if (is_string($referer) && $referer !== '') {
                    $parts = parse_url($referer);
                    $host = $parts['host'] ?? null;
                    $scheme = $parts['scheme'] ?? null;

                    // Sécurité : ne mémoriser que les URLs locales (évite les open redirects)
                    if (in_array($scheme, ['http', 'https'], true) && $host === $request->getHost()) {
                        redirect()->setIntendedUrl($referer);
                    }
                }

                return response()->json([
                    'message' => 'Unauthenticated.',
                    'redirect' => $loginUrl,
                ], 401);
            }

            // Pour les requêtes JSON (API), laisser le comportement par défaut (401 JSON)
            if ($request->expectsJson()) {
                return null;
            }

            return redirect()->guest($loginUrl);
        });

        /**
         * Les SPAs (Inertia) peuvent se retrouver avec un token CSRF "stale" lorsque la session expire.
         * Cela se manifeste par un 419 "Page Expired" sur une requête XHR/Inertia.
         *
         * Solution : forcer un rechargement complet via Inertia::location() afin de :
         * - recréer une session,
         * - régénérer un token CSRF valide,
         * - et permettre la ré-authentification automatique via le cookie "remember me" (si présent).
         */
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->header('X-Inertia')) {
                return Inertia::location($request->fullUrl());
            }

            return null;
        });
    })->create();
