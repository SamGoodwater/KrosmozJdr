<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Config\ScrappingConfigLoader;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
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
    private const ENTITY_LIMITS = [
        'class' => 19,
        'monster' => 5000,
        'item' => 30000,
        'spell' => 20000,
        'panoply' => 1000, // Estimation, à ajuster selon les données réelles
        // Aliases (items DofusDB)
        'resource' => 30000,
        'consumable' => 30000,
    ];
    public function __construct(
        private ScrappingOrchestrator $orchestrator,
        private ScrappingConfigLoader $configLoader
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

        foreach (self::ENTITY_LIMITS as $type => $maxId) {
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
    public function importMonster(Request $request, int $id): JsonResponse
    {
        try {
            $options = $this->extractOptions($request);
            $result = $this->orchestrator->importMonster($id, $options);
            
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
            Log::error('Erreur lors de l\'import de monstre via API', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
        // Même source DofusDB (/items). La conversion/intégration décide de la table cible.
        return $this->importItem($request, $id);
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
                'entities.*.type' => ['required', 'string', 'in:class,monster,item,spell,panoply'],
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
     * Import d'une plage d'ID
     */
    public function importRange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(self::ENTITY_LIMITS))],
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

        $maxId = self::ENTITY_LIMITS[$type];
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
            'type' => ['required', 'string', Rule::in(array_keys(self::ENTITY_LIMITS))],
        ]);

        $type = $validated['type'];
        $maxId = self::ENTITY_LIMITS[$type];

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

        if (!array_key_exists($normalizedType, self::ENTITY_LIMITS)) {
            return response()->json([
                'success' => false,
                'message' => 'Type d\'entité non supporté',
            ], 422);
        }

        $maxId = self::ENTITY_LIMITS[$normalizedType];
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

