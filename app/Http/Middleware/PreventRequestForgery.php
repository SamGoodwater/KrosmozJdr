<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;
use Illuminate\Http\Request;

class PreventRequestForgery extends Middleware
{
    /**
     * URIs excluded from CSRF / request-forgery verification.
     *
     * Aucune route applicative n’est exclue : l’atelier DofusDB est sous `/api/dofusdb`
     * (session + CSRF). Les tests bypassent ce middleware via {@see handle()}.
     *
     * @var array<int, string>
     */
    protected $except = [];

    protected function inExceptArray($request)
    {
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return true;
        }

        return parent::inExceptArray($request);
    }

    protected function tokensMatch($request)
    {
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return true;
        }

        return parent::tokensMatch($request);
    }

    public function handle($request, \Closure $next)
    {
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
