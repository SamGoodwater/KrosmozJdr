<?php

namespace App\Policies\Entity;

/**
 * Policy d'autorisation pour l'entité Campaign.
 *
 * Les règles sont pilotées par `state` + `read_level`/`write_level`.
 */
class CampaignPolicy extends BaseEntityPolicy
{
    // Tout est géré par BaseEntityPolicy.
}
