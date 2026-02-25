<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Remplit les tables resource_types, consumable_types, item_types depuis l’API DofusDB.
 *
 * Une seule commande : récupère tous les item-types via l’API (superTypeId → Ressource / Consommable / Équipement),
 * écrit les fichiers database/seeders/data/*.php puis exécute les 3 seeders pour synchroniser la BDD.
 * Aucun type n’est oublié : la classification repose sur l’API (https://api.dofusdb.fr/item-types).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_TYPES_ITEM_BDD_SEEDER.md
 */
class SeedItemTypesFromApiCommand extends Command
{
    protected $signature = 'scrapping:seed-item-types
                            {--lang=fr : Langue du catalogue DofusDB}
                            {--skip-cache : Ignorer le cache du catalogue}
                            {--no-files : Ne pas écrire les fichiers data (seulement exécuter les seeders sur les fichiers existants)}';

    protected $description = 'Remplit les types item (ressource / consommable / équipement) depuis l’API DofusDB puis les seeders';

    public function handle(): int
    {
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');
        $noFiles = (bool) $this->option('no-files');

        if (!$noFiles) {
            $this->info('Étape 1/2 : extraction depuis l’API DofusDB (item-types, toutes les pages)…');
            $this->call(ExtractItemTypesCommand::class, [
                '--lang' => $lang,
                '--skip-cache' => true, // toujours refaire les appels API pour avoir les 232 types
            ]);
        } else {
            $this->info('Étape 1/2 : ignorée (--no-files). Utilisation des fichiers data existants.');
        }

        $this->info('Étape 2/2 : exécution des seeders (resource_types, consumable_types, item_types)…');
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\Type\\ResourceTypeSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\Type\\ConsumableTypeSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\Type\\ItemTypeSeeder']);

        $this->info('Terminé. Les types sont à jour depuis l’API DofusDB.');

        return self::SUCCESS;
    }
}
