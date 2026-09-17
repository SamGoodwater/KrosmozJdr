<?php

declare(strict_types=1);

namespace App\Http\Controllers\Scrapping\Concerns;

use App\Enums\EntityState;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Mise à jour en masse des flags de registre (`allow_scrap`, `show_in_catalog`).
 *
 * `decision` reste accepté comme alias historique (allowed → scrap, sinon non).
 *
 * @internal
 */
trait BulkDecisionUpdateTrait
{
    /**
     * @param  class-string<Model>  $modelClass
     * @param  callable(string):string|null  $normalizeDecision
     */
    protected function bulkUpdateDecision(Request $request, string $modelClass, ?callable $normalizeDecision = null): JsonResponse
    {
        $this->authorize('updateAny', $modelClass);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1'],
            'allow_scrap' => ['nullable', 'boolean'],
            'show_in_catalog' => ['nullable', 'boolean'],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked,used,unused'],
            'state' => ['nullable', 'string', EntityState::rule()],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $patch = $this->extractTypeRegistryFlagPatch($validated, $request, $normalizeDecision);
        if ($patch === []) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun champ à mettre à jour.',
            ], 422);
        }

        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            /** @var Collection<int, Model> $models */
            $models = $modelClass::query()->whereIn('id', $ids)->get();

            foreach ($ids as $id) {
                $model = $models->firstWhere('id', $id);
                if (! $model) {
                    $errors[] = ['id' => $id, 'error' => 'Not found'];

                    continue;
                }

                try {
                    $this->authorize('update', $model);

                    foreach ($patch as $key => $value) {
                        $model->setAttribute($key, $value);
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
     * @param  array<string, mixed>  $validated
     * @param  callable(string):string|null  $normalizeDecision
     * @return array<string, mixed>
     */
    protected function extractTypeRegistryFlagPatch(array $validated, Request $request, ?callable $normalizeDecision = null): array
    {
        $patch = [];

        if (array_key_exists('allow_scrap', $validated) && $validated['allow_scrap'] !== null) {
            $patch['allow_scrap'] = $request->boolean('allow_scrap');
        }
        if (array_key_exists('show_in_catalog', $validated) && $validated['show_in_catalog'] !== null) {
            $patch['show_in_catalog'] = $request->boolean('show_in_catalog');
        }
        if (
            $normalizeDecision !== null
            && array_key_exists('decision', $validated)
            && is_string($validated['decision'])
            && $validated['decision'] !== ''
        ) {
            $patch['decision'] = $normalizeDecision($validated['decision']);
        }
        if (array_key_exists('state', $validated) && is_string($validated['state']) && $validated['state'] !== '') {
            $patch['state'] = $validated['state'];
        }

        return $patch;
    }
}
