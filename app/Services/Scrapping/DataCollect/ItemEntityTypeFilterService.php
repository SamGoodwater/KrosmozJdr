<?php

namespace App\Services\Scrapping\DataCollect;

use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use Illuminate\Support\Facades\Cache;

/**
 * Dérive des filtres typeId/typeIds/typeIdsNot pour les "alias" de /items.
 *
 * @description
 * - `resource`, `consumable`, `equipment` sont des vues métier autour de DofusDB `/items`.
 * - La source de vérité éditable est `resources/scrapping/sources/dofusdb/item-super-types.json`
 * - Les registries DB (resource_types / consumable_types) priment quand elles sont remplies (decision=allowed).
 */
class ItemEntityTypeFilterService
{
    public const TYPE_MODE_ALL = 'all';
    public const TYPE_MODE_ALLOWED = 'allowed';
    public const TYPE_MODE_SELECTED = 'selected';

    public function __construct(
        private DofusDbItemTypesCatalogService $itemTypesCatalog,
        private DofusDbItemSuperTypeMappingService $itemSuperTypeMapping,
    ) {}

    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function applyDefaults(string $entity, array $filters, string $typeMode = self::TYPE_MODE_ALLOWED): array
    {
        $entity = strtolower($entity);
        $typeMode = $this->normalizeTypeMode($typeMode);

        $hasExplicitType = array_key_exists('typeId', $filters) || array_key_exists('typeIds', $filters) || array_key_exists('typeIdsNot', $filters);
        if ($hasExplicitType) {
            return $filters;
        }

        // En mode "selected", on ne doit pas injecter de defaults : l'UI fournit explicitement typeIds/typeIdsNot
        if ($typeMode === self::TYPE_MODE_SELECTED) {
            return $filters;
        }

        $defaults = $this->defaultFiltersForEntity($entity, $typeMode);
        return empty($defaults) ? $filters : array_replace($filters, $defaults);
    }

    /**
     * @return array<string,mixed>
     */
    public function defaultFiltersForEntity(string $entity, string $typeMode = self::TYPE_MODE_ALLOWED): array
    {
        $entity = strtolower($entity);
        $typeMode = $this->normalizeTypeMode($typeMode);

        return match ($entity) {
            'resource' => $this->defaultResourceFilters($typeMode),
            'consumable' => $this->defaultConsumableFilters($typeMode),
            'equipment' => $this->defaultEquipmentFilters($typeMode),
            default => [],
        };
    }

