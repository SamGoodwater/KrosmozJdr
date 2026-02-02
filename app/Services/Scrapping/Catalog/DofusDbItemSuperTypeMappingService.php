<?php

namespace App\Services\Scrapping\Catalog;

/**
 * Lecture de la config de mapping superTypeId -> catégories métier.
 *
 * Source de vérité V2 : `resources/scrapping/v2/sources/dofusdb/item-super-types.json`.
 * L'ancien chemin (resources/scrapping/sources/dofusdb/) est déprécié.
 */
class DofusDbItemSuperTypeMappingService
{
    private string $basePath;

    public function __construct(?string $basePath = null)
    {
        $this->basePath = $basePath ?? base_path('resources/scrapping/v2');
    }

    /**
     * @return array<string,mixed>
     */
    public function getConfig(): array
    {
        $path = rtrim($this->basePath, '/') . '/sources/dofusdb/item-super-types.json';
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

    /**
     * TypeIds à exclure de toute collecte item (ressource, consumable, equipment).
     * Ex. : consommables obsolètes des Songes, La source - l'héritage des Dofus, apparat.
     *
     * @return list<int>
     */
    public function getExcludedTypeIds(): array
    {
        $cfg = $this->getConfig();
        $ids = $cfg['excludedTypeIds'] ?? [];
        if (!is_array($ids)) {
            return [];
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));

        return array_values(array_filter($ids, fn (int $id): bool => $id > 0));
    }
}

