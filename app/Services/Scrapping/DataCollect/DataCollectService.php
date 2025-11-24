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
        
        if (empty($data)) {
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
        
        if (empty($data)) {
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
        
        if (empty($data)) {
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
        
        // Collecte du sort principal
        $spellUrl = $this->buildDofusDbUrl('spells', $dofusdbId);
        $spellData = $this->fetchFromDofusDb($spellUrl);
        
        if (empty($spellData)) {
            throw new \Exception("Impossible de récupérer les données du sort ID {$dofusdbId}");
        }
        
        // Collecte des niveaux du sort
        $levelsUrl = $this->buildDofusDbUrl('spell-levels', $dofusdbId);
        $levelsData = $this->fetchFromDofusDb($levelsUrl);
        
        $data = $spellData;
        if (!empty($levelsData)) {
            $data['levels'] = $levelsData;
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
        $baseUrl = $this->config['dofusdb_base_url'] ?? 'https://api.dofusdb.fr';
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
     * @return int Nombre d'éléments supprimés du cache
     */
    public function clearCache(): int
    {
        $pattern = 'dofusdb_*';
        $deleted = 0;
        
        foreach (Cache::getStore()->many([$pattern]) as $key => $value) {
            if (Cache::forget($key)) {
                $deleted++;
            }
        }
        
        Log::info('Cache des données collectées nettoyé', ['deleted_count' => $deleted]);
        
        return $deleted;
    }
}
