<?php

namespace App\Services\Scrapping\Catalog;

/**
 * Lecture de la config de mapping superTypeId -> catégories métier.
 *
 * @description
 * Source de vérité "éditable" par config JSON (sans toucher au code) :
 * `resources/scrapping/sources/dofusdb/item-super-types.json`.
 */
class DofusDbItemSuperTypeMappingService
{
    /**
     * @return array<string,mixed>
     */
    public function getConfig(): array
    {
        $path = base_path('resources/scrapping/sources/dofusdb/item-super-types.json');
        if (!is_file($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    /**
     * @return array{strategy:string,superTypeIds:array<int,int>,excludeSuperTypeIds:array<int,int>}
     */
    public function getGroup(string $group): array
    {
        $cfg = $this->getConfig();
        $groups = $cfg['groups'] ?? null;
        if (!is_array($groups)) {
            $groups = [];
        }

        $g = $groups[$group] ?? null;
        if (!is_array($g)) {
            $g = [];
        }

        $strategy = isset($g['strategy']) && is_string($g['strategy']) ? strtolower($g['strategy']) : 'include';
        if (!in_array($strategy, ['include', 'exclude'], true)) {
            $strategy = 'include';
        }

        $superTypeIds = $g['superTypeIds'] ?? [];
        if (!is_array($superTypeIds)) {
            $superTypeIds = [];
        }

        $excludeSuperTypeIds = $g['excludeSuperTypeIds'] ?? [];
        if (!is_array($excludeSuperTypeIds)) {
            $excludeSuperTypeIds = [];
        }

        return [
            'strategy' => $strategy,
            'superTypeIds' => array_values(array_unique(array_map('intval', $superTypeIds))),
            'excludeSuperTypeIds' => array_values(array_unique(array_map('intval', $excludeSuperTypeIds))),
        ];
    }
}

