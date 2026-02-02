<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Npc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Bulk update pour les NPCs.
 *
 * @description
 * Applique un patch sur une liste d'IDs (sélection multiple). Seuls les champs fournis sont modifiés.
 *
 * @example
 * PATCH /api/entities/npcs/bulk
 * { "ids":[1,2,3], "breed_id":5, "specialization_id":2, "age":"25 ans", "size":"1m75" }
 */
class NpcBulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Npc::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:npcs,id'],

            // Champs bulk (les clés absentes ne sont pas modifiées)
            'breed_id' => ['sometimes', 'nullable', 'integer', 'exists:breeds,id'],
            'specialization_id' => ['sometimes', 'nullable', 'integer', 'exists:specializations,id'],
            'story' => ['sometimes', 'nullable', 'string'],
            'historical' => ['sometimes', 'nullable', 'string'],
            'age' => ['sometimes', 'nullable', 'string', 'max:255'],
            'size' => ['sometimes', 'nullable', 'string', 'max:255'],
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
            'breed_id',
            'specialization_id',
            'story',
            'historical',
            'age',
            'size',
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
            $models = Npc::query()->whereIn('id', $ids)->get();

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

