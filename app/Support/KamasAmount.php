<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Parse un montant en kamas (entier, plancher 0) depuis un entier, un flottant ou une chaîne.
 *
 * @example KamasAmount::parse('1 200 kamas') === 1200
 */
final class KamasAmount
{
    public static function parse(mixed $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_int($value)) {
            return max(0, $value);
        }

        if (is_float($value)) {
            return (int) max(0, round($value));
        }

        $digits = preg_replace('/[^\d\-]/', '', (string) $value);
        if (! is_string($digits) || $digits === '' || $digits === '-') {
            return 0;
        }

        return max(0, (int) $digits);
    }
}
