<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\User;
use App\Models\Scrapping\PendingResourceTypeItem;
use App\Models\Type\ResourceType;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\DataCollect\ItemEntityTypeFilterService;
use App\Services\Scrapping\Http\DofusDbClient;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Orchestrator\Orchestrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        private Orchestrator $orchestrator,
        private CollectService $collectService,
        private ItemEntityTypeFilterService $itemEntityTypeFilters,
        private DofusDbClient $dofusDbClient,
        private DofusDbItemTypesCatalogService $itemTypesCatalog,
    ) {}

    private function stripDofusdbSuffix(?string $name): ?string
    {
        return $this->itemTypesCatalog->stripDofusdbSuffix($name);
    }

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

        // Sécurité UX: ne pas afficher des types hors "Ressource" (drops/recettes).
        // On filtre la registry sur le groupe superType Ressource.
        $resourceTypeIds = $this->itemEntityTypeFilters->getTypeIdsForGroup('resource');
        if (!empty($resourceTypeIds)) {
            $query->whereIn('dofusdb_type_id', $resourceTypeIds);
        }

        if (is_string($decision) && in_array($decision, ['pending', 'allowed', 'blocked'], true)) {
            $query->where('decision', $decision);
        }

        $rows = $query->get([
            'id',
            'name',
            'dofusdb_type_id',
            'decision',
            'seen_count',
            'last_seen_at',
        ]);

        // Améliorer les placeholders "DofusDB type #X" en allant chercher le vrai nom côté DofusDB.
        foreach ($rows as $model) {
            $typeId = is_numeric($model->dofusdb_type_id) ? (int) $model->dofusdb_type_id : 0;
            if ($typeId <= 0) continue;

            $currentName = $this->stripDofusdbSuffix(is_string($model->name) ? $model->name : null);
            $isPlaceholder = $currentName === null || $currentName === '' || str_starts_with($currentName, 'DofusDB type #');

            if (!$isPlaceholder) {
                // On nettoie juste un éventuel suffixe (DofusDB) en sortie sans écraser le nom en base
                $model->name = $currentName;
                continue;
            }

            $resolved = $this->itemTypesCatalog->fetchName($typeId, 'fr', false);
            if ($resolved) {
                // On met à jour la base uniquement si le nom actuel est un placeholder.
                $model->name = $resolved;
                try {
                    $model->save();
                } catch (\Throwable) {
                    // Non bloquant: on renvoie quand même le nom résolu
                }
            } else {
                $model->name = $currentName ?: $model->name;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
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
     * Mise à jour en masse des ResourceType (registry DofusDB).
     *
     * @description
     * Applique les champs fournis à une liste d'IDs. Seuls les champs envoyés sont modifiés.
     *
     * @example
     * PATCH /api/scrapping/resource-types/bulk
     * { "ids":[1,2,3], "decision":"allowed", "state":"playable", "read_level":0, "write_level":4 }
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', ResourceType::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1'],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked,used,unused'],
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:' . User::ROLE_GUEST, 'max:' . User::ROLE_SUPER_ADMIN],
            'write_level' => [
                'nullable',
                'integer',
                'min:' . User::ROLE_GUEST,
                'max:' . User::ROLE_SUPER_ADMIN,
                Rule::when($request->input('read_level') !== null, ['gte:read_level']),
            ],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $patch = [];
        if (array_key_exists('decision', $validated) && $validated['decision'] !== null) {
            $patch['decision'] = $this->normalizeDecision($validated['decision']);
        }
        if (array_key_exists('state', $validated) && $validated['state'] !== null) {
            $patch['state'] = $validated['state'];
        }
        if (array_key_exists('read_level', $validated) && $validated['read_level'] !== null) {
            $patch['read_level'] = (int) $validated['read_level'];
        }
        if (array_key_exists('write_level', $validated) && $validated['write_level'] !== null) {
            $patch['write_level'] = (int) $validated['write_level'];
        }

        if (empty($patch)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun champ à mettre à jour.',
            ], 422);
        }

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $models = ResourceType::query()->whereIn('id', $ids)->get();

            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (!$model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];
                    continue;
                }

                try {
                    $this->authorize('update', $model);

                    // On n’applique la registry qu’aux types liés DofusDB, mais on laisse la MAJ possible
                    // (ex: state/read_level/write_level) même si dofusdb_type_id est null.
                    foreach ($patch as $k => $v) {
                        $model->{$k} = $v;
                    }
                    $model->save();
                    $updated++;
                } catch (\Throwable $e) {
                    $errors[] = ['id' => $id, 'error' => $e->getMessage()];
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour en masse.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => count($errors) === 0,
            'summary' => [
                'requested' => count($ids),
                'updated' => $updated,
                'errors' => count($errors),
            ],
            'errors' => $errors,
        ]);
    }

    /**
     * Supprime une entrée de registry (soft delete).
     *
     * @example
     * DELETE /api/scrapping/resource-types/{resourceType}
     */
    public function destroy(ResourceType $resourceType): JsonResponse
    {
        $this->authorize('delete', $resourceType);

        $resourceType->delete();

        return response()->json([
            'success' => true,
        ]);
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
                    $raw = $this->collectService->fetchOne('dofusdb', 'item', (int) $dofusdbItemId);
                    $preview = [
                        'id' => (int) ($raw['id'] ?? $dofusdbItemId),
                        'typeId' => isset($raw['typeId']) ? (int) $raw['typeId'] : null,
                        'name' => is_array($raw['name'] ?? null) ? ($raw['name']['fr'] ?? reset($raw['name']) ?: null) : ($raw['name'] ?? null),
                    ];
                } catch (\Throwable) {
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
                $result = $this->orchestrator->runOne('dofusdb', 'item', $itemId, [
                    'integrate' => true,
                    'dry_run' => false,
                ]);
                $resourceId = null;
                $ok = $result->isSuccess();
                if ($ok && $result->getIntegrationResult()?->isSuccess()) {
                    $data = $result->getIntegrationResult()->getData();
                    if (($data['table'] ?? '') === 'resources') {
                        $resourceId = $data['id'] ?? null;
                        $stats['imported']++;
                    }
                }
                $importCache[$itemId] = [
                    'ok' => $ok && $resourceId !== null,
                    'resource_id' => is_numeric($resourceId) ? (int) $resourceId : null,
                    'error' => $ok ? null : $result->getMessage(),
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


