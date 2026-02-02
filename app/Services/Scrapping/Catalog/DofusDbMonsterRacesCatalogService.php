<?php

namespace App\Services\Scrapping\Catalog;

use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Cache;

/**
 * Catalogue des races de monstres DofusDB (/monster-races).
 *
 * @description
 * Permet de récupérer la liste complète des races et de résoudre raceId -> nom,
 * avec cache applicatif (évite N requêtes).
 */
class DofusDbMonsterRacesCatalogService
{
    public function __construct(private DofusDbClient $client) {}

    /**
     * @return array<int, array{id:int, name: string|null}>
     */
    public function listAll(string $lang = 'fr', bool $skipCache = false): array
    {
        $lang = $lang ?: 'fr';
        $cacheKey = "dofusdb_monster_races_catalog_{$lang}";
        $ttl = (int) config('scrapping.data_collect.cache_ttl', 3600);

        if (!$skipCache && $ttl > 0) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                /** @var array<int, array{id:int, name: string|null}> $cached */
                return $cached;
            }
        }

        $baseUrl = rtrim((string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr'), '/');

        $limit = 50;
        $skip = 0;
        $out = [];

        while (true) {
            $url = "{$baseUrl}/monster-races?lang={$lang}&\$limit={$limit}&\$skip={$skip}";
            $resp = $this->client->getJson($url, ['skip_cache' => $skipCache]);
            $data = $resp['data'] ?? null;
            if (!is_array($data)) {
                break;
            }

            foreach ($data as $row) {
                if (!is_array($row)) continue;
                $id = $row['id'] ?? null;
                if (!(is_int($id) || (is_string($id) && ctype_digit($id)))) continue;
                $id = (int) $id;

                $name = null;
                $nm = $row['name'] ?? null;
                if (is_array($nm)) {
                    $name = $nm[$lang] ?? ($nm['fr'] ?? ($nm['en'] ?? null));
                } elseif (is_string($nm)) {
                    $name = $nm;
                }

                $out[$id] = [
                    'id' => $id,
                    'name' => is_string($name) ? $name : null,
                ];
            }

            $apiLimit = isset($resp['limit']) && (is_int($resp['limit']) || (is_string($resp['limit']) && ctype_digit($resp['limit'])))
                ? (int) $resp['limit']
                : $limit;
            $returned = count($data);
            $total = isset($resp['total']) && (is_int($resp['total']) || is_float($resp['total']) || (is_string($resp['total']) && is_numeric($resp['total'])))
                ? (int) $resp['total']
                : null;

            $skip += max(1, $apiLimit);
            if ($returned < $apiLimit) break;
            if ($total !== null && $skip >= $total) break;
        }

        $list = array_values($out);
        usort($list, fn ($a, $b) => (string) ($a['name'] ?? '') <=> (string) ($b['name'] ?? ''));

        if (!$skipCache && $ttl > 0) {
            Cache::put($cacheKey, $list, $ttl);
        }

        return $list;
    }

    /**
     * @return array<int,string> map raceId => name
     */
    public function mapNames(string $lang = 'fr', bool $skipCache = false): array
    {
        $map = [];
        foreach ($this->listAll($lang, $skipCache) as $r) {
            $id = (int) ($r['id'] ?? 0);
            if ($id <= 0) continue;
            $name = $r['name'] ?? null;
            if (is_string($name) && $name !== '') {
                $map[$id] = $name;
            }
        }
        return $map;
    }

    /**
     * Résout un nom de race (ou slug) vers l'ID DofusDB.
     * Comparaison insensible à la casse et aux espaces.
     *
     * @return int|null raceId ou null si non trouvé
     */
    public function findRaceIdByName(string $name, string $lang = 'fr', bool $skipCache = false): ?int
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }
        $normalize = static function (string $s): string {
            $s = mb_strtolower($s, 'UTF-8');
            $s = preg_replace('/\s+/', ' ', $s) ?? $s;

            return trim($s);
        };
        $needle = $normalize($name);
        foreach ($this->listAll($lang, $skipCache) as $r) {
            $raceName = $r['name'] ?? null;
            if (!is_string($raceName) || $raceName === '') {
                continue;
            }
            if ($normalize($raceName) === $needle) {
                $id = (int) ($r['id'] ?? 0);

                return $id > 0 ? $id : null;
            }
        }

        return null;
    }
}

