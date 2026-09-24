<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * États / conditions : visibilité via {@see BaseEntityPolicy} ; mutations admin only.
 */
class ConditionPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
