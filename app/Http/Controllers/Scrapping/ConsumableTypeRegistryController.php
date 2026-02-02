<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Scrapping\Concerns\BulkDecisionUpdateTrait;
use App\Models\Type\ConsumableType;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API de gestion des typeId DofusDB détectés (registry) pour les consommables.
 *
 * Permet de marquer un typeId comme "utilisé" (allowed), "non utilisé" (blocked)
 * ou le remettre en attente (pending).
 */
class ConsumableTypeRegistryController extends Controller
{
    use BulkDecisionUpdateTrait;

    public function __construct(
        private DofusDbClient $dofusDbClient,
        private DofusDbItemTypesCatalogService $itemTypesCatalog,
    ) {}

    /**
     * Normalise un libellé métier (used/unused) vers le stockage (allowed/blocked).
     */
    private function normalizeDecision(string $decision): string
    {
        return match ($decision) {
            'used' => ConsumableType::DECISION_ALLOWED,
            'unused' => ConsumableType::DECISION_BLOCKED,
            default => $decision,
        };
    }

    private function stripDofusdbSuffix(?string $name): ?string
    {
        return $this->itemTypesCatalog->stripDofusdbSuffix($name);
    }

    /**
     * Liste des ConsumableType avec dofusdb_type_id, filtrable par décision.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ConsumableType::class);

        $decision = $request->query('decision');
        if (is_string($decision)) {
            $decision = $this->normalizeDecision($decision);
        }

        $query = ConsumableType::query()
            ->whereNotNull('dofusdb_type_id')
            ->orderByDesc('last_seen_at');

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
                $model->name = $currentName;
                continue;
            }

            $resolved = $this->itemTypesCatalog->fetchName($typeId, 'fr', false);
            if ($resolved) {
                $model->name = $resolved;
                try {
                    $model->save();
                } catch (\Throwable) {
                    // Non bloquant
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
     * Mise à jour en masse des ConsumableType (registry DofusDB).
     *
     * @example
     * PATCH /api/scrapping/consumable-types/bulk
     * { "ids":[1,2,3], "decision":"allowed" }
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        return $this->bulkUpdateDecision($request, ConsumableType::class, fn (string $d) => $this->normalizeDecision($d));
    }

    /**
     * Supprime une entrée de registry (soft delete).
     *
     * @example
     * DELETE /api/scrapping/consumable-types/{consumableType}
     */
    public function destroy(ConsumableType $consumableType): JsonResponse
    {
        $this->authorize('delete', $consumableType);

        $consumableType->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Met à jour la décision d'un type détecté.
     */
    public function updateDecision(Request $request, ConsumableType $consumableType): JsonResponse
    {
        $this->authorize('update', $consumableType);

        if ($consumableType->dofusdb_type_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type n’est pas lié à un typeId DofusDB.',
            ], 422);
        }

        $validated = $request->validate([
            'decision' => ['required', 'string', 'in:pending,allowed,blocked,used,unused'],
        ]);

        $consumableType->decision = $this->normalizeDecision($validated['decision']);
        $consumableType->save();

        return response()->json([
            'success' => true,
            'data' => $consumableType->only([
                'id',
                'name',
                'dofusdb_type_id',
                'decision',
                'seen_count',
                'last_seen_at',
            ]),
        ]);
    }
}

