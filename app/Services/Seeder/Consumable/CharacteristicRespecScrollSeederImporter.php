<?php

declare(strict_types=1);

namespace App\Services\Seeder\Consumable;

use App\Models\Entity\Consumable;
use App\Models\Type\ConsumableType;

/**
 * Importe les parchemins de caractéristique JDR (respec, sans recette, prix custom).
 *
 * Idempotent. Upsert sur `dofusdb_id`. Type 76 passé en `playable`.
 *
 * @example $result = app(CharacteristicRespecScrollSeederImporter::class)->import();
 */
final class CharacteristicRespecScrollSeederImporter
{
    /**
     * @return array{
     *     created: list<string>,
     *     updated: list<string>,
     *     skipped: list<string>
     * }
     */
    public function import(?CharacteristicRespecScrollCatalog $catalog = null): array
    {
        $catalog ??= CharacteristicRespecScrollCatalog::load();
        $created = [];
        $updated = [];
        $skipped = [];

        $this->ensureTypePlayable($catalog);
        $typeId = $this->resolveTypeId($catalog);
        if ($typeId === null) {
            $skipped[] = 'Type '.$catalog->consumableTypeName().' introuvable';

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        }

        foreach ($catalog->entries() as $entry) {
            $consumable = Consumable::query()
                ->where('dofusdb_id', $entry['dofusdb_id'])
                ->first();
            $wasNew = $consumable === null;
            $consumable ??= new Consumable;

            $consumable->fill([
                'name' => $entry['name'],
                'level' => $entry['level'],
                'effect' => CharacteristicRespecScrollCatalog::effectText($entry['points'], $entry['of']),
                'recipe' => null,
                'rarity' => $entry['rarity'],
                'state' => Consumable::STATE_AUTO,
                'read_level' => 0,
                'write_level' => 3,
                'dofus_version' => '3',
                'auto_update' => false,
                'consumable_type_id' => $typeId,
                'created_by' => null,
                'dofusdb_id' => $entry['dofusdb_id'],
                'price_calculated' => 0,
                'price_custom' => $entry['price'],
            ]);
            if ($entry['description'] !== null) {
                $consumable->description = $entry['description'];
            }
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

    private function ensureTypePlayable(CharacteristicRespecScrollCatalog $catalog): void
    {
        ConsumableType::query()
            ->where('dofusdb_type_id', $catalog->consumableTypeDofusId())
            ->update([
                'state' => ConsumableType::STATE_PLAYABLE,
                'show_in_catalog' => true,
            ]);
    }

    private function resolveTypeId(CharacteristicRespecScrollCatalog $catalog): ?int
    {
        $id = ConsumableType::query()
            ->where('dofusdb_type_id', $catalog->consumableTypeDofusId())
            ->value('id');
        if (is_numeric($id)) {
            return (int) $id;
        }

        $id = ConsumableType::query()
            ->where('name', $catalog->consumableTypeName())
            ->value('id');

        return is_numeric($id) ? (int) $id : null;
    }
}
