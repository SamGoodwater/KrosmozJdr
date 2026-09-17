<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\DofusdbRefreshRequest;
use App\Services\Entity\EntityDofusdbRefreshService;
use App\Support\DofusdbRefreshableEntities;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Mise à jour DofusDB unitaire : id local, policy update, MJ+.
 *
 * @example POST /api/entities/spells/12/dofusdb-refresh {"mode":"preview"}
 */
class EntityDofusdbRefreshController extends Controller
{
    public function __invoke(DofusdbRefreshRequest $request, string $entityType, int $id): JsonResponse
    {
        if (! DofusdbRefreshableEntities::isRefreshable($entityType)) {
            return response()->json([
                'success' => false,
                'message' => 'Ce type d’entité n’est pas importable depuis DofusDB.',
            ], 422);
        }

        $entity = $this->resolveEntity($entityType, $id);
        $this->authorize('update', $entity);

        try {
            $payload = app(EntityDofusdbRefreshService::class)->run(
                $entity,
                $request->user(),
                $request->mode(),
                $request->force(),
            );
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        return response()->json($payload, $payload['success'] ? 200 : 400);
    }

    private function resolveEntity(string $entityType, int $id): Model
    {
        $entity = EntityModelRegistry::resolveModel($entityType, $id);
        if (! $entity instanceof Model) {
            abort(404);
        }

        return $entity;
    }
}
