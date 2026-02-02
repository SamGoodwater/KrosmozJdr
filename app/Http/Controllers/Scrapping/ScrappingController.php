<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Entity\Creature;
use App\Services\Scrapping\Config\ScrappingConfigLoader;
use App\Services\Scrapping\Constants\EntityLimits;
use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use App\Services\Scrapping\V2\Orchestrator\Orchestrator as ScrappingV2Orchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Contrôleur principal pour le système de scrapping
 * 
 * Utilise l'orchestrateur pour effectuer les imports complets
 * (collecte → conversion → intégration) depuis DofusDB vers KrosmozJDR.
 * 
 * @package App\Http\Controllers\Scrapping
 */
class ScrappingController extends Controller
{
    public function __construct(
        private ScrappingOrchestrator $orchestrator,
        private ScrappingConfigLoader $configLoader,
        private DataCollectService $dataCollectService
    ) {}

    /**
     * Récupère les métadonnées des types d'entités (limites, etc.)
     * 
     * @return JsonResponse
     */
    public function meta(): JsonResponse
    {
        // Refonte in-place : si une config JSON existe, elle devient source de vérité pour les métadonnées.
        // Fallback sur les limites historiques si une entité n'est pas encore configurée.
        $metaByType = [];

        try {
            foreach ($this->configLoader->listEntities('dofusdb') as $entity) {
                $cfg = $this->configLoader->loadEntity('dofusdb', $entity);
                $maxId = (int) (($cfg['meta']['maxId'] ?? 0) ?: 0);
                if ($maxId > 0) {
                    $metaByType[$entity] = [
                        'type' => $entity,
                        'maxId' => $maxId,
                        'label' => $cfg['label'] ?? $this->getEntityLabel($entity),
                    ];
                }
            }
        } catch (\Throwable $e) {
            // On ne casse pas l'UI si la config est absente/invalide
            Log::warning('Impossible de charger les métadonnées depuis la config scrapping', [
                'error' => $e->getMessage(),
            ]);
        }

        foreach (EntityLimits::LIMITS as $type => $maxId) {
            if (!isset($metaByType[$type])) {
                $metaByType[$type] = [
                    'type' => $type,
                    'maxId' => $maxId,
                    'label' => $this->getEntityLabel($type),
                ];
            }
        }

        $meta = array_values($metaByType);

        return response()->json([
            'success' => true,
            'data' => $meta,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Retourne le label d'un type d'entité
     */
    private function getEntityLabel(string $type): string
    {
        return match ($type) {
            'class' => 'Classe',
            'monster' => 'Monstre',
            'item' => 'Objet',
            'spell' => 'Sort',
            'panoply' => 'Panoplie',
            'resource' => 'Ressource',
            'consumable' => 'Consommable',
            'equipment' => 'Équipement',
            default => ucfirst($type),
        };
    }

    /**
     * Import d'une classe depuis DofusDB
     * 
     * @param Request $request
     * @param int $id ID de la classe dans DofusDB (1-19)
     * @return JsonResponse
     */
    public function importClass(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importClass($id, $options);
            
            if ($result['success']) {
                $response = [
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ];
                
                // Ajouter les relations importées si présentes
                if (isset($result['related']) && !empty($result['related'])) {
                    $response['related'] = $result['related'];
                }
                
                return response()->json($response, 201);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? 'Erreur inconnue',
                'timestamp' => now()->toISOString(),
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de classe via API', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de la classe',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'un monstre depuis DofusDB
     * 
     * @param Request $request
     * @param int $id ID du monstre dans DofusDB (1-5000)
     * @return JsonResponse
     */
    /**
     * Import d'un monstre : collecte legacy (avec spells/drops), puis pipeline V2 (conversion BDD + validation + intégration).
     * Les relations (sorts, drops) sont importées en cascade via l'orchestrateur legacy.
     */
    public function importMonster(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $includeRelations = $options['include_relations'] ?? true;
            $validateOnly = (bool) ($options['validate_only'] ?? false);
            $dryRun = (bool) ($options['dry_run'] ?? false);

            $rawData = $this->dataCollectService->collectMonster($id, $includeRelations, $includeRelations, $options);

            if ($rawData === []) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune donnée collectée pour ce monstre.',
                    'timestamp' => now()->toISOString(),
                ], 404);
            }

            $v2Orchestrator = ScrappingV2Orchestrator::default();
            $v2Options = [
                'convert' => true,
                'validate' => true,
                'integrate' => !$validateOnly && !$dryRun,
                'dry_run' => $dryRun,
                'force_update' => (bool) ($options['force_update'] ?? false),
                'ignore_unvalidated' => false,
            ];

            $v2Result = $v2Orchestrator->runOneWithRaw('dofusdb', 'monster', $rawData, $v2Options);

            if (!$v2Result->isSuccess()) {
                return response()->json([
                    'success' => false,
                    'message' => $v2Result->getMessage(),
                    'errors' => $v2Result->getValidationErrors(),
                    'timestamp' => now()->toISOString(),
                ], 400);
            }

            if ($validateOnly) {
                return response()->json([
                    'success' => true,
                    'message' => 'Validation uniquement (sans intégration)',
                    'data' => [
                        'action' => 'validated',
                        'raw' => $rawData,
                        'converted' => $v2Result->getConverted(),
                    ],
                    'related' => [],
                    'timestamp' => now()->toISOString(),
                ], 200);
            }

            $integrationResult = $v2Result->getIntegrationResult();
            $creatureId = $integrationResult?->getCreatureId();
            $monsterId = $integrationResult?->getMonsterId();
            $data = [
                'creature_id' => $creatureId,
                'monster_id' => $monsterId,
                'creature_action' => $integrationResult?->getCreatureAction() ?? '',
                'monster_action' => $integrationResult?->getMonsterAction() ?? '',
            ];

            $relatedResults = [];
            if ($includeRelations && $creatureId !== null) {
                $importedSpellIds = [];
                if (isset($rawData['spells']) && is_array($rawData['spells'])) {
                    foreach ($rawData['spells'] as $spellData) {
                        $spellId = $spellData['id'] ?? null;
                        if ($spellId) {
                            try {
                                $spellResult = $this->orchestrator->importSpell($spellId, array_merge($options, ['include_relations' => false]));
                                $importedSpellIds[] = $spellResult['data']['id'] ?? null;
                                $relatedResults[] = ['type' => 'spell', 'id' => $spellId, 'result' => $spellResult];
                            } catch (\Exception $e) {
                                Log::warning('Erreur import sort associé au monstre', ['monster_id' => $id, 'spell_id' => $spellId, 'error' => $e->getMessage()]);
                            }
                        }
                    }
                }
                $importedResourceIds = [];
                if (isset($rawData['drops']) && is_array($rawData['drops'])) {
                    foreach ($rawData['drops'] as $resourceData) {
                        $resourceId = $resourceData['id'] ?? null;
                        if ($resourceId) {
                            try {
                                $resourceResult = $this->orchestrator->importItem($resourceId, array_merge($options, ['include_relations' => false]));
                                $importedResourceIds[] = $resourceResult['data']['id'] ?? null;
                                $relatedResults[] = ['type' => 'resource', 'id' => $resourceId, 'result' => $resourceResult];
                            } catch (\Exception $e) {
                                Log::warning('Erreur import ressource associée au monstre', ['monster_id' => $id, 'resource_id' => $resourceId, 'error' => $e->getMessage()]);
                            }
                        }
                    }
                }
                $creature = Creature::find($creatureId);
                if ($creature) {
                    $validSpellIds = array_filter($importedSpellIds);
                    if ($validSpellIds !== []) {
                        $creature->spells()->sync($validSpellIds);
                    }
                    $validResourceIds = array_filter($importedResourceIds);
                    if ($validResourceIds !== []) {
                        $creature->resources()->sync(array_fill_keys($validResourceIds, ['quantity' => '1']));
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Monstre importé avec succès (pipeline V2)',
                'data' => $data,
                'related' => $relatedResults,
                'timestamp' => now()->toISOString(),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de monstre via API', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import du monstre',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'un objet depuis DofusDB
     * 
     * @param Request $request
     * @param int $id ID de l'objet dans DofusDB (1-30000)
     * @return JsonResponse
     */
    public function importItem(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importItem($id, $options);
            
            if ($result['success']) {
                $response = [
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ];
                
                // Ajouter les relations importées si présentes
                if (isset($result['related']) && !empty($result['related'])) {
                    $response['related'] = $result['related'];
                }
                
                return response()->json($response, 201);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? 'Erreur inconnue',
                'timestamp' => now()->toISOString(),
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import d\'objet via API', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de l\'objet',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'une ressource (via /items/{id}) avec validation type "ressource".
     */
    public function importResource(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importResource($id, $options);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Ressource importée avec succès',
                    'data' => $result['data'] ?? null,
                    'timestamp' => now()->toISOString(),
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Erreur lors de l\'import de la ressource',
                'error' => $result['error'] ?? 'Erreur inconnue',
                'timestamp' => now()->toISOString(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'import de ressource via API', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de la ressource',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'un consommable (alias de importItem).
     */
    public function importConsumable(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importConsumable($id, $options);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'] ?? 'Consommable importé avec succès',
                    'data' => $result['data'] ?? null,
                    'timestamp' => now()->toISOString(),
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Erreur lors de l\'import du consommable',
                'error' => $result['error'] ?? 'Erreur inconnue',
                'timestamp' => now()->toISOString(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Erreur lors de l\'import de consommable via API', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import du consommable',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'un sort depuis DofusDB
     * 
     * @param Request $request
     * @param int $id ID du sort dans DofusDB (1-20000)
     * @return JsonResponse
     */
    public function importSpell(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importSpell($id, $options);
            
            if ($result['success']) {
                $response = [
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ];
                
                // Ajouter les relations importées si présentes
                if (isset($result['related']) && !empty($result['related'])) {
                    $response['related'] = $result['related'];
                }
                
                return response()->json($response, 201);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? 'Erreur inconnue',
                'timestamp' => now()->toISOString(),
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de sort via API', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import du sort',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import en lot de plusieurs entités
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function importBatch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'entities' => ['required', 'array', 'min:1'],
                'entities.*.type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'entities.*.id' => ['required', 'integer', 'min:1'],
            ]);
            
            $options = $this->extractOptions($request);
            $entities = $request->input('entities');
            
            $result = $this->orchestrator->importBatch($entities, $options);
            
            $statusCode = $result['success'] ? 201 : 207; // 207 Multi-Status pour les résultats partiels
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] 
                    ? 'Tous les imports ont réussi' 
                    : 'Certains imports ont échoué',
                'summary' => $result['summary'],
                'results' => $result['results'],
                'timestamp' => now()->toISOString(),
            ], $statusCode);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
                'timestamp' => now()->toISOString(),
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import en lot via API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import en lot',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import avec fusion : compare l'existant Krosmoz et DofusDB, applique les choix par propriété.
     *
     * @param Request $request type, dofusdb_id, choices (optionnel, clés plates -> "krosmoz"|"dofusdb")
     * @return JsonResponse
     */
    public function importWithMerge(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:class,monster,item,spell,panoply,resource,consumable,equipment'],
                'dofusdb_id' => ['required', 'integer', 'min:1'],
                'choices' => ['nullable', 'array'],
                'choices.*' => ['string', 'in:krosmoz,dofusdb'],
            ]);

            $type = (string) $validated['type'];
            $dofusdbId = (int) $validated['dofusdb_id'];
            $choices = is_array($validated['choices'] ?? null) ? $validated['choices'] : [];
            $options = $this->extractOptions($request);

            $result = $this->orchestrator->importWithMerge($type, $dofusdbId, $choices, $options);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Import avec fusion impossible',
                    'error' => $result['error'] ?? null,
                    'timestamp' => now()->toISOString(),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Import avec fusion effectué.',
                'data' => $result['data'] ?? null,
                'timestamp' => now()->toISOString(),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
                'timestamp' => now()->toISOString(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur import avec fusion', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import avec fusion',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import d'une plage d'ID
     */
    public function importRange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(EntityLimits::LIMITS))],
            'start_id' => ['required', 'integer', 'min:1'],
            'end_id' => ['required', 'integer', 'min:1'],
        ]);

        $type = $validated['type'];
        $startId = (int) $validated['start_id'];
        $endId = (int) $validated['end_id'];

        if ($startId > $endId) {
            return response()->json([
                'success' => false,
                'message' => 'La valeur de début doit être inférieure ou égale à la valeur de fin',
            ], 422);
        }

        $maxId = EntityLimits::LIMITS[$type];
        if ($startId < 1 || $endId > $maxId) {
            return response()->json([
                'success' => false,
                'message' => "La plage doit être comprise entre 1 et {$maxId}",
            ], 422);
        }

        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importRange($type, $startId, $endId, $options);

            $statusCode = $result['success'] ? 201 : 207;

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success']
                    ? 'Import de plage terminé'
                    : 'Import de plage avec erreurs',
                'summary' => $result['summary'],
                'results' => $result['results'],
                'range' => [
                    'type' => $type,
                    'start' => $startId,
                    'end' => $endId,
                ],
                'timestamp' => now()->toISOString(),
            ], $statusCode);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de plage', [
                'type' => $type,
                'start_id' => $startId,
                'end_id' => $endId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import de la plage',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Import complet d'un type d'entité
     */
    public function importAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(EntityLimits::LIMITS))],
        ]);

        $type = $validated['type'];
        $maxId = EntityLimits::LIMITS[$type];

        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importRange($type, 1, $maxId, $options);

            $statusCode = $result['success'] ? 201 : 207;

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success']
                    ? 'Import complet terminé'
                    : 'Import complet avec erreurs',
                'summary' => $result['summary'],
                'results' => $result['results'],
                'range' => [
                    'type' => $type,
                    'start' => 1,
                    'end' => $maxId,
                ],
                'timestamp' => now()->toISOString(),
            ], $statusCode);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import complet', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import complet',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Prévisualisation d'une entité avant import
     */
    public function preview(string $type, int $id): JsonResponse
    {
        $normalizedType = strtolower($type);

        if (!array_key_exists($normalizedType, EntityLimits::LIMITS)) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'entité non supporté',
            ], 422);
        }

        $maxId = EntityLimits::LIMITS[$normalizedType];
        if ($id < 1 || $id > $maxId) {
            return response()->json([
                'success' => false,
                'message' => "L'identifiant doit être compris entre 1 et {$maxId}",
            ], 422);
        }

        $preview = $this->orchestrator->previewEntity($normalizedType, $id);

        if ($preview['success']) {
            return response()->json([
                'success' => true,
                'data' => $preview,
                'timestamp' => now()->toISOString(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $preview['message'] ?? 'Erreur lors de la prévisualisation',
            'error' => $preview['error'] ?? null,
            'timestamp' => now()->toISOString(),
        ], 500);
    }

    /**
     * Extrait les options depuis la requête
     * 
     * @param Request $request
     * @return array
     */
    private function extractOptions(Request $request): array
    {
        $options = [];
        
        // Options possibles
        if ($request->has('skip_cache')) {
            $options['skip_cache'] = $request->boolean('skip_cache');
        }
        
        if ($request->has('force_update')) {
            $options['force_update'] = $request->boolean('force_update');
        }
        
        if ($request->has('dry_run')) {
            $options['dry_run'] = $request->boolean('dry_run');
        }
        
        if ($request->has('validate_only')) {
            $options['validate_only'] = $request->boolean('validate_only');
        }

        // Option pour activer/désactiver le traitement des images (par défaut true)
        if ($request->has('with_images')) {
            $options['with_images'] = $request->boolean('with_images');
        } else {
            $options['with_images'] = true;
        }
        
        // Option pour inclure les relations (par défaut true)
        if ($request->has('include_relations')) {
            $options['include_relations'] = $request->boolean('include_relations');
        } else {
            $options['include_relations'] = true; // Par défaut, on inclut les relations
        }
        
        return $options;
    }
}

