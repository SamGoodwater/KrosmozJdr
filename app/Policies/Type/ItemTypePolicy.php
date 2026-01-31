<?php

namespace App\Policies\Type;

use App\Models\Type\ItemType;
use App\Policies\Entity\BaseEntityPolicy;

/**
 * Policy d'autorisation pour les types d'objets (ItemType).
 *
 * Hérite des règles par défaut :
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class ItemTypePolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
}

