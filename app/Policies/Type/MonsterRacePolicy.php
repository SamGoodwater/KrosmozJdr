<?php

namespace App\Policies\Type;

use App\Models\Type\MonsterRace;
use App\Policies\Entity\BaseEntityPolicy;

/**
 * Policy d'autorisation pour les races de monstres (MonsterRace).
 *
 * Hérite des règles par défaut :
 * - view/viewAny : public
 * - create/update/delete : admin uniquement
 */
class MonsterRacePolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
}

