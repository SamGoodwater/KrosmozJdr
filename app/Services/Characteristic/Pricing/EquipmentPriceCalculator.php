<?php

namespace App\Services\Characteristic\Pricing;

/**
 * Calcule un prix indicatif d'équipement à partir des bonus normés.
 *
 * @example
 * $price = $calculator->calculate(['strength' => 2], ['strength' => 500], 8, 2);
 */
final class EquipmentPriceCalculator
{
    /**
     * @param  array<string, int|float>  $bonus
     * @param  array<string, int|float|null>  $basePricePerUnit
     */
    public function calculate(array $bonus, array $basePricePerUnit, int $level, int $powerIndex): int
    {
        $sum = 0.0;
        foreach ($bonus as $key => $value) {
            $unit = $basePricePerUnit[$key] ?? null;
            if ($unit === null || ! is_numeric($unit) || ! is_numeric($value)) {
                continue;
            }
            $sum += abs((float) $value) * (float) $unit;
        }

        if ($sum <= 0) {
            return 0;
        }

        $levelMultiplier = 1 + max(0, $level - 1) * 0.08;
        $powerMultiplier = 0.75 + max(0, min(4, $powerIndex)) * 0.25;

        return (int) max(1, round($sum * $levelMultiplier * $powerMultiplier));
    }
}
