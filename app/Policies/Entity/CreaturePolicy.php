<?php

namespace App\Policies\Entity;

use App\Models\Entity\Creature;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

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

    /**
     * Stats runtime (JSON) pour fiches consultables sans compte.
     *
     * Aligné sur la visibilité du monstre / PNJ lié : un brouillon ne fuit pas
     * via l’id créature (énumérable). Une créature orpheline suit {@see parent::view()}.
     *
     * @example Gate::forUser($guest)->allows('viewResolvedStats', $creature)
     */
    public function viewResolvedStats(?User $user, Model $model): bool
    {
        if (! $model instanceof Creature) {
            return false;
        }

        $model->loadMissing(['monster', 'npc']);
        $monster = $model->monster;
        $npc = $model->npc;
        $hasShell = $monster !== null || $npc !== null;

        if ($monster !== null && Gate::forUser($user)->allows('view', $monster)) {
            return true;
        }

        if ($npc !== null && Gate::forUser($user)->allows('view', $npc)) {
            return true;
        }

        if ($hasShell) {
            return false;
        }

        return parent::view($user, $model);
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
