<?php

declare(strict_types=1);

namespace App\Policies\Entity;

use App\Policies\Entity\Concerns\AdminMutationsOnly;

/**
 * Boutiques : visibilité via {@see BaseEntityPolicy} ; mutations admin only.
 */
class ShopPolicy extends BaseEntityPolicy
{
    use AdminMutationsOnly;
}
