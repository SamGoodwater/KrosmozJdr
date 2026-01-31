<?php

namespace App\Policies\Type;

use App\Models\Type\SpellType;
use App\Policies\Entity\BaseEntityPolicy;

/**
 * Policy d'autorisation pour les types de sorts (SpellType).
 *
 * Hérite des règles par défaut :
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class SpellTypePolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
}

