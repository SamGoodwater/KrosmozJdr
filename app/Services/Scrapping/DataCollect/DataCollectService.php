<?php

namespace App\Services\Scrapping\DataCollect;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service de collecte de données depuis des sites externes
 * 
 * Récupère les données brutes depuis des sites comme DofusDB
 * et les prépare pour la conversion et l'intégration.
 * 
 * @package App\Services\Scrapping\DataCollect
 */
class DataCollectService
{
    /**
     * Configuration du service
     */
    private array $config;

    /**
     * Constructeur du service de collecte
     */
    public function __construct()
    {
        $this->config = config('scrapping.data_collect', []);
    }

    /**
     * Collecte d'une classe depuis DofusDB
     * 
     * @param int $dofusdbId ID de la classe dans DofusDB
     * @return array Données brutes de la classe
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectClass(int $dofusdbId): array
    {
        Log::info('Collecte classe depuis DofusDB', ['dofusdb_id' => $dofusdbId]);
        
        $url = $this->buildDofusDbUrl('breeds', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données de la classe ID {$dofusdbId}");
        }
        
        Log::info('Classe collectée avec succès', ['dofusdb_id' => $dofusdbId]);
        
        return $data;
    }

    /**
     * Collecte d'un monstre depuis DofusDB
     * 
     * @param int $dofusdbId ID du monstre dans DofusDB
     * @return array Données brutes du monstre
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectMonster(int $dofusdbId): array
    {
        Log::info('Collecte monstre depuis DofusDB', ['dofusdb_id' => $dofusdbId]);
        
        $url = $this->buildDofusDbUrl('monsters', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données du monstre ID {$dofusdbId}");
        }
        
        Log::info('Monstre collecté avec succès', ['dofusdb_id' => $dofusdbId]);
        
        return $data;
    }

    /**
     * Collecte d'un objet depuis DofusDB
     * 
     * @param int $dofusdbId ID de l'objet dans DofusDB
     * @return array Données brutes de l'objet
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectItem(int $dofusdbId): array
    {
        Log::info('Collecte objet depuis DofusDB', ['dofusdb_id' => $dofusdbId]);
        
        $url = $this->buildDofusDbUrl('items', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données de l'objet ID {$dofusdbId}");
        }
        
        Log::info('Objet collecté avec succès', ['dofusdb_id' => $dofusdbId]);
        
        return $data;
    }

    /**
     * Collecte d'un sort depuis DofusDB
     * 
     * @param int $dofusdbId ID du sort dans DofusDB
     * @return array Données brutes du sort
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectSpell(int $dofusdbId): array
    {
        Log::info('Collecte sort depuis DofusDB', ['dofusdb_id' => $dofusdbId]);
        
        // L'API DofusDB ne supporte pas /spells/{id} ni $filter
        // Il faut récupérer les sorts par pagination et trouver celui avec l'ID correspondant
        $baseUrl = $this->config['dofusdb']['base_url'] ?? 'https://api.dofusdb.fr';
        $lang = $this->config['dofusdb']['default_language'] ?? 'fr';
        $limit = 100; // Récupérer par lots de 100
        $skip = 0;
        $spellData = null;
        
        // Recherche du sort par pagination
        while ($spellData === null) {
            $spellUrl = "{$baseUrl}/spells?lang={$lang}&\$limit={$limit}&\$skip={$skip}";
            $response = $this->fetchFromDofusDb($spellUrl);
            
            if (!isset($response['data']) || !is_array($response['data'])) {
                break;
            }
            
            // Chercher le sort avec l'ID correspondant
            foreach ($response['data'] as $spell) {
                if (isset($spell['id']) && $spell['id'] == $dofusdbId) {
                    $spellData = $spell;
                    break;
                }
            }
            
            // Si on a trouvé le sort, arrêter la boucle
            if ($spellData !== null) {
                break;
            }
            
            // Si on a récupéré moins de résultats que la limite, on a atteint la fin
            if (count($response['data']) < $limit) {
                break;
            }
            
            $skip += $limit;
        }
        
        if ($spellData === null) {
            throw new \Exception("Impossible de récupérer les données du sort ID {$dofusdbId}");
        }
        
        // Collecte des niveaux du sort (même approche par pagination)
        $levelsUrl = "{$baseUrl}/spell-levels?lang={$lang}&\$limit={$limit}&\$skip=0";
        $levelsResponse = $this->fetchFromDofusDb($levelsUrl);
        $levels = [];
        
        if (isset($levelsResponse['data']) && is_array($levelsResponse['data'])) {
            foreach ($levelsResponse['data'] as $level) {
                if (isset($level['spellId']) && $level['spellId'] == $dofusdbId) {
                    $levels[] = $level;
                }
            }
        }
        
        $data = $spellData;
        if (!empty($levels)) {
            $data['levels'] = $levels;
        }
        
        Log::info('Sort collecté avec succès', ['dofusdb_id' => $dofusdbId]);
        
        return $data;
    }

    /**
     * Collecte d'un effet depuis DofusDB
     * 
     * @param int $dofusdbId ID de l'effet dans DofusDB
     * @return array Données brutes de l'effet
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectEffect(int $dofusdbId): array
    {
        Log::info('Collecte effet depuis DofusDB', ['dofusdb_id' => $dofusdbId]);
        
        $url = $this->buildDofusDbUrl('effects', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        if (empty($data)) {
            throw new \Exception("Impossible de récupérer les données de l'effet ID {$dofusdbId}");
        }
        
        Log::info('Effet collecté avec succès', ['dofusdb_id' => $dofusdbId]);
        
        return $data;
    }

    /**
     * Construit l'URL DofusDB pour une entité donnée
     * 
     * @param string $entityType Type d'entité (breeds, monsters, items, etc.)
     * @param int $entityId ID de l'entité
     * @return string URL complète
     */
    private function buildDofusDbUrl(string $entityType, int $entityId): string
    {
        $baseUrl = $this->config['dofusdb']['base_url'] 
            ?? $this->config['dofusdb_base_url'] 
            ?? 'https://api.dofusdb.fr';
        return "{$baseUrl}/{$entityType}/{$entityId}";
    }

