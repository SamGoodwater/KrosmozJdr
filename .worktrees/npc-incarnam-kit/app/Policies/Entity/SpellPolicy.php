<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Sorts : lecture selon états / niveaux ; édition par l’auteur ou un admin.
 */
class SpellPolicy extends BaseEntityPolicy
{
    public function update(User $user, Model $model): bool
    {
        if (! $model instanceof Spell) {
            return false;
        }

        return (int) $user->id === (int) $model->created_by || $user->isAdmin();
    }

    public function updateAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Model $model): bool
    {
        if (! $model instanceof Spell) {
            return false;
        }

        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function manageAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
