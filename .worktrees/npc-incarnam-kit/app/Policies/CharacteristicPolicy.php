<?php

namespace App\Policies;

use App\Models\Characteristic;
use App\Models\User;

/**
 * Policy pour le modèle Characteristic (administration des caractéristiques).
 *
 * Les actions d'édition (view, create, update, delete) sont réservées aux admins et super_admins.
 */
class CharacteristicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Characteristic $characteristic): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Characteristic $characteristic): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Characteristic $characteristic): bool
    {
        return $user->isAdmin();
    }
}
