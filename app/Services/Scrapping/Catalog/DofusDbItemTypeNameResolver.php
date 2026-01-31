<?php

namespace App\Services\Scrapping\Catalog;

use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Résout les noms des item-types DofusDB (`/item-types/{id}`).
 *
 * @description
 * Utilisé par les registries (resource_types / item_types / consumable_types)
 * pour remplacer les placeholders "DofusDB type #X" par le vrai libellé.
 */
class DofusDbItemTypeNameResolver
{
    /**
     * Cache local (par requête PHP) pour éviter de refetch plusieurs fois.
     *
     * @var array<int, string|null>
     */
    private array $cache = [];

    public function __construct(private DofusDbClient $client) {}

    public function stripDofusdbSuffix(?string $name): ?string
    {
        if (!$name) return $name;
        $n = trim($name);
        if (str_ends_with($n, ' (DofusDB)')) {
            $n = trim(substr($n, 0, -strlen(' (DofusDB)')));
        }
        return $n;
    }

    public function fetchName(int $typeId, bool $skipCache = false, ?string $logKey = null): ?string
    {
        if ($typeId <= 0) return null;
        if (array_key_exists($typeId, $this->cache)) {
            return $this->cache[$typeId];
        }

        $baseUrl = (string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr');
        $lang = (string) config('scrapping.data_collect.default_language', 'fr');
        $url = rtrim($baseUrl, '/') . "/item-types/{$typeId}?lang={$lang}";

        $ttl = (int) config('scrapping.data_collect.cache_ttl', 3600);
        $cacheKey = "scrapping_dofusdb_item_type_name_{$lang}_{$typeId}";
        if (!$skipCache && $ttl > 0) {
            $cached = Cache::get($cacheKey);
            if (is_string($cached)) {
                // '' = not found
                $name = $cached !== '' ? $cached : null;
                $this->cache[$typeId] = $name;
                return $name;
            }
        }

        try {
            $payload = $this->client->getJson($url, ['skip_cache' => $skipCache]);

            // DofusDB peut renvoyer l'entité directement, ou une forme "data".
            $row = $payload;
            if (isset($payload['data']) && is_array($payload['data']) && isset($payload['data'][0]) && is_array($payload['data'][0])) {
                $row = (array) $payload['data'][0];
            }

            $name = null;
            if (isset($row['name']) && is_array($row['name'])) {
                $cand = $row['name']['fr'] ?? $row['name'][$lang] ?? null;
                if (is_string($cand) && trim($cand) !== '') {
                    $name = trim($cand);
                }
            } elseif (isset($row['name']) && is_string($row['name']) && trim($row['name']) !== '') {
                $name = trim($row['name']);
            }

            $name = $this->stripDofusdbSuffix($name);
            $this->cache[$typeId] = $name;

            if (!$skipCache && $ttl > 0) {
                Cache::put($cacheKey, $name ?? '', $ttl);
            }

            return $name;
        } catch (\Throwable $e) {
            Log::debug(($logKey ?: 'dofusdb-item-types') . ': cannot resolve dofusdb item-type name', [
                'typeId' => $typeId,
                'error' => $e->getMessage(),
            ]);
            $this->cache[$typeId] = null;

            if (!$skipCache && $ttl > 0) {
                Cache::put($cacheKey, '', $ttl);
            }

            return null;
        }
    }
}

