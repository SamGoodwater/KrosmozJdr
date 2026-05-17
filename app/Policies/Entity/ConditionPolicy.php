<?php

namespace App\Policies\Entity;

use App\Models\Entity\Condition;
use App\Models\User;

class ConditionPolicy
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
    public function view(?User $user, Condition $condition): bool
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

    public function createAny(User $user): bool
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Condition $condition): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update models in bulk / via édition multiple.
     */
    public function updateAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Condition $condition): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Condition $condition): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Condition $condition): bool
    {
        return $user->isAdmin();
    }
}
