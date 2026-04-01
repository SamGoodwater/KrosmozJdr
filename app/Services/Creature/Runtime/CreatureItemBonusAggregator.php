<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Models\Entity\Item;

/**
 * Agrège les bonus JSON des objets liés à une créature (quantités prises en compte).
 *
 * @example
 *   $agg = new CreatureItemBonusAggregator();
 *   $totals = $agg->aggregateTotals($creature->items);
 *   $lines = $agg->aggregatePerItemLines($creature->items);
 */
final class CreatureItemBonusAggregator
{
    /**
     * Somme par clé courte (ex. strength, athletics) — même convention que ItemEffectsToBonusConverter.
     *
     * @param  \Illuminate\Support\Collection<int, Item>|\Illuminate\Database\Eloquent\Collection<int, Item>  $items
     * @return array<string, int>
     */
    public function aggregateTotals($items): array
    {
        $totals = [];
        foreach ($items as $item) {
            $qty = (int) ($item->pivot->quantity ?? 1);
            if ($qty < 1) {
                $qty = 1;
            }
            $decoded = $this->decodeBonus($item->bonus);
            foreach ($decoded as $key => $val) {
                $k = (string) $key;
                $totals[$k] = ($totals[$k] ?? 0) + $val * $qty;
            }
        }

        return $totals;
    }

    /**
     * Détail par objet (pour décomposition UI).
     *
     * @param  \Illuminate\Support\Collection<int, Item>|\Illuminate\Database\Eloquent\Collection<int, Item>  $items
     * @return list<array{item_id: int, name: string, quantity: int, bonuses: array<string, int>}>
     */
    public function aggregatePerItemLines($items): array
    {
        $lines = [];
        foreach ($items as $item) {
            $qty = (int) ($item->pivot->quantity ?? 1);
            if ($qty < 1) {
                $qty = 1;
            }
            $decoded = $this->decodeBonus($item->bonus);
            if ($decoded === []) {
                continue;
            }
            $scaled = [];
            foreach ($decoded as $k => $v) {
                $scaled[(string) $k] = $v * $qty;
            }
            $lines[] = [
                'item_id' => (int) $item->id,
                'name' => (string) $item->name,
                'quantity' => $qty,
                'bonuses' => $scaled,
            ];
        }

        return $lines;
    }

    /**
     * @return array<string, int>
     */
    private function decodeBonus(?string $bonus): array
    {
        if ($bonus === null || trim($bonus) === '') {
            return [];
        }
        try {
            $decoded = json_decode($bonus, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }
        if (! is_array($decoded)) {
            return [];
        }
        $out = [];
        foreach ($decoded as $key => $value) {
            if (! is_string($key)) {
                continue;
            }
            if (! is_numeric($value)) {
                continue;
            }
            $out[$key] = (int) $value;
        }

        return $out;
    }
}
