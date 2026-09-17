<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Pricing;

/**
 * Prix automatique d’un équipement : bonus + 150 kamas / niveau + 200 kamas / rareté.
 *
 * @example
 * $price = $calculator->calculate(['strength' => 2], ['strength' => 1000], 8, 1);
 */
final class EquipmentPriceCalculator
{
    public const KAMAS_PER_LEVEL = 150;

    public const KAMAS_PER_RARITY = 200;

    /**
     * @param  array<string, int|float>  $bonus
     * @param  array<string, int|float|null>  $basePricePerUnit
     */
    public function calculate(array $bonus, array $basePricePerUnit, int $level, int $rarity): int
    {
        $sum = 0.0;
        foreach ($bonus as $key => $value) {
            $unit = $basePricePerUnit[$key] ?? null;
            if ($unit === null || ! is_numeric($unit) || ! is_numeric($value)) {
                continue;
            }
            $sum += (float) $value * (float) $unit;
        }

        $sum += self::KAMAS_PER_LEVEL * max(0, $level);
        $sum += self::KAMAS_PER_RARITY * max(0, $rarity);

        return (int) max(0, round($sum));
    }
}
