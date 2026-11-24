<?php

namespace App\Http\Controllers\Api;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
use App\Models\Entity\Aptitude;
use App\Support\Entity\AccessLevelMutationGuard;
use App\Support\Entity\EntityStateGate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class AptitudeBulkController extends Controller
{
    public function bulkUpdate(Request $request): JsonResponse
    {
        $this->authorize('updateAny', Aptitude::class);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1', 'exists:aptitudes,id'],
            'state' => ['sometimes', 'nullable', 'string', EntityState::rule()],
            'read_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'description' => ['sometimes', 'nullable', 'string'],
            'effect' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'reuse' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'duration' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'range' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);
        $validated = AccessLevelMutationGuard::stripUnlessAdmin($request->user(), $validated);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        $patch = [];
        foreach ([
            'state', 'read_level', 'write_level', 'description', 'effect', 'image',
            'reuse', 'duration', 'range',
        ] as $k) {
            if (array_key_exists($k, $validated)) {
                $patch[$k] = $validated[$k];
            }
        }

        if ($patch === []) {
            return response()->json(['success' => false, 'message' => 'Aucun champ à mettre à jour.'], 422);
        }

        $updated = 0;
        $errors = [];
        DB::beginTransaction();
        try {
            $models = Aptitude::query()->whereIn('id', $ids)->get();
            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (! $model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];

                    continue;
                }
                try {
                    $this->authorize('update', $model);
                    EntityStateGate::authorizeHttpTransition($request->user(), $model, $patch['state'] ?? null);
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
