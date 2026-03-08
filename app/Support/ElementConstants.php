<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Constantes partagées pour les éléments (Spell, Capability).
 *
 * Valeur numérique unique par élément ou combinaison (0-29).
 * Source de vérité pour le backend.
 */
final class ElementConstants
{
    public const ELEMENT = [
        0 => 'Neutre',
        1 => 'Terre',
        2 => 'Feu',
        3 => 'Air',
        4 => 'Eau',
        5 => 'Neutre-Terre',
        6 => 'Neutre-Feu',
        7 => 'Neutre-Air',
        8 => 'Neutre-Eau',
        9 => 'Terre-Feu',
        10 => 'Terre-Air',
        11 => 'Terre-Eau',
        12 => 'Feu-Air',
        13 => 'Feu-Eau',
        14 => 'Air-Eau',
        15 => 'Neutre-Terre-Feu',
        16 => 'Neutre-Terre-Air',
        17 => 'Neutre-Terre-Eau',
        18 => 'Neutre-Feu-Air',
        19 => 'Neutre-Feu-Eau',
        20 => 'Neutre-Air-Eau',
        21 => 'Terre-Feu-Air',
        22 => 'Terre-Feu-Eau',
        23 => 'Terre-Air-Eau',
        24 => 'Feu-Air-Eau',
        25 => 'Neutre-Terre-Feu-Air',
        26 => 'Neutre-Terre-Feu-Eau',
        27 => 'Neutre-Terre-Air-Eau',
        28 => 'Neutre-Feu-Air-Eau',
        29 => 'Neutre-Terre-Feu-Air-Eau',
    ];

    /** Index des éléments primaires (0=Neutre, 1=Terre, 2=Feu, 3=Air, 4=Eau). */
    public const PRIMARIES = [0, 1, 2, 3, 4];

    /**
     * Mapping legacy capability element (string) → valeur int.
     * Utilisé pour migration et import legacy.
     */
    public const LEGACY_STRING_TO_INT = [
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 0,  // Chance → Neutre
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
        return self::ELEMENT[$value] ?? null;
    }

    public static function isValid(int $value): bool
    {
        return isset(self::ELEMENT[$value]);
    }

    /**
     * Token couleur Tailwind (ex: amber-700) pour affichage badge.
     */
    public static function getColorToken(int $value): string
    {
        $map = [
            0 => 'slate-500',   // Neutre
            1 => 'amber-700',   // Terre
            2 => 'red-600',     // Feu
            3 => 'emerald-600', // Air
            4 => 'blue-600',    // Eau
        ];

        return $map[$value] ?? 'zinc-500';
    }

    /** @deprecated Utiliser getColorToken */
    public static function getDaisyColor(int $value): string
    {
        return self::getColorToken($value);
    }
}
