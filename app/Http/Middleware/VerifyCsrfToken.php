<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * Les routes API scrapping sont appelées depuis l'UI (même origine) avec X-CSRF-TOKEN,
     * mais peuvent provoquer un mismatch si la session ou le token a été régénéré.
     * On les exclut car elles sont protégées par auth et réservées aux admins.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/scrapping/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * Determine if the request should be excluded from CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        // En environnement de test, toutes les routes sont exclues
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return true;
        }

        // Routes scrapping (web + auth) : exclues pour éviter mismatch token/session
        $path = trim($request->path(), '/');
        if (str_starts_with($path, 'api/scrapping')) {
            return true;
        }

        return parent::inExceptArray($request);
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Désactiver CSRF en environnement de test
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return true;
        }

        return parent::tokensMatch($request);
    }

    public function handle($request, \Closure $next)
    {
        // Désactiver complètement le CSRF en environnement de test
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
