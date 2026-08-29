<?php

namespace App\Policies\Type;

/**
 * Policy d'autorisation pour les types de consommables (ConsumableType).
 *
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class ConsumableTypePolicy extends TypeRegistryPolicy
{
    //
}
