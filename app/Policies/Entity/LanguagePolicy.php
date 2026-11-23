<?php

namespace App\Policies\Entity;

use App\Models\Entity\Language;
use App\Models\User;

/**
 * Référentiel des langues — lecture publique (Bibliothèque) ; CRUD MJ+.
 */
class LanguagePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Language $language): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isGameMaster();
    }

    public function update(User $user, Language $language): bool
    {
        return $user->isGameMaster();
    }

    public function delete(User $user, Language $language): bool
    {
        return $user->isGameMaster();
    }
}
