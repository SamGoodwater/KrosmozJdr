<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Models\Entity\Spell;
use App\Models\User;
use App\Policies\Entity\Concerns\AdminMutationsOnly;
use Illuminate\Database\Eloquent\Model;

/**
 * Sorts : lecture selon états / niveaux ; édition par l’auteur ou un admin.
 */
class SpellPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;

    /**
     * Auteur ou admin (override du trait admin-only).
     */
    public function update(User $user, Model $model): bool
    {
        if (! $model instanceof Spell) {
            return false;
        }

        return (int) $user->id === (int) $model->created_by || $user->isAdmin();
    }
}
