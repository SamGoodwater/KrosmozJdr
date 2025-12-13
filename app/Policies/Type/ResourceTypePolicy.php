<?php

namespace App\Policies\Type;

use App\Models\Type\ResourceType;
use App\Policies\Entity\BaseEntityPolicy;

/**
 * Policy d'autorisation pour les types de ressource (ResourceType).
 *
 * Hérite des règles par défaut :
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class ResourceTypePolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
}


