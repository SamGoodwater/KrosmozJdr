<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsureAdminAreaAccess;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RequirePasswordWithInactivity;
use App\Support\ProjectSchedule\ProjectScheduleRegistrar;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        ProjectScheduleRegistrar::register($schedule);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Exclure les routes API scrapping de la vérification CSRF (appelées depuis l'UI, auth + role:admin).
        $middleware->validateCsrfTokens([
            'api/scrapping',
            'api/scrapping/*',
        ]);

        // Enregistrer les middlewares
        $middleware->alias([
            'role' => CheckRole::class,
            'password.confirm' => RequirePasswordWithInactivity::class,
            'verified' => EnsureEmailIsVerified::class,
            'admin.area' => EnsureAdminAreaAccess::class,
            'content.area' => \App\Http\Middleware\EnsureContentManagementAccess::class,
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

        /**
         * Rendu personnalisé des erreurs HTTP (403, 404, 419, 429, 500, 503, etc.).
         */
        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            $status = $e->getStatusCode();
            $messages = [
                403 => ['title' => 'Accès refusé', 'description' => 'Tu n’as pas les permissions nécessaires pour accéder à cette page.'],
                404 => ['title' => 'Page introuvable', 'description' => 'La page demandée n’existe pas ou a été déplacée.'],
                419 => ['title' => 'Session expirée', 'description' => 'Ta session a expiré. Recharge la page et réessaie.'],
                429 => ['title' => 'Trop de requêtes', 'description' => 'Tu envoies trop de requêtes en peu de temps. Patiente quelques instants.'],
                500 => ['title' => 'Erreur serveur', 'description' => 'Une erreur interne est survenue. Réessaie dans quelques instants.'],
                503 => ['title' => 'Service indisponible', 'description' => 'Le service est temporairement indisponible. Réessaie un peu plus tard.'],
            ];
            $meta = $messages[$status] ?? [
                'title' => 'Une erreur est survenue',
                'description' => 'Une erreur inattendue s’est produite lors du traitement de la requête.',
            ];
            $hint = (bool) config('app.debug')
                ? 'Mode debug actif : consulte les logs Laravel (storage/logs/laravel.log) et la console navigateur pour plus de détails.'
                : null;

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $meta['title'],
                    'description' => $meta['description'],
                    'hint' => $hint,
                    'status' => $status,
                ], $status);
            }

            if ($request->header('X-Inertia')) {
                return Inertia::render('Errors/HttpError', [
                    'status' => $status,
                    'title' => $meta['title'],
                    'description' => $meta['description'],
                    'hint' => $hint,
                ])->toResponse($request)->setStatusCode($status);
            }

            return response()->view('errors.http', [
                'status' => $status,
                'title' => $meta['title'],
                'description' => $meta['description'],
                'hint' => $hint,
            ], $status);
        });
    })->create();
