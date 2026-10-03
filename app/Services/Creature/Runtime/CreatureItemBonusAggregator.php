<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Models\Entity\Item;
use App\Support\Entity\KrosmozItemBonusDecoder;
use Illuminate\Support\Collection;

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
    public function __construct(
        private readonly KrosmozItemBonusDecoder $decoder = new KrosmozItemBonusDecoder,
    ) {}

    /**
     * Somme par clé courte (ex. strength, athletics) — même convention que ItemEffectsToBonusConverter.
     *
     * @param  Collection<int, Item>|\Illuminate\Database\Eloquent\Collection<int, Item>  $items
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
            $decoded = $this->decodeItemBonuses($item);
            foreach ($decoded as $key => $val) {
                $k = (string) $key;
                $totals[$k] = ($totals[$k] ?? 0) + (int) round($val * $qty);
            }
        }

        return $totals;
    }

    /**
     * Détail par objet (pour décomposition UI).
     *
     * @param  Collection<int, Item>|\Illuminate\Database\Eloquent\Collection<int, Item>  $items
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
            $decoded = $this->decodeItemBonuses($item);
            if ($decoded === []) {
                continue;
            }
            $scaled = [];
            foreach ($decoded as $k => $v) {
                $scaled[(string) $k] = (int) round($v * $qty);
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
     * @param  Item|object{effect?: mixed, bonus?: mixed}  $item
     * @return array<string, float>
     */
    private function decodeItemBonuses(object $item): array
    {
        return $this->decoder->decode($item->effect ?? null, $item->bonus ?? null);
    }
}
