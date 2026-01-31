<?php

namespace App\Http\Controllers\Scrapping;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Scrapping\Concerns\BulkDecisionUpdateTrait;
use App\Models\Type\ItemType;
use App\Services\Scrapping\Catalog\DofusDbItemTypeNameResolver;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API de gestion des typeId DofusDB détectés (registry) pour les équipements/objets.
 *
 * Permet de marquer un typeId comme "utilisé" (allowed), "non utilisé" (blocked)
 * ou le remettre en attente (pending).
 */
class ItemTypeRegistryController extends Controller
{
    use BulkDecisionUpdateTrait;

    public function __construct(
        private DofusDbClient $dofusDbClient,
        private DofusDbItemTypeNameResolver $nameResolver,
    ) {}

    /**
     * Normalise un libellé métier (used/unused) vers le stockage (allowed/blocked).
     */
    private function normalizeDecision(string $decision): string
    {
        return match ($decision) {
            'used' => ItemType::DECISION_ALLOWED,
            'unused' => ItemType::DECISION_BLOCKED,
            default => $decision,
        };
    }

    private function stripDofusdbSuffix(?string $name): ?string
    {
        return $this->nameResolver->stripDofusdbSuffix($name);
    }

    /**
     * Liste des ItemType avec dofusdb_type_id, filtrable par décision.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ItemType::class);

        $decision = $request->query('decision');
        if (is_string($decision)) {
            $decision = $this->normalizeDecision($decision);
        }

        $query = ItemType::query()
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
                // On nettoie juste un éventuel suffixe (DofusDB) en sortie sans écraser le nom en base
                $model->name = $currentName;
                continue;
            }

            $resolved = $this->nameResolver->fetchName($typeId, false, 'item-types');
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
     * Mise à jour en masse des ItemType (registry DofusDB).
     *
     * @example
     * PATCH /api/scrapping/item-types/bulk
     * { "ids":[1,2,3], "decision":"allowed" }
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        return $this->bulkUpdateDecision($request, ItemType::class, fn (string $d) => $this->normalizeDecision($d));
    }

    /**
     * Supprime une entrée de registry (soft delete).
     *
     * @example
     * DELETE /api/scrapping/item-types/{itemType}
     */
    public function destroy(ItemType $itemType): JsonResponse
    {
        $this->authorize('delete', $itemType);

        $itemType->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Met à jour la décision d'un type détecté.
     */
    public function updateDecision(Request $request, ItemType $itemType): JsonResponse
    {
        $this->authorize('update', $itemType);

        if ($itemType->dofusdb_type_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type n’est pas lié à un typeId DofusDB.',
            ], 422);
        }

        $validated = $request->validate([
            // On accepte aussi les alias UX: used/unused
            'decision' => ['required', 'string', 'in:pending,allowed,blocked,used,unused'],
        ]);

        $itemType->decision = $this->normalizeDecision($validated['decision']);
        $itemType->save();

        return response()->json([
            'success' => true,
            'data' => $itemType->only([
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

