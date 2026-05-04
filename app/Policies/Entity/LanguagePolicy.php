<?php

namespace App\Policies\Entity;

use App\Models\Entity\Language;
use App\Models\User;

/**
 * Référentiel des langues — CRUD réservé aux administrateurs.
 */
class LanguagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Language $language): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Language $language): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Language $language): bool
    {
        return $user->isAdmin();
    }
}
