<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Creature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Bulk update pour les créatures.
 *
 * @description
 * Applique un patch sur une liste d'IDs (sélection multiple). Seuls les champs fournis sont modifiés.
 *
 * @example
 * PATCH /api/entities/creatures/bulk
 * { "ids":[1,2,3], "level":"50", "hostility":3, "life":"30", "usable":true, "is_visible":"guest" }
 */
class CreatureBulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Creature::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:creatures,id'],

            // Champs bulk (les clés absentes ne sont pas modifiées)
            'level' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hostility' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:4'],
            'life' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pa' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pm' => ['sometimes', 'nullable', 'string', 'max:255'],
            'usable' => ['sometimes', 'nullable', 'boolean'],
            'is_visible' => ['sometimes', 'nullable', 'string', 'in:guest,user,player,game_master,admin'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $patch = [];
        foreach ([
            'level',
            'hostility',
            'life',
            'pa',
            'pm',
            'usable',
            'is_visible',
        ] as $k) {
            if (array_key_exists($k, $validated)) {
                $patch[$k] = $validated[$k];
            }
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
            $models = Creature::query()->whereIn('id', $ids)->get();

            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (!$model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];
                    continue;
                }

                try {
                    $this->authorize('update', $model);
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
}

