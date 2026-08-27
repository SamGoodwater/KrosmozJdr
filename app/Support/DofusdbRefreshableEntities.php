<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Types d’entités JDR rafraîchissables depuis DofusDB (id local → alias pipeline).
 *
 * @example DofusdbRefreshableEntities::pipelineAlias('monsters') // 'monster'
 */
final class DofusdbRefreshableEntities
{
    /** @var array<string, string> type API pluriel → alias collect/orchestrateur */
    public const ALIASES = [
        'monsters' => 'monster',
        'breeds' => 'class',
        'resources' => 'resource',
        'items' => 'item',
        'consumables' => 'consumable',
        'panoplies' => 'panoply',
        'spells' => 'spell',
    ];

    /**
     * Champs à ne pas écraser en mode images seules (complété par le téléchargement d’images).
     *
     * @var list<string>
     */
    public const IMAGE_ONLY_EXCLUDE = [
        'name',
        'description',
        'level',
        'effect',
        'raw',
        'pa',
        'pm',
        'po',
        'po_min',
        'po_max',
        'weight',
        'price',
        'rarity',
        'official_id',
        'size',
        'is_boss',
        'boss_pa',
        'monster_race_id',
        'item_type_id',
        'resource_type_id',
        'consumable_type_id',
        'spell_type_id',
    ];

    public static function isRefreshable(string $entityType): bool
    {
        $normalized = EntityModelRegistry::normalizeType($entityType);

        return isset(self::ALIASES[$normalized]);
    }

    public static function pipelineAlias(string $entityType): ?string
    {
        $normalized = EntityModelRegistry::normalizeType($entityType);

        return self::ALIASES[$normalized] ?? null;
    }
}
