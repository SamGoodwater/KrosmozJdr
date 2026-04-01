<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Accès à l’espace administration (tableau de bord, navigation latérale commune).
 *
 * Réservé aux meneurs de jeu et rôles supérieurs (game_master, admin, super_admin).
 */
class EnsureAdminAreaAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();
        if ($user === null || ! $user->isGameMaster()) {
            abort(403, 'Accès réservé à l’équipe d’administration.');
        }

        return $next($request);
    }
}
