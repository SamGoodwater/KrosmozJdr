<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Accès à la gestion du contenu (game_master et rôles supérieurs).
 */
class EnsureContentManagementAccess
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();
        if ($user === null || ! $user->isGameMaster()) {
            abort(403, 'Accès réservé à la gestion du contenu (meneur de jeu et plus).');
        }

        return $next($request);
    }
}
