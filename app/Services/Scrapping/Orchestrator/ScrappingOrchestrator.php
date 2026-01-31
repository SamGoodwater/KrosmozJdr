<?php

namespace App\Services\Scrapping\Orchestrator;

use App\Services\Scrapping\Config\ScrappingConfigLoader;
use App\Services\Scrapping\Config\FormatterRegistry;
use App\Services\Scrapping\Config\ConfigDrivenConverter;
use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\DataCollect\ItemEntityTypeFilterService;
use App\Services\Scrapping\DataConversion\DataConversionService;
use App\Services\Scrapping\DataIntegration\DataIntegrationService;
use App\Models\Entity\Panoply;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

/**
 * Service d'orchestration du processus de scrapping
 * 
 * Coordonne l'ensemble du processus de récupération, conversion et intégration
 * des données depuis des sites externes vers KrosmozJDR.
 * 
 * @package App\Services\Scrapping\Orchestrator
 */
class ScrappingOrchestrator
{
    /**
     * Mapping des méthodes par type d'entité
     */
    private const ENTITY_METHODS = [
        'class' => [
            'collect' => 'collectClass',
            'convert' => 'convertClass',
            'import' => 'importClass',
        ],
        'monster' => [
            'collect' => 'collectMonster',
            'convert' => 'convertMonster',
            'import' => 'importMonster',
        ],
        'item' => [
            'collect' => 'collectItem',
            'convert' => 'convertItem',
            'import' => 'importItem',
        ],
        'spell' => [
            'collect' => 'collectSpell',
            'convert' => 'convertSpell',
            'import' => 'importSpell',
        ],
        'panoply' => [
            'collect' => 'collectPanoply',
            'convert' => 'convertPanoply',
            'import' => 'importPanoply',
        ],
        // "resource" est techniquement un item DofusDB (endpoint /items),
        // mais on le traite séparément pour éviter d'importer des IDs non-ressources.
        'resource' => [
            'collect' => 'collectResource',
            'convert' => 'convertResource',
            'import' => 'importResource',
        ],
        // "consumable" est aussi un item DofusDB (endpoint /items).
        'consumable' => [
            'collect' => 'collectConsumable',
            'convert' => 'convertConsumable',
            'import' => 'importConsumable',
        ],
        // "equipment" = items hors resources/consumables (par défaut).
        'equipment' => [
            'collect' => 'collectEquipment',
            'convert' => 'convertEquipment',
            'import' => 'importEquipment',
        ],
    ];
    /**
     * Constructeur du service d'orchestration
     */
    public function __construct(
        private DataCollectService $dataCollectService,
        private DataConversionService $dataConversionService,
        private DataIntegrationService $dataIntegrationService,
        private ScrappingConfigLoader $configLoader,
        private ItemEntityTypeFilterService $itemEntityTypeFilters,
    ) {}

    /**
     * Conversion via config JSON quand disponible, sinon fallback legacy.
     *
     * @param string $normalizedType
     * @param array<string,mixed> $rawData
     * @return array<string,mixed>
     */
    private function convertUsingConfigOrLegacy(string $normalizedType, array $rawData): array
    {
        try {
            $entityCfg = $this->configLoader->loadEntity('dofusdb', $normalizedType);
            $registry = FormatterRegistry::fromDefaultPath();
            $client = app(DofusDbClient::class);
            $effects = new DofusDbEffectCatalog($client);
            $converter = new ConfigDrivenConverter($registry, $effects);

            return $converter->convert($entityCfg, $rawData, [
                'lang' => 'fr',
                'apply_side_effects' => false,
            ]);
        } catch (\Throwable $e) {
            $methods = $this->getEntityMethods($normalizedType);
            return $this->dataConversionService->{$methods['convert']}($rawData);
        }
    }

