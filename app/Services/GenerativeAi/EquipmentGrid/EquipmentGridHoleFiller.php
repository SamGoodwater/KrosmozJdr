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

        $bonus = $this->templateBonus(
            $definition,
            $cell->slotKey,
            $slot['category'],
            $voie,
            $cell->voieKey,
            $cell->level
        );
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
     * Réécrit l’`effect` des objets grille `draft`/`auto`/`raw` selon le slot (2.6.1).
     *
     * @return int Nombre d’objets mis à jour
     */
    public function refreshDraftGridBonuses(EquipmentGridDefinition $definition): int
    {
        $prefix = $definition->officialIdPrefix;
        $updated = 0;
        Item::query()
            ->where('official_id', 'like', $prefix.':%')
            ->whereIn('state', [
                EntityState::Draft->value,
                EntityState::Auto->value,
                EntityState::Raw->value,
            ])
            ->orderBy('id')
            ->each(function (Item $item) use ($definition, &$updated): void {
                $parts = explode(':', (string) $item->official_id);
                // ia-grid:{slot}:{voie}:{niveau}
                if (count($parts) < 4) {
                    return;
                }
                $slotKey = $parts[1];
                $voieKey = $parts[2];
                $level = (int) $parts[3];
                $slot = $definition->slot($slotKey);
                $voie = $definition->voie($voieKey);
                if ($slot === null || $voie === null || $level < 1) {
                    return;
                }
                $bonus = $this->templateBonus(
                    $definition,
                    $slotKey,
                    $slot['category'],
                    $voie,
                    $voieKey,
                    $level
                );
                $encoded = json_encode($bonus, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                if ($item->effect === $encoded) {
                    return;
                }
                $item->effect = $encoded;
                $item->save();
                $updated++;
            });

        return $updated;
    }

    /**
     * Bonus de trou alignés sur 2.6.1 (emplacement) et la voie (élément).
     *
     * @param  array{label: string, keys: list<string>, primary_key: string, damage_key: string, dofus_characteristic_ids: list<int>, dofus_element_ids: list<int>}  $voie
     * @return array<string, int>
     */
    public function templateBonus(
        EquipmentGridDefinition $definition,
        string $slotKey,
        string $category,
        array $voie,
        string $voieKey,
        int $level
    ): array {
        $value = $definition->bandValue($level);
        if ($category === 'weapon' || $slotKey === 'weapon') {
            return [$voie['damage_key'] => $value];
        }

        $key = match ($slotKey) {
            'cape' => $voie['primary_key'],
            'hat' => $voieKey === 'neutre' ? 'wisdom' : 'vitality',
            'amulet' => match ($voieKey) {
                'eau', 'air' => 'dodge_action_points',
                'neutre' => 'critical_hit',
                default => 'action_points',
            },
            'belt' => match ($voieKey) {
                'air' => 'dodge',
                'eau', 'neutre' => 'wakfu_recharge',
                default => 'tackle',
            },
            'boots' => match ($voieKey) {
                'eau', 'air', 'neutre' => 'dodge_movement_points',
                default => 'movement_points',
            },
            'ring' => match ($voieKey) {
                'feu', 'eau' => 'heal_bonus',
                'neutre' => 'summoning',
                default => 'range',
            },
            default => $voie['primary_key'],
        };

        $max = match ($key) {
            'action_points' => 5,
            'movement_points' => 2,
            'dodge_action_points', 'dodge_movement_points', 'wakfu_recharge', 'critical_hit' => 3,
            'strength', 'intelligence', 'chance', 'agility', 'vitality', 'wisdom' => 4,
            'heal_bonus' => 5,
            'summoning' => 5,
            'range' => 6,
            'tackle', 'dodge' => 10,
            default => $value,
        };

        return [$key => min($value, $max)];
    }
}
