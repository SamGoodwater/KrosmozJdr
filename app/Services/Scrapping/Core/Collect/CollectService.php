<?php

namespace App\Services\Scrapping\Core\Collect;

use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service de collecte : exécute les requêtes décrites dans la config (endpoints, pagination, filtres).
 *
 * Lit la config « requêtes » pour une entité, appelle l’API DofusDB, gère la pagination
 * en utilisant le limit effectif renvoyé par l’API.
 */
final class CollectService
{
    private const ENTITY_ALIASES = ['class' => 'breed'];

    public function __construct(
        private ConfigLoader $configLoader,
        private ?DofusDbClient $dofusDbClient = null,
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
        $configEntity = self::ENTITY_ALIASES[$entity] ?? $entity;
        $sourceConfig = $this->configLoader->loadSource($source);
        $entityConfig = $this->configLoader->loadEntity($source, $configEntity);

        $baseUrl = rtrim((string) ($sourceConfig['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $lang = (string) ($sourceConfig['defaultLanguage'] ?? 'fr');

        $fetchOne = $entityConfig['endpoints']['fetchOne'] ?? null;
        if (!is_array($fetchOne) || empty($fetchOne['pathTemplate'])) {
            return $this->fetchOneViaFetchMany($source, $configEntity, $id, $options);
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
     * Récupère la recette DofusDB pour un objet (ingredientIds + quantities).
     * Utilise l'endpoint /recipes?resultId= pour obtenir les quantités réelles.
     *
     * @param array{skip_cache?: bool} $options
     * @return array{ingredientIds: list<int>, quantities: list<int>}|null
     */
    public function fetchRecipeByResultId(string $source, int $resultId, array $options = []): ?array
    {
        $sourceConfig = $this->configLoader->loadSource($source);
        $baseUrl = rtrim((string) ($sourceConfig['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $lang = (string) ($sourceConfig['defaultLanguage'] ?? 'fr');
        $query = http_build_query(['resultId' => $resultId, 'lang' => $lang], '', '&', PHP_QUERY_RFC3986);
        $url = $baseUrl . '/recipes?' . $query;
        $response = $this->getJson($url, $options);
        $data = $response['data'] ?? [];
        if (!is_array($data) || $data === []) {
            return null;
        }
        $first = $data[0] ?? null;
        if (!is_array($first)) {
            return null;
        }
        $ingredientIds = $first['ingredientIds'] ?? [];
        $quantities = $first['quantities'] ?? [];
        if (!is_array($ingredientIds)) {
            $ingredientIds = [];
        }
        if (!is_array($quantities)) {
            $quantities = [];
        }

        return [
            'ingredientIds' => array_values($ingredientIds),
            'quantities' => array_values($quantities),
        ];
    }

    /**
     * Récupère les IDs de sorts liés à une classe (breed) via l'API /spell-levels?breedId=.
     *
     * @return list<int>
     */
    public function fetchSpellIdsByBreedId(string $source, int $breedId, array $options = []): array
    {
        $sourceConfig = $this->configLoader->loadSource($source);
        $baseUrl = rtrim((string) ($sourceConfig['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $lang = (string) ($sourceConfig['defaultLanguage'] ?? 'fr');
        $query = http_build_query(['breedId' => $breedId, 'lang' => $lang, '$limit' => 500], '', '&', PHP_QUERY_RFC3986);
        $url = $baseUrl . '/spell-levels?' . $query;
        $response = $this->getJson($url, $options);
        $data = $response['data'] ?? [];
        if (!is_array($data)) {
            return [];
        }
        $spellIds = [];
        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }
            $id = $row['spellId'] ?? $row['spell_id'] ?? $row['id'] ?? null;
            if ($id !== null && (is_int($id) || ctype_digit((string) $id))) {
                $spellIds[] = (int) $id;
            }
        }

        return array_values(array_unique($spellIds));
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
        if (!is_array($strategy)) {
            return $items;
        }

        if (($strategy['filterOutCosmetic'] ?? false) === true) {
            return array_values(array_filter($items, static function ($row): bool {
                if (!is_array($row)) {
                    return false;
                }
                return ($row['isCosmetic'] ?? true) !== true;
            }));
        }

        if (($strategy['groupBy'] ?? '') !== 'superTypeId' || ($strategy['outputShape'] ?? '') !== 'uniqueSuperTypes') {
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
        $result = $this->fetchManyWithFeathersEncoding($source, $entity, ['id' => $id], ['limit' => 1, 'offset' => 0] + $options);
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
        Log::info('Collecte: GET', ['url' => $url]);

        if ($this->dofusDbClient !== null) {
            return $this->dofusDbClient->getJson($url, [
                'skip_cache' => (bool) ($options['skip_cache'] ?? false),
            ]);
        }

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

    /**
     * Variante fetchMany retournant meta (skip, pages, returned) compatible recherche/batch.
     *
     * @param array<string, mixed> $filters
     * @param array{skip_cache?: bool, limit?: int, offset?: int, start_skip?: int, max_pages?: int, max_items?: int, page_size?: int} $options
     * @return array{items: list<array<string, mixed>>, meta: array{total: int, limit: int, skip: int, pages: int, returned: int}}
     */
    public function fetchManyResult(string $source, string $entity, array $filters = [], array $options = []): array
    {
        $configEntity = self::ENTITY_ALIASES[$entity] ?? $entity;
        $result = $this->fetchManyWithFeathersEncoding($source, $configEntity, $filters, $options);
        $meta = $result['meta'];
        return [
            'items' => $result['items'],
            'meta' => [
                'total' => $meta['total'],
                'limit' => $meta['limit'],
                'skip' => $meta['skip'],
                'pages' => $meta['pages'],
                'returned' => $meta['returned'],
            ],
        ];
    }

    /**
     * fetchMany avec encodage Feathers (tableaux en key[]=v) et meta complète.
     *
     * @param array<string, mixed> $filters
     * @param array{skip_cache?: bool, limit?: int, offset?: int, start_skip?: int, max_pages?: int, max_items?: int, page_size?: int} $options
     * @return array{items: list<array<string, mixed>>, meta: array{total: int, limit: int, offset: int, skip: int, pages: int, returned: int, collected: int}}
     */
    private function fetchManyWithFeathersEncoding(string $source, string $entity, array $filters = [], array $options = []): array
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
        $defaults = $this->interpolateQueryArray($fetchMany['queryDefaults'] ?? [], $lang);
        $limit = (int) ($options['limit'] ?? 100);
        if ($limit < 0) {
            $limit = 0;
        }
        $skip = (int) ($options['offset'] ?? $options['start_skip'] ?? 0);
        if ($skip < 0) {
            $skip = 0;
        }
        $pageSize = max(1, (int) ($options['page_size'] ?? 50));
        $maxPages = (int) ($options['max_pages'] ?? 50);
        $maxItems = (int) ($options['max_items'] ?? 5000);
        if ($maxPages <= 0) {
            $maxPages = 1000000;
        }
        if ($maxItems <= 0) {
            $maxItems = 1000000;
        }
        $allItems = [];
        $total = 0;
        $page = 0;
        $effectiveLimit = $pageSize;
        $initialSkip = $skip;

        while (true) {
            $page++;
            if ($page > $maxPages) {
                break;
            }
            $requestLimit = ($limit > 0 && count($allItems) === 0) ? min($limit, $pageSize) : $pageSize;
            $query = array_merge($defaults, ['$limit' => $requestLimit, '$skip' => $skip], $this->filtersToFeathersQuery($entityConfig, $filters));
            $queryString = $this->buildFeathersQueryString($query);
            $url = $baseUrl . '/' . ltrim($path, '/') . '?' . $queryString;
            $response = $this->getJson($url, $options);
            $dataList = $response['data'] ?? [];
            if (!is_array($dataList)) {
                $dataList = [];
            }
            $total = (int) ($response['total'] ?? $total);
            $apiLimit = isset($response['limit']) && (is_int($response['limit']) || (is_string($response['limit']) && ctype_digit($response['limit'])))
                ? (int) $response['limit']
                : 0;
            if ($apiLimit > 0) {
                $effectiveLimit = $apiLimit;
            }
            foreach ($dataList as $item) {
                if (is_array($item)) {
                    $allItems[] = $item;
                    if (count($allItems) >= $maxItems) {
                        break 2;
                    }
                }
            }
            $skip += $effectiveLimit;
            if ($total > 0 && $skip >= $total) {
                break;
            }
            if (empty($dataList) || (count($dataList) < $effectiveLimit)) {
                break;
            }
        }
        if ($total === 0) {
            $total = count($allItems) + $initialSkip;
        }
        $items = $this->applyCollectStrategy($entityConfig, $allItems, $lang);
        $returned = count($items);
        $pages = $page;

        return [
            'items' => $items,
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $initialSkip,
                'skip' => $initialSkip,
                'pages' => $pages,
                'returned' => $returned,
                'collected' => $returned,
            ],
        ];
    }

    /**
     * Construit une query string compatible Feathers (tableaux en key[]=v).
     *
     * @param array<string, mixed> $query
     */
    private function buildFeathersQueryString(array $query): string
    {
        $pairs = [];
        foreach ($query as $k => $v) {
            if (!is_string($k) || $k === '') {
                continue;
            }
            $this->flattenQueryPairs($pairs, $k, $v);
        }
        $out = [];
        foreach ($pairs as [$key, $value]) {
            $out[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }
        return implode('&', $out);
    }

    /**
     * @param array<int, array{0:string, 1:string|int|float|bool}> $pairs
     */
    private function flattenQueryPairs(array &$pairs, string $prefix, mixed $value): void
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (is_int($k)) {
                    $this->flattenQueryPairs($pairs, $prefix . '[]', $v);
                } else {
                    $this->flattenQueryPairs($pairs, $prefix . '[' . (string) $k . ']', $v);
                }
            }
            return;
        }
        if (is_bool($value)) {
            $pairs[] = [$prefix, $value ? 'true' : 'false'];
            return;
        }
        if (is_int($value) || is_float($value)) {
            $pairs[] = [$prefix, (string) $value];
            return;
        }
        if (is_string($value) && $value !== '') {
            $pairs[] = [$prefix, $value];
            return;
        }
        if (is_string($value) && $value === '') {
            return;
        }
        if ($value !== null) {
            $pairs[] = [$prefix, (string) $value];
        }
    }

    /**
     * Filtres métier → query Feathers (DofusDB : race pour monster, pas raceId).
     *
     * @param array<string, mixed> $entityConfig
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    private function filtersToFeathersQuery(array $entityConfig, array $filters): array
    {
        $supported = $entityConfig['filters']['supported'] ?? [];
        if (!is_array($supported)) {
            return [];
        }
        $supportedKeys = [];
        foreach ($supported as $f) {
            if (is_array($f) && isset($f['key']) && is_string($f['key']) && $f['key'] !== '') {
                $supportedKeys[$f['key']] = $f;
            }
        }
        $entity = (string) ($entityConfig['entity'] ?? '');
        $q = [];

        foreach ($filters as $key => $value) {
            if (!is_string($key) || !isset($supportedKeys[$key])) {
                continue;
            }
            switch ($key) {
                case 'id':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['id'] = (int) $value;
                    }
                    break;
                case 'idMin':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['id'] = ($q['id'] ?? []);
                        if (is_array($q['id'])) {
                            $q['id']['$gte'] = (int) $value;
                        }
                    }
                    break;
                case 'idMax':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['id'] = ($q['id'] ?? []);
                        if (is_array($q['id'])) {
                            $q['id']['$lte'] = (int) $value;
                        }
                    }
                    break;
                case 'ids':
                    if (is_array($value)) {
                        $max = (int) ($supportedKeys[$key]['max'] ?? 500);
                        $ids = [];
                        foreach ($value as $v) {
                            if (is_int($v) || (is_string($v) && ctype_digit($v))) {
                                $ids[] = (int) $v;
                            }
                        }
                        $ids = array_values(array_unique($ids));
                        if ($max > 0) {
                            $ids = array_slice($ids, 0, $max);
                        }
                        if (!empty($ids)) {
                            $q['id'] = ['$in' => $ids];
                        }
                    }
                    break;
                case 'name':
                    if (is_string($value) && $value !== '') {
                        $q['name'] = ['$search' => $value];
                    }
                    break;
                case 'raceId':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['race'] = (int) $value;
                    }
                    break;
                case 'raceIds':
                    if (is_array($value)) {
                        $max = (int) ($supportedKeys[$key]['max'] ?? 5000);
                        $ids = [];
                        foreach ($value as $v) {
                            if (is_int($v) || (is_string($v) && ctype_digit($v))) {
                                $ids[] = (int) $v;
                            }
                        }
                        $ids = array_values(array_unique($ids));
                        if ($max > 0) {
                            $ids = array_slice($ids, 0, $max);
                        }
                        if (!empty($ids)) {
                            $q['race'] = ($q['race'] ?? []);
                            if (is_array($q['race'])) {
                                $q['race']['$in'] = $ids;
                            }
                        }
                    }
                    break;
                case 'breedId':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['breedId'] = (int) $value;
                    }
                    break;
                case 'typeId':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['typeId'] = (int) $value;
                    }
                    break;
                case 'typeIds':
                    if (is_array($value)) {
                        $max = (int) ($supportedKeys[$key]['max'] ?? 5000);
                        $ids = [];
                        foreach ($value as $v) {
                            if (is_int($v) || (is_string($v) && ctype_digit($v))) {
                                $ids[] = (int) $v;
                            }
                        }
                        $ids = array_values(array_unique($ids));
                        if ($max > 0) {
                            $ids = array_slice($ids, 0, $max);
                        }
                        if (!empty($ids)) {
                            $q['typeId'] = ($q['typeId'] ?? []);
                            if (is_array($q['typeId'])) {
                                $q['typeId']['$in'] = $ids;
                            }
                        }
                    }
                    break;
                case 'typeIdsNot':
                    if (is_array($value)) {
                        $max = (int) ($supportedKeys[$key]['max'] ?? 8000);
                        $ids = [];
                        foreach ($value as $v) {
                            if (is_int($v) || (is_string($v) && ctype_digit($v))) {
                                $ids[] = (int) $v;
                            }
                        }
                        $ids = array_values(array_unique($ids));
                        if ($max > 0) {
                            $ids = array_slice($ids, 0, $max);
                        }
                        if (!empty($ids)) {
                            $q['typeId'] = ($q['typeId'] ?? []);
                            if (is_array($q['typeId'])) {
                                $q['typeId']['$nin'] = $ids;
                            }
                        }
                    }
                    break;
                case 'levelMin':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['level'] = ($q['level'] ?? []);
                        if (is_array($q['level'])) {
                            $q['level']['$gte'] = (int) $value;
                        }
                    }
                    break;
                case 'levelMax':
                    if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                        $q['level'] = ($q['level'] ?? []);
                        if (is_array($q['level'])) {
                            $q['level']['$lte'] = (int) $value;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        return $q;
    }
}
