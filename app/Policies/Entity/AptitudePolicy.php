<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * Aptitudes : visibilité via {@see BaseEntityPolicy} ; mutations admin only.
 */
class AptitudePolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
