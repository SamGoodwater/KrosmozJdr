<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entity\Consumable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Bulk update pour les consommables.
 *
 * @description
 * Applique un patch sur une liste d'IDs (sélection multiple). Seuls les champs fournis sont modifiés.
 *
 * @example
 * PATCH /api/entities/consumables/bulk
 * { "ids":[1,2,3], "level":"50", "rarity":3, "consumable_type_id":5, "usable":true, "auto_update":false, "is_visible":"guest" }
 */
class ConsumableBulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Consumable::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:consumables,id'],

            // Champs bulk (les clés absentes ne sont pas modifiées)
            'level' => ['sometimes', 'nullable', 'string', 'max:255'],
            'rarity' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'consumable_type_id' => ['sometimes', 'nullable', 'integer', 'exists:consumable_types,id'],
            'price' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'dofusdb_id' => ['sometimes', 'nullable', 'string', 'max:255'],
            'usable' => ['sometimes', 'nullable', 'boolean'],
            'is_visible' => ['sometimes', 'nullable', 'string', 'in:guest,user,player,game_master,admin'],
            'auto_update' => ['sometimes', 'nullable', 'boolean'],
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
            'level',
            'rarity',
            'consumable_type_id',
            'price',
            'description',
            'dofusdb_id',
            'usable',
            'is_visible',
            'auto_update',
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
            $models = Consumable::query()->whereIn('id', $ids)->get();

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

