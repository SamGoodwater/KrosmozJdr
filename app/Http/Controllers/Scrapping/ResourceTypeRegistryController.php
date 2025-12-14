<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Scrapping\PendingResourceTypeItem;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API de gestion des typeId DofusDB détectés (registry) pour les ressources.
 *
 * Permet de marquer un typeId comme "utilisé" (allowed), "non utilisé" (blocked)
 * ou le remettre en attente (pending)
 * les typeId DofusDB rencontrés par le scrapping.
 */
class ResourceTypeRegistryController extends Controller
{
    /**
     * Normalise un libellé métier (used/unused) vers le stockage (allowed/blocked).
     */
    private function normalizeDecision(string $decision): string
    {
        return match ($decision) {
            'used' => ResourceType::DECISION_ALLOWED,
            'unused' => ResourceType::DECISION_BLOCKED,
            default => $decision,
        };
    }

    public function __construct(
        private ScrappingOrchestrator $orchestrator,
        private DataCollectService $collector
    ) {}

    /**
     * Liste des ResourceType avec dofusdb_type_id, filtrable par décision.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        $decision = $request->query('decision');
        if (is_string($decision)) {
            $decision = $this->normalizeDecision($decision);
        }

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
     * Retourne des exemples d'items "pending" pour un ResourceType (utile pour décider Utiliser / Ne pas utiliser).
     *
     * @example
     * GET /api/scrapping/resource-types/{id}/pending-items?limit=5&with_preview=1
     */
    public function pendingItems(Request $request, ResourceType $resourceType): JsonResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        if ($resourceType->dofusdb_type_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type n’est pas lié à un typeId DofusDB.',
            ], 422);
        }

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
            'with_preview' => ['nullable', 'boolean'],
        ]);

        $limit = (int) ($validated['limit'] ?? 5);
        $withPreview = (bool) ($validated['with_preview'] ?? true);
        $typeId = (int) $resourceType->dofusdb_type_id;

        // 1) On récupère une fenêtre de lignes récentes et on en extrait N IDs uniques
        $recentRows = PendingResourceTypeItem::query()
            ->where('dofusdb_type_id', $typeId)
            ->orderByDesc('created_at')
            ->limit($limit * 25)
            ->get();

        $itemIds = $recentRows->pluck('dofusdb_item_id')->unique()->take($limit)->values()->all();

        if (empty($itemIds)) {
            return response()->json([
                'success' => true,
                'data' => [
                    'resourceType' => $resourceType->only(['id', 'name', 'dofusdb_type_id', 'decision']),
                    'items' => [],
                ],
            ]);
        }

        // 2) Charger toutes les lignes pour ces IDs (pour avoir plusieurs contextes)
        $rows = PendingResourceTypeItem::query()
            ->where('dofusdb_type_id', $typeId)
            ->whereIn('dofusdb_item_id', $itemIds)
            ->orderByDesc('created_at')
            ->get();

        $items = [];
        foreach ($rows->groupBy('dofusdb_item_id') as $dofusdbItemId => $group) {
            $preview = null;
            if ($withPreview) {
                try {
                    $raw = $this->collector->collectItem((int) $dofusdbItemId, false);
                    $preview = [
                        'id' => (int) ($raw['id'] ?? $dofusdbItemId),
                        'typeId' => isset($raw['typeId']) ? (int) $raw['typeId'] : null,
                        'name' => is_array($raw['name'] ?? null) ? ($raw['name']['fr'] ?? reset($raw['name']) ?: null) : ($raw['name'] ?? null),
                    ];
                } catch (\Throwable) {
                    // Pas bloquant : on affiche au moins l'ID
                    $preview = null;
                }
            }

            $items[] = [
                'dofusdb_item_id' => (int) $dofusdbItemId,
                'preview' => $preview,
                'examples' => $group->take(8)->map(fn (PendingResourceTypeItem $r) => [
                    'context' => $r->context,
                    'source_entity_type' => $r->source_entity_type,
                    'source_entity_dofusdb_id' => $r->source_entity_dofusdb_id,
                    'quantity' => $r->quantity,
                    'created_at' => optional($r->created_at)->toISOString(),
                ])->values()->all(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'resourceType' => $resourceType->only(['id', 'name', 'dofusdb_type_id', 'decision']),
                'items' => $items,
            ],
        ]);
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
            // On accepte aussi les alias UX: used/unused
            'decision' => ['required', 'string', 'in:pending,allowed,blocked,used,unused'],
            'replay_pending' => ['nullable', 'boolean'],
            'replay_limit' => ['nullable', 'integer', 'min:1', 'max:5000'],
        ]);

        $resourceType->decision = $this->normalizeDecision($validated['decision']);
        $resourceType->save();

        // Optionnel: si on autorise un type, on peut déclencher un replay immédiatement.
        $replayRequested = (bool) ($validated['replay_pending'] ?? false);
        $replaySummary = null;
        if ($resourceType->decision === ResourceType::DECISION_ALLOWED && $replayRequested) {
            $limit = (int) ($validated['replay_limit'] ?? 500);
            $replaySummary = $this->doReplayPending($resourceType, $limit);
        }

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
            'replay' => $replaySummary,
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
        $replay = $this->doReplayPending($resourceType, $limit);

        return response()->json([
            'success' => ($replay['summary']['errors'] ?? 0) === 0,
            ...$replay,
        ]);
    }

    /**
     * Effectue le replay des PendingResourceTypeItem pour un ResourceType autorisé.
     *
     * @param ResourceType $resourceType
     * @param int $limit
     * @return array{summary: array<string,int>, results: array<int, mixed>}
     */
    private function doReplayPending(ResourceType $resourceType, int $limit): array
    {
        $typeId = (int) $resourceType->dofusdb_type_id;

        $pendingRows = PendingResourceTypeItem::query()
            ->where('dofusdb_type_id', $typeId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        $results = [];
        $stats = [
            'rows' => (int) $pendingRows->count(),
            'unique_items' => (int) $pendingRows->pluck('dofusdb_item_id')->unique()->count(),
            'imported' => 0,
            'pivots_applied' => 0,
            'deleted_rows' => 0,
            'errors' => 0,
        ];

        // Cache d'import: dofusdb_item_id => ['ok' => bool, 'resource_id' => ?int, 'error' => ?string]
        $importCache = [];

        foreach ($pendingRows as $row) {
            $itemId = (int) $row->dofusdb_item_id;

            if (!isset($importCache[$itemId])) {
                $importRes = $this->orchestrator->importResource($itemId, [
                    'include_relations' => false,
                    'dry_run' => false,
                ]);

                $resourceId = null;
                if (($importRes['success'] ?? false) === true) {
                    // importResource retourne ['data' => $result] où $result contient 'id' (id ressource) + 'table'
                    $resourceId = $importRes['data']['id'] ?? null;
                    $stats['imported']++;
                }

                $importCache[$itemId] = [
                    'ok' => (bool) ($importRes['success'] ?? false),
                    'resource_id' => is_numeric($resourceId) ? (int) $resourceId : null,
                    'error' => $importRes['error'] ?? null,
                ];
            }

            $importInfo = $importCache[$itemId];

            if (!$importInfo['ok'] || !$importInfo['resource_id']) {
                $stats['errors']++;
                $results[] = [
                    'pending_id' => $row->id,
                    'dofusdb_item_id' => $itemId,
                    'context' => $row->context,
                    'success' => false,
                    'error' => $importInfo['error'] ?? 'Import ressource échoué',
                ];
                continue;
            }

            $pivotApplied = false;
            $pivotReason = null;

            $qty = (int) ($row->quantity ?? 1);
            if ($qty < 1) {
                $qty = 1;
            }

            // Best effort: réappliquer les pivots quand la source existe déjà en base.
            try {
                if ($row->context === 'recipe' && $row->source_entity_type && $row->source_entity_dofusdb_id) {
                    $sourceId = (int) $row->source_entity_dofusdb_id;
                    if ($row->source_entity_type === 'item') {
                        $item = Item::where('dofusdb_id', (string) $sourceId)->first();
                        if ($item) {
                            $item->resources()->syncWithoutDetaching([
                                $importInfo['resource_id'] => ['quantity' => (string) $qty],
                            ]);
                            $pivotApplied = true;
                        } else {
                            $pivotReason = 'Item source non trouvé en base';
                        }
                    }
                    if ($row->source_entity_type === 'consumable') {
                        $consumable = Consumable::where('dofusdb_id', (string) $sourceId)->first();
                        if ($consumable) {
                            $consumable->resources()->syncWithoutDetaching([
                                $importInfo['resource_id'] => ['quantity' => (string) $qty],
                            ]);
                            $pivotApplied = true;
                        } else {
                            $pivotReason = 'Consumable source non trouvé en base';
                        }
                    }
                }

                if ($row->context === 'drops' && $row->source_entity_type === 'monster' && $row->source_entity_dofusdb_id) {
                    $monster = Monster::where('dofusdb_id', (string) $row->source_entity_dofusdb_id)->first();
                    if ($monster && $monster->creature) {
                        $monster->creature->resources()->syncWithoutDetaching([
                            $importInfo['resource_id'] => ['quantity' => (string) $qty],
                        ]);
                        $pivotApplied = true;
                    } else {
                        $pivotReason = 'Monstre source non trouvé en base';
                    }
                }
            } catch (\Throwable $e) {
                $pivotReason = $e->getMessage();
            }

            if ($pivotApplied) {
                $stats['pivots_applied']++;
            }

            // On purge l'entrée pending dès que la ressource a été importée.
            // Si un pivot n'a pas pu être appliqué (source absente), un futur import complet
            // du monstre/item reconstituera les relations via include_relations=true.
            $row->delete();
            $stats['deleted_rows']++;

            $results[] = [
                'pending_id' => $row->id,
                'dofusdb_item_id' => $itemId,
                'context' => $row->context,
                'success' => true,
                'resource_id' => $importInfo['resource_id'],
                'pivot_applied' => $pivotApplied,
                'pivot_reason' => $pivotReason,
            ];
        }

        return [
            'summary' => $stats,
            'results' => $results,
        ];
    }
}


