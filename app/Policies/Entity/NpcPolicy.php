<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Models\Entity\Npc;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * PNJ : visibilité via {@see BaseEntityPolicy}.
 */
class NpcPolicy extends BaseEntityPolicy
{
    public function update(User $user, Model $model): bool
    {
        if (! $model instanceof Npc) {
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
        if (! $model instanceof Npc) {
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
