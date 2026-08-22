<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;

/**
 * Résumé panoplie embarqué dans les payloads d’équipement (vues + table).
 */
final class ItemPanoplyPayload
{
    /**
     * Relations à eager-loader pour {@see fromItem()}.
     *
     * @return list<string>
     *
     * @example
     * Item::query()->with(ItemPanoplyPayload::eagerLoad());
     */
    public static function eagerLoad(): array
    {
        return ['panoplies.items'];
    }

    /**
     * @return list<array<string, mixed>>
     *
     * @example
     * ItemPanoplyPayload::fromItem($item);
     */
    public static function fromItem(Item $item): array
    {
        if (! $item->relationLoaded('panoplies')) {
            return [];
        }

        return $item->panoplies
            ->map(static fn (Panoply $panoply) => self::fromPanoply($panoply))
            ->values()
            ->all();
    }

    /**
     * @return array{id: int, name: string, bonus: mixed, items: list<array<string, mixed>>}
     *
     * @example
     * ItemPanoplyPayload::fromPanoply($panoply);
     */
    public static function fromPanoply(Panoply $panoply): array
    {
        $items = $panoply->relationLoaded('items') ? $panoply->items : collect();

        return [
            'id' => (int) $panoply->id,
            'name' => (string) ($panoply->name ?? ''),
            'bonus' => $panoply->bonus,
            'items' => $items->map(static function ($item): array {
                return [
                    'id' => (int) $item->id,
                    'name' => (string) ($item->name ?? ''),
                    'image' => $item->image,
                    'level' => $item->level,
                ];
            })->values()->all(),
        ];
    }
}
