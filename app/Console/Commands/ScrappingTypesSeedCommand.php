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
class ScrappingTypesSeedCommand extends Command
{
    protected $signature = 'scrapping:types:seed
                            {--lang=fr : Langue du catalogue DofusDB}
                            {--skip-cache : Ignorer le cache du catalogue}
                            {--no-files : Ne pas écrire les fichiers data (seulement exécuter les seeders sur les fichiers existants)}';

    protected $description = 'Remplit les types item (ressource / consommable / équipement) depuis l’API DofusDB puis les seeders';
    protected $aliases = ['scrapping:seed-item-types'];

    public function handle(): int
    {
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');
        $noFiles = (bool) $this->option('no-files');

        if (!$noFiles) {
            $this->info('Étape 1/2 : extraction depuis l’API DofusDB (item-types, toutes les pages)…');
            $extractCode = $this->call(ScrappingTypesExtractCommand::class, [
                '--lang' => $lang,
                '--skip-cache' => $skipCache,
            ]);
            if ($extractCode !== self::SUCCESS) {
                $this->error('Échec de l’extraction des types item.');
                return self::FAILURE;
            }
        } else {
            $this->info('Étape 1/2 : ignorée (--no-files). Utilisation des fichiers data existants.');
        }

        $this->info('Étape 2/2 : exécution des seeders (resource_types, consumable_types, item_types)…');
        $seeders = [
            'Database\\Seeders\\Type\\ResourceTypeSeeder',
            'Database\\Seeders\\Type\\ConsumableTypeSeeder',
            'Database\\Seeders\\Type\\ItemTypeSeeder',
        ];
        foreach ($seeders as $seederClass) {
            $code = Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->error("Échec du seeder {$seederClass}.");
                return self::FAILURE;
            }
        }

        $this->info('Terminé. Les types sont à jour depuis l’API DofusDB.');

        return self::SUCCESS;
    }
}