    /**
     * Récupère les données depuis DofusDB avec gestion du cache
     * 
     * @param string $url URL à récupérer
     * @return array Données récupérées
     * @throws \Exception En cas d'erreur de récupération
     */
    private function fetchFromDofusDb(string $url): array
    {
        $cacheKey = 'dofusdb_' . md5($url);
        $cacheTtl = $this->config['cache_ttl'] ?? 3600; // 1 heure par défaut
        
        // Vérification du cache
        if (Cache::has($cacheKey)) {
            Log::info('Données récupérées depuis le cache', ['url' => $url]);
            return Cache::get($cacheKey);
        }
        
        try {
            $response = Http::timeout($this->config['timeout'] ?? 30)
                ->retry($this->config['retry_attempts'] ?? 3, $this->config['retry_delay'] ?? 1000)
                ->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // S'assurer que $data est un tableau
                if (!is_array($data)) {
                    $data = [];
                }
                
                // Mise en cache des données
                Cache::put($cacheKey, $data, $cacheTtl);
                
                Log::info('Données récupérées depuis DofusDB', [
                    'url' => $url,
                    'status' => $response->status(),
                    'data_size' => strlen(json_encode($data))
                ]);
                
                return $data;
            } else {
                throw new \Exception("Erreur HTTP {$response->status()} lors de la récupération depuis {$url}");
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération depuis DofusDB', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Vérifie la disponibilité du service DofusDB
     * 
     * @return bool True si le service est disponible
     */
    public function isDofusDbAvailable(): bool
    {
        try {
            $baseUrl = $this->config['dofusdb_base_url'] ?? 'https://api.dofusdb.fr';
            $response = Http::timeout(5)->get("{$baseUrl}/health");
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('Service DofusDB non disponible', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Nettoie le cache des données collectées
     * 
     * Utilise les tags de cache si disponibles, sinon utilise le préfixe
     * pour identifier et supprimer les clés de cache DofusDB.
     * 
     * @return int Nombre d'éléments supprimés du cache
     */
    public function clearCache(): int
    {
        $cacheConfig = $this->config['cache'] ?? [];
        $tags = $cacheConfig['tags'] ?? [];
        $prefix = $cacheConfig['prefix'] ?? 'dofusdb:';
        
        try {
            // Si le driver supporte les tags (Redis, Memcached), utilise-les
            if (!empty($tags) && method_exists(Cache::getStore(), 'tags')) {
                Cache::tags($tags)->flush();
                Log::info('Cache des données collectées nettoyé via tags', ['tags' => $tags]);
                return 1; // Tags flush retourne un bool, on retourne 1 pour indiquer le succès
            }
            
            // Sinon, on essaie de supprimer par préfixe (nécessite Redis ou un driver qui supporte les patterns)
            $store = Cache::getStore();
            
            // Pour Redis, on peut utiliser la commande KEYS avec pattern
            if (method_exists($store, 'connection') && method_exists($store->connection(), 'keys')) {
                $connection = $store->connection();
                $keys = $connection->keys($prefix . '*');
                $deleted = 0;
                
                foreach ($keys as $key) {
                    // Retirer le préfixe Redis si présent
                    $cleanKey = str_replace(config('cache.prefix', ''), '', $key);
                    if (Cache::forget($cleanKey)) {
                        $deleted++;
                    }
                }
                
                Log::info('Cache des données collectées nettoyé via préfixe', [
                    'prefix' => $prefix,
                    'deleted_count' => $deleted
                ]);
                
                return $deleted;
            }
            
            // Fallback : flush complet du cache (à utiliser avec précaution)
            Log::warning('Impossible de nettoyer le cache par pattern, flush complet effectué');
            Cache::flush();
            return 1;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage du cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
