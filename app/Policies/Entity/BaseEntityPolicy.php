<?php

namespace App\Policies\Entity;

use App\Models\User;
use App\Services\EntityDisplay\EntityDisplayVisibilityService;
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
     * L’auteur de la fiche peut toujours la consulter et la modifier (hors admin).
     */
    protected function isAuthor(?User $user, Model $model): bool
    {
        if ($user === null || ! isset($model->created_by) || $model->created_by === null) {
            return false;
        }

        return (int) $user->id === (int) $model->created_by;
    }

    /**
     * Matrice « Gérer l’affichage » : rôle minimal pour voir un état donné (avant read_level / write_level).
     */
    protected function passesDisplayVisibilityGate(?User $user, Model $model): bool
    {
        $key = app(EntityDisplayVisibilityService::class)->permissionKeyForModel($model);
        if ($key === null) {
            return true;
        }

        $state = (string) ($model->state ?? 'draft');

        return app(EntityDisplayVisibilityService::class)->viewerMeetsMinimumRole($user, $key, $state);
    }

    protected function userLevel(?User $user): int
    {
        // Invité = 0
        return $user ? (int) ($user->role ?? 0) : 0;
    }

    protected function state(Model $model): string
    {
        return (string) ($model->state ?? 'draft');
    }

    protected function readLevel(Model $model): int
    {
        return (int) ($model->read_level ?? 0);
    }

    protected function writeLevel(Model $model): int
    {
        // Par défaut, un niveau "éditeur" (MJ).
        return (int) ($model->write_level ?? 3);
    }

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
        if ($user && $user->isAdmin()) {
            return true;
        }

        if ($this->isAuthor($user, $model)) {
            return true;
        }

        if (! $this->passesDisplayVisibilityGate($user, $model)) {
            return false;
        }

        $state = $this->state($model);
        $level = $this->userLevel($user);

        // Archivé : matrice + read_level (comme playable, sans réservation implicite « admin only »).
        if ($state === 'archived') {
            return $level >= $this->readLevel($model);
        }

        // Jouable : lisible si niveau >= read_level
        if ($state === 'playable') {
            return $level >= $this->readLevel($model);
        }

        // Raw / draft / auto : réservé aux éditeurs (niveau >= write_level).
        return $user !== null && $level >= $this->writeLevel($model);
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
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isAuthor($user, $model)) {
            return true;
        }

        return $this->userLevel($user) >= $this->writeLevel($model);
    }

    /**
     * Determine whether the user can update models in bulk / via édition multiple.
     *
     * @description
     * Par défaut, identique à update().
     */
    public function updateAny(User $user): bool
    {
        return $user->isGameMaster();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * Par défaut, seuls les admins peuvent supprimer.
     */
    public function delete(User $user, Model $model): bool
    {
        return $this->update($user, $model);
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
     * Par défaut, la restauration est réservée aux admins.
     */
    public function restore(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * Par défaut, la suppression définitive est réservée aux admins.
     */
    public function forceDelete(User $user, Model $model): bool
    {
        return $user->isAdmin();
    }
}
