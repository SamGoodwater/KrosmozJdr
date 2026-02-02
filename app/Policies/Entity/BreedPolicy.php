<?php

namespace App\Policies\Entity;

use App\Models\Entity\Breed;
use App\Models\User;
use Illuminate\Auth\Access\Response;

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
