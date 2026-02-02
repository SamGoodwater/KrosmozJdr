<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Services\Scrapping\Core\Collect\CollectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur de test pour la collecte (CollectService).
 *
 * Permet de tester la collecte de données depuis l'API DofusDB.
 */
class DataCollectController extends Controller
{
    public function __construct(
        private CollectService $collectService
    ) {}

    /**
     * Test de la disponibilité de l'API DofusDB
     */
    public function testApi(): JsonResponse
    {
        try {
            $response = Http::timeout(5)->get('https://api.dofusdb.fr/breeds/1');
            $isAvailable = $response->successful();

            return response()->json([
                'success' => true,
                'message' => 'Test de l\'API DofusDB',
                'data' => [
                    'api_available' => $isAvailable,
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du test de l\'API DofusDB', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de l\'API',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test de collecte d'une classe spécifique (V2 breed)
     */
    public function testCollectClass(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer|min:1|max:19']);

        try {
            $classData = $this->collectService->fetchOne('dofusdb', 'breed', (int) $request->id);
            return response()->json([
                'success' => true,
                'message' => "Classe {$request->id} collectée avec succès",
                'data' => ['class_id' => $request->id, 'class_data' => $classData, 'timestamp' => now()->toISOString()],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur collecte classe', ['class_id' => $request->id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte de la classe {$request->id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test de collecte d'un monstre spécifique (V2)
     */
    public function testCollectMonster(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer|min:1|max:5000']);

        try {
            $monsterData = $this->collectService->fetchOne('dofusdb', 'monster', (int) $request->id);
            return response()->json([
                'success' => true,
                'message' => "Monstre {$request->id} collecté avec succès",
                'data' => ['monster_id' => $request->id, 'monster_data' => $monsterData, 'timestamp' => now()->toISOString()],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur collecte monstre', ['monster_id' => $request->id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte du monstre {$request->id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test de collecte d'un objet spécifique (V2)
     */
    public function testCollectItem(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer|min:1|max:30000']);

        try {
            $itemData = $this->collectService->fetchOne('dofusdb', 'item', (int) $request->id);
            return response()->json([
                'success' => true,
                'message' => "Objet {$request->id} collecté avec succès",
                'data' => ['item_id' => $request->id, 'item_data' => $itemData, 'timestamp' => now()->toISOString()],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur collecte objet', ['item_id' => $request->id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte de l'objet {$request->id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test de collecte d'un sort spécifique (V2)
     */
    public function testCollectSpell(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer|min:1|max:20000']);

        try {
            $spellData = $this->collectService->fetchOne('dofusdb', 'spell', (int) $request->id);
            return response()->json([
                'success' => true,
                'message' => "Sort {$request->id} collecté avec succès",
                'data' => ['spell_id' => $request->id, 'spell_data' => $spellData, 'timestamp' => now()->toISOString()],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur collecte sort', ['spell_id' => $request->id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte du sort {$request->id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test de collecte d'un effet (non exposé en V2 : retourne 501)
     */
    public function testCollectEffect(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|integer|min:1|max:1000']);
        return response()->json([
            'success' => false,
            'message' => 'Collecte d\'effet non disponible en V2.',
            'error' => 'not_implemented',
        ], 501);
    }

    /**
     * Test de nettoyage du cache (V2 n'utilise pas de cache dédié)
     */
    public function testClearCache(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'V2 n\'utilise pas de cache dédié.',
            'data' => ['cleared_entries' => 0, 'timestamp' => now()->toISOString()],
        ]);
    }

    /**
     * Test de collecte d'un item par ID (type_id utilisé comme ID item pour compatibilité route)
     */
    public function testCollectItemsByType(Request $request): JsonResponse
    {
        $request->validate([
            'type_id' => 'required|integer|min:1|max:205',
            'limit' => 'integer|min:1|max:100',
        ]);
        $limit = (int) $request->get('limit', 5);
        $itemId = (int) $request->type_id;

        try {
            $itemData = $this->collectService->fetchOne('dofusdb', 'item', $itemId);
            return response()->json([
                'success' => true,
                'message' => "Objet {$itemId} collecté",
                'data' => [
                    'type_id' => $request->type_id,
                    'limit' => $limit,
                    'sample_item' => $itemData,
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur collecte item par type', ['type_id' => $request->type_id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la collecte d'objets de type {$request->type_id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
