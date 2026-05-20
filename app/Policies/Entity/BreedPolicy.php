<?php

namespace App\Policies\Entity;

use App\Models\Entity\Breed;
use App\Models\User;
use App\Services\EntityDisplay\EntityDisplayVisibilityService;

/**
 * Lecture des classes : jouable selon {@see Breed::$read_level}, brouillon réservé à l’auteur ou au niveau {@see Breed::$write_level}.
 */
class BreedPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Breed $breed): bool
    {
        if ($user?->isAdmin()) {
            return true;
        }

        if ($user !== null && $breed->created_by !== null && (int) $user->id === (int) $breed->created_by) {
            return true;
        }

        $entityKey = app(EntityDisplayVisibilityService::class)->permissionKeyForModel($breed);
        if ($entityKey !== null && ! app(EntityDisplayVisibilityService::class)->viewerMeetsMinimumRole($user, $entityKey, (string) $breed->state)) {
            return false;
        }

        $state = (string) $breed->state;
        $level = $user !== null ? (int) ($user->role ?? 0) : 0;

        if ($state === Breed::STATE_ARCHIVED) {
            return $level >= (int) $breed->read_level;
        }

        if ($state === Breed::STATE_PLAYABLE) {
            return $level >= (int) $breed->read_level;
        }

        // Brouillon / brut : pas d’invité ; rôle suffisant pour l’édition de la fiche.
        if ($user === null) {
            return false;
        }

        return $level >= (int) $breed->write_level;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Breed $breed): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update models in bulk.
     */
    public function updateAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Breed $breed): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Breed $breed): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Breed $breed): bool
    {
        return false;
    }
}
