<?php

declare(strict_types=1);

namespace App\Policies\Entity\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Mutations d’entité réservées aux admins (`isAdmin()`).
 *
 * À utiliser sur les policies qui restreignent écriture / bulk / publication
 * par rapport à {@see \App\Policies\Entity\BaseEntityPolicy} (où `updateAny`
 * est MJ et `update` autorise auteur / write_level).
 *
 * @example
 * class MonsterPolicy extends BaseEntityPolicy
 * {
 *     use AdminMutationsOnly;
 * }
 */
trait AdminMutationsOnly
{
    /**
     * Modification unitaire : admin uniquement.
     */
    public function update(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Édition multiple / bulk : admin uniquement.
     */
    public function updateAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Suppression unitaire : admin uniquement.
     */
    public function delete(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Suppression multiple : admin uniquement.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Actions de maintenance / admin : admin uniquement.
     */
    public function manageAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Publication `playable` : admin uniquement (relecteur = admin sur ces types).
     */
    public function publish(User $user, ?Model $model = null): bool
    {
        return $user->isAdmin();
    }
}
