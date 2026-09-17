<?php

namespace App\Policies\Type;

/**
 * Policy d'autorisation pour les types de sorts (SpellType).
 *
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class SpellTypePolicy extends TypeRegistryPolicy
{
    //
}
