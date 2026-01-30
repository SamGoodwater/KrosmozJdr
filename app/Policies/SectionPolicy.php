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
    public function viewAny(?User $user): bool
    {
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
        if ($user && $user->isAdmin()) {
            return true;
        }
        
        // Utiliser la méthode du modèle qui gère correctement les invités et la visibilité
        return $section->canBeViewedBy($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?\App\Models\Page $page = null): bool
    {
        // Par défaut, la création d'une section nécessite un contexte de page.
        // (On autorise explicitement via `authorize('create', [Section::class, $page])`.)
        if (!$page) {
            return false;
        }

        return $user->can('update', $page);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Section $section): bool
    {
        // Utiliser la méthode canBeEditedBy du modèle (basée sur write_level)
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
        return $user->isAdmin();
    }
}
