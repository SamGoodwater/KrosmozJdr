<?php

namespace App\Policies;

use App\Models\Characteristic;
use App\Models\User;

/**
 * Policy pour le modèle Characteristic (administration des caractéristiques).
 *
 * Édition réservée aux maîtres du jeu et plus (contenu de jeu).
 */
class CharacteristicPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isGameMaster();
    }

    public function view(User $user, Characteristic $characteristic): bool
    {
        return $user->isGameMaster();
    }

    public function create(User $user): bool
    {
        return $user->isGameMaster();
    }

    public function update(User $user, Characteristic $characteristic): bool
    {
        return $user->isGameMaster();
    }

    public function delete(User $user, Characteristic $characteristic): bool
    {
        return $user->isGameMaster();
    }
}