    /**
     * @return array<int,int>
     */
    public function getAllowedTypeIdsFromRegistry(string $entity): array
    {
        $entity = strtolower(trim($entity));
        $cacheKey = "scrapping_allowed_type_ids_{$entity}";
        $ttl = (int) config('scrapping.data_collect.cache_ttl', 3600);

        if ($ttl > 0) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                /** @var array<int,int> $cached */
                return $cached;
            }
        }

        $result = [];
        try {
            if ($entity === 'resource') {
                $result = array_values(array_unique(array_map('intval', ResourceType::query()->allowed()->pluck('dofusdb_type_id')->all())));
            }
            if ($entity === 'consumable') {
                $result = array_values(array_unique(array_map('intval', ConsumableType::query()->allowed()->pluck('dofusdb_type_id')->all())));
            }
            if ($entity === 'equipment') {
                $result = array_values(array_unique(array_map('intval', ItemType::query()->allowed()->pluck('dofusdb_type_id')->all())));
            }
        } catch (\Throwable) {
            $result = [];
        }

        if ($ttl > 0) {
            Cache::put($cacheKey, $result, $ttl);
        }

        return $result;
    }

    /**
     * Liste des typeId DofusDB présents en base pour une entité (resource, consumable, equipment).
     * Source de vérité : les tables resource_types, consumable_types, item_types.
     *
     * @return array<int,int>
     */
    public function getTypeIdsFromRegistry(string $entity): array
    {
        $entity = strtolower(trim($entity));
        try {
            $ids = match ($entity) {
                'resource' => ResourceType::query()->whereNotNull('dofusdb_type_id')->pluck('dofusdb_type_id')->all(),
                'consumable' => ConsumableType::query()->whereNotNull('dofusdb_type_id')->pluck('dofusdb_type_id')->all(),
                'equipment' => ItemType::query()->whereNotNull('dofusdb_type_id')->pluck('dofusdb_type_id')->all(),
                default => [],
            };
            return array_values(array_unique(array_map('intval', $ids)));
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return array<int,int>
     */
    public function getTypeIdsForGroup(string $group): array
    {
        $lang = (string) config('scrapping.data_collect.default_language', 'fr');
        $g = $this->itemSuperTypeMapping->getGroup($group);
        $superTypeIds = $g['superTypeIds'] ?? [];
        if (!is_array($superTypeIds) || empty($superTypeIds)) {
            return [];
        }

        return $this->itemTypesCatalog->getTypeIdsForSuperTypes($superTypeIds, $lang, false);
    }

    /**
     * Détermine si un `typeId` DofusDB est autorisé pour une vue métier donnée.
     *
     * @description
     * Utilise les defaults calculés (registry DB -> config superType) :
     * - si on a `typeIds`: autorisé si in_array(typeId, typeIds)
     * - si on a `typeIdsNot`: autorisé si !in_array(typeId, typeIdsNot)
     */
    public function isTypeIdAllowedForEntity(string $entity, int $typeId, string $typeMode = self::TYPE_MODE_ALLOWED): bool
    {
        $entity = strtolower($entity);
        $typeId = (int) $typeId;
        $typeMode = $this->normalizeTypeMode($typeMode);
        if ($typeId <= 0) {
            return false;
        }

        // Cas simples: resource/consumable
        if ($entity === 'resource' || $entity === 'consumable') {
            if ($typeMode === self::TYPE_MODE_ALLOWED) {
                $allowed = $this->getAllowedTypeIdsFromRegistry($entity);
                if (!empty($allowed)) {
                    return in_array($typeId, $allowed, true);
                }
                return true;
            }

            $fromRegistry = $this->getTypeIdsFromRegistry($entity);
            return !empty($fromRegistry) && in_array($typeId, $fromRegistry, true);
        }

        if ($entity === 'equipment') {
            if ($typeMode === self::TYPE_MODE_ALLOWED) {
                $allowed = $this->getAllowedTypeIdsFromRegistry('equipment');
                if (!empty($allowed)) {
                    return in_array($typeId, $allowed, true);
                }
                return true;
            }

            $fromRegistry = $this->getTypeIdsFromRegistry('equipment');
            return !empty($fromRegistry) && in_array($typeId, $fromRegistry, true);
        }

        return false;
    }

    /**
     * @return array<string,mixed>
     */
    private function defaultResourceFilters(string $typeMode): array
    {
        if ($typeMode === self::TYPE_MODE_ALLOWED) {
            $allowed = $this->getAllowedTypeIdsFromRegistry('resource');
            if (!empty($allowed)) {
                return ['typeIds' => $allowed];
            }
        }

        return [];
    }

    /**
     * @return array<string,mixed>
     */
    private function defaultConsumableFilters(string $typeMode): array
    {
        if ($typeMode === self::TYPE_MODE_ALLOWED) {
            $allowed = $this->getAllowedTypeIdsFromRegistry('consumable');
            if (!empty($allowed)) {
                return ['typeIds' => $allowed];
            }
        }

        return [];
    }

    /**
     * @return array<string,mixed>
     */
    private function defaultEquipmentFilters(string $typeMode): array
    {
        if ($typeMode === self::TYPE_MODE_ALLOWED) {
            $allowed = $this->getAllowedTypeIdsFromRegistry('equipment');
            if (!empty($allowed)) {
                return ['typeIds' => $allowed];
            }
        }

        return [];
    }

    private function normalizeTypeMode(string $mode): string
    {
        $mode = strtolower(trim((string) $mode));
        return match ($mode) {
            self::TYPE_MODE_ALL => self::TYPE_MODE_ALL,
            self::TYPE_MODE_SELECTED => self::TYPE_MODE_SELECTED,
            default => self::TYPE_MODE_ALLOWED,
        };
    }
}

