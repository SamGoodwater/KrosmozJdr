<?php

namespace App\Services\Scrapping\Catalog;

use App\Services\Scrapping\Constants\DofusDbLimits;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Cache;

/**
 * Catalogue DofusDB : item-types + superTypes (paginated).
 *
 * @description
 * DofusDB limite fréquemment les listes à 50 éléments par page.
 * Ce service pagine l'endpoint `/item-types`, regroupe par `superTypeId`
 * et expose des helpers pour dériver des listes de `typeId` par superType.
 */
class DofusDbItemTypesCatalogService
{
    public function __construct(private DofusDbClient $client) {}

    /**
     * @return array{
     *   meta: array{total:int|null,pages:int,returned:int,lang:string,limit:int},
     *   superTypes: array<int, array{id:int,name:string|null,types:array<int,array{id:int,name:string|null,categoryId:int|null,isInEncyclopedia:bool|null}>}>
     * }
     */
    public function getCatalog(string $lang = 'fr', bool $skipCache = false): array
    {
        $cacheKey = 'scrapping_dofusdb_item_types_catalog_' . $lang;
        $ttl = (int) config('scrapping.data_collect.cache_ttl', 3600);

        if (!$skipCache) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return $cached;
            }
        }

        $baseUrl = (string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr');
        $limit = DofusDbLimits::PAGE_LIMIT;
        $skip = 0;
        $page = 0;
        $total = null;

        /** @var array<int, array{id:int,name:string|null,types:array<int,array<string,mixed>>}> $bySuperType */
        $bySuperType = [];

        while (true) {
            $page++;

            $url = rtrim($baseUrl, '/') . '/item-types?lang=' . urlencode($lang) . '&$limit=' . $limit . '&$skip=' . $skip;
            $payload = $this->client->getJson($url, ['skip_cache' => $skipCache]);

            $data = $payload['data'] ?? null;
            if (!is_array($data) || count($data) === 0) {
                break;
            }

            if ($total === null && isset($payload['total']) && (is_int($payload['total']) || (is_string($payload['total']) && is_numeric($payload['total'])))) {
                $total = (int) $payload['total'];
            }

            $effectiveLimit = isset($payload['limit']) && (is_int($payload['limit']) || (is_string($payload['limit']) && ctype_digit($payload['limit'])))
                ? (int) $payload['limit']
                : 0;
            if ($effectiveLimit <= 0) {
                $effectiveLimit = count($data);
            }

            foreach ($data as $row) {
                if (!is_array($row)) continue;

                $typeId = isset($row['id']) ? (int) $row['id'] : 0;
                $superTypeId = isset($row['superTypeId']) ? (int) $row['superTypeId'] : 0;
                if ($typeId <= 0 || $superTypeId <= 0) {
                    continue;
                }

                $typeName = null;
                if (isset($row['name']) && is_array($row['name'])) {
                    $cand = $row['name'][$lang] ?? $row['name']['fr'] ?? null;
                    if (is_string($cand) && trim($cand) !== '') {
                        $typeName = trim($cand);
                    }
                }

                $superTypeName = null;
                if (isset($row['superType']) && is_array($row['superType'])) {
                    $st = $row['superType'];
                    if (isset($st['name']) && is_array($st['name'])) {
                        $cand = $st['name'][$lang] ?? $st['name']['fr'] ?? null;
                        if (is_string($cand) && trim($cand) !== '') {
                            $superTypeName = trim($cand);
                        }
                    }
                }

                if (!isset($bySuperType[$superTypeId])) {
                    $bySuperType[$superTypeId] = [
                        'id' => $superTypeId,
                        'name' => $superTypeName,
                        'types' => [],
                    ];
                } elseif (!$bySuperType[$superTypeId]['name'] && $superTypeName) {
                    $bySuperType[$superTypeId]['name'] = $superTypeName;
                }

                $bySuperType[$superTypeId]['types'][] = [
                    'id' => $typeId,
                    'name' => $typeName,
                    'categoryId' => isset($row['categoryId']) ? (int) $row['categoryId'] : null,
                    'isInEncyclopedia' => isset($row['isInEncyclopedia']) ? (bool) $row['isInEncyclopedia'] : null,
                ];
            }

            $skip += $effectiveLimit;
            if ($total !== null && $skip >= $total) {
                break;
            }
            if (count($data) < $effectiveLimit) {
                break;
            }
        }

        $superTypes = array_values($bySuperType);
        foreach ($superTypes as &$st) {
            $types = $st['types'] ?? [];
            usort($types, fn ($a, $b) => ((int) ($a['id'] ?? 0)) <=> ((int) ($b['id'] ?? 0)));
            $st['types'] = $types;
        }
        unset($st);
        usort($superTypes, fn ($a, $b) => ((int) ($a['id'] ?? 0)) <=> ((int) ($b['id'] ?? 0)));

        $typeCount = 0;
        foreach ($superTypes as $st) {
            $typeCount += is_array($st['types'] ?? null) ? count($st['types']) : 0;
        }

        $result = [
            'meta' => [
                'total' => $total,
                'pages' => $page,
                'returned' => $typeCount,
                'lang' => $lang,
                'limit' => $limit,
            ],
            'superTypes' => $superTypes,
        ];

        if (!$skipCache && $ttl > 0) {
            Cache::put($cacheKey, $result, $ttl);
        }

        return $result;
    }

    /**
     * @param array<int,int> $superTypeIds
     * @return array<int,int>
     */
    public function getTypeIdsForSuperTypes(array $superTypeIds, string $lang = 'fr', bool $skipCache = false): array
    {
        $superTypeIds = array_values(array_unique(array_map('intval', $superTypeIds)));
        $catalog = $this->getCatalog($lang, $skipCache);

        $wanted = array_flip($superTypeIds);
        $ids = [];

        foreach ($catalog['superTypes'] as $st) {
            $sid = (int) ($st['id'] ?? 0);
            if ($sid <= 0 || !isset($wanted[$sid])) {
                continue;
            }
            $types = $st['types'] ?? [];
            if (!is_array($types)) continue;
            foreach ($types as $t) {
                $tid = is_array($t) && isset($t['id']) ? (int) $t['id'] : 0;
                if ($tid > 0) $ids[] = $tid;
            }
        }

        $ids = array_values(array_unique($ids));
        sort($ids);
        return $ids;
    }

    /**
     * @return array<int,int>
     */
    public function getAllSuperTypeIds(string $lang = 'fr', bool $skipCache = false): array
    {
        $catalog = $this->getCatalog($lang, $skipCache);
        $ids = [];
        foreach ($catalog['superTypes'] as $st) {
            $sid = (int) ($st['id'] ?? 0);
            if ($sid > 0) $ids[] = $sid;
        }
        $ids = array_values(array_unique($ids));
        sort($ids);
        return $ids;
    }

    /**
     * Retourne le superTypeId DofusDB pour un typeId donné (ex: 15 → 9 pour Ressource).
     * Utilisé pour ne pas traiter comme "ressource" un typeId en registry resource_types
     * qui est en réalité un équipement (superType ≠ 9).
     *
     * @return int|null superTypeId ou null si typeId inconnu
     */
    public function getSuperTypeIdForTypeId(int $typeId, string $lang = 'fr', bool $skipCache = false): ?int
    {
        if ($typeId <= 0) {
            return null;
        }
        $catalog = $this->getCatalog($lang, $skipCache);
        foreach ($catalog['superTypes'] ?? [] as $st) {
            $sid = (int) ($st['id'] ?? 0);
            $types = $st['types'] ?? [];
            if (!is_array($types)) {
                continue;
            }
            foreach ($types as $t) {
                $tid = is_array($t) && isset($t['id']) ? (int) $t['id'] : 0;
                if ($tid === $typeId) {
                    return $sid > 0 ? $sid : null;
                }
            }
        }
        return null;
    }

    /**
     * Retire le suffixe « (DofusDB) » d'un libellé.
     */
    public function stripDofusdbSuffix(?string $name): ?string
    {
        if (!$name) {
            return $name;
        }
        $n = trim($name);
        if (str_ends_with($n, ' (DofusDB)')) {
            $n = trim(substr($n, 0, -strlen(' (DofusDB)')));
        }

        return $n;
    }

    /**
     * Résout un typeId DofusDB vers un nom (via le catalogue).
     */
    public function fetchName(int $typeId, string $lang = 'fr', bool $skipCache = false): ?string
    {
        if ($typeId <= 0) {
            return null;
        }
        $catalog = $this->getCatalog($lang, $skipCache);
        foreach ($catalog['superTypes'] ?? [] as $st) {
            foreach ($st['types'] ?? [] as $t) {
                $tid = is_array($t) && isset($t['id']) ? (int) $t['id'] : 0;
                if ($tid === $typeId) {
                    $name = $t['name'] ?? null;

                    return is_string($name) ? $this->stripDofusdbSuffix($name) : null;
                }
            }
        }

        return null;
    }

    /**
     * Résout un nom (type d'objet ou superType/catégorie) vers la liste des typeIds.
     * Si le nom correspond à un superType (ex. "Ressource"), retourne tous les typeIds de ce superType.
     * Sinon cherche un type dont le nom correspond et retourne [typeId].
     *
     * @return array<int,int> liste de typeIds (vide si non trouvé)
     */
    public function resolveTypeIdsByName(string $name, string $lang = 'fr', bool $skipCache = false): array
    {
        $name = trim($name);
        if ($name === '') {
            return [];
        }
        $normalize = static function (string $s): string {
            $s = mb_strtolower($s, 'UTF-8');
            $s = preg_replace('/\s+/', ' ', $s) ?? $s;

            return trim($s);
        };
        $needle = $normalize($name);
        $catalog = $this->getCatalog($lang, $skipCache);

        foreach ($catalog['superTypes'] ?? [] as $st) {
            $stName = $st['name'] ?? null;
            if (is_string($stName) && $stName !== '' && $normalize($stName) === $needle) {
                $sid = (int) ($st['id'] ?? 0);
                if ($sid > 0) {
                    return $this->getTypeIdsForSuperTypes([$sid], $lang, $skipCache);
                }
            }
            foreach ($st['types'] ?? [] as $t) {
                $tName = $t['name'] ?? null;
                if (is_string($tName) && $tName !== '' && $normalize($tName) === $needle) {
                    $tid = (int) ($t['id'] ?? 0);
                    if ($tid > 0) {
                        return [$tid];
                    }
                }
            }
        }

        return [];
    }
}

