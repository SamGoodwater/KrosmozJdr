<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * PNJ : visibilité via {@see BaseEntityPolicy} ; mutations admin only.
 */
class NpcPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
