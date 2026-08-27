<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Types visibles par défaut dans les catalogues objets / ressources / consommables.
 *
 * @description
 * Source unique pour le backfill `show_in_catalog` (migration + seeders).
 * Ne pas confondre avec `decision` (inclus dans scrap / maj de masse).
 */
final class CatalogTypeVisibility
{
    /**
     * Ids DofusDB d’équipements cochés par défaut (emplacements de jeu, pas apparats).
     *
     * @var list<int>
     */
    public const ITEM_DOFUS_IDS = [
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 16, 17, 18, 19, 20, 21, 23, 82, 151, 271,
    ];

    /**
     * Ids DofusDB de ressources cochés par défaut (hors quêtes / souvenirs / essences).
     *
     * @var list<int>
     */
    public const RESOURCE_DOFUS_IDS = [
        34, 35, 36, 38, 39, 40, 41, 46, 47, 48, 50, 51, 53, 54, 55, 56, 57, 58, 59, 60, 61, 63, 65, 66, 68,
        70, 71, 78, 95, 96, 98, 103, 104, 105, 106, 107, 108, 109, 110, 111, 119, 148, 152, 164, 174, 183,
        185, 189, 195, 209, 228, 233, 262,
    ];

    /**
     * Ids DofusDB de consommables cochés par défaut (hors certificats / coffres / fées).
     *
     * @var list<int>
     */
    public const CONSUMABLE_DOFUS_IDS = [
        12, 25, 27, 28, 30, 33, 37, 42, 43, 49, 69, 75, 76, 79, 83, 85, 88, 94, 100,
        157, 173, 187, 203, 216, 310, 322,
    ];

    /**
     * @example CatalogTypeVisibility::itemShouldShow(1) // true (Amulette)
     */
    public static function itemShouldShow(int $dofus_type_id): bool
    {
        return in_array($dofus_type_id, self::ITEM_DOFUS_IDS, true);
    }

    /**
     * @example CatalogTypeVisibility::resourceShouldShow(38) // true (Bois)
     */
    public static function resourceShouldShow(int $dofus_type_id): bool
    {
        return in_array($dofus_type_id, self::RESOURCE_DOFUS_IDS, true);
    }

    /**
     * @example CatalogTypeVisibility::consumableShouldShow(12) // true (Potion)
     */
    public static function consumableShouldShow(int $dofus_type_id): bool
    {
        return in_array($dofus_type_id, self::CONSUMABLE_DOFUS_IDS, true);
    }
}
