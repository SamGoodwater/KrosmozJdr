<?php

namespace App\Policies\Entity;

/**
 * Policy d'autorisation pour l'entité Scenario.
 *
 * Les règles sont pilotées par `state` + `read_level`/`write_level`.
 */
class ScenarioPolicy extends BaseEntityPolicy
{
    // Tout est géré par BaseEntityPolicy.
}
