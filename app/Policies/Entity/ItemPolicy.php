<?php

namespace App\Policies\Entity;

use App\Models\Entity\Item;
use App\Models\User;

/**
 * Policy d'autorisation pour l'entité Item.
 *
 * Utilise BaseEntityPolicy pour les méthodes communes.
 */
class ItemPolicy extends BaseEntityPolicy
{
    // Toutes les méthodes sont héritées de BaseEntityPolicy
    // Pas besoin de les redéfinir car elles suivent le pattern standard
}
