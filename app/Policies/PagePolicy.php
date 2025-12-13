<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy d'autorisation pour l'entité Page.
 *
 * Définit les règles d'accès (lecture, écriture, suppression, restauration, etc.) selon le rôle, l'association et la visibilité.
 * S'appuie sur la matrice des privilèges du projet.
 */
class PagePolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * Signature Laravel : Gate::allows('viewAny', Page::class)
     * → un seul paramètre : l'utilisateur.
     */
    public function viewAny(?User $user): bool
    {
        // Par défaut : la liste est réservée aux utilisateurs connectés.
        // Les admins peuvent toujours lister.
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     * 
     * @param \App\Models\User|null $user L'utilisateur (null pour les invités)
     * @param \App\Models\Page $page La page à vérifier
     * @return bool
     */
    public function view(?User $user, Page $page): bool
    {
        return $page->canBeViewedBy($user);
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
    public function update(User $user, Page $page): bool
    {
        return $page->canBeEditedBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        return $page->canBeEditedBy($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Page $page): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return $user->isAdmin();
    }
}
