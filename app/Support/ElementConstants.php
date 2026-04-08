<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Constantes partagées pour les éléments (Spell, Capability).
 *
 * Stockage : masque 7 bits (voir {@see ElementBitmask}).
 */
final class ElementConstants
{
    /** @var array<int, string> @deprecated Utiliser ElementBitmask::PRIMARY_LABELS ou ElementBitmask::label() */
    public const ELEMENT = [];

    /** Index des primaires sélectionnables (0–6). */
    public const PRIMARIES = [0, 1, 2, 3, 4, 5, 6];

    /**
     * Mapping legacy capability element (string) → valeur int (masque ou ancien code ≤29 normalisé à la volée).
     *
     * @deprecated Étendre au besoin pour sagesse/vitalité textuelles
     */
    public const LEGACY_STRING_TO_INT = [
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 0,
        'neutral' => 0,
        'earth' => 1,
        'terre' => 1,
        'fire' => 2,
        'feu' => 2,
        'air' => 3,
        'water' => 4,
        'eau' => 4,
    ];

    public static function getLabel(int $value): ?string
    {
        $mask = ElementBitmask::normalize($value);
        if ($mask === 0) {
            return null;
        }

        return ElementBitmask::label($mask);
    }

    public static function isValid(int $value): bool
    {
        return ElementBitmask::isValidMask(ElementBitmask::normalize($value));
    }

    /**
     * Token couleur Tailwind pour un primaire seul (0–6).
     */
    public static function getColorToken(int $primaryIndex): string
    {
        $map = [
            0 => 'slate-500',
            1 => 'amber-700',
            2 => 'red-600',
            3 => 'emerald-600',
            4 => 'blue-600',
            5 => 'violet-500',
            6 => 'lime-600',
        ];

        return $map[$primaryIndex] ?? 'zinc-500';
    }

    /** @deprecated Utiliser getColorToken */
    public static function getDaisyColor(int $value): string
    {
        return self::getColorToken($value);
    }
}
