<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
        private ScrappingOrchestrator $orchestrator
    ) {}

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
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ], 201);
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
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ], 201);
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
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ], 201);
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
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'],
                    'timestamp' => now()->toISOString(),
                ], 201);
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
                'entities.*.type' => ['required', 'string', 'in:class,monster,item,spell'],
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
        
        return $options;
    }
}

