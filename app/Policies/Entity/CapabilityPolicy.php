<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * Capacités : lecture publique restreinte par état ; écriture admin.
 */
class CapabilityPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
