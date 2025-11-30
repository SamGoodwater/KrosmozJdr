<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
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
