<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

/**
 * Mapping effectId DofusDB vers sous-effet KrosmozJDR.
 * Utilise par SpellEffectsConversionService.
 *
 * @see docs/50-Fonctionnalités/Scrapping/DOFUSDB_EFFECTS_CONVERSION.md
 */
final class DofusDbEffectMapping
{
    /** Slug du sous-effet de repli pour les effectId non mappés (valeur seule, pas de caractéristique). */
    public const SUB_EFFECT_SLUG_OTHER = 'autre';

    private const ELEMENT_ID_TO_KEY = [
        0 => 'neutral',
        1 => 'fire',
        2 => 'water',
        3 => 'earth',
        4 => 'air',
    ];

    /** effectId => [sub_effect_slug, characteristic_source: 'element'|'none'] */
    private const EFFECT_ID_TO_SUB_EFFECT = [
        96 => ['frapper', 'element'],
        97 => ['frapper', 'element'],
        98 => ['frapper', 'element'],
        99 => ['frapper', 'element'],
        100 => ['frapper', 'element'],
    ];

    public static function getSubEffectForEffectId(int $effectId): ?array
    {
        return self::EFFECT_ID_TO_SUB_EFFECT[$effectId] ?? null;
    }

    public static function elementIdToCharacteristicKey(?int $elementId): ?string
    {
        if ($elementId === null) {
            return null;
        }
        return self::ELEMENT_ID_TO_KEY[$elementId] ?? null;
    }
}
