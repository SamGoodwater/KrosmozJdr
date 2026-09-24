<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Models\Entity\Panoply;
use App\Models\User;
use App\Policies\Entity\Concerns\AdminMutationsOnly;
use Illuminate\Database\Eloquent\Model;

/**
 * Panoplies : visibilité via {@see BaseEntityPolicy} ; update hybride, reste admin-only.
 */
class PanoplyPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;

    /**
     * Admin, auteur, ou niveau ≥ write_level (override du trait admin-only).
     */
    public function update(User $user, Model $model): bool
    {
        if (! $model instanceof Panoply) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isAuthor($user, $model)) {
            return true;
        }

        return $this->userLevel($user) >= $this->writeLevel($model);
    }
}
