<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Encodage élément sort / capability : masque 7 bits (bit i = primaire i actif).
 *
 * Primaires : 0 Neutre, 1 Terre, 2 Feu, 3 Air, 4 Eau, 5 Sagesse, 6 Vitalité.
 * Stockage : masque 7 bits (Air = 8, Eau = 16). Ne plus interpréter 0–29 comme l’ancien code combinaisons.
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

    /** @var array<string, int> */
    public const SLUG_TO_PRIMARY = [
        'neutral' => 0,
        'earth' => 1,
        'terre' => 1,
        'fire' => 2,
        'feu' => 2,
        'air' => 3,
        'water' => 4,
        'eau' => 4,
        'wisdom' => 5,
        'sagesse' => 5,
        'element_wisdom' => 5,
        'vitality' => 6,
        'vitalite' => 6,
        'element_vitality' => 6,
        'fixed_damage_neutral_spell' => 0,
        'fixed_damage_earth_spell' => 1,
        'fixed_damage_fire_spell' => 2,
        'fixed_damage_air_spell' => 3,
        'fixed_damage_water_spell' => 4,
        'fixed_damage_sagesse_spell' => 5,
        'fixed_damage_vitalite_spell' => 6,
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
     * Indice primaire (0–6) depuis un slug d’élément ou une clé de dégâts fixes.
     */
    public static function primaryFromSlug(string $slug): ?int
    {
        $key = strtolower(trim($slug));
        if ($key === '' || ! array_key_exists($key, self::SLUG_TO_PRIMARY)) {
            return null;
        }

        return self::SLUG_TO_PRIMARY[$key];
    }

    /**
     * Masque 7 bits depuis un slug (`air` → 8).
     *
     * @example ElementBitmask::fromSlug('air'); // 8
     */
    public static function fromSlug(string $slug): ?int
    {
        $primary = self::primaryFromSlug($slug);

        return $primary === null ? null : self::fromPrimaries([$primary]);
    }

    /**
     * Normalise une valeur stockée : masque 7 bits (plus de conversion 0–29).
     */
    public static function normalize(int $value): int
    {
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
