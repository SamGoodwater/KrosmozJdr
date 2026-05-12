<?php

declare(strict_types=1);

namespace App\Console\Commands\Scrapping;

use App\Console\ArtisanExitCode;
use Database\Seeders\Type\ConsumableTypeSeeder;
use Database\Seeders\Type\ItemTypeSeeder;
use Database\Seeders\Type\ResourceTypeSeeder;
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
                            {--no-files : Ne pas écrire les fichiers data (seulement exécuter les seeders sur les fichiers existants)}
                            {--only= : Sous-ensemble des seeders item-types (virgules) : resource, consumable, item ou equipment (syn. item) ; all ou vide = les trois}';

    protected $description = 'Remplit les types item (ressource / consommable / équipement) depuis l’API DofusDB puis les seeders';

    protected $aliases = ['scrapping:seed-item-types'];

    /** @var array<string, class-string> */
    private const ITEM_TYPE_SEEDERS = [
        'resource' => ResourceTypeSeeder::class,
        'consumable' => ConsumableTypeSeeder::class,
        'item' => ItemTypeSeeder::class,
    ];

    public function handle(): int
    {
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');
        $noFiles = (bool) $this->option('no-files');

        $keys = $this->resolveItemTypeSeederKeys((string) $this->option('only'));
        if ($keys === []) {
            $this->error('Aucun type item valide dans --only (attendu : resource, consumable, item, equipment, all).');

            return ArtisanExitCode::FAILURE;
        }

        if (! $noFiles) {
            $this->info('Étape 1/2 : extraction depuis l’API DofusDB (item-types, toutes les pages)…');
            $extractCode = $this->call(ScrappingTypesExtractCommand::class, [
                '--lang' => $lang,
                '--skip-cache' => $skipCache,
            ]);
            if ($extractCode !== ArtisanExitCode::SUCCESS) {
                $this->error('Échec de l’extraction des types item.');

                return ArtisanExitCode::FAILURE;
            }
        } else {
            $this->info('Étape 1/2 : ignorée (--no-files). Utilisation des fichiers data existants.');
        }

        $this->info('Étape 2/2 : exécution des seeders item-types ('.implode(', ', $keys).')…');
        foreach ($keys as $key) {
            $seederClass = self::ITEM_TYPE_SEEDERS[$key];
            $code = Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);
            $this->output->write(Artisan::output());
            if ($code !== 0) {
                $this->error("Échec du seeder {$seederClass}.");

                return ArtisanExitCode::FAILURE;
            }
        }

        $this->info('Terminé. Les types item sélectionnés sont à jour depuis l’API DofusDB.');

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return list<string> clés resource|consumable|item
     */
    private function resolveItemTypeSeederKeys(string $onlyOption): array
    {
        $onlyOption = strtolower(trim($onlyOption));
        if ($onlyOption === '' || $onlyOption === 'all') {
            return ['resource', 'consumable', 'item'];
        }

        $out = [];
        foreach (array_filter(array_map('trim', explode(',', $onlyOption))) as $token) {
            $t = $token === 'equipment' ? 'item' : $token;
            if (! isset(self::ITEM_TYPE_SEEDERS[$t])) {
                continue;
            }
            if (! in_array($t, $out, true)) {
                $out[] = $t;
            }
        }

        return $out;
    }
}
