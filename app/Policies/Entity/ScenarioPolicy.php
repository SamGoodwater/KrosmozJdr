<?php

namespace App\Policies\Entity;

use App\Models\Entity\Scenario;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScenarioPolicy
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
    public function view(?User $user, Scenario $scenario): bool
    {
        // Accessible à tous, même sans authentification
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Scenario $scenario): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $scenario->users->contains($user->id);
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
    public function delete(User $user, Scenario $scenario): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $scenario->users->contains($user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Scenario $scenario): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Scenario $scenario): bool
    {
        return false;
    }
}
