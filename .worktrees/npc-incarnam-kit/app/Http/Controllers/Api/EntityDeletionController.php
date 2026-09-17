<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Support\EntityModelRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API générique pour corbeille/restauration/suppression définitive des entités JDR.
 */
class EntityDeletionController extends Controller
{
    public function __construct(
        private readonly EntityDeletionService $deletionService
    ) {}

    public function impact(string $entityType, int $id): JsonResponse
    {
        $entity = $this->resolveEntity($entityType, $id, true);
        $this->authorize('delete', $entity);

        return response()->json($this->deletionService->impactSummary($entity));
    }

    public function delete(Request $request, string $entityType, int $id): JsonResponse
    {
        $actor = $this->actor($request);
        $entity = $this->resolveEntity($entityType, $id);
        $this->deletionService->softDelete($entity, $actor);

        return response()->json(['message' => 'Entité placée en corbeille.']);
    }

    public function restore(Request $request, string $entityType, int $id): JsonResponse
    {
        $actor = $this->actor($request);
        $entity = $this->resolveEntity($entityType, $id, true);
        $this->deletionService->restore($entity, $actor);

        return response()->json(['message' => 'Entité restaurée.']);
    }

    public function forceDelete(Request $request, string $entityType, int $id): JsonResponse
    {
        $actor = $this->actor($request);
        $entity = $this->resolveEntity($entityType, $id, true);
        $this->deletionService->forceDelete($entity, $actor);

        return response()->json(['message' => 'Entité supprimée définitivement.']);
    }

    private function resolveEntity(string $entityType, int $id, bool $withTrashed = false): Model
    {
        $entity = EntityModelRegistry::resolveModel($entityType, $id, $withTrashed);
        if (! $entity instanceof Model) {
            abort(404);
        }

        return $entity;
    }

    private function actor(Request $request): User
    {
        $user = $request->user();
        if (! $user instanceof User) {
            abort(401);
        }

        return $user;
    }
}
