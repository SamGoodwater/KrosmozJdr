<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Models\Type\SpellType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API d'administration des types de sorts (SpellType).
 *
 * @description
 * Utilisé par l'UI (pages + modals) pour lister et valider en masse via le champ `state`.
 */
class SpellTypeApiController extends Controller
{
    /**
     * Liste des types de sorts (filtrable par état).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', SpellType::class);

        $state = $request->query('state');

        $query = SpellType::query()->orderBy('name');

        if (is_string($state) && in_array($state, ['raw', 'draft', 'playable', 'archived'], true)) {
            $query->where('state', $state);
        }

        $rows = $query->get([
            'id',
            'name',
            'description',
            'color',
            'icon',
            'state',
            'created_at',
            'updated_at',
        ]);

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Mise à jour en masse du champ `state`.
     *
     * @example
     * PATCH /api/types/spell-types/bulk
     * { "ids":[1,2,3], "state":"playable" }
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', SpellType::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1'],
            'state' => ['required', 'string', 'in:raw,draft,playable,archived'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $state = (string) $validated['state'];

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $models = SpellType::query()->whereIn('id', $ids)->get();

            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (!$model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];
                    continue;
                }

                try {
                    $this->authorize('update', $model);

                    $model->state = $state;
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
     * Supprime un type de sort (soft delete).
     *
     * @example
     * DELETE /api/types/spell-types/{spellType}
     */
    public function destroy(SpellType $spellType): JsonResponse
    {
        $this->authorize('delete', $spellType);

        $spellType->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

