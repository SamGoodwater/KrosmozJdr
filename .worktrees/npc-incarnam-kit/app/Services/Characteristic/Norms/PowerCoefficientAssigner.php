<?php

namespace App\Services\Characteristic\Norms;

/**
 * Assigne une puissance reproductible entre très faible et très forte.
 *
 * @example
 * $index = (new PowerCoefficientAssigner())->assign('item:123');
 */
final class PowerCoefficientAssigner
{
    /**
     * @param  list<int>  $weights  Poids pour very_weak, weak, neutral, strong, very_strong.
     */
    public function assign(string $seed, array $weights = [10, 25, 40, 20, 5]): int
    {
        $weights = array_values(array_map(static fn ($v) => max(0, (int) $v), $weights));
        $total = array_sum($weights);
        if ($total <= 0) {
            return NormsResolver::NEUTRAL_INDEX;
        }

        $hash = crc32($seed);
        $roll = $hash % $total;
        $cursor = 0;
        foreach ($weights as $index => $weight) {
            $cursor += $weight;
            if ($roll < $cursor) {
                return min($index, count(NormsResolver::POWER_LEVELS) - 1);
            }
        }

        return NormsResolver::NEUTRAL_INDEX;
    }

    public function fromRarity(?int $rarity): int
    {
        return match (true) {
            $rarity === null => NormsResolver::NEUTRAL_INDEX,
            $rarity <= 1 => 1,
            $rarity === 2 => 2,
            $rarity === 3 => 3,
            default => 4,
        };
    }
}
