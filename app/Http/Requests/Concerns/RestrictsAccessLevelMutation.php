<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Support\Entity\AccessLevelMutationGuard;

/**
 * Empêche un non-admin de modifier `read_level` / `write_level` (SEC-07).
 *
 * Les champs sont retirés avant validation : le front peut les renvoyer
 * sans erreur, mais seuls les admins les appliquent.
 *
 * @example use RestrictsAccessLevelMutation; // dans un FormRequest d’entité
 */
trait RestrictsAccessLevelMutation
{
    /**
     * Retire read_level / write_level si l’acteur n’est pas admin.
     */
    protected function stripAccessLevelsUnlessAdmin(): void
    {
        if (AccessLevelMutationGuard::userMayMutate($this->user())) {
            return;
        }

        foreach (AccessLevelMutationGuard::KEYS as $key) {
            $this->request->remove($key);
        }
    }
}
