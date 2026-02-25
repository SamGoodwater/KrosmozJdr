<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use Illuminate\Console\Command;

/**
 * Extrait les types d'items DofusDB (catalogue + item-super-types.json)
 * vers database/seeders/data/ (resource_types.php, consumable_types.php, item_types.php).
 *
 * Phase 3 du plan types item BDD / seeders. À lancer une fois pour initialiser les fichiers,
 * puis utiliser db:export-seeder-data --item-types après réglages en UI.
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_TYPES_ITEM_BDD_SEEDER.md
 */
class ExtractItemTypesCommand extends Command
{
    protected $signature = 'scrapping:extract-item-types
                            {--lang=fr : Langue du catalogue DofusDB}
                            {--skip-cache : Ignorer le cache du catalogue}';

    protected $description = 'Extrait les types item DofusDB par catégorie (resource/consumable/equipment) vers database/seeders/data/';

    public function __construct(
        private readonly DofusDbItemTypesCatalogService $catalogService,
        private readonly DofusDbItemSuperTypeMappingService $mappingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');

        $this->info('Chargement du catalogue DofusDB (item-types)…');
        $catalog = $this->catalogService->getCatalog($lang, $skipCache);
        $excludedTypeIds = array_flip($this->mappingService->getExcludedTypeIds());

        $resourceTypes = [];
        $consumableTypes = [];
        $itemTypes = [];

        foreach ($catalog['superTypes'] ?? [] as $st) {
            $superTypeId = (int) ($st['id'] ?? 0);
            $category = $this->mappingService->getCategoryForSuperTypeId($superTypeId);
            if ($category === null) {
                continue;
            }
            foreach ($st['types'] ?? [] as $t) {
                $typeId = (int) ($t['id'] ?? 0);
                if ($typeId <= 0 || isset($excludedTypeIds[$typeId])) {
                    continue;
                }
                $name = $this->catalogService->stripDofusdbSuffix($t['name'] ?? null) ?? 'Type ' . $typeId;
                $row = [
                    'dofusdb_type_id' => $typeId,
                    'name' => $name,
                    'decision' => 'pending',
                    'state' => 'draft',
                ];
                if ($category === 'resource') {
                    $resourceTypes[] = $row;
                } elseif ($category === 'consumable') {
                    $consumableTypes[] = $row;
                } else {
                    $itemTypes[] = $row;
                }
            }
        }

        usort($resourceTypes, fn ($a, $b) => $a['dofusdb_type_id'] <=> $b['dofusdb_type_id']);
        usort($consumableTypes, fn ($a, $b) => $a['dofusdb_type_id'] <=> $b['dofusdb_type_id']);
        usort($itemTypes, fn ($a, $b) => $a['dofusdb_type_id'] <=> $b['dofusdb_type_id']);

        $dir = database_path('seeders/data');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->writeFile($dir . '/resource_types.php', $resourceTypes, 'resource_types', 'Ressources (superType 9)');
        $this->writeFile($dir . '/consumable_types.php', $consumableTypes, 'consumable_types', 'Consommables (superTypes 6, 70)');
        $this->writeFile($dir . '/item_types.php', $itemTypes, 'item_types', 'Équipements (hors resource/consumable exclus)');

        $this->info('Extraction terminée : ' . count($resourceTypes) . ' ressources, ' . count($consumableTypes) . ' consommables, ' . count($itemTypes) . ' équipements.');

        return self::SUCCESS;
    }

    /**
     * @param list<array{dofusdb_type_id:int,name:string,decision:string,state:string}> $data
     */
    private function writeFile(string $path, array $data, string $label, string $comment): void
    {
        $content = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * {$label} – {$comment}.\n"
            . " * Généré par : php artisan scrapping:extract-item-types\n"
            . " * Régénéré depuis la BDD par : php artisan db:export-seeder-data --item-types\n */\n\nreturn "
            . var_export($data, true) . ";\n";
        file_put_contents($path, $content);
        $this->line('  → ' . $path . ' (' . count($data) . ' entrées)');
    }
}
