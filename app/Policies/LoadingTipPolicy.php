<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LoadingTip;
use App\Models\User;

/**
 * Astuces d’écran de chargement — CRUD réservé aux administrateurs.
 */
class LoadingTipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, LoadingTip $loadingTip): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, LoadingTip $loadingTip): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, LoadingTip $loadingTip): bool
    {
        return $user->isAdmin();
    }
}
