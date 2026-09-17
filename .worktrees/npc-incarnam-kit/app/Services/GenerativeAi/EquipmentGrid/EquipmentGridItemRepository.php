<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\EquipmentGrid;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use Illuminate\Database\Eloquent\Collection;

/**
 * Charge les objets des slots de la grille depuis la base.
 *
 * @example
 * $items = $repository->forDefinition($definition);
 */
final class EquipmentGridItemRepository
{
    public function __construct(
        private readonly EquipmentBonusDecoder $decoder = new EquipmentBonusDecoder,
    ) {}

    /**
     * @return list<EquipmentGridItem>
     */
    public function forDefinition(EquipmentGridDefinition $definition): array
    {
        $typeIds = $definition->allDofusTypeIds();
        if ($typeIds === []) {
            return [];
        }

        /** @var Collection<int, Item> $rows */
        $rows = Item::query()
            ->with('itemType:id,dofusdb_type_id')
            ->whereNot('state', EntityState::Archived->value)
            ->whereHas('itemType', static function ($query) use ($typeIds): void {
                $query->whereIn('dofusdb_type_id', $typeIds);
            })
            ->get([
                'id',
                'name',
                'level',
                'state',
                'bonus',
                'effect',
                'item_type_id',
                'dofusdb_id',
                'official_id',
            ]);

        $out = [];
        foreach ($rows as $row) {
            $out[] = $this->map($row);
        }

        return $out;
    }

    public function map(Item $item): EquipmentGridItem
    {
        $type = $item->relationLoaded('itemType') ? $item->itemType : $item->itemType()->first();
        $level = is_numeric($item->level) ? (int) $item->level : 1;

        return new EquipmentGridItem(
            id: (int) $item->id,
            name: (string) $item->name,
            level: max(1, $level),
            state: (string) $item->state,
            itemTypeId: $item->item_type_id !== null ? (int) $item->item_type_id : null,
            dofusdbTypeId: $type instanceof ItemType && $type->dofusdb_type_id !== null
                ? (int) $type->dofusdb_type_id
                : null,
            dofusdbId: $item->dofusdb_id !== null && $item->dofusdb_id !== '' ? (string) $item->dofusdb_id : null,
            officialId: $item->official_id !== null && $item->official_id !== '' ? (string) $item->official_id : null,
            bonuses: $this->decoder->decode($item->effect, $item->bonus),
        );
    }
}
