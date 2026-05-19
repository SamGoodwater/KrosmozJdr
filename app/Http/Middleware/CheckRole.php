<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Super-admin humain : dépasse les contrôles de rôle (comme anciennement tout super_admin hors compte système)
        if ($request->user()->isInteractiveSuperAdmin()) {
            return $next($request);
        }

        // Vérifie si l'utilisateur a l'un des rôles requis
        foreach ($roles as $role) {
            if ($request->user()->verifyRole($role)) {
                return $next($request);
            }
        }

        // Si l'utilisateur n'a pas les droits nécessaires
        abort(403, 'Accès non autorisé.');
    }
}
