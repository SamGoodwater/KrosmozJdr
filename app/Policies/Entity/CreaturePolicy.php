<?php

namespace App\Policies\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy d'autorisation pour l'entité Creature.
 *
 * La créature n'est pas une entité exposée : elle sert uniquement de parent pour NPC et Monster.
 * Aucun accès au tableau, à la création ni à la manipulation directe des créatures.
 */
class CreaturePolicy extends BaseEntityPolicy
{
    public function viewAny(?User $user): bool
    {
        return false;
    }

    public function view(?User $user, Model $model): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function createAny(User $user): bool
    {
        return false;
    }

    public function update(User $user, Model $model): bool
    {
        return false;
    }

    public function updateAny(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Model $model): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function manageAny(User $user): bool
    {
        return false;
    }
}
