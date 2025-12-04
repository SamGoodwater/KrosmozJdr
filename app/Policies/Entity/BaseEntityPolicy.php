<?php

namespace App\Policies\Entity;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy de base pour les entités.
 *
 * Fournit les méthodes communes pour les policies d'entités simples.
 * Les policies spécifiques peuvent étendre cette classe ou la surcharger.
 */
abstract class BaseEntityPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * Par défaut, accessible à tous (même sans authentification).
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * Par défaut, accessible à tous (même sans authentification).
     */
    public function view(?User $user, Model $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * Par défaut, seuls les admins peuvent créer.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     *
     * Par défaut, seuls les admins peuvent modifier.
     */
    public function update(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * Par défaut, seuls les admins peuvent supprimer.
     */
    public function delete(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * Par défaut, la restauration n'est pas autorisée.
     */
    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * Par défaut, la suppression définitive n'est pas autorisée.
     */
    public function forceDelete(User $user, Model $model): bool
    {
        return false;
    }
}

