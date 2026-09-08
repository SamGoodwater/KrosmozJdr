<?php

namespace App\Policies\Type;

/**
 * Policy d'autorisation pour les types d'objets (ItemType).
 *
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class ItemTypePolicy extends TypeRegistryPolicy
{
    //
}
