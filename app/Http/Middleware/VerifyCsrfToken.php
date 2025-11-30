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
    public function handle($request, \Closure $next)
    {
        // Désactiver complètement le CSRF en environnement de test
        // En Laravel 11, l'environnement de test est défini dans phpunit.xml via APP_ENV=testing
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
