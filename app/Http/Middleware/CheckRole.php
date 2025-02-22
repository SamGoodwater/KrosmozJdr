<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Si l'utilisateur est super_admin, on le laisse passer
        if ($request->user()->verifyRole('super_admin')) {
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
