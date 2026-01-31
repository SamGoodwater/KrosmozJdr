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
                // fallback: si registry vide, on laisse passer via config superType (utile au bootstrap)
                $fromCfg = $this->getTypeIdsForGroup($entity);
                return !empty($fromCfg) && in_array($typeId, $fromCfg, true);
            }

            // all/selected: on se base sur la config superType
            $fromCfg = $this->getTypeIdsForGroup($entity);
            return !empty($fromCfg) && in_array($typeId, $fromCfg, true);
        }

        if ($entity === 'equipment') {
            // allowed: si on a une registry item_types (decision=allowed), on la respecte.
            if ($typeMode === self::TYPE_MODE_ALLOWED) {
                $allowed = $this->getAllowedTypeIdsFromRegistry('equipment');
                if (!empty($allowed)) {
                    return in_array($typeId, $allowed, true);
                }
            }

            // all: basé sur la config superType (exclusions)
            $defaults = $this->defaultEquipmentFilters(self::TYPE_MODE_ALL);
            if (isset($defaults['typeIds']) && is_array($defaults['typeIds'])) {
                $ids = array_values(array_unique(array_map('intval', $defaults['typeIds'])));
                return !empty($ids) && in_array($typeId, $ids, true);
            }
            if (isset($defaults['typeIdsNot']) && is_array($defaults['typeIdsNot'])) {
                $idsNot = array_values(array_unique(array_map('intval', $defaults['typeIdsNot'])));
                return empty($idsNot) || !in_array($typeId, $idsNot, true);
            }
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

        $fromCfg = $this->getTypeIdsForGroup('resource');
        return empty($fromCfg) ? [] : ['typeIds' => $fromCfg];
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

        $fromCfg = $this->getTypeIdsForGroup('consumable');
        return empty($fromCfg) ? [] : ['typeIds' => $fromCfg];
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
            // fallback: si la registry n'est pas encore prête, on retombe sur la stratégie "all"
        }

        $lang = (string) config('scrapping.data_collect.default_language', 'fr');
        $g = $this->itemSuperTypeMapping->getGroup('equipment');
        $strategy = $g['strategy'] ?? 'include';

        if ($strategy === 'include') {
            $superTypeIds = $g['superTypeIds'] ?? [];
            if (!is_array($superTypeIds) || empty($superTypeIds)) {
                return [];
            }
            $typeIds = $this->itemTypesCatalog->getTypeIdsForSuperTypes($superTypeIds, $lang, false);
            return empty($typeIds) ? [] : ['typeIds' => $typeIds];
        }

        $excludeSuperTypeIds = $g['excludeSuperTypeIds'] ?? [];
        if (!is_array($excludeSuperTypeIds) || empty($excludeSuperTypeIds)) {
            return [];
        }
        $typeIdsNot = $this->itemTypesCatalog->getTypeIdsForSuperTypes($excludeSuperTypeIds, $lang, false);
        return empty($typeIdsNot) ? [] : ['typeIdsNot' => $typeIdsNot];
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

