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
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }
        return $user !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Section $section): bool
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }
        if ($section->users->contains($user->id)) {
            return true;
        }
        return (bool) $section->is_visible;
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
        return in_array($user->role, ['game_master', 'admin', 'super_admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Section $section): bool
    {
        return $user->can('update', $section->page);
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
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
