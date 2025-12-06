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
     */
    public function view(User $user, Section $section): bool
    {
        if (in_array($user->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN, 4, 5, 'admin', 'super_admin'])) {
            return true;
        }
        // Charger la relation users si elle n'est pas déjà chargée
        if (!$section->relationLoaded('users')) {
            try {
                $section->load('users');
            } catch (\Exception $e) {
                // Si la relation ne peut pas être chargée, continuer avec les autres vérifications
            }
        }
        if ($section->relationLoaded('users') && $section->users->contains($user->id)) {
            return true;
        }
        // is_visible est déjà un enum Visibility grâce au cast
        $visibility = $section->is_visible instanceof \App\Enums\Visibility 
            ? $section->is_visible 
            : \App\Enums\Visibility::tryFrom($section->is_visible);
        if (!$visibility) {
            return false;
        }
        return $visibility->isAccessibleBy($user);
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
