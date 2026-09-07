<?php

declare(strict_types=1);

namespace App\Support\Creature;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * Tailles de créature (monstre et PNJ) : 0 Minuscule … 5 Gigantesque.
 *
 * @example CreatureSize::label(2) // 'Moyen'
 */
final class CreatureSize
{
    public const MINUSCULE = 0;

    public const PETIT = 1;

    public const MOYEN = 2;

    public const GRAND = 3;

    public const COLOSSAL = 4;

    public const GIGANTESQUE = 5;

    /** @var array<int, string> */
    public const LABELS = [
        self::MINUSCULE => 'Minuscule',
        self::PETIT => 'Petit',
        self::MOYEN => 'Moyen',
        self::GRAND => 'Grand',
        self::COLOSSAL => 'Colossal',
        self::GIGANTESQUE => 'Gigantesque',
    ];

    /**
     * @return list<int>
     */
    public static function values(): array
    {
        return array_keys(self::LABELS);
    }

    public static function label(int $size): string
    {
        return self::LABELS[$size] ?? (string) $size;
    }

    public static function rule(): In
    {
        return Rule::in(self::values());
    }

    /**
     * Convertit une ancienne valeur libre (string « Moyen », « 1m75 », entier) en 0–5.
     */
    public static function fromLegacy(mixed $value): int
    {
        if ($value === null || $value === '') {
            return self::MOYEN;
        }
        if (is_numeric($value)) {
            $n = (int) $value;

            return max(self::MINUSCULE, min(self::GIGANTESQUE, $n));
        }

        $normalized = mb_strtolower(trim((string) $value));
        $byLabel = [];
        foreach (self::LABELS as $int => $label) {
            $byLabel[mb_strtolower($label)] = $int;
        }

        return $byLabel[$normalized] ?? self::MOYEN;
    }
}
