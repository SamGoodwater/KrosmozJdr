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
     */
    public function viewAny(User $user, Page $page): bool
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $page): bool
    {
        if (in_array($user->role, ['game_master', 'admin', 'super_admin'])) {
            return true;
        }
        if ($page->users->contains($user->id)) {
            return true;
        }
        return (bool) $page->is_visible;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        if (in_array($user->role, ['game_master', 'admin', 'super_admin'])) {
            return true;
        }
        return $page->users->contains($user->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }
        return $page->users->contains($user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
