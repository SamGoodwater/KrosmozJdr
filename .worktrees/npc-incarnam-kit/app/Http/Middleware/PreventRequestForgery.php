<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery as Middleware;
use Illuminate\Http\Request;

class PreventRequestForgery extends Middleware
{
    /**
     * URIs excluded from CSRF / request-forgery verification.
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

    protected function inExceptArray($request)
    {
        if (app()->environment('testing') || config('app.env') === 'testing') {
            return true;
        }

        $path = trim($request->path(), '/');
        if (str_starts_with($path, 'api/scrapping')) {
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
