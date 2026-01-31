<?php

namespace App\Services\Scrapping\DataCollect;

use App\Services\Scrapping\Config\ScrappingConfigLoader;
use App\Services\Scrapping\Http\DofusDbClient;

/**
 * Collecteur DofusDB piloté par config JSON.
 *
 * @description
 * Fournit un fetchOne générique basé sur `resources/scrapping/sources/dofusdb/entities/*.json`
 * pour rendre la partie "collect" rapide à étendre à toutes les entités.
 */
class ConfigDrivenDofusDbCollector
{
    public function __construct(
        private ScrappingConfigLoader $loader,
        private DofusDbClient $client,
    ) {}

    /**
     * @param array{skip_cache?:bool} $options
     * @return array<string,mixed>
     */
    public function fetchOne(string $entity, int $id, array $options = []): array
    {
        $sourceCfg = $this->loader->loadSource('dofusdb');
        $entityCfg = $this->loader->loadEntity('dofusdb', $entity);

        $baseUrl = rtrim((string) ($sourceCfg['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $defaultLang = (string) ($sourceCfg['defaultLanguage'] ?? 'fr');

        $fetchOne = $entityCfg['endpoints']['fetchOne'] ?? null;
        if (!is_array($fetchOne) || !isset($fetchOne['pathTemplate'])) {
            // Fallback (utile pour spell) : simuler un fetchOne via fetchMany(id=...).
            $many = $this->fetchMany($entity, ['id' => $id], ['limit' => 1] + $options);
            $first = $many[0] ?? null;
            if (!is_array($first)) {
                throw new \InvalidArgumentException("Config dofusdb/{$entity}: endpoints.fetchOne absent et fetchMany n'a rien retourné pour id={$id}");
            }
            return $first;
        }

        $pathTemplate = (string) $fetchOne['pathTemplate'];
        $path = str_replace('{id}', (string) $id, $pathTemplate);

        $queryDefaults = $fetchOne['queryDefaults'] ?? [];
        if (!is_array($queryDefaults)) {
            $queryDefaults = [];
        }
        $queryDefaults = $this->interpolate($queryDefaults, ['lang' => $defaultLang]);

        $url = $baseUrl . $path;
        if (!empty($queryDefaults)) {
            $url .= '?' . http_build_query($queryDefaults);
        }

        return $this->client->getJson($url, [
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
        ]);
    }

    /**
     * Fetch paginé d'une entité via Feathers (DofusDB).
     *
     * @param array<string,mixed> $filters
     * @param array{skip_cache?:bool, limit?:int, max_pages?:int, max_items?:int} $options
     * @return array<int, array<string,mixed>>
     */
    public function fetchMany(string $entity, array $filters = [], array $options = []): array
    {
        return $this->fetchManyResult($entity, $filters, $options)['items'];
    }

    /**
     * Variante retournant aussi des métadonnées (total/limit/pages).
     *
     * @param array<string,mixed> $filters
     * @param array{skip_cache?:bool, limit?:int, max_pages?:int, max_items?:int} $options
     * @return array{items: array<int, array<string,mixed>>, meta: array<string,mixed>}
     */
    public function fetchManyResult(string $entity, array $filters = [], array $options = []): array
    {
        $sourceCfg = $this->loader->loadSource('dofusdb');
        $entityCfg = $this->loader->loadEntity('dofusdb', $entity);

        $baseUrl = rtrim((string) ($sourceCfg['baseUrl'] ?? 'https://api.dofusdb.fr'), '/');
        $defaultLang = (string) ($sourceCfg['defaultLanguage'] ?? 'fr');

        $fetchMany = $entityCfg['endpoints']['fetchMany'] ?? null;
        if (!is_array($fetchMany) || !isset($fetchMany['path'])) {
            throw new \InvalidArgumentException("Config dofusdb/{$entity}: endpoints.fetchMany.path manquant");
        }

        $path = (string) $fetchMany['path'];

        $queryDefaults = $fetchMany['queryDefaults'] ?? [];
        if (!is_array($queryDefaults)) {
            $queryDefaults = [];
        }
        $query = $this->interpolate($queryDefaults, ['lang' => $defaultLang]);

        $limit = (int) ($options['limit'] ?? ($query['$limit'] ?? 100));
        if ($limit <= 0) {
            $limit = 100;
        }
        $skip = (int) ($query['$skip'] ?? 0);
        if ($skip < 0) {
            $skip = 0;
        }

        // Permet de reprendre une pagination depuis la CLI / API.
        if (isset($options['start_skip'])) {
            $skip = max(0, (int) $options['start_skip']);
        }

        $query['$limit'] = $limit;
        $query['$skip'] = $skip;

        $filterQuery = $this->filtersToFeathersQuery($entityCfg, $filters);
        $query = array_replace_recursive($query, $filterQuery);

        // 0 = illimité (borné par max_items pour la sécurité)
        $maxPages = (int) ($options['max_pages'] ?? 50);
        $maxItems = (int) ($options['max_items'] ?? 5000);
        if ($maxPages <= 0) {
            $maxPages = 1000000;
        }
        if ($maxItems <= 0) {
            $maxItems = 1000000;
        }

        $items = [];
        $page = 0;
        $total = null;
        $initialSkip = $skip;
        $effectiveLimit = $limit;

        while (true) {
            $page++;
            if ($page > $maxPages) {
                break;
            }
            $query['$skip'] = $skip;

            $url = $baseUrl . $path . '?' . $this->buildFeathersQueryString($query);
            $resp = $this->client->getJson($url, [
                'skip_cache' => (bool) ($options['skip_cache'] ?? false),
            ]);

            // Feathers: { data: [], total, limit, skip }
            $data = $resp['data'] ?? null;
            if (is_array($data)) {
                // Certains services DofusDB capent le limit (souvent 50).
                // On doit avancer le $skip avec le limit réellement appliqué pour ne pas "sauter" des pages.
                $apiLimit = isset($resp['limit']) && (is_int($resp['limit']) || (is_string($resp['limit']) && ctype_digit($resp['limit'])))
                    ? (int) $resp['limit']
                    : 0;
                if ($apiLimit > 0) {
                    $effectiveLimit = $apiLimit;
                }

                foreach ($data as $row) {
                    if (is_array($row)) {
                        $items[] = $row;
                        if (count($items) >= $maxItems) {
                            break 2;
                        }
                    }
                }

                if (isset($resp['total']) && (is_int($resp['total']) || is_float($resp['total']) || (is_string($resp['total']) && is_numeric($resp['total'])))) {
                    $total = (int) $resp['total'];
                }

                $skip += $effectiveLimit;
                if ($total !== null && $skip >= $total) {
                    break;
                }
                if (empty($data)) {
                    break;
                }
                // Si l'API renvoie moins que le limit effectif, on est en fin de dataset.
                if ($effectiveLimit > 0 && count($data) < $effectiveLimit) {
                    break;
                }
                continue;
            }

            // Fallback: si l'API renvoie directement une liste
            if (array_is_list($resp)) {
                /** @var array<int, array<string,mixed>> $resp */
                return [
                    'items' => $resp,
                    'meta' => [
                        'total' => count($resp),
                        'limit' => $effectiveLimit,
                        'skip' => $initialSkip,
                        'pages' => 1,
                        'returned' => count($resp),
                    ],
                ];
            }

            return [
                'items' => [],
                'meta' => [
                    'total' => 0,
                    'limit' => $effectiveLimit,
                    'skip' => $initialSkip,
                    'pages' => $page,
                    'returned' => 0,
                ],
            ];
        }

        return [
            'items' => $items,
            'meta' => [
                'total' => $total,
                'limit' => $effectiveLimit,
                'skip' => $initialSkip,
                'pages' => $page,
                'returned' => count($items),
            ],
        ];
    }

    /**
     * Génère une querystring Feathers compatible.
     *
     * @description
     * `http_build_query()` encode les listes avec des indices (`[0]`, `[1]`),
     * ce qui est parfois interprété côté DofusDB comme un objet au lieu d'un array.
     * Ex: `typeId[$in][0]=12` -> cast Number fail.
     *
     * Ici, on encode les listes en `[]` répétés :
     * `typeId[$in][]=12&typeId[$in][]=13`.
     *
     * @param array<string,mixed> $query
     */
    private function buildFeathersQueryString(array $query): string
    {
        $pairs = [];
        foreach ($query as $k => $v) {
            if (!is_string($k) || $k === '') continue;
            $this->flattenQueryPairs($pairs, $k, $v);
        }

        $out = [];
        foreach ($pairs as [$key, $value]) {
            $out[] = rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }

        return implode('&', $out);
    }

    /**
     * @param array<int, array{0:string,1:string|int|float|bool}> $pairs
     * @param mixed $value
     */
    private function flattenQueryPairs(array &$pairs, string $prefix, mixed $value): void
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (is_int($k)) {
                    // array list -> [] (pas d'index)
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
            // évite d'envoyer des filtres vides
            return;
        }

        if ($value !== null) {
            $pairs[] = [$prefix, (string) $value];
        }
    }

    /**
     * @param array<string,mixed> $data
     * @param array<string,string> $vars
     * @return array<string,mixed>
     */
    private function interpolate(array $data, array $vars): array
    {
        foreach ($data as $k => $v) {
            if (is_string($v)) {
                foreach ($vars as $name => $value) {
                    $v = str_replace('{' . $name . '}', $value, $v);
                }
                $data[$k] = $v;
            }
        }
        return $data;
    }

    /**
     * Convertit les filtres "métier" du JSON en query Feathers safe.
     *
     * @param array<string,mixed> $entityCfg
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    private function filtersToFeathersQuery(array $entityCfg, array $filters): array
    {
        $supported = $entityCfg['filters']['supported'] ?? [];
        if (!is_array($supported)) {
            $supported = [];
        }

        $supportedKeys = [];
        foreach ($supported as $f) {
            if (is_array($f) && isset($f['key']) && is_string($f['key']) && $f['key'] !== '') {
                $supportedKeys[$f['key']] = $f;
            }
        }

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
                        // DofusDB expose le champ `race` (pas `raceId`) sur /monsters
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
                    // Ignoré: filtre supporté mais pas encore mappé
                    break;
            }
        }

        return $q;
    }
}

