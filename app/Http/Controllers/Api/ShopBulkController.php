<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Bulk update pour les boutiques.
 *
 * @description
 * Applique un patch sur une liste d'IDs (sélection multiple). Seuls les champs fournis sont modifiés.
 *
 * @example
 * PATCH /api/entities/shops/bulk
 * { "ids":[1,2,3], "location":"Brakmar", "price":100, "state":"playable", "read_level":0, "write_level":3, "description":"Nouvelle description" }
 */
class ShopBulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Shop::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:shops,id'],

            // Champs bulk (les clés absentes ne sont pas modifiées)
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'state' => ['sometimes', 'nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],

            // Champs "métier" utiles en édition multiple (nullable => possibilité de vider)
            'description' => ['sometimes', 'nullable', 'string'],
            'npc_id' => ['sometimes', 'nullable', 'integer', 'exists:npcs,id'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
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
            'location',
            'price',
            'state',
            'read_level',
            'write_level',
            'description',
            'npc_id',
            'image',
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
            $models = Shop::query()->whereIn('id', $ids)->get();

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

