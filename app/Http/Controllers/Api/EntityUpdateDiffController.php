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
}
