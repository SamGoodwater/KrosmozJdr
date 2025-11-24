<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\DataCollect\DataCollectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur de test pour le service DataCollect
 * 
 * Permet de tester la collecte de données depuis l'API DofusDB
 * sans passer par l'orchestrateur complet.
 */
class DataCollectController extends Controller
{
    public function __construct(
        private DataCollectService $dataCollectService
    ) {}

    /**
     * Test de la disponibilité de l'API DofusDB
     */
    public function testApi(): JsonResponse
    {
        try {
            $isAvailable = $this->dataCollectService->isDofusDbAvailable();
            
            return response()->json([
                'success' => true,
                'message' => 'Test de l\'API DofusDB',
                'data' => [
                    'api_available' => $isAvailable,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du test de l\'API DofusDB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de l\'API',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de collecte d'une classe spécifique
     */
    public function testCollectClass(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer|min:1|max:19'
        ]);

        try {
            $classData = $this->dataCollectService->collectClass($request->id);
            
            return response()->json([
                'success' => true,
                'message' => "Classe {$request->id} collectée avec succès",
                'data' => [
                    'class_id' => $request->id,
                    'class_data' => $classData,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la collecte de la classe', [
                'class_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte de la classe {$request->id}",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de collecte d'un monstre spécifique
     */
    public function testCollectMonster(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer|min:1|max:5000'
        ]);

        try {
            $monsterData = $this->dataCollectService->collectMonster($request->id);
            
            return response()->json([
                'success' => true,
                'message' => "Monstre {$request->id} collecté avec succès",
                'data' => [
                    'monster_id' => $request->id,
                    'monster_data' => $monsterData,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la collecte du monstre', [
                'monster_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte du monstre {$request->id}",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de collecte d'un objet spécifique
     */
    public function testCollectItem(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer|min:1|max:30000'
        ]);

        try {
            $itemData = $this->dataCollectService->collectItem($request->id);
            
            return response()->json([
                'success' => true,
                'message' => "Objet {$request->id} collecté avec succès",
                'data' => [
                    'item_id' => $request->id,
                    'item_data' => $itemData,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la collecte de l\'objet', [
                'item_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte de l'objet {$request->id}",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de collecte d'un sort spécifique
     */
    public function testCollectSpell(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer|min:1|max:20000'
        ]);

        try {
            $spellData = $this->dataCollectService->collectSpell($request->id);
            
            return response()->json([
                'success' => true,
                'message' => "Sort {$request->id} collecté avec succès",
                'data' => [
                    'spell_id' => $request->id,
                    'spell_data' => $spellData,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la collecte du sort', [
                'spell_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte du sort {$request->id}",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de collecte d'un effet spécifique
     */
    public function testCollectEffect(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|integer|min:1|max:1000'
        ]);

        try {
            $effectData = $this->dataCollectService->collectEffect($request->id);
            
            return response()->json([
                'success' => true,
                'message' => "Effet {$request->id} collecté avec succès",
                'data' => [
                    'effect_id' => $request->id,
                    'effect_data' => $effectData,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la collecte de l\'effet', [
                'effect_id' => $request->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte de l'effet {$request->id}",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de nettoyage du cache
     */
    public function testClearCache(): JsonResponse
    {
        try {
            $clearedCount = $this->dataCollectService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache nettoyé avec succès',
                'data' => [
                    'cleared_entries' => $clearedCount,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du nettoyage du cache', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage du cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de collecte en lot d'objets par type
     */
    public function testCollectItemsByType(Request $request): JsonResponse
    {
        $request->validate([
            'type_id' => 'required|integer|min:1|max:205',
            'limit' => 'integer|min:1|max:100'
        ]);

        $limit = $request->get('limit', 5);

        try {
            // Utiliser une méthode de collecte en lot si disponible
            // Pour l'instant, on teste avec un seul objet
            $itemData = $this->dataCollectService->collectItem($request->type_id);
            
            return response()->json([
                'success' => true,
                'message' => "Objets de type {$request->type_id} collectés avec succès",
                'data' => [
                    'type_id' => $request->type_id,
                    'limit' => $limit,
                    'sample_item' => $itemData,
                    'timestamp' => now()->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la collecte d\'objets par type', [
                'type_id' => $request->type_id,
                'limit' => $limit,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte d'objets de type {$request->type_id}",
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
