<?php

namespace App\Services\Scrapping\Http;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client HTTP DofusDB avec cache/retry centralisés.
 *
 * @description
 * Centralise la logique d'appel HTTP vers DofusDB (timeouts, retries, cache),
 * afin d'éviter la duplication entre les différents services de scrapping.
 *
 * @example
 * $data = $client->getJson('https://api.dofusdb.fr/monsters/42?lang=fr', ['skip_cache' => true]);
 */
class DofusDbClient
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private array $config = [])
    {
        // Optionnel : la config peut être injectée, sinon on lit config('scrapping.data_collect').
        if (empty($this->config)) {
            $this->config = (array) config('scrapping.data_collect', []);
        }
    }

    /**
     * Récupère une réponse JSON DofusDB (array) avec cache optionnel.
     *
     * @param string $url
     * @param array{skip_cache?:bool, cache_ttl?:int|null} $options
     * @return array<string, mixed>
     */
    public function getJson(string $url, array $options = []): array
    {
        $skipCache = (bool) ($options['skip_cache'] ?? false);
        $cacheKey = 'dofusdb_' . md5($url);
        $cacheTtl = $options['cache_ttl'] ?? ($this->config['cache_ttl'] ?? 3600);

        if (!$skipCache && Cache::has($cacheKey)) {
            Log::info('Données récupérées depuis le cache', ['url' => $url]);
            return (array) Cache::get($cacheKey);
        }

        $timeout = (int) ($this->config['timeout'] ?? 30);
        $retryAttempts = (int) ($this->config['retry_attempts'] ?? 3);
        $retryDelayMs = (int) ($this->config['retry_delay'] ?? 1000);

        try {
            $response = Http::timeout($timeout)
                ->retry($retryAttempts, $retryDelayMs)
                ->get($url);

            if (!$response->successful()) {
                throw new \RuntimeException("Erreur HTTP {$response->status()} lors de la récupération depuis {$url}");
            }

            $data = $response->json();
            if (!is_array($data)) {
                $data = [];
            }

            if (!$skipCache && $cacheTtl !== null) {
                Cache::put($cacheKey, $data, (int) $cacheTtl);
            }

            Log::info('Données récupérées depuis DofusDB', [
                'url' => $url,
                'status' => $response->status(),
                'data_size' => strlen(json_encode($data)),
                'skip_cache' => $skipCache,
            ]);

            /** @var array<string, mixed> $data */
            return $data;
        } catch (\Throwable $e) {
            Log::error('Erreur lors de la récupération depuis DofusDB', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

