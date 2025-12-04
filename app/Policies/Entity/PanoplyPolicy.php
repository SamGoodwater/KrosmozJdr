<?php

namespace App\Policies\Entity;

use App\Models\Entity\Panoply;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PanoplyPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Accessible à tous, même sans authentification
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Panoply $panoply): bool
    {
        // Accessible à tous, même sans authentification
        return true;
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
    public function update(User $user, Panoply $panoply): bool
    {
        // Un utilisateur peut modifier sa propre panoplie, ou un admin peut modifier n'importe quelle panoplie
        return $panoply->created_by === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Panoply $panoply): bool
    {
        // Un utilisateur peut supprimer sa propre panoplie, ou un admin peut supprimer n'importe quelle panoplie
        return $panoply->created_by === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Panoply $panoply): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Panoply $panoply): bool
    {
        return false;
    }
}
