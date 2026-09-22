<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use App\Services\Characteristic\Pricing\EntityPriceRecalculator;

/**
 * Importe l’échelle de soins hors combat : types, prix des ressources, fiches, recettes.
 *
 * Idempotent. Recette = `recipe_quantity` × ressource de palier (prix = consommable / 10).
 * Sans ressource scrapée, le consommable est tout de même créé avec `price_custom` au barème.
 *
 * @example $result = app(HealingConsumableSeederImporter::class)->import();
 */
final class HealingConsumableSeederImporter
{
    public function __construct(
        private readonly EntityPriceRecalculator $priceRecalculator,
    ) {}

    /**
     * @return array{
     *     resources: int,
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?HealingConsumableCatalog $catalog = null): array
    {
        $catalog ??= HealingConsumableCatalog::load();
        $created = [];
        $updated = [];
        $skipped = [];

        $this->ensureTypesPlayable($catalog);
        $resourcesPatched = $this->patchResources($catalog);

        foreach ($catalog->entries() as $entry) {
            $typeId = $this->resolveTypeId($catalog, $entry['kind']);
            if ($typeId === null) {
                $skipped[] = $entry['name'].' : type '.$entry['kind'].' introuvable';

                continue;
            }

            $consumable = $this->findConsumable($entry);
            $wasNew = $consumable === null;
            $consumable ??= new Consumable;

            $attributes = [
                'name' => $entry['name'],
                'level' => $entry['level'],
                'effect' => HealingConsumableCatalog::effectText($entry['heal']),
                'bonus' => HealingConsumableCatalog::bonusJson($entry['heal']),
                'recipe' => null,
                'rarity' => 0,
                'state' => Consumable::STATE_AUTO,
                'read_level' => 0,
                'write_level' => 3,
                'dofus_version' => '3',
                'auto_update' => false,
                'consumable_type_id' => $typeId,
                'created_by' => null,
            ];
            if ($entry['dofusdb_id'] !== null) {
                $attributes['dofusdb_id'] = $entry['dofusdb_id'];
            }
            if ($entry['official_id'] !== null) {
                $attributes['official_id'] = $entry['official_id'];
            }
            if ($entry['description'] !== null) {
                $attributes['description'] = $entry['description'];
            }

            $consumable->fill($attributes);
            $consumable->save();

            $resourceId = Resource::query()
                ->where('dofusdb_id', $entry['resource_dofusdb_id'])
                ->value('id');

            if (is_numeric($resourceId)) {
                $consumable->resources()->sync([
                    (int) $resourceId => ['quantity' => $catalog->recipeQuantity()],
                ]);
                $this->priceRecalculator->recalculateConsumable($consumable->fresh(['resources']), true);
            } else {
                $consumable->resources()->sync([]);
                $consumable->price_calculated = 0;
                $consumable->price_custom = $entry['price'];
                $consumable->save();
                $skipped[] = $entry['name'].' : ressource '.$entry['resource_name'].' absente, prix manuel '.$entry['price'];
            }

            $wasNew ? $created[] = $entry['name'] : $updated[] = $entry['name'];
        }

        return [
            'resources' => $resourcesPatched,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    private function ensureTypesPlayable(HealingConsumableCatalog $catalog): void
    {
        foreach ($catalog->kinds() as $kind) {
            ConsumableType::query()
                ->where('dofusdb_type_id', $kind['consumable_type_dofus_id'])
                ->update([
                    'state' => ConsumableType::STATE_PLAYABLE,
                    'show_in_catalog' => true,
                ]);
        }
    }

    /**
     * Prix = barème / 10, jouable, gelé vis-à-vis du scrapping.
     */
    private function patchResources(HealingConsumableCatalog $catalog): int
    {
        $patched = 0;
        $seen = [];

        foreach ($catalog->entries() as $entry) {
            $dofusdbId = $entry['resource_dofusdb_id'];
            if (isset($seen[$dofusdbId])) {
                continue;
            }
            $seen[$dofusdbId] = true;

            $resource = Resource::query()->where('dofusdb_id', $dofusdbId)->first();
            if ($resource === null) {
                continue;
            }

            $resource->price = (string) $entry['resource_price'];
            $resource->state = Resource::STATE_PLAYABLE;
            $resource->auto_update = false;
            $resource->save();
            $patched++;
        }

        return $patched;
    }

    /**
     * @param  array{kind: string, dofusdb_id: string|null, official_id: string|null}  $entry
     */
    private function findConsumable(array $entry): ?Consumable
    {
        if ($entry['dofusdb_id'] !== null) {
            $byDofus = Consumable::query()->where('dofusdb_id', $entry['dofusdb_id'])->first();
            if ($byDofus !== null) {
                return $byDofus;
            }
        }
        if ($entry['official_id'] !== null) {
            return Consumable::query()->where('official_id', $entry['official_id'])->first();
        }

        return null;
    }

    private function resolveTypeId(HealingConsumableCatalog $catalog, string $kind): ?int
    {
        $meta = $catalog->kinds()[$kind] ?? null;
        if ($meta === null) {
            return null;
        }

        $id = ConsumableType::query()
            ->where('dofusdb_type_id', $meta['consumable_type_dofus_id'])
            ->value('id');
        if (is_numeric($id)) {
            return (int) $id;
        }

        $id = ConsumableType::query()
            ->where('name', $meta['consumable_type_name'])
            ->value('id');

        return is_numeric($id) ? (int) $id : null;
    }
}
