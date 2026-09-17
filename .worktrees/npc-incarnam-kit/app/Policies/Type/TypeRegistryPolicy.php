<?php

declare(strict_types=1);

namespace App\Policies\Type;

use App\Models\User;
use App\Policies\Entity\BaseEntityPolicy;
use Illuminate\Database\Eloquent\Model;

/**
 * Mutations des registres de types (flags, état, suppression) : admin uniquement.
 *
 * `BaseEntityPolicy::update` autorise aussi l’auteur et `role >= write_level`.
 * Les races auto-créées au scrap ont `write_level=3`, ce qui laisserait un MJ
 * modifier `allow_scrap` ou supprimer l’entrée via `/api/types/*`.
 *
 * @example
 * Gate::authorize('update', $monsterRace); // true seulement si $user->isAdmin()
 */
abstract class TypeRegistryPolicy extends BaseEntityPolicy
{
    public function update(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    public function updateAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }
}
