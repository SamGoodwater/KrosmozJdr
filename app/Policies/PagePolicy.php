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
        // Les admins/super_admin peuvent toujours lister les pages
        if ($user && in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])) {
            return true;
        }

        // Par défaut, on autorise l'accès à la liste pour les utilisateurs connectés
        // (la policy/les scopes limiteront ensuite ce qu'ils voient réellement)
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Page $page): bool
    {
        if (in_array($user->role, [User::ROLE_GAME_MASTER, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 3, 4, 5, 'game_master', 'admin', 'super_admin'])) {
            return true;
        }
        // Charger la relation users si elle n'est pas déjà chargée
        if (!$page->relationLoaded('users')) {
            $page->load('users');
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
        // Vérifier les rôles avec les constantes (entiers) ou les noms (strings)
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Page $page): bool
    {
        // Les super_admin peuvent toujours modifier
        if (in_array($user->role, [User::ROLE_SUPER_ADMIN, 5, 'super_admin'])) {
            return true;
        }

        // Utiliser la méthode canBeEditedBy du modèle qui prend en compte can_edit_role
        return $page->canBeEditedBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Page $page): bool
    {
        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])) {
            return true;
        }
        return $page->users->contains($user->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Page $page): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Page $page): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
    }
}
