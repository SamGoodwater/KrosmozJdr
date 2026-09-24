<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * Monstres : visibilité pilotée par {@see BaseEntityPolicy} + restrictions d’édition réservées aux admins.
 */
class MonsterPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
