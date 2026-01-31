<?php

namespace App\Services\Scrapping\DataCollect;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Scrapping\PendingResourceTypeItem;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Facades\Http;
use App\Services\Scrapping\DataCollect\ConfigDrivenDofusDbCollector;
use App\Services\Scrapping\DataCollect\ItemEntityTypeFilterService;

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
     * Client HTTP pour DofusDB
     */
    private DofusDbClient $client;

    /**
     * Collecteur générique basé sur config JSON.
     */
    private ConfigDrivenDofusDbCollector $configCollector;

    /**
     * Constructeur du service de collecte
     */
    public function __construct(DofusDbClient $client, ConfigDrivenDofusDbCollector $configCollector)
    {
        $this->config = config('scrapping.data_collect', []);
        $this->client = $client;
        $this->configCollector = $configCollector;
    }

    /**
     * Collecte d'une classe depuis DofusDB avec ses sorts associés
     * 
     * @param int $dofusdbId ID de la classe dans DofusDB
     * @param bool $includeSpells Si true, collecte également les sorts de la classe
     * @return array Données brutes de la classe avec ses sorts si demandé
     * @throws \Exception En cas d'erreur de collecte
     */
    public function collectClass(int $dofusdbId, bool $includeSpells = true, array $options = []): array
    {
        Log::info('Collecte classe depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_spells' => $includeSpells]);
        
        $data = $this->fetchEntityFromConfigOrFallback('class', $dofusdbId, 'breeds', $options);
        
        // Vérifier que les données sont valides (pas vide et contient au moins un ID)
        if (empty($data) || !isset($data['id'])) {
            throw new \Exception("Impossible de récupérer les données de la classe ID {$dofusdbId}");
        }
        
        // Collecte des sorts associés à cette classe
        if ($includeSpells) {
            $data['spells'] = $this->collectClassSpells($dofusdbId, $options);
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
    private function collectClassSpells(int $breedId, array $options = []): array
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
            $response = $this->fetchFromDofusDb($levelsUrl, $options);
            
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
                $spellData = $this->collectSpell($spellId, false, true, $options); // false = ne pas inclure les niveaux (déjà récupérés)
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
    public function collectMonster(int $dofusdbId, bool $includeSpells = true, bool $includeDrops = true, array $options = []): array
    {
        Log::info('Collecte monstre depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_spells' => $includeSpells, 'include_drops' => $includeDrops]);
        
        $data = $this->fetchEntityFromConfigOrFallback('monster', $dofusdbId, 'monsters', $options);
        
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
                        $spellData = $this->collectSpell($spellId, false, true, $options);
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
                $quantity = is_array($drop) ? ($drop['quantity'] ?? null) : null;
                if ($itemId) {
                    try {
                        $itemData = $this->collectItem($itemId, true, $options);
                        // Vérifier si c'est une ressource (superType Ressource)
                        $typeId = $itemData['typeId'] ?? null;
                        if ($typeId) {
                            $typeId = (int) $typeId;

                            // IMPORTANT: ne pas polluer la registry "resource-types" avec des équipements.
                            // On ne mémorise en pending que si le typeId appartient au groupe Ressource.
                            if (!$this->isResourceGroupTypeId($typeId)) {
                                continue;
                            }

                            if ($this->isAllowedResourceTypeId($typeId)) {
                                $resources[] = $itemData;
                            } else {
                                $this->rememberPendingResourceCandidate(
                                    $typeId,
                                    (int) $itemId,
                                    'drops',
                                    'monster',
                                    $dofusdbId,
                                    $quantity !== null ? (int) $quantity : null
                                );
                            }
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
    public function collectItem(int $dofusdbId, bool $includeRecipe = true, array $options = []): array
    {
        Log::info('Collecte objet depuis DofusDB', ['dofusdb_id' => $dofusdbId, 'include_recipe' => $includeRecipe]);
        
        $data = $this->fetchEntityFromConfigOrFallback('item', $dofusdbId, 'items', $options);
        
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
                        $itemData = $this->collectItem($itemId, false, $options); // Ne pas inclure la recette pour éviter la récursion infinie
                        // Vérifier si c'est une ressource
                        $typeId = $itemData['typeId'] ?? null;
                        if ($typeId) {
                            $typeId = (int) $typeId;

                            // Seuls les items du groupe Ressource peuvent être considérés comme ingrédients "ressources".
                            if (!$this->isResourceGroupTypeId($typeId)) {
                                continue;
                            }

                            if ($this->isAllowedResourceTypeId($typeId)) {
                                $recipeResources[] = [
                                    'resource' => $itemData,
                                    'quantity' => $quantity,
                                ];
                            } else {
                                $this->rememberPendingResourceCandidate(
                                    $typeId,
                                    (int) $itemId,
                                    'recipe',
                                    'item',
                                    $dofusdbId,
                                    (int) $quantity
                                );
                            }
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
     * Collecte une page d'items depuis DofusDB (endpoint /items)
     *
     * @param int $skip Décalage (pagination)
     * @param int $limit Taille de page
     * @param array<string, mixed> $extraQuery Paramètres additionnels (ex: filtres)
     * @return array Données brutes DofusDB (souvent {total, limit, skip, data})
     *
     * @example
     * $page = $service->collectItemsPage(0, 100);
     * $items = $page['data'] ?? [];
     */
    public function collectItemsPage(int $skip = 0, int $limit = 100, array $extraQuery = []): array
    {
        $baseUrl = $this->config['dofusdb']['base_url']
            ?? $this->config['dofusdb_base_url']
            ?? 'https://api.dofusdb.fr';
        $lang = $this->config['dofusdb']['default_language'] ?? 'fr';

        // DofusDB attend des paramètres "$limit" et "$skip" (Feathers)
        $url = "{$baseUrl}/items?lang={$lang}&\$limit={$limit}&\$skip={$skip}";
        foreach ($extraQuery as $key => $value) {
            // Support des paramètres multiples (ex: typeId[$in][]=15, typeId[$in][]=35)
            if (is_array($value)) {
                foreach ($value as $v) {
                    $url .= '&' . $key . '=' . rawurlencode((string) $v);
                }
                continue;
            }
            $url .= '&' . $key . '=' . rawurlencode((string) $value);
        }

        return $this->fetchFromDofusDb($url);
    }

    /**
     * Indique si un typeId DofusDB est actuellement autorisé comme "ressource".
     *
     * @param int $typeId
     * @return bool
     *
     * @example
     * if ($service->isAllowedResourceTypeId(15)) { ... }
     */
    public function isAllowedResourceTypeId(int $typeId): bool
    {
        // Source de vérité: registry + config superType (via service dédié).
        // Fallback sur legacy si le conteneur n'est pas prêt (tests isolés).
        try {
            /** @var ItemEntityTypeFilterService $svc */
            $svc = app(ItemEntityTypeFilterService::class);
            return $svc->isTypeIdAllowedForEntity('resource', (int) $typeId);
        } catch (\Throwable) {
            return $this->isResourceType($typeId);
        }
    }

    /**
     * Indique si un typeId DofusDB appartient au groupe métier "resource" (superType Ressource),
     * indépendamment de son statut allowed/pending/blocked.
     */
    private function isResourceGroupTypeId(int $typeId): bool
    {
        try {
            /** @var ItemEntityTypeFilterService $svc */
            $svc = app(ItemEntityTypeFilterService::class);
            return $svc->isTypeIdAllowedForEntity('resource', (int) $typeId, ItemEntityTypeFilterService::TYPE_MODE_ALL);
        } catch (\Throwable) {
            // fallback: si service indisponible, best effort via legacy
            return $this->isResourceType((int) $typeId);
        }
    }

    /**
     * Mémorise un item DofusDB rencontré dans un contexte "ressource" (recette/drops)
     * lorsque son typeId n'est pas encore autorisé.
     */
    private function rememberPendingResourceCandidate(
        int $typeId,
        int $itemId,
        string $context,
        ?string $sourceEntityType,
        ?int $sourceEntityDofusdbId,
        ?int $quantity
    ): void {
        try {
            PendingResourceTypeItem::firstOrCreate(
                [
                    'dofusdb_type_id' => $typeId,
                    'dofusdb_item_id' => $itemId,
                    'context' => $context,
                    'source_entity_type' => $sourceEntityType,
                    'source_entity_dofusdb_id' => $sourceEntityDofusdbId,
                ],
                [
                    'quantity' => $quantity,
                ]
            );
        } catch (\Throwable $e) {
            // On ne casse pas l'import pour un souci de log/mémoire
            Log::warning('Impossible d\'enregistrer un item en attente (typeId non autorisé)', [
                'type_id' => $typeId,
                'item_id' => $itemId,
                'context' => $context,
                'error' => $e->getMessage(),
            ]);
        }
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
        $resourcesConfig = $this->config['resources'] ?? [];
        $useDatabaseRegistry = (bool) ($resourcesConfig['use_database_registry'] ?? false);

        // Source of truth DB (recommandé) : resource_types.dofusdb_type_id + decision
        if ($useDatabaseRegistry) {
            $type = \App\Models\Type\ResourceType::touchDofusdbType($typeId);

            // pending => refus par défaut (l'utilisateur doit valider via UX)
            if ($type->decision === 'allowed') {
                return true;
            }
            if ($type->decision === 'blocked' || $type->decision === 'pending') {
                return false;
            }

            // Valeur inattendue => refus par défaut
            return false;
        }

        $allowlist = $resourcesConfig['type_ids_allowlist'] ?? [15, 35];
        $denylist = $resourcesConfig['type_ids_denylist'] ?? [];

        // Blacklist prioritaire
        if (in_array($typeId, $denylist, true)) {
            return false;
        }

        // Par défaut: allowlist stricte (on étend au fur et à mesure)
        $isAllowed = in_array($typeId, $allowlist, true);

        // Log des typeId inconnus pour faciliter l'extension de la liste
        if (!$isAllowed && ($resourcesConfig['log_unknown_type_ids'] ?? true)) {
            static $alreadyLogged = [];
            if (!isset($alreadyLogged[$typeId])) {
                $alreadyLogged[$typeId] = true;
                Log::warning('TypeId DofusDB inconnu pour les ressources (à ajouter à la allowlist ou denylist)', [
                    'type_id' => $typeId,
                    'hint' => 'config(scrapping.data_collect.resources.type_ids_allowlist|type_ids_denylist)',
                ]);
            }
        }

        return $isAllowed;
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
    public function collectSpell(int $dofusdbId, bool $includeLevels = true, bool $includeSummon = true, array $options = []): array
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
            $response = $this->fetchFromDofusDb($spellUrl, $options);
            
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
            $levelsResponse = $this->fetchFromDofusDb($levelsUrl, $options);
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
        
        $data = $this->fetchEntityFromConfigOrFallback('effect', $dofusdbId, 'effects', []);
        
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
     * Fetch d'une entité via config JSON si disponible, sinon fallback sur buildDofusDbUrl.
     *
     * @param string $configEntityKey ex: class|monster|item|panoply|effect
     * @param int $dofusdbId
     * @param string $fallbackEndpoint ex: breeds|monsters|items|item-sets|effects
     * @param array $options
     * @return array
     */
    private function fetchEntityFromConfigOrFallback(string $configEntityKey, int $dofusdbId, string $fallbackEndpoint, array $options): array
    {
        try {
            return $this->configCollector->fetchOne($configEntityKey, $dofusdbId, $options);
        } catch (\Throwable $e) {
            // Fallback: comportement historique
            $url = $this->buildDofusDbUrl($fallbackEndpoint, $dofusdbId);
            return $this->fetchFromDofusDb($url, $options);
        }
    }

    /**
     * Récupère les données depuis DofusDB avec gestion du cache
     * 
     * @param string $url URL à récupérer
     * @return array Données récupérées
     * @throws \Exception En cas d'erreur de récupération
     */
    private function fetchFromDofusDb(string $url, array $options = []): array
    {
        return $this->client->getJson($url, [
            'skip_cache' => (bool) ($options['skip_cache'] ?? false),
            'cache_ttl' => $options['cache_ttl'] ?? null,
        ]);
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
        $prefix = $cacheConfig['prefix'] ?? 'dofusdb_';
        
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
