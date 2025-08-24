<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Policy de gestion des droits sur l'entité User.
 *
 * Règles principales :
 * - Le super_admin a tous les droits (méthode before)
 * - Un utilisateur peut voir/créer n'importe quel utilisateur
 * - Un utilisateur peut modifier/supprimer son propre compte, ou un admin peut le faire
 * - Seul un admin peut restaurer
 * - Seul un super_admin peut supprimer définitivement
 */
class UserPolicy
{
    /**
     * Donne tous les droits au super_admin avant toute vérification spécifique.
     *
     * @param User $user Utilisateur courant
     * @return bool|null true pour tout autoriser, null sinon
     */
    public function before(User $user): ?bool
    {
        if ($user->role === User::ROLE_SUPER_ADMIN) { // super_admin = 5
            return true;
        }
        return null; // Important de retourner null pour ne pas court-circuiter les autres méthodes
    }

    /**
     * Détermine si l'utilisateur peut voir la liste des utilisateurs.
     *
     * @param User $user Utilisateur courant
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut voir un utilisateur donné.
     *
     * @param User $user Utilisateur courant
     * @param User $model Utilisateur à consulter
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut créer un utilisateur.
     *
     * @param User $user Utilisateur courant
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut modifier un utilisateur donné.
     *
     * @param User $user Utilisateur courant
     * @param User $model Utilisateur à modifier
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $model->id === $user->id || $user->verifyRole(User::ROLE_ADMIN); // admin = 4
    }

    /**
     * Détermine si l'utilisateur peut supprimer un utilisateur donné.
     *
     * @param User $user Utilisateur courant
     * @param User $model Utilisateur à supprimer
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $model->id === $user->id || $user->verifyRole(User::ROLE_ADMIN); // admin = 4
    }

    /**
     * Détermine si l'utilisateur peut restaurer un utilisateur donné.
     *
     * @param User $user Utilisateur courant
     * @param User $model Utilisateur à restaurer
     * @return bool
     */
    public function restore(User $user, User $model): bool
    {
        return $user->verifyRole(User::ROLE_ADMIN); // admin = 4
    }

    /**
     * Détermine si l'utilisateur peut supprimer définitivement un utilisateur donné.
     *
     * @param User $user Utilisateur courant
     * @param User $model Utilisateur à supprimer définitivement
     * @return bool
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->verifyRole(User::ROLE_SUPER_ADMIN); // super_admin = 5
    }

    /**
     * Détermine si l'utilisateur peut modifier le rôle d'un autre utilisateur.
     * Seul le super_admin peut promouvoir en admin, personne ne peut promouvoir en super_admin.
     *
     * @param User $user Utilisateur courant
     * @param User $target Utilisateur à modifier
     * @return bool
     */
    public function updateRole(User $user, User $target): bool
    {
        // Le super_admin peut tout faire (déjà géré par before)
        if ($user->role === User::ROLE_SUPER_ADMIN) { // super_admin = 5
            return true;
        }

        // Un admin peut modifier le rôle de tout le monde sauf des admins et super_admins
        if ($user->role === User::ROLE_ADMIN) { // admin = 4
            return !in_array($target->role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN]); // admin = 4, super_admin = 5
        }

        // Les autres rôles ne peuvent rien faire
        return false;
    }
}
