<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * Traits créature : visibilité via {@see BaseEntityPolicy} ; mutations admin only.
 */
class CreatureTraitPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
