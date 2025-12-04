<?php

namespace App\Policies\Entity;

use App\Models\Entity\Creature;
use App\Models\User;

/**
 * Policy d'autorisation pour l'entité Creature.
 *
 * Utilise BaseEntityPolicy pour les méthodes communes.
 */
class CreaturePolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
    // Pas besoin de les redéfinir car elles suivent le pattern standard
}
