<?php

declare(strict_types=1);

namespace App\Policies\Type;

use App\Models\User;
use App\Policies\Entity\BaseEntityPolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Mutations des registres de types (flags, état, suppression) : MJ et plus.
 *
 * `BaseEntityPolicy::update` autorise aussi l’auteur et `role >= write_level`.
 * On force ici le seuil MJ pour les flags catalogue / scrap via `/api/types/*`.
 *
 * @example
 * Gate::authorize('update', $monsterRace); // true si $user->isGameMaster()
 */
abstract class TypeRegistryPolicy extends BaseEntityPolicy
{
    public function view(?User $user, Model $model): bool
    {
        if ($user?->isGameMaster() === true) {
            return true;
        }

        return parent::view($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->isGameMaster();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->isGameMaster();
    }

    public function updateAny(User $user): bool
    {
        return $user->isGameMaster();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->isGameMaster();
    }

    public function publish(User $user, ?Model $model = null): bool
    {
        return $user->isGameMaster();
    }
}
