<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Models\Entity\Capability;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Capacités : lecture publique restreinte par état ; écriture admin.
 */
class CapabilityPolicy extends BaseEntityPolicy
{
    public function update(User $user, Model $model): bool
    {
        if (! $model instanceof Capability) {
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
        if (! $model instanceof Capability) {
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
