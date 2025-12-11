<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy d'autorisation pour l'entité Section.
 *
 * Définit les règles d'accès (lecture, écriture, suppression, restauration, etc.) selon le rôle, l'association et la visibilité.
 * Prend en compte les droits sur la page parente pour les actions d'écriture.
 * S'appuie sur la matrice des privilèges du projet.
 */
class SectionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])) {
            return true;
        }
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     * 
     * @param \App\Models\User|null $user L'utilisateur (null pour les invités)
     * @param \App\Models\Section $section La section à vérifier
     * @return bool
     */
    public function view(?User $user, Section $section): bool
    {
        // Les admins peuvent toujours voir
        if ($user && in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])) {
            return true;
        }
        
        // Utiliser la méthode du modèle qui gère correctement les invités et la visibilité
        return $section->canBeViewedBy($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $pageId = request('page_id');
        if ($pageId) {
            $page = \App\Models\Page::find($pageId);
            if ($page && $user->can('update', $page)) {
                return true;
            }
        }
        return in_array($user->role, [User::ROLE_GAME_MASTER, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 3, 4, 5, 'game_master', 'admin', 'super_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Section $section): bool
    {
        // Utiliser la méthode canBeEditedBy du modèle qui prend en compte can_edit_role
        return $section->canBeEditedBy($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Section $section): bool
    {
        return $user->can('update', $section->page);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Section $section): bool
    {
        return $user->can('update', $section->page);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Section $section): bool
    {
        return in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin']);
    }
}
