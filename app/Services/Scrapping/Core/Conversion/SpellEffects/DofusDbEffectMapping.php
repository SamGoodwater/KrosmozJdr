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
    private const ELEMENT_ID_TO_KEY = [
        0 => 'neutre',
        1 => 'feu',
        2 => 'eau',
        3 => 'terre',
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
