<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Models\User;

/**
 * Crée des objets `draft` pour les cases vides (sans source Dofus, jamais `playable`).
 *
 * Identifiant stable : `ia-grid:{slot}:{voie}:{niveau}`. Relancer `--write` est idempotent.
 *
 * @example
 * $created = $filler->fillHoles($definition, $report->holeCells(), limit: 10);
 */
final class EquipmentGridHoleFiller
{
    /**
     * @param  list<EquipmentGridCell>  $holes
     * @return list<array{id: int, official_id: string, name: string, slot: string, voie: string, level: int}>
     */
    public function fillHoles(EquipmentGridDefinition $definition, array $holes, ?int $limit = null): array
    {
        $created = [];
        foreach ($holes as $cell) {
            if (! $cell->isHole) {
                continue;
            }
            if ($limit !== null && count($created) >= $limit) {
                break;
            }
            $row = $this->fillCell($definition, $cell);
            if ($row !== null) {
                $created[] = $row;
            }
        }

        return $created;
    }

    /**
     * @return array{id: int, official_id: string, name: string, slot: string, voie: string, level: int}|null
     */
    public function fillCell(EquipmentGridDefinition $definition, EquipmentGridCell $cell): ?array
    {
        $officialId = $definition->officialId($cell->slotKey, $cell->voieKey, $cell->level);
        $existing = Item::query()->where('official_id', $officialId)->first();
        if ($existing instanceof Item) {
            return null;
        }

        $slot = $definition->slot($cell->slotKey);
        $voie = $definition->voie($cell->voieKey);
        if ($slot === null || $voie === null) {
            return null;
        }

        $itemType = ItemType::query()
            ->where('dofusdb_type_id', $slot['default_dofusdb_type_id'])
            ->first();
        if (! $itemType instanceof ItemType) {
            throw new \RuntimeException(
                'Type d’objet introuvable pour le slot '.$cell->slotKey.' (dofusdb_type_id '.$slot['default_dofusdb_type_id'].').'
            );
        }

        $bonus = $this->templateBonus($definition, $slot['category'], $voie, $cell->level);
        $encoded = json_encode($bonus, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $name = trim($definition->namePrefix.' '.$slot['label'].' '.$voie['label'].' '.$cell->level);

        $item = new Item;
        $item->fill([
            'official_id' => $officialId,
            'dofusdb_id' => null,
            'name' => $name,
            'level' => (string) $cell->level,
            'description' => 'Objet de grille JDR (trou). Bonus techniques, à relire avant publication. Pas de source Dofus.',
            'effect' => $encoded,
            'bonus' => $encoded,
            'state' => EntityState::Draft->value,
            'auto_update' => false,
            'item_type_id' => $itemType->id,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'rarity' => 0,
            'dofus_version' => '3',
            'created_by' => null,
        ]);
        $item->save();

        return [
            'id' => (int) $item->id,
            'official_id' => $officialId,
            'name' => $name,
            'slot' => $cell->slotKey,
            'voie' => $cell->voieKey,
            'level' => $cell->level,
        ];
    }

    /**
     * @param  array{label: string, keys: list<string>, primary_key: string, damage_key: string, dofus_characteristic_ids: list<int>, dofus_element_ids: list<int>}  $voie
     * @return array<string, int>
     */
    public function templateBonus(EquipmentGridDefinition $definition, string $category, array $voie, int $level): array
    {
        $value = $definition->bandValue($level);
        if ($category === 'weapon') {
            return [$voie['damage_key'] => $value];
        }

        return [$voie['primary_key'] => $value];
    }
}
