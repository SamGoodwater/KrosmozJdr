<?php

namespace App\Policies\Type;

/**
 * Policy d'autorisation pour les races de monstres (MonsterRace).
 *
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class MonsterRacePolicy extends TypeRegistryPolicy
{
    //
}
