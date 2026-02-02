<?php

namespace App\Services\Scrapping\V2\Collect;

use App\Services\Scrapping\V2\Config\ConfigLoader;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service de collecte V2 : exécute les requêtes décrites dans la config (endpoints, pagination, filtres).
 *
 * Lit la config « requêtes » pour une entité, appelle l’API DofusDB, gère la pagination
 * en utilisant le limit effectif renvoyé par l’API.
 */
final class CollectService
{
    public function __construct(
        private ConfigLoader $configLoader
    ) {
    }

    /**
     * Récupère un seul objet par ID.
     *
     * @param array{skip_cache?: bool} $options
     * @return array<string, mixed>
     */
    public function fetchOne(string $source, string $entity, int $id, array $options = []): array
    {
        $sourceConfig = $this->configLoader->loadSource($source);
        $entityConfig = $this->configLoader->loadEntity($source, $entity);

        $baseUrl = rtrim((string) ($sourceConfig['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $lang = (string) ($sourceConfig['defaultLanguage'] ?? 'fr');

        $fetchOne = $entityConfig['endpoints']['fetchOne'] ?? null;
        if (!is_array($fetchOne) || empty($fetchOne['pathTemplate'])) {
            return $this->fetchOneViaFetchMany($source, $entity, $id, $options);
        }

        $path = str_replace('{id}', (string) $id, (string) $fetchOne['pathTemplate']);
        $query = $this->interpolateQuery($fetchOne['queryDefaults'] ?? [], $lang);
        $url = $baseUrl . $path . ($query !== '' ? '?' . $query : '');

        $data = $this->getJson($url, $options);
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    /**
     * Récupère une liste d'objets (pagination API en interne). limit=0 => tout, offset=0 par défaut.
     *
     * @param array<string, mixed> $filters Filtres (id, idMin, idMax, ids, name, raceId, levelMin, levelMax, etc.)
     * @param array{skip_cache?: bool, limit?: int, offset?: int, page_size?: int} $options limit=0 => tout, offset=0 par défaut, page_size=50 pour les requêtes API
     * @return array{items: list<array<string, mixed>>, meta: array{total: int, limit: int, offset: int, collected: int}}
     */
    public function fetchMany(string $source, string $entity, array $filters = [], array $options = []): array
    {
        $sourceConfig = $this->configLoader->loadSource($source);
        $entityConfig = $this->configLoader->loadEntity($source, $entity);

        $baseUrl = rtrim((string) ($sourceConfig['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $lang = (string) ($sourceConfig['defaultLanguage'] ?? 'fr');

        $fetchMany = $entityConfig['endpoints']['fetchMany'] ?? null;
        if (!is_array($fetchMany) || empty($fetchMany['path'])) {
            throw new \InvalidArgumentException("Config entité '{$source}/{$entity}': endpoints.fetchMany.path requis.");
        }

        $path = (string) $fetchMany['path'];
        $defaults = $fetchMany['queryDefaults'] ?? [];
        $limit = (int) ($options['limit'] ?? 0);
        $offset = (int) ($options['offset'] ?? 0);
        $pageSize = max(1, (int) ($options['page_size'] ?? 50));

        $allItems = [];
        $skip = $offset;
        $total = 0;

        while (true) {
            $queryParams = array_merge(
                $this->interpolateQueryArray($defaults, $lang),
                ['$limit' => $pageSize, '$skip' => $skip],
                $this->filtersToQueryParams($entityConfig, $filters)
            );
            $query = http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
            $url = $baseUrl . '/' . ltrim($path, '/') . '?' . $query;

            $response = $this->getJson($url, $options);
            $dataList = $response['data'] ?? [];
            if (!is_array($dataList)) {
                $dataList = [];
            }

            $total = (int) ($response['total'] ?? $total);
            $effectiveLimit = (int) ($response['limit'] ?? count($dataList) ?: $pageSize);

            $remaining = $limit > 0 ? $limit - count($allItems) : \PHP_INT_MAX;
            foreach ($dataList as $item) {
                if ($remaining <= 0) {
                    break;
                }
                if (is_array($item)) {
                    $allItems[] = $item;
                    $remaining--;
                }
            }

            if (count($dataList) < $effectiveLimit) {
                break;
            }
            if ($limit > 0 && count($allItems) >= $limit) {
                break;
            }

            $skip += $effectiveLimit;
        }

        if ($total === 0) {
            $total = count($allItems) + $offset;
        }

        $items = $this->applyCollectStrategy($entityConfig, $allItems, $lang);

        return [
            'items' => $items,
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'collected' => count($items),
            ],
        ];
    }

    /**
     * Applique une stratégie de post-traitement sur les items collectés (ex. groupement par superTypeId).
     *
     * @param array<string, mixed> $entityConfig
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    private function applyCollectStrategy(array $entityConfig, array $items, string $lang): array
    {
        $strategy = $entityConfig['meta']['collectStrategy'] ?? null;
        if (!is_array($strategy) || ($strategy['groupBy'] ?? '') !== 'superTypeId' || ($strategy['outputShape'] ?? '') !== 'uniqueSuperTypes') {
            return $items;
        }

        $bySuperTypeId = [];
        foreach ($items as $row) {
            if (!is_array($row)) {
                continue;
            }
            $superTypeId = isset($row['superTypeId']) ? (int) $row['superTypeId'] : 0;
            if ($superTypeId <= 0) {
                continue;
            }
            if (isset($bySuperTypeId[$superTypeId])) {
                continue;
            }
            $name = null;
            if (isset($row['superType']) && is_array($row['superType']) && isset($row['superType']['name'])) {
                $nm = $row['superType']['name'];
                $name = is_array($nm) ? ($nm[$lang] ?? $nm['fr'] ?? $nm['en'] ?? null) : $nm;
            }
            $bySuperTypeId[$superTypeId] = [
                'id' => $superTypeId,
                'name' => is_string($name) ? $name : null,
            ];
        }
        usort($bySuperTypeId, fn ($a, $b) => ($a['id'] ?? 0) <=> ($b['id'] ?? 0));

        return array_values($bySuperTypeId);
    }

    /**
     * Simule fetchOne via fetchMany (id=…&$limit=1) quand fetchOne n’existe pas ou n’est pas fiable.
     *
     * @param array{skip_cache?: bool} $options
     * @return array<string, mixed>
     */
    private function fetchOneViaFetchMany(string $source, string $entity, int $id, array $options = []): array
    {
        $result = $this->fetchMany($source, $entity, ['id' => $id], ['limit' => 1, 'offset' => 0] + $options);
        $first = $result['items'][0] ?? null;

        return is_array($first) ? $first : [];
    }

    /**
     * Construit les paramètres de requête Feathers à partir des filtres config.
     *
     * @param array<string, mixed> $entityConfig
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    private function filtersToQueryParams(array $entityConfig, array $filters): array
    {
        $params = [];
        $supported = $entityConfig['filters']['supported'] ?? [];
        if (!is_array($supported)) {
            return $params;
        }

        $keyToFeathers = [
            'id' => 'id',
            'idMin' => 'id[$gte]',
            'idMax' => 'id[$lte]',
            'ids' => 'id[$in][]',
            'name' => 'name[$search]',
            'raceId' => 'raceId',
            'raceIds' => 'raceIds[]',
            'levelMin' => 'level[$gte]',
            'levelMax' => 'level[$lte]',
            'typeId' => 'typeId',
            'typeIds' => 'typeId[$in][]',
        ];

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $featherKey = $keyToFeathers[$key] ?? $key;
            if (is_array($value) && str_ends_with((string) $featherKey, '[]')) {
                foreach ($value as $v) {
                    $params[$featherKey] = $v;
                }
            } else {
                $params[$featherKey] = $value;
            }
        }

        return $params;
    }

    /**
     * @param array<string, mixed> $queryDefaults
     */
    private function interpolateQuery(array $queryDefaults, string $lang): string
    {
        $params = $this->interpolateQueryArray($queryDefaults, $lang);

        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param array<string, mixed> $queryDefaults
     * @return array<string, mixed>
     */
    private function interpolateQueryArray(array $queryDefaults, string $lang): array
    {
        $params = [];
        foreach ($queryDefaults as $k => $v) {
            if (is_string($v)) {
                $v = str_replace('{lang}', $lang, $v);
            }
            $params[$k] = $v;
        }

        return $params;
    }

    /**
     * @param array{skip_cache?: bool} $options
     * @return array<string, mixed>
     */
    private function getJson(string $url, array $options = []): array
    {
        Log::info('Collecte V2: GET', ['url' => $url]);

        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException("Erreur HTTP {$response->status()} : {$url}");
        }

        $data = $response->json();
        if (!is_array($data)) {
            return [];
        }

        return $data;
    }
}
