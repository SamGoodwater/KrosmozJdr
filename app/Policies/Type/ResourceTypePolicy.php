<?php

namespace App\Policies\Type;

/**
 * Policy d'autorisation pour les types de ressource (ResourceType).
 *
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class ResourceTypePolicy extends TypeRegistryPolicy
{
    //
}
