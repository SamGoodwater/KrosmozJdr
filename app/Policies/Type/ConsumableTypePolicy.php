<?php

namespace App\Policies\Type;

use App\Models\Type\ConsumableType;
use App\Policies\Entity\BaseEntityPolicy;

/**
 * Policy d'autorisation pour les types de consommables (ConsumableType).
 *
 * Hérite des règles par défaut :
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class ConsumableTypePolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
}

