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
     * Collecte d'une classe depuis DofusDB avec ses sorts associés
     * 
     * @param int $dofusdbId ID de la classe dans DofusDB
     * @param bool $includeSpells Si true, collecte également les sorts de la classe
     * @return array Données brutes de la classe avec ses sorts si demandé
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectClass(int $dofusdbId, bool $includeSpells = true): array
    {
        Log::info('Collecte classe depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_spells' => $includeSpells]);
        
        $url = $this->buildDofusDbUrl('breeds', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données de la classe ID {$dofusdbId}");
        }
        
        // Collecte des sorts associés à cette classe
        if ($includeSpells) {
            $data['spells'] = $this->collectClassSpells($dofusdbId);
        }
        
        Log::info('Classe collectée avec succès', ['dofusdb_id' => $dofusdbId, 'spells_count' => count($data['spells'] ?? [])]);
        
        return $data;
    }
    
    /**
     * Collecte les sorts associés à une classe
     * 
     * @param int $breedId ID de la classe dans DofusDB
     * @return array Liste des sorts de la classe
     */
    private function collectClassSpells(int $breedId): array
    {
        $baseUrl = $this->config['dofusdb']['base_url'] ?? 'https://api.dofusdb.fr';
        $lang = $this->config['dofusdb']['default_language'] ?? 'fr';
        $limit = 100;
        $skip = 0;
        $spells = [];
        $spellIds = [];
        
        // Récupérer les spell-levels associés à cette classe (spellBreed)
        while (true) {
            $levelsUrl = "{$baseUrl}/spell-levels?lang={$lang}&\$limit={$limit}&\$skip={$skip}";
            $response = $this->fetchFromDofusDb($levelsUrl);
            
            if (!isset($response['data']) || !is_array($response['data'])) {
                break;
            }
            
            foreach ($response['data'] as $level) {
                if (isset($level['spellBreed']) && $level['spellBreed'] == $breedId) {
                    $spellId = $level['spellId'] ?? null;
                    if ($spellId && !in_array($spellId, $spellIds)) {
                        $spellIds[] = $spellId;
                    }
                }
            }
            
            if (count($response['data']) < $limit) {
                break;
            }
            
            $skip += $limit;
        }
        
        // Collecter les données complètes de chaque sort
        foreach ($spellIds as $spellId) {
            try {
                $spellData = $this->collectSpell($spellId, false); // false = ne pas inclure les niveaux (déjà récupérés)
                $spells[] = $spellData;
            } catch (\Exception $e) {
                Log::warning('Impossible de collecter le sort associé à la classe', [
                    'breed_id' => $breedId,
                    'spell_id' => $spellId,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $spells;
    }

    /**
     * Collecte d'un monstre depuis DofusDB avec ses sorts et ressources associées
     * 
     * @param int $dofusdbId ID du monstre dans DofusDB
     * @param bool $includeSpells Si true, collecte également les sorts du monstre
     * @param bool $includeDrops Si true, collecte également les ressources droppées
     * @return array Données brutes du monstre avec ses relations si demandées
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectMonster(int $dofusdbId, bool $includeSpells = true, bool $includeDrops = true): array
    {
        Log::info('Collecte monstre depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_spells' => $includeSpells, 'include_drops' => $includeDrops]);
        
        $url = $this->buildDofusDbUrl('monsters', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données du monstre ID {$dofusdbId}");
        }
        
        // Collecte des sorts du monstre
        if ($includeSpells && isset($data['spells']) && is_array($data['spells'])) {
            $spells = [];
            foreach ($data['spells'] as $spellRef) {
                $spellId = is_array($spellRef) ? ($spellRef['id'] ?? $spellRef) : $spellRef;
                if ($spellId) {
                    try {
                        $spellData = $this->collectSpell($spellId, false);
                        $spells[] = $spellData;
                    } catch (\Exception $e) {
                        Log::warning('Impossible de collecter le sort associé au monstre', [
                            'monster_id' => $dofusdbId,
                            'spell_id' => $spellId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            $data['spells'] = $spells;
        }
        
        // Collecte des ressources droppées (drops)
        if ($includeDrops && isset($data['drops']) && is_array($data['drops'])) {
            $resources = [];
            foreach ($data['drops'] as $drop) {
                $itemId = is_array($drop) ? ($drop['itemId'] ?? $drop['id'] ?? null) : $drop;
                if ($itemId) {
                    try {
                        $itemData = $this->collectItem($itemId);
                        // Vérifier si c'est une ressource (typeId dans TYPES_RESOURCES)
                        $typeId = $itemData['typeId'] ?? null;
                        if ($typeId && $this->isResourceType($typeId)) {
                            $resources[] = $itemData;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Impossible de collecter la ressource associée au monstre', [
                            'monster_id' => $dofusdbId,
                            'item_id' => $itemId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            $data['drops'] = $resources;
        }
        
        Log::info('Monstre collecté avec succès', [
            'dofusdb_id' => $dofusdbId,
            'spells_count' => count($data['spells'] ?? []),
            'resources_count' => count($data['drops'] ?? [])
        ]);
        
        return $data;
    }

    /**
     * Collecte d'un objet depuis DofusDB avec sa recette si applicable
     * 
     * @param int $dofusdbId ID de l'objet dans DofusDB
     * @param bool $includeRecipe Si true, collecte également les ressources de la recette
     * @return array Données brutes de l'objet avec sa recette si demandée
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectItem(int $dofusdbId, bool $includeRecipe = true): array
    {
        Log::info('Collecte objet depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_recipe' => $includeRecipe]);
        
        $url = $this->buildDofusDbUrl('items', $dofusdbId);
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données de l'objet ID {$dofusdbId}");
        }
        
        // Collecte de la recette (ressources nécessaires pour la fabrication)
        if ($includeRecipe && isset($data['recipe']) && is_array($data['recipe'])) {
            $recipeResources = [];
            foreach ($data['recipe'] as $recipeItem) {
                $itemId = is_array($recipeItem) ? ($recipeItem['itemId'] ?? $recipeItem['id'] ?? null) : $recipeItem;
                $quantity = is_array($recipeItem) ? ($recipeItem['quantity'] ?? 1) : 1;
                
                if ($itemId) {
                    try {
                        $itemData = $this->collectItem($itemId, false); // Ne pas inclure la recette pour éviter la récursion infinie
                        // Vérifier si c'est une ressource
                        $typeId = $itemData['typeId'] ?? null;
                        if ($typeId && $this->isResourceType($typeId)) {
                            $recipeResources[] = [
                                'resource' => $itemData,
                                'quantity' => $quantity
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::warning('Impossible de collecter la ressource de la recette', [
                            'item_id' => $dofusdbId,
                            'recipe_item_id' => $itemId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            $data['recipe'] = $recipeResources;
        }
        
        Log::info('Objet collecté avec succès', [
            'dofusdb_id' => $dofusdbId,
            'recipe_resources_count' => count($data['recipe'] ?? [])
        ]);
        
        return $data;
    }
    
    /**
     * Collecte d'une panoplie depuis DofusDB avec ses items associés
     * 
     * @param int $dofusdbId ID de la panoplie dans DofusDB
     * @param bool $includeItems Si true, collecte également les items de la panoplie
     * @return array Données brutes de la panoplie avec ses items si demandé
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectPanoply(int $dofusdbId, bool $includeItems = true): array
    {
        Log::info('Collecte panoplie depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_items' => $includeItems]);
        
        // L'endpoint pour les panoplies est item-sets
        $baseUrl = $this->config['dofusdb']['base_url'] 
            ?? $this->config['dofusdb_base_url'] 
            ?? 'https://api.dofusdb.fr';
        $url = "{$baseUrl}/item-sets/{$dofusdbId}";
        $data = $this->fetchFromDofusDb($url);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données de la panoplie ID {$dofusdbId}");
        }
        
        // Les items sont déjà dans la réponse, mais on peut les enrichir si nécessaire
        if ($includeItems && isset($data['items']) && is_array($data['items'])) {
            // Les items sont déjà complets dans la réponse, on les préserve tels quels
            // Mais on peut extraire juste les IDs pour faciliter le traitement
            $itemIds = [];
            foreach ($data['items'] as $item) {
                $itemId = is_array($item) ? ($item['id'] ?? null) : $item;
                if ($itemId) {
                    $itemIds[] = $itemId;
                }
            }
            $data['item_ids'] = $itemIds;
        }
        
        Log::info('Panoplie collectée avec succès', [
            'dofusdb_id' => $dofusdbId,
            'items_count' => count($data['items'] ?? [])
        ]);
        
        return $data;
    }

    /**
     * Vérifie si un typeId correspond à une ressource
     * 
     * @param int $typeId Type ID de l'objet
     * @return bool True si c'est une ressource
     */
    private function isResourceType(int $typeId): bool
    {
        // Types de ressources selon la configuration DofusDB
        // Basé sur les types définis dans fields_config.php
        // Les ressources sont généralement les types 15 (ressources) et 35 (fleurs)
        // et d'autres types dans la plage des ressources
        $resourceTypes = [
            15, // resources
            35, // flowers
            // Ajouter d'autres types de ressources si nécessaire
        ];
        
        return in_array($typeId, $resourceTypes);
    }

    /**
     * Collecte d'un sort depuis DofusDB avec son monstre invoqué si applicable
     * 
     * @param int $dofusdbId ID du sort dans DofusDB
     * @param bool $includeLevels Si true, collecte également les niveaux du sort
     * @param bool $includeSummon Si true, collecte le monstre invoqué pour les sorts d'invocation
     * @return array Données brutes du sort avec ses relations si demandées
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectSpell(int $dofusdbId, bool $includeLevels = true, bool $includeSummon = true): array
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
        
        $data = $spellData;
        
        // Collecte des niveaux du sort si demandé (nécessaire pour les invocations aussi)
        if ($includeLevels || $includeSummon) {
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
            
            if ($includeLevels && !empty($levels)) {
                $data['levels'] = $levels;
            }
            
            // Les niveaux sont stockés temporairement pour vérifier les invocations
            if ($includeSummon && !empty($levels)) {
                $data['_levels_for_summon'] = $levels;
            }
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
     * @param string $entityType Type d'entité (breeds, monsters, items, item-sets, etc.)
     * @param int $entityId ID de l'entité
     * @return string URL complète
     */
    private function buildDofusDbUrl(string $entityType, int $entityId): string
    {
        $baseUrl = $this->config['dofusdb']['base_url'] 
            ?? $this->config['dofusdb_base_url'] 
            ?? 'https://api.dofusdb.fr';
        
        // Gérer les cas spéciaux d'endpoints
        if ($entityType === 'item-sets') {
            return "{$baseUrl}/item-sets/{$entityId}";
        }
        
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
