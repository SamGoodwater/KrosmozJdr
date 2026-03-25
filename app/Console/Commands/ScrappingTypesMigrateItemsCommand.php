<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use Illuminate\Console\Command;

/**
 * Migre les item_type_id des équipements existants vers les superTypes.
 *
 * Avant : item_types stockait des typeIds (sous-types : Arc, Baguette, Marteau).
 * Après : item_types stocke des superTypeIds (types : Amulette, Arme, Bouclier).
 *
 * Cette commande met à jour les items existants pour pointer vers le bon ItemType (superTypeId).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PROPRIETES_ITEMS_RESOURCES_CONSUMABLES.md
 */
class ScrappingTypesMigrateItemsCommand extends Command
{
    protected $signature = 'scrapping:types:migrate-items
                            {--dry-run : Afficher les changements sans les appliquer}
                            {--skip-cache : Ignorer le cache du catalogue}';

    protected $description = 'Migre les item_type_id des équipements vers les superTypes (type au lieu de sous-type)';

    public function __construct(
        private readonly DofusDbItemTypesCatalogService $catalogService,
        private readonly DofusDbItemSuperTypeMappingService $mappingService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $skipCache = (bool) $this->option('skip-cache');

        $equipmentSuperTypeIds = [1, 2, 3, 4, 5, 7, 10, 11, 12, 13];
        $superTypeIdsSet = array_flip($equipmentSuperTypeIds);

        $items = Item::whereNotNull('item_type_id')->with('itemType')->get();
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($items as $item) {
            $itemType = $item->itemType;
            if ($itemType === null) {
                $this->warn("Item #{$item->id} : item_type_id {$item->item_type_id} orphelin (type introuvable).");
                $errors++;

                continue;
            }

            $dofusdbTypeId = (int) $itemType->dofusdb_type_id;
            if ($dofusdbTypeId <= 0) {
                $skipped++;

                continue;
            }

            if (isset($superTypeIdsSet[$dofusdbTypeId])) {
                $skipped++;

                continue;
            }

            $superTypeId = $this->catalogService->getSuperTypeIdForTypeId($dofusdbTypeId, 'fr', $skipCache);
            if ($superTypeId === null) {
                $this->warn("Item #{$item->id} : typeId {$dofusdbTypeId} inconnu dans le catalogue.");
                $errors++;

                continue;
            }

            $category = $this->mappingService->getCategoryForSuperTypeId($superTypeId);
            if ($category !== 'equipment') {
                $this->warn("Item #{$item->id} : typeId {$dofusdbTypeId} → superTypeId {$superTypeId} (catégorie {$category}), ignoré.");
                $skipped++;

                continue;
            }

            $targetType = ItemType::where('dofusdb_type_id', $superTypeId)->first();
            if ($targetType === null) {
                $this->error("Item #{$item->id} : ItemType avec superTypeId {$superTypeId} introuvable. Exécutez scrapping:types:seed.");
                $errors++;

                continue;
            }

            if ($targetType->id === $item->item_type_id) {
                $skipped++;

                continue;
            }

            if ($dryRun) {
                $this->line("Item #{$item->id} ({$item->name}) : {$itemType->name} (typeId {$dofusdbTypeId}) → {$targetType->name} (superTypeId {$superTypeId})");
                $updated++;

                continue;
            }

            $item->item_type_id = $targetType->id;
            $item->save();
            $updated++;
        }

        $this->info("Terminé : {$updated} mis à jour, {$skipped} ignorés, {$errors} erreurs.");
        if ($dryRun && $updated > 0) {
            $this->info('Relancez sans --dry-run pour appliquer les changements.');
        }

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
