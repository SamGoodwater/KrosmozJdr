<?php

namespace App\Http\Controllers\Scrapping\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Trait de factorisation pour les registries "decision" (pending/allowed/blocked)
 * basées sur des typeId DofusDB.
 *
 * @internal
 */
trait BulkDecisionUpdateTrait
{
    /**
     * Applique une mise à jour en masse du champ `decision`.
     *
     * @param  class-string<Model>  $modelClass
     * @param  callable(string):string  $normalizeDecision
     */
    protected function bulkUpdateDecision(Request $request, string $modelClass, callable $normalizeDecision): JsonResponse
    {
        $this->authorize('updateAny', $modelClass);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1'],
            // On accepte aussi les alias UX: used/unused
            'decision' => ['required_without:show_in_catalog', 'nullable', 'string', 'in:pending,allowed,blocked,used,unused'],
            'show_in_catalog' => ['required_without:decision', 'nullable', 'boolean'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        if (count($ids) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Sélection invalide.',
            ], 422);
        }

        $hasDecision = array_key_exists('decision', $validated) && is_string($validated['decision']) && $validated['decision'] !== '';
        $hasCatalog = array_key_exists('show_in_catalog', $validated) && $validated['show_in_catalog'] !== null;
        $decision = $hasDecision ? $normalizeDecision((string) $validated['decision']) : null;
        $showInCatalog = $hasCatalog ? $request->boolean('show_in_catalog') : null;

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

                    // On n’applique la registry qu’aux entrées liées à DofusDB.
                    if ($model->getAttribute('dofusdb_type_id') === null) {
                        $errors[] = ['id' => $id, 'error' => 'Not linked to dofusdb_type_id'];

                        continue;
                    }

                    if ($decision !== null) {
                        $model->setAttribute('decision', $decision);
                    }
                    if ($showInCatalog !== null) {
                        $model->setAttribute('show_in_catalog', $showInCatalog);
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
