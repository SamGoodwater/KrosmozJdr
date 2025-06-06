<?php

namespace App\Policies\Entity;

use App\Models\Entity\Attribute;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttributePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tous les rôles peuvent voir la liste des attributs
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attribute $attribute): bool
    {
        // Tous les rôles peuvent voir un attribut
        return true;
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
    public function update(User $user, Attribute $attribute): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attribute $attribute): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attribute $attribute): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attribute $attribute): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }
}