    /**
     * Import d'une classe depuis DofusDB avec ses sorts associés
     * 
     * @param int $dofusdbId ID de la classe dans DofusDB
     * @param array $options Options d'import (include_relations: bool pour activer l'import des relations)
     * @return array Résultat de l'import
     */
    public function importClass(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import classe', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        $includeRelations = $options['include_relations'] ?? true;
        
        try {
            // 1. Collecte des données depuis DofusDB (avec sorts si demandé)
            $rawData = $this->dataCollectService->collectClass($dofusdbId, $includeRelations, $options);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->dataConversionService->convertClass($rawData);

            // Option: validation uniquement (collect + conversion, pas d'écriture)
            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                    'related' => [],
                    'message' => 'Validation uniquement (sans intégration)',
                ];
            }
            
            // 3. Intégration dans la base KrosmozJDR (support options: dry_run, force_update, with_images)
            $result = $this->dataIntegrationService->integrateClass($convertedData, $options);
            
            // 4. Import en cascade des sorts associés
            $relatedResults = [];
            $importedSpellIds = [];
            if ($includeRelations && isset($rawData['spells']) && is_array($rawData['spells'])) {
                foreach ($rawData['spells'] as $spellData) {
                    $spellId = $spellData['id'] ?? null;
                    if ($spellId) {
                        try {
                            $spellResult = $this->importSpell($spellId, array_merge($options, ['include_relations' => false])); // Ne pas importer le monstre invoqué pour éviter la récursion
                            $importedSpellIds[] = $spellResult['data']['id'] ?? null;
                            $relatedResults[] = [
                                'type' => 'spell',
                                'id' => $spellId,
                                'result' => $spellResult
                            ];
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors de l\'import du sort associé à la classe', [
                                'class_id' => $dofusdbId,
                                'spell_id' => $spellId,
                                'error' => $e->getMessage()
                            ]);
                            $relatedResults[] = [
                                'type' => 'spell',
                                'id' => $spellId,
                                'success' => false,
                                'error' => $e->getMessage()
                            ];
                        }
                    }
                }
            }
            
            // 5. Créer les relations après l'import en cascade
            if ($includeRelations && !empty($importedSpellIds)) {
                $class = \App\Models\Entity\Classe::find($result['id']);
                if ($class) {
                    // Filtrer les IDs valides
                    $validSpellIds = array_filter($importedSpellIds);
                    if (!empty($validSpellIds)) {
                        $class->spells()->sync($validSpellIds);
                        Log::info('Relations créées après import en cascade', [
                            'class_id' => $class->id,
                            'spell_count' => count($validSpellIds)
                        ]);
                    }
                }
            }
            
            Log::info('Import classe terminé avec succès', [
                'dofusdb_id' => $dofusdbId,
                'related_count' => count($relatedResults)
            ]);
            
            return [
                'success' => true,
                'data' => $result,
                'related' => $relatedResults,
                'message' => 'Classe importée avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de classe', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import de la classe'
            ];
        }
    }

    /**
     * Import d'un monstre depuis DofusDB avec ses sorts et ressources associés
     * 
     * @param int $dofusdbId ID du monstre dans DofusDB
     * @param array $options Options d'import (include_relations: bool pour activer l'import des relations)
     * @return array Résultat de l'import
     */
    public function importMonster(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import monstre', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        $includeRelations = $options['include_relations'] ?? true;
        
        try {
            // 1. Collecte des données depuis DofusDB (avec sorts et drops si demandé)
            $rawData = $this->dataCollectService->collectMonster($dofusdbId, $includeRelations, $includeRelations, $options);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->convertUsingConfigOrLegacy('monster', $rawData);

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                    'related' => [],
                    'message' => 'Validation uniquement (sans intégration)',
                ];
            }
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateMonster($convertedData, $options);
            
            // 4. Import en cascade des sorts et ressources associés
            $relatedResults = [];
            $importedSpellIds = [];
            $importedResourceIds = [];
            
            // Import des sorts
            if ($includeRelations && isset($rawData['spells']) && is_array($rawData['spells'])) {
                foreach ($rawData['spells'] as $spellData) {
                    $spellId = $spellData['id'] ?? null;
                    if ($spellId) {
                        try {
                            $spellResult = $this->importSpell($spellId, array_merge($options, ['include_relations' => false]));
                            $importedSpellIds[] = $spellResult['data']['id'] ?? null;
                            $relatedResults[] = [
                                'type' => 'spell',
                                'id' => $spellId,
                                'result' => $spellResult
                            ];
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors de l\'import du sort associé au monstre', [
                                'monster_id' => $dofusdbId,
                                'spell_id' => $spellId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            // Import des ressources (drops)
            if ($includeRelations && isset($rawData['drops']) && is_array($rawData['drops'])) {
                foreach ($rawData['drops'] as $resourceData) {
                    $resourceId = $resourceData['id'] ?? null;
                    if ($resourceId) {
                        try {
                            $resourceResult = $this->importItem($resourceId, array_merge($options, ['include_relations' => false]));
                            $importedResourceIds[] = $resourceResult['data']['id'] ?? null;
                            $relatedResults[] = [
                                'type' => 'resource',
                                'id' => $resourceId,
                                'result' => $resourceResult
                            ];
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors de l\'import de la ressource associée au monstre', [
                                'monster_id' => $dofusdbId,
                                'resource_id' => $resourceId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            // 5. Créer les relations après l'import en cascade
            if ($includeRelations) {
                $creature = \App\Models\Entity\Creature::find($result['creature_id'] ?? null);
                if ($creature) {
                    // Synchroniser les sorts
                    $validSpellIds = array_filter($importedSpellIds);
                    if (!empty($validSpellIds)) {
                        $creature->spells()->sync($validSpellIds);
                        Log::info('Relations sorts créées après import en cascade', [
                            'creature_id' => $creature->id,
                            'spell_count' => count($validSpellIds)
                        ]);
                    }
                    
                    // Synchroniser les ressources (avec quantités si disponibles)
                    $validResourceIds = array_filter($importedResourceIds);
                    if (!empty($validResourceIds)) {
                        $resourceData = [];
                        foreach ($validResourceIds as $resourceId) {
                            $resourceData[$resourceId] = ['quantity' => '1']; // Quantité par défaut
                        }
                        $creature->resources()->sync($resourceData);
                        Log::info('Relations ressources créées après import en cascade', [
                            'creature_id' => $creature->id,
                            'resource_count' => count($resourceData)
                        ]);
                    }
                }
            }
            
            Log::info('Import monstre terminé avec succès', [
                'dofusdb_id' => $dofusdbId,
                'related_count' => count($relatedResults)
            ]);
            
            return [
                'success' => true,
                'data' => [
                    'creature_id' => $result['creature_id'] ?? null,
                    'monster_id' => $result['monster_id'] ?? null,
                    'creature_action' => $result['creature_action'] ?? null,
                    'monster_action' => $result['monster_action'] ?? null,
                ],
                'related' => $relatedResults,
                'message' => 'Monstre importé avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de monstre', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import du monstre'
            ];
        }
    }

    /**
     * Import d'un objet depuis DofusDB avec sa recette associée
     * 
     * @param int $dofusdbId ID de l'objet dans DofusDB
     * @param array $options Options d'import (include_relations: bool pour activer l'import des relations)
     * @return array Résultat de l'import
     */
    public function importItem(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import objet', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        $includeRelations = $options['include_relations'] ?? true;
        
        try {
            // 1. Collecte des données depuis DofusDB (avec recette si demandé)
            $rawData = $this->dataCollectService->collectItem($dofusdbId, $includeRelations, $options);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->convertUsingConfigOrLegacy('item', $rawData);

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                    'related' => [],
                    'message' => 'Validation uniquement (sans intégration)',
                ];
            }
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateItem($convertedData, $options);
            
            // 4. Import en cascade des ressources de la recette
            $relatedResults = [];
            if ($includeRelations && isset($rawData['recipe']) && is_array($rawData['recipe'])) {
                foreach ($rawData['recipe'] as $recipeItem) {
                    $resourceData = $recipeItem['resource'] ?? $recipeItem;
                    $resourceId = $resourceData['id'] ?? null;
                    if ($resourceId) {
                        try {
                            $resourceResult = $this->importItem($resourceId, array_merge($options, ['include_relations' => false])); // Ne pas inclure la recette pour éviter la récursion infinie
                            $relatedResults[] = [
                                'type' => 'resource',
                                'id' => $resourceId,
                                'quantity' => $recipeItem['quantity'] ?? 1,
                                'result' => $resourceResult
                            ];
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors de l\'import de la ressource de la recette', [
                                'item_id' => $dofusdbId,
                                'resource_id' => $resourceId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            Log::info('Import objet terminé avec succès', [
                'dofusdb_id' => $dofusdbId,
                'related_count' => count($relatedResults)
            ]);
            
            return [
                'success' => true,
                'data' => $result,
                'related' => $relatedResults,
                'message' => 'Objet importé avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import d\'objet', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import de l\'objet'
            ];
        }
    }

    /**
     * Collecte une ressource depuis DofusDB (via /items/{id}) et valide son type.
     */
    public function collectResource(int $dofusdbId): array
    {
        $raw = $this->dataCollectService->collectItem($dofusdbId, false, []);
        $typeId = (int) ($raw['typeId'] ?? 0);

        if (!$typeId || !$this->itemEntityTypeFilters->isTypeIdAllowedForEntity('resource', $typeId)) {
            throw new \Exception("L'item DofusDB #{$dofusdbId} n'est pas une ressource autorisée (typeId={$typeId}).");
        }

        return $raw;
    }

    /**
     * Conversion d'une ressource (même pipeline que convertItem).
     */
    public function convertResource(array $rawData): array
    {
        return $this->convertUsingConfigOrLegacy('resource', $rawData);
    }

    /**
     * Import d'une ressource (collecte → conversion → intégration).
     */
    public function importResource(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import ressource', ['dofusdb_id' => $dofusdbId, 'options' => $options]);

        try {
            $rawData = $this->collectResource($dofusdbId);
            $convertedData = $this->convertResource($rawData);

            // Safety: on s'assure qu'on va bien dans la table resources
            if (($convertedData['type'] ?? null) !== 'resource') {
                return [
                    'success' => false,
                    'message' => "Import ignoré : l'item #{$dofusdbId} n'est pas mappé en ressource.",
                    'error' => 'not_a_resource',
                    'data' => ['dofusdb_id' => $dofusdbId],
                ];
            }

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'message' => 'Validation uniquement (sans intégration)',
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                ];
            }

            $result = $this->dataIntegrationService->integrateItem($convertedData, $options);

            return [
                'success' => true,
                'message' => 'Ressource importée avec succès',
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur import ressource', ['dofusdb_id' => $dofusdbId, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'import de la ressource',
                'error' => $e->getMessage(),
                'data' => ['dofusdb_id' => $dofusdbId],
            ];
        }
    }

    /**
     * Collecte un consommable depuis DofusDB (via /items/{id}) et valide son type.
     */
    public function collectConsumable(int $dofusdbId): array
    {
        $raw = $this->dataCollectService->collectItem($dofusdbId, false, []);
        $typeId = (int) ($raw['typeId'] ?? 0);

        if (!$typeId || !$this->itemEntityTypeFilters->isTypeIdAllowedForEntity('consumable', $typeId)) {
            throw new \Exception("L'item DofusDB #{$dofusdbId} n'est pas un consommable autorisé (typeId={$typeId}).");
        }

        return $raw;
    }

    /**
     * Conversion d'un consommable (même pipeline que item, mais config dédiée).
     */
    public function convertConsumable(array $rawData): array
    {
        return $this->convertUsingConfigOrLegacy('consumable', $rawData);
    }

    /**
     * Import d'un consommable (collecte → conversion → intégration).
     */
    public function importConsumable(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import consommable', ['dofusdb_id' => $dofusdbId, 'options' => $options]);

        try {
            $rawData = $this->collectConsumable($dofusdbId);
            $convertedData = $this->convertConsumable($rawData);

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'message' => 'Validation uniquement (sans intégration)',
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                ];
            }

            $result = $this->dataIntegrationService->integrateItem($convertedData, $options);

            return [
                'success' => true,
                'message' => 'Consommable importé avec succès',
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur import consommable', ['dofusdb_id' => $dofusdbId, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'import du consommable',
                'error' => $e->getMessage(),
                'data' => ['dofusdb_id' => $dofusdbId],
            ];
        }
    }

    /**
     * Collecte un équipement depuis DofusDB (via /items/{id}) et exclut ressources/consumables.
     */
    public function collectEquipment(int $dofusdbId): array
    {
        $raw = $this->dataCollectService->collectItem($dofusdbId, false, []);
        $typeId = (int) ($raw['typeId'] ?? 0);

        if (!$typeId) {
            throw new \Exception("Impossible de déterminer le typeId de l'item DofusDB #{$dofusdbId}.");
        }

        // 1) Exclure explicitement ressources + consommables (selon registry/config)
        if ($this->itemEntityTypeFilters->isTypeIdAllowedForEntity('resource', $typeId)) {
            throw new \Exception("L'item DofusDB #{$dofusdbId} est une ressource (typeId={$typeId}).");
        }

        if ($this->itemEntityTypeFilters->isTypeIdAllowedForEntity('consumable', $typeId)) {
            throw new \Exception("L'item DofusDB #{$dofusdbId} est un consommable (typeId={$typeId}).");
        }

        // 2) Vérifier qu'il est bien autorisé comme équipement (selon config superType)
        if (!$this->itemEntityTypeFilters->isTypeIdAllowedForEntity('equipment', $typeId)) {
            throw new \Exception("L'item DofusDB #{$dofusdbId} n'est pas un équipement autorisé (typeId={$typeId}).");
        }

        return $raw;
    }

    /**
     * Conversion d'un équipement (config dédiée).
     */
    public function convertEquipment(array $rawData): array
    {
        return $this->convertUsingConfigOrLegacy('equipment', $rawData);
    }

    /**
     * Import d'un équipement (collecte → conversion → intégration).
     */
    public function importEquipment(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import équipement', ['dofusdb_id' => $dofusdbId, 'options' => $options]);

        try {
            $rawData = $this->collectEquipment($dofusdbId);
            $convertedData = $this->convertEquipment($rawData);

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'message' => 'Validation uniquement (sans intégration)',
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                ];
            }

            $result = $this->dataIntegrationService->integrateItem($convertedData, $options);

            return [
                'success' => true,
                'message' => 'Équipement importé avec succès',
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur import équipement', ['dofusdb_id' => $dofusdbId, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'import de l\'équipement',
                'error' => $e->getMessage(),
                'data' => ['dofusdb_id' => $dofusdbId],
            ];
        }
    }

    /**
     * Import d'un sort depuis DofusDB avec son monstre invoqué si applicable
     * 
     * @param int $dofusdbId ID du sort dans DofusDB
     * @param array $options Options d'import (include_relations: bool pour activer l'import des relations)
     * @return array Résultat de l'import
     */
    public function importSpell(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import sort', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        $includeRelations = $options['include_relations'] ?? true;
        
        try {
            // 1. Collecte des données depuis DofusDB (avec monstre invoqué si demandé)
            $rawData = $this->dataCollectService->collectSpell($dofusdbId, true, $includeRelations, $options);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->convertUsingConfigOrLegacy('spell', $rawData);

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                    'related' => [],
                    'message' => 'Validation uniquement (sans intégration)',
                ];
            }
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateSpell($convertedData, $options);
            
            // 4. Import en cascade du monstre invoqué (si c'est un sort d'invocation)
            $relatedResults = [];
            $importedMonsterId = null;
            
            if ($includeRelations && isset($rawData['summon']) && is_array($rawData['summon'])) {
                $summonId = $rawData['summon']['id'] ?? null;
                if ($summonId) {
                    try {
                        $summonResult = $this->importMonster($summonId, array_merge($options, ['include_relations' => false])); // Ne pas inclure les sorts/drops pour éviter la récursion
                        $importedMonsterId = $summonResult['data']['monster_id'] ?? null;
                        $relatedResults[] = [
                            'type' => 'monster',
                            'id' => $summonId,
                            'result' => $summonResult
                        ];
                    } catch (\Exception $e) {
                        Log::warning('Erreur lors de l\'import du monstre invoqué', [
                            'spell_id' => $dofusdbId,
                            'monster_id' => $summonId,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            
            // 5. Créer les relations après l'import en cascade
            if ($includeRelations && $importedMonsterId) {
                $spell = \App\Models\Entity\Spell::find($result['id'] ?? null);
                $monster = \App\Models\Entity\Monster::find($importedMonsterId);
                
                if ($spell && $monster) {
                    $spell->monsters()->sync([$monster->id]);
                    Log::info('Relation invocation créée après import en cascade', [
                        'spell_id' => $spell->id,
                        'monster_id' => $monster->id
                    ]);
                }
            }
            
            Log::info('Import sort terminé avec succès', [
                'dofusdb_id' => $dofusdbId,
                'related_count' => count($relatedResults)
            ]);
            
            return [
                'success' => true,
                'data' => $result,
                'related' => $relatedResults,
                'message' => 'Sort importé avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de sort', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import du sort'
            ];
        }
    }

    /**
     * Import en lot de plusieurs entités
     * 
     * @param array $entities Liste des entités à importer
     * @param array $options Options d'import
     * @return array Résultat de l'import en lot
     */
    public function importBatch(array $entities, array $options = []): array
    {
        Log::info('Début import en lot', ['entities_count' => count($entities), 'options' => $options]);
        
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($entities as $entity) {
            try {
                $method = 'import' . ucfirst($entity['type']);
                $result = $this->$method($entity['id'], $options);
                
                // Ajouter l'entité au résultat pour l'affichage
                $result['entity'] = $entity;
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                
                $results[] = $result;
                
            } catch (\Exception $e) {
                $errorCount++;
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'entity' => $entity
                ];
            }
        }
        
        Log::info('Import en lot terminé', [
            'total' => count($entities),
            'success' => $successCount,
            'errors' => $errorCount
        ]);
        
        return [
            'success' => $errorCount === 0,
            'results' => $results,
            'summary' => [
                'total' => count($entities),
                'success' => $successCount,
                'errors' => $errorCount
            ]
        ];
    }

    /**
     * Import d'une plage d'identifiants (inclusifs)
     */
    public function importRange(string $type, int $startId, int $endId, array $options = []): array
    {
        $normalizedType = strtolower($type);
        $this->getEntityMethods($normalizedType); // Validation du type

        $entities = [];

        for ($id = $startId; $id <= $endId; $id++) {
            $entities[] = [
                'type' => $normalizedType,
                'id' => $id,
            ];
        }

        return $this->importBatch($entities, $options);
    }

    /**
     * Prévisualisation d'une entité avant import
     */
    public function previewEntity(string $type, int $dofusdbId): array
    {
        $normalizedType = strtolower($type);
        $methods = $this->getEntityMethods($normalizedType);

        try {
            $collectMethod = $methods['collect'];
            if (method_exists($this->dataCollectService, $collectMethod)) {
                $rawData = $this->dataCollectService->{$collectMethod}($dofusdbId);
            } elseif (method_exists($this, $collectMethod)) {
                $rawData = $this->{$collectMethod}($dofusdbId);
            } else {
                throw new \Exception("Méthode de collecte introuvable: {$collectMethod}");
            }
            $convertedData = $this->convertUsingConfigOrLegacy($normalizedType, $rawData);

            $existing = $this->dataIntegrationService->findExistingEntity($normalizedType, $convertedData);

            return [
                'success' => true,
                'raw' => $rawData,
                'converted' => $convertedData,
                'existing' => $existing,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la prévisualisation', [
                'type' => $normalizedType,
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Impossible de prévisualiser cette entité',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Import avec fusion : prévisualise, fusionne selon les choix (Krosmoz vs DofusDB par propriété), intègre.
     *
     * @param string $type Type d'entité (class, monster, item, spell, etc.)
     * @param int $dofusdbId ID DofusDB
     * @param array<string,string> $choices Clés plates (dot notation) -> "krosmoz"|"dofusdb"
     * @param array<string,mixed> $options Options d'import (with_images, force_update, etc.)
     * @return array{success:bool, message?:string, error?:string, data?:array}
     */
    public function importWithMerge(string $type, int $dofusdbId, array $choices, array $options = []): array
    {
        $normalizedType = strtolower($type);

        try {
            $preview = $this->previewEntity($normalizedType, $dofusdbId);
            if (!$preview['success']) {
                return [
                    'success' => false,
                    'message' => $preview['message'] ?? 'Prévisualisation impossible',
                    'error' => $preview['error'] ?? null,
                ];
            }

            $converted = $preview['converted'] ?? [];
            $existing = $preview['existing'] ?? null;

            if (!$existing || !isset($existing['record'])) {
                // Pas d'existant : import classique
                return $this->importEntity($normalizedType, $dofusdbId, $options);
            }

            $merged = $this->mergeFromChoices($converted, $existing['record'], $choices);
            return $this->integrateMergedEntity($normalizedType, $merged, $options);
        } catch (\Throwable $e) {
            Log::error('Erreur import avec fusion', [
                'type' => $normalizedType,
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Import avec fusion impossible',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fusionne converted et existing.record selon les choix (clés plates -> krosmoz|dofusdb).
     *
     * @param array<string,mixed> $converted Données converties DofusDB
     * @param array<string,mixed> $existingRecord Enregistrement existant Krosmoz
     * @param array<string,string> $choices Choix par clé plate
     * @return array<string,mixed> Données fusionnées (structure nested)
     */
    private function mergeFromChoices(array $converted, array $existingRecord, array $choices): array
    {
        $convertedFlat = Arr::dot($converted);
        $existingFlat = Arr::dot($existingRecord);
        // Partir de la structure convertie (attendue par l'intégration), puis surcharger par l'existant où choix = krosmoz
        $mergedFlat = $convertedFlat;
        foreach ($existingFlat as $key => $value) {
            if (($choices[$key] ?? 'dofusdb') === 'krosmoz') {
                $mergedFlat[$key] = $value;
            }
        }

        $merged = [];
        foreach ($mergedFlat as $key => $value) {
            Arr::set($merged, $key, $value);
        }
        return $merged;
    }

    /**
     * Appelle l'intégration avec les données déjà fusionnées (sans collecte ni conversion).
     *
     * @param string $type Type d'entité
     * @param array<string,mixed> $mergedData Données fusionnées
     * @param array<string,mixed> $options Options d'import
     * @return array{success:bool, data?:array, message?:string, error?:string}
     */
    private function integrateMergedEntity(string $type, array $mergedData, array $options): array
    {
        $type = strtolower($type);
        try {
            $result = match ($type) {
                'class' => $this->dataIntegrationService->integrateClass($mergedData, $options),
                'monster' => $this->dataIntegrationService->integrateMonster($mergedData, $options),
                'item', 'resource', 'consumable', 'equipment' => $this->dataIntegrationService->integrateItem($mergedData, $options),
                'spell' => $this->dataIntegrationService->integrateSpell($mergedData, $options),
                'panoply' => $this->dataIntegrationService->integratePanoply($mergedData, $options),
                default => throw new \InvalidArgumentException("Type non supporté pour intégration fusionnée: {$type}"),
            };
            return [
                'success' => true,
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur intégration fusionnée', ['type' => $type, 'error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Intégration impossible',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Import d'une entité par type et ID DofusDB (délègue aux méthodes dédiées).
     */
    private function importEntity(string $type, int $dofusdbId, array $options): array
    {
        return match ($type) {
            'class' => $this->importClass($dofusdbId, $options),
            'monster' => $this->importMonster($dofusdbId, $options),
            'item' => $this->importItem($dofusdbId, $options),
            'resource' => $this->importResource($dofusdbId, $options),
            'consumable' => $this->importConsumable($dofusdbId, $options),
            'equipment' => $this->importEquipment($dofusdbId, $options),
            'spell' => $this->importSpell($dofusdbId, $options),
            'panoply' => $this->importPanoply($dofusdbId, $options),
            default => [
                'success' => false,
                'message' => "Type non supporté: {$type}",
                'error' => "Type non supporté: {$type}",
            ],
        };
    }

    /**
     * Import d'une panoplie depuis DofusDB avec ses items associés
     * 
     * @param int $dofusdbId ID de la panoplie dans DofusDB
     * @param array $options Options d'import (include_relations: bool pour activer l'import des relations)
     * @return array Résultat de l'import
     */
    public function importPanoply(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import panoplie', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        $includeRelations = $options['include_relations'] ?? true;
        
        try {
            // 1. Collecte des données depuis DofusDB (avec items si demandé)
            $rawData = $this->dataCollectService->collectPanoply($dofusdbId, $includeRelations);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->dataConversionService->convertPanoply($rawData);

            if ((bool) ($options['validate_only'] ?? false)) {
                return [
                    'success' => true,
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $convertedData,
                    ],
                    'related' => [],
                    'message' => 'Validation uniquement (sans intégration)',
                ];
            }
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integratePanoply($convertedData, $options);
            
            // 4. Import en cascade des items associés
            $relatedResults = [];
            $importedItemIds = [];
            
            if ($includeRelations && isset($rawData['items']) && is_array($rawData['items'])) {
                foreach ($rawData['items'] as $itemData) {
                    $itemId = is_array($itemData) ? ($itemData['id'] ?? null) : $itemData;
                    if ($itemId) {
                        try {
                            $itemResult = $this->importItem($itemId, array_merge($options, ['include_relations' => false])); // Ne pas inclure la recette pour éviter la récursion
                            $importedItemIds[] = $itemResult['data']['id'] ?? null;
                            $relatedResults[] = [
                                'type' => 'item',
                                'id' => $itemId,
                                'result' => $itemResult
                            ];
                        } catch (\Exception $e) {
                            Log::warning('Erreur lors de l\'import de l\'item associé à la panoplie', [
                                'panoply_id' => $dofusdbId,
                                'item_id' => $itemId,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
            
            // 5. Créer les relations après l'import en cascade
            if ($includeRelations && !empty($importedItemIds)) {
                $panoply = Panoply::find($result['id'] ?? null);
                if ($panoply) {
                    // Filtrer les IDs valides
                    $validItemIds = array_filter($importedItemIds);
                    if (!empty($validItemIds)) {
                        $panoply->items()->sync($validItemIds);
                        Log::info('Relations items créées après import en cascade', [
                            'panoply_id' => $panoply->id,
                            'item_count' => count($validItemIds)
                        ]);
                    }
                }
            }
            
            Log::info('Import panoplie terminé avec succès', [
                'dofusdb_id' => $dofusdbId,
                'related_count' => count($relatedResults)
            ]);
            
            return [
                'success' => true,
                'data' => [
                    'id' => $result['id'] ?? null,
                    'action' => $result['action'] ?? null,
                ],
                'related' => $relatedResults,
                'message' => 'Panoplie importée avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de panoplie', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import de la panoplie'
            ];
        }
    }

    /**
     * Récupère les méthodes associées à un type d'entité
     *
     * @throws \InvalidArgumentException
     */
    private function getEntityMethods(string $type): array
    {
        $normalized = strtolower($type);

        if (!isset(self::ENTITY_METHODS[$normalized])) {
            throw new \InvalidArgumentException("Type d'entité non supporté : {$type}");
        }

        return self::ENTITY_METHODS[$normalized];
    }

    /**
     * @return array<int,int>
     */
}
