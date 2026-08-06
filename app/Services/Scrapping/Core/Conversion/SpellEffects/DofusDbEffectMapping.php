<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Support\DofusDbElementId;

/**
 * Mapping effectId DofusDB vers sous-effet KrosmozJDR (constante PHP).
 *
 * @deprecated Phase 2 : préférer la table dofusdb_effect_mappings et DofusdbEffectMappingService.
 *             Conservé comme fallback lorsque la BDD est vide (DofusdbEffectMappingService délègue ici).
 * @see docs/features/effects/README.md
 * @see docs/features/scrapping/README.md
 */
final class DofusDbEffectMapping
{
    /** Slug du sous-effet de repli pour les effectId non mappés (valeur seule, pas de caractéristique). */
    public const SUB_EFFECT_SLUG_OTHER = 'autre';

    /** effectId => [sub_effect_slug, characteristic_source: 'element'|'none'] */
    private const EFFECT_ID_TO_SUB_EFFECT = [
        96 => ['frapper', 'element'],
        97 => ['frapper', 'element'],
        98 => ['frapper', 'element'],
        99 => ['frapper', 'element'],
        100 => ['frapper', 'element'],
        1020 => ['protéger', 'none'],
        1039 => ['protéger', 'none'],
        1040 => ['protéger', 'none'],
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

        return DofusDbElementId::toKrosmozSlug($elementId);
    }
}
