<?php

declare(strict_types=1);

namespace App\Support\Entity;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;

/**
 * Résumé panoplie embarqué dans les payloads d’équipement (vues + table).
 *
 * Ne retient que les panoplies et pièces `view` pour le visiteur : un objet
 * jouable ne doit pas exposer un set brouillon ni ses autres pièces cachées.
 */
final class ItemPanoplyPayload
{
    /**
     * Relations à eager-loader pour {@see fromItem()}, bornées au visiteur.
     *
     * @return array<string, \Closure(Relation): void>
     *
     * @example
     * Item::query()->with(ItemPanoplyPayload::eagerLoad($request->user()));
     */
    public static function eagerLoad(?User $user = null): array
    {
        return [
            'panoplies' => static function ($query) use ($user): void {
                $query->visibleToUser($user);
            },
            'panoplies.items' => static function ($query) use ($user): void {
                $query->visibleToUser($user);
            },
        ];
    }

    /**
     * @return list<array<string, mixed>>
     *
     * @example
     * ItemPanoplyPayload::fromItem($item, $request->user());
     */
    public static function fromItem(Item $item, ?User $user = null): array
    {
        if (! $item->relationLoaded('panoplies')) {
            return [];
        }

        return $item->panoplies
            ->filter(static fn (Panoply $panoply): bool => Gate::forUser($user)->allows('view', $panoply))
            ->map(static fn (Panoply $panoply): array => self::fromPanoply($panoply, $user))
            ->values()
            ->all();
    }

    /**
     * @return array{id: int, name: string, bonus: mixed, items: list<array<string, mixed>>}
     *
     * @example
     * ItemPanoplyPayload::fromPanoply($panoply, $request->user());
     */
    public static function fromPanoply(Panoply $panoply, ?User $user = null): array
    {
        $items = $panoply->relationLoaded('items') ? $panoply->items : collect();

        return [
            'id' => (int) $panoply->id,
            'name' => (string) ($panoply->name ?? ''),
            'bonus' => $panoply->bonus,
            'items' => $items
                ->filter(static fn ($item): bool => $item instanceof Item && Gate::forUser($user)->allows('view', $item))
                ->map(static function ($item): array {
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
