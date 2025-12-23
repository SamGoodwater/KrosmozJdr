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
     * Determine whether the user can create models in bulk / via édition multiple.
     *
     * @description
     * Utile pour exposer une permission "globale" au frontend (ex: afficher la sélection + panneau bulk).
     * Par défaut, identique à create().
     */
    public function createAny(User $user): bool
    {
        return $this->create($user);
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
     * Determine whether the user can update models in bulk / via édition multiple.
     *
     * @description
     * Par défaut, identique à update().
     */
    public function updateAny(User $user): bool
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
     * Determine whether the user can delete models in bulk / via actions multiples.
     *
     * @description
     * Par défaut, identique à delete().
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can access "maintenance/admin" actions
     * (ex: rafraîchir / resync / actions techniques).
     *
     * @description
     * Cette ability est volontairement séparée de updateAny/deleteAny pour permettre
     * un fine-tuning futur (ex: autoriser updateAny à des rôles non-admin sans leur
     * donner accès aux actions de maintenance).
     */
    public function manageAny(User $user): bool
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

