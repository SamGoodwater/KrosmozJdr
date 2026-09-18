<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Entity\EntityUpdateDiffService;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Rétablit l’instantané d’avant une maj DofusDB ou une conversion IA.
 *
 * @example POST /api/entities/items/12/update-diff/restore {"snapshot_id":"…"}
 */
class EntityUpdateDiffController extends Controller
{
    public function restore(Request $request, string $entityType, int $id): JsonResponse
    {
        $validated = $request->validate([
            'snapshot_id' => ['required', 'uuid'],
        ]);

        $entity = EntityModelRegistry::resolveModel($entityType, $id);
        if (! $entity instanceof Model) {
            return response()->json([
                'success' => false,
                'message' => 'Entité introuvable.',
            ], 404);
        }

        $this->authorize('update', $entity);

        try {
            app(EntityUpdateDiffService::class)->restore(
                $request->user(),
                $entityType,
                $id,
                (string) $validated['snapshot_id'],
            );
        } catch (HttpException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        return response()->json([
            'success' => true,
            'message' => 'Version précédente rétablie.',
        ]);
    }

    /**
     * Enregistre le mix choisi : les clés listées reviennent à l’instantané, le reste reste nouveau.
     *
     * @example POST /api/entities/items/12/update-diff/apply {"snapshot_id":"…","restore_keys":["name"]}
     */
    public function apply(Request $request, string $entityType, int $id): JsonResponse
    {
        $validated = $request->validate([
            'snapshot_id' => ['required', 'uuid'],
            'restore_keys' => ['sometimes', 'array', 'max:200'],
            'restore_keys.*' => ['string', 'max:120'],
        ]);

        $entity = EntityModelRegistry::resolveModel($entityType, $id);
        if (! $entity instanceof Model) {
            return response()->json([
                'success' => false,
                'message' => 'Entité introuvable.',
            ], 404);
        }

        $this->authorize('update', $entity);

        $restoreKeys = [];
        foreach ($validated['restore_keys'] ?? [] as $key) {
            if (is_string($key) && trim($key) !== '') {
                $restoreKeys[] = trim($key);
            }
        }

        try {
            app(EntityUpdateDiffService::class)->apply(
                $request->user(),
                $entityType,
                $id,
                (string) $validated['snapshot_id'],
                $restoreKeys,
            );
        } catch (HttpException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
        }

        $keptOld = count($restoreKeys);

        return response()->json([
            'success' => true,
            'message' => $keptOld === 0
                ? 'Nouvelle version conservée.'
                : ($keptOld === 1
                    ? '1 champ rétabli, le reste de la nouvelle version est conservé.'
                    : "{$keptOld} champs rétablis, le reste de la nouvelle version est conservé."),
        ]);
    }
}
