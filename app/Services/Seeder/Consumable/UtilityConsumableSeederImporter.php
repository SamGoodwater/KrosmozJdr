<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use App\Models\Entity\Consumable;
use App\Models\Type\ConsumableType;

/**
 * Importe les consommables utilitaires JDR (sans recette, prix custom).
 *
 * Idempotent. Upsert sur `dofusdb_id` ou `official_id`.
 *
 * @example $result = app(UtilityConsumableSeederImporter::class)->import();
 */
final class UtilityConsumableSeederImporter
{
    /**
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?UtilityConsumableCatalog $catalog = null): array
    {
        $catalog ??= UtilityConsumableCatalog::load();
        $created = [];
        $updated = [];
        $skipped = [];

        $this->ensureTypesPlayable($catalog);

        foreach ($catalog->entries() as $entry) {
            $typeId = $this->resolveTypeId($entry['type_dofus_id']);
            if ($typeId === null) {
                $skipped[] = $entry['name'].' : type '.$entry['type_dofus_id'].' introuvable';

                continue;
            }

            $consumable = $this->findConsumable($entry);
            $wasNew = $consumable === null;
            $consumable ??= new Consumable;

            $attributes = [
                'name' => $entry['name'],
                'level' => $entry['level'],
                'effect' => $entry['effect'],
                'recipe' => null,
                'rarity' => $entry['rarity'],
                'state' => Consumable::STATE_AUTO,
                'read_level' => 0,
                'write_level' => 3,
                'dofus_version' => '3',
                'auto_update' => false,
                'consumable_type_id' => $typeId,
                'created_by' => null,
                'price_calculated' => 0,
                'price_custom' => $entry['price'],
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
            $consumable->resources()->sync([]);

            $wasNew ? $created[] = $entry['name'] : $updated[] = $entry['name'];
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ];
    }

    private function ensureTypesPlayable(UtilityConsumableCatalog $catalog): void
    {
        foreach ($catalog->types() as $type) {
            ConsumableType::query()
                ->where('dofusdb_type_id', $type['dofusdb_type_id'])
                ->update([
                    'state' => ConsumableType::STATE_PLAYABLE,
                    'show_in_catalog' => true,
                ]);
        }
    }

    /**
     * @param  array{dofusdb_id: string|null, official_id: string|null}  $entry
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

    private function resolveTypeId(int $dofusTypeId): ?int
    {
        $id = ConsumableType::query()
            ->where('dofusdb_type_id', $dofusTypeId)
            ->value('id');

        return is_numeric($id) ? (int) $id : null;
    }
}
