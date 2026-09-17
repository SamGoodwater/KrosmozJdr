<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Models\Entity\CreatureTrait;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Traits créature : visibilité via {@see BaseEntityPolicy}.
 */
class CreatureTraitPolicy extends BaseEntityPolicy
{
    public function update(User $user, Model $model): bool
    {
        if (! $model instanceof CreatureTrait) {
            return false;
        }

        return $user->isAdmin();
    }

    public function updateAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Model $model): bool
    {
        if (! $model instanceof CreatureTrait) {
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
