<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * Spécialisations : visibilité via {@see BaseEntityPolicy} ; mutations admin only.
 */
class SpecializationPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
