<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Encodage élément sort / capability : masque 7 bits (bit i = primaire i actif).
 *
 * Primaires : 0 Neutre, 1 Terre, 2 Feu, 3 Air, 4 Eau, 5 Sagesse, 6 Vitalité.
 * Ancien schéma 0–29 (combinaisons sans Sagesse/Vitalité) est migré vers ce masque.
 *
 * @see resources/js/Utils/Entity/Elements.js
 */
final class ElementBitmask
{
    public const MAX_MASK = 127;

    /** @var array<int, string> */
    public const PRIMARY_LABELS = [
        0 => 'Neutre',
        1 => 'Terre',
        2 => 'Feu',
        3 => 'Air',
        4 => 'Eau',
        5 => 'Sagesse',
        6 => 'Vitalité',
    ];

    /**
     * Ancien code 0–29 → liste d’indices primaires (0–4 uniquement), identique à l’historique frontend.
     *
     * @var array<int, list<int>>
     */
    private const LEGACY_CODE_TO_PRIMARIES = [
        0 => [0],
        1 => [1],
        2 => [2],
        3 => [3],
        4 => [4],
        5 => [0, 1],
        6 => [0, 2],
        7 => [0, 3],
        8 => [0, 4],
        9 => [1, 2],
        10 => [1, 3],
        11 => [1, 4],
        12 => [2, 3],
        13 => [2, 4],
        14 => [3, 4],
        15 => [0, 1, 2],
        16 => [0, 1, 3],
        17 => [0, 1, 4],
        18 => [0, 2, 3],
        19 => [0, 2, 4],
        20 => [0, 3, 4],
        21 => [1, 2, 3],
        22 => [1, 2, 4],
        23 => [1, 3, 4],
        24 => [2, 3, 4],
        25 => [0, 1, 2, 3],
        26 => [0, 1, 2, 4],
        27 => [0, 1, 3, 4],
        28 => [0, 2, 3, 4],
        29 => [0, 1, 2, 3, 4],
    ];

    /** @return list<int> indices 0–6 triés */
    public static function toPrimaries(int $mask): array
    {
        $mask &= self::MAX_MASK;
        $out = [];
        for ($i = 0; $i <= 6; $i++) {
            if ($mask & (1 << $i)) {
                $out[] = $i;
            }
        }

        return $out;
    }

    /** @param  list<int>  $indices  indices 0–6 */
    public static function fromPrimaries(array $indices): int
    {
        $m = 0;
        foreach ($indices as $i) {
            $i = (int) $i;
            if ($i >= 0 && $i <= 6) {
                $m |= (1 << $i);
            }
        }

        return $m & self::MAX_MASK;
    }

    public static function legacyCodeToMask(int $legacyCode): int
    {
        if ($legacyCode < 0 || $legacyCode > 29) {
            return 0;
        }
        $primaries = self::LEGACY_CODE_TO_PRIMARIES[$legacyCode] ?? [0];

        return self::fromPrimaries($primaries);
    }

    /**
     * Normalise une valeur stockée : 0–29 → masque migré ; sinon tronque à 7 bits.
     */
    public static function normalize(int $value): int
    {
        if ($value >= 0 && $value <= 29) {
            return self::legacyCodeToMask($value);
        }

        return $value & self::MAX_MASK;
    }

    public static function label(int $mask): string
    {
        $mask &= self::MAX_MASK;
        $primaries = self::toPrimaries($mask);
        if ($primaries === []) {
            return '—';
        }

        $parts = [];
        foreach ($primaries as $i) {
            $parts[] = self::PRIMARY_LABELS[$i] ?? '?';
        }

        return implode('-', $parts);
    }

    /**
     * Options filtre tableau (valeur masque 1–127).
     *
     * @return list<array{value: string, label: string}>
     */
    public static function allFilterOptions(): array
    {
        $out = [];
        for ($m = 1; $m <= self::MAX_MASK; $m++) {
            $out[] = [
                'value' => (string) $m,
                'label' => self::label($m),
            ];
        }

        return $out;
    }

    public static function isValidMask(int $mask): bool
    {
        return $mask >= 0 && $mask <= self::MAX_MASK;
    }
}
