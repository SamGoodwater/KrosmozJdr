<?php

declare(strict_types=1);

namespace App\Console\Concerns;

/**
 * Trait pour les commandes qui ne doivent s'exécuter qu'en local ou en testing.
 * En production, affiche un message d'erreur et indique à la commande de quitter en échec.
 */
trait GuardsProductionEnvironment
{
    /**
     * Vérifie que l'environnement est local ou testing. Sinon affiche une erreur.
     *
     * @return bool true si la commande peut continuer, false sinon (quitter avec FAILURE)
     */
    protected function guardDevelopmentOnly(): bool
    {
        if (app()->environment(['local', 'testing'])) {
            return true;
        }

        $this->error('Cette commande est désactivée en production. Utilisez-la uniquement en local.');

        return false;
    }
}
