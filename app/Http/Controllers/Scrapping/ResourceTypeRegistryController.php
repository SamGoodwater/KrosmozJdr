<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Scrapping\PendingResourceTypeItem;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API de gestion des typeId DofusDB détectés (registry) pour les ressources.
 *
 * Permet de valider (allowed), blacklister (blocked) ou remettre en attente (pending)
 * les typeId DofusDB rencontrés par le scrapping.
 */
class ResourceTypeRegistryController extends Controller
{
    public function __construct(
        private ScrappingOrchestrator $orchestrator
    ) {}

    /**
     * Liste des ResourceType avec dofusdb_type_id, filtrable par décision.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        $decision = $request->query('decision');

        $query = ResourceType::query()
            ->whereNotNull('dofusdb_type_id')
            ->orderByDesc('last_seen_at');

        if (is_string($decision) && in_array($decision, ['pending', 'allowed', 'blocked'], true)) {
            $query->where('decision', $decision);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get([
                'id',
                'name',
                'dofusdb_type_id',
                'decision',
                'seen_count',
                'last_seen_at',
            ]),
        ]);
    }

    /**
     * Liste des types en attente de décision.
     */
    public function pending(Request $request): JsonResponse
    {
        $request->merge(['decision' => 'pending']);
        return $this->index($request);
    }

    /**
     * Met à jour la décision d'un type détecté.
     */
    public function updateDecision(Request $request, ResourceType $resourceType): JsonResponse
    {
        $this->authorize('update', $resourceType);

        if ($resourceType->dofusdb_type_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type n’est pas lié à un typeId DofusDB.',
            ], 422);
        }

        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:pending,allowed,blocked'],
        ]);

        $resourceType->decision = $validated['decision'];
        $resourceType->save();

        return response()->json([
            'success' => true,
            'data' => $resourceType->only([
                'id',
                'name',
                'dofusdb_type_id',
                'decision',
                'seen_count',
                'last_seen_at',
            ]),
        ]);
    }

    /**
     * Réimporte tous les items DofusDB mémorisés pour ce typeId (si decision=allowed).
     */
    public function replayPending(Request $request, ResourceType $resourceType): JsonResponse
    {
        $this->authorize('update', $resourceType);

        if ($resourceType->dofusdb_type_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type n’est pas lié à un typeId DofusDB.',
            ], 422);
        }

        if ($resourceType->decision !== 'allowed') {
            return response()->json([
                'success' => false,
                'message' => 'Le type doit être autorisé avant réimport.',
            ], 422);
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
        ]);

        $limit = (int) ($validated['limit'] ?? 100);
        $typeId = (int) $resourceType->dofusdb_type_id;

        $pending = PendingResourceTypeItem::query()
            ->where('dofusdb_type_id', $typeId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $uniqueItemIds = $pending->pluck('dofusdb_item_id')->unique()->values()->all();

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($uniqueItemIds as $itemId) {
            try {
                // On passe par importItem : la conversion décidera "resource" grâce à la registry DB.
                $res = $this->orchestrator->importItem((int) $itemId, ['include_relations' => false]);
                $results[] = [
                    'id' => (int) $itemId,
                    'success' => $res['success'] ?? false,
                    'result' => $res,
                ];
                if (($res['success'] ?? false) === true) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Throwable $e) {
                $errorCount++;
                $results[] = [
                    'id' => (int) $itemId,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Si tout est OK, on purge les entrées mémorisées pour ce typeId
        if ($errorCount === 0) {
            PendingResourceTypeItem::where('dofusdb_type_id', $typeId)->delete();
        }

        return response()->json([
            'success' => $errorCount === 0,
            'summary' => [
                'total' => count($uniqueItemIds),
                'success' => $successCount,
                'errors' => $errorCount,
            ],
            'results' => $results,
        ]);
    }
}


