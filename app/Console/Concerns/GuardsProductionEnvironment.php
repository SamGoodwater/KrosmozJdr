<?php

declare(strict_types=1);

namespace App\Console\Concerns;

/**
 * Trait pour les commandes qui ne doivent pas s’exécuter en production (ou uniquement en local/testing).
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

    /**
     * Interdit uniquement `APP_ENV=production` (staging et autres environnements non prod restent autorisés).
     *
     * @return bool true si la commande peut continuer
     */
    protected function guardNotProduction(string $message = 'Cette commande est interdite en production.'): bool
    {
        if (! app()->environment('production')) {
            return true;
        }

        $this->error($message);

        return false;
    }
}
