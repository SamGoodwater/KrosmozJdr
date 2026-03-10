<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response;

/**
 * Confirmation mot de passe avec ré-verrouillage après inactivité.
 *
 * Exige une confirmation du mot de passe avant d'accéder aux routes protégées.
 * Une fois confirmé, l'accès reste débloqué pendant la durée d'inactivité max
 * (défaut 1 h). Chaque accès à une route protégée réinitialise le compteur.
 *
 * @see config/auth.php — password_inactivity_timeout
 */
class RequirePasswordWithInactivity
{
    public function __construct(
        protected ResponseFactory $responseFactory,
        protected UrlGenerator $urlGenerator
    ) {}

    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null, int $inactivityTimeoutSeconds = 0): Response
    {
        $timeout = $inactivityTimeoutSeconds > 0
            ? $inactivityTimeoutSeconds
            : (int) config('auth.password_inactivity_timeout', 3600);

        if ($this->shouldConfirmPassword($request, $timeout)) {
            if ($request->expectsJson()) {
                return $this->responseFactory->json([
                    'message' => 'Password confirmation required.',
                ], 423);
            }

            return $this->responseFactory->redirectGuest(
                $this->urlGenerator->route($redirectToRoute ?: 'password.confirm')
            );
        }

        $request->session()->put('auth.password_last_activity_at', Date::now()->unix());

        return $next($request);
    }

    protected function shouldConfirmPassword(Request $request, int $inactivityTimeout): bool
    {
        if (! $request->session()->has('auth.password_confirmed_at')) {
            return true;
        }

        $lastActivity = $request->session()->get(
            'auth.password_last_activity_at',
            $request->session()->get('auth.password_confirmed_at', 0)
        );

        return (Date::now()->unix() - $lastActivity) > $inactivityTimeout;
    }
}
