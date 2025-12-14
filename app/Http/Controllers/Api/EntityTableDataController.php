<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Entity\ResourceResource;
use App\Http\Resources\Type\ResourceTypeResource;
use App\Models\Entity\Resource;
use App\Models\Type\ResourceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Fournit des datasets "non paginés" destinés aux tables côté frontend.
 *
 * @description
 * Utilisé pour activer un mode "client" (TanStack Table) : on charge un lot conséquent
 * (limité) puis on filtre/tri/pagine instantanément côté navigateur.
 *
 * Sécurité:
 * - autorisation via policies (viewAny)
 * - limite stricte `limit` pour éviter les charges excessives
 *
 * @example
 * GET /api/entity-table/resources?limit=5000
 * GET /api/entity-table/resource-types?limit=5000
 */
class EntityTableDataController extends Controller
{
    /**
     * Liste non paginée des ressources (limité).
     */
    public function resources(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Resource::class);

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $query = Resource::query()->with(['createdBy', 'resourceType']);

        // Optionnel : on peut appliquer les mêmes filtres que la page index pour réduire le volume.
        if ($request->filled('search')) {
            $search = (string) $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        foreach (['level', 'resource_type_id'] as $key) {
            if ($request->has($key) && $request->get($key) !== '') {
                $query->where($key, $request->get($key));
            }
        }

        foreach (['rarity', 'usable', 'auto_update'] as $key) {
            if ($request->has($key) && $request->get($key) !== '') {
                $query->where($key, (int) $request->get($key));
            }
        }

        $sortColumn = (string) $request->get('sort', 'id');
        $sortOrder = (string) $request->get('order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        if (in_array($sortColumn, ['id', 'name', 'level', 'rarity', 'price', 'weight', 'usable', 'auto_update', 'dofusdb_id', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        return response()->json([
            'data' => ResourceResource::collection($rows),
            'meta' => [
                'limit' => $limit,
                'returned' => $rows->count(),
            ],
        ]);
    }

    /**
     * Liste non paginée des types de ressources (limité).
     */
    public function resourceTypes(Request $request): JsonResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        $limit = (int) $request->integer('limit', 5000);
        $limit = max(1, min($limit, 20000));

        $query = ResourceType::query()->withCount('resources');

        if ($request->filled('search')) {
            $search = (string) $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dofusdb_type_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('decision')) {
            $decision = (string) $request->get('decision');
            if (in_array($decision, [ResourceType::DECISION_PENDING, ResourceType::DECISION_ALLOWED, ResourceType::DECISION_BLOCKED], true)) {
                $query->where('decision', $decision);
            }
        }

        $sortColumn = (string) $request->get('sort', 'id');
        $sortOrder = (string) $request->get('order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        if (in_array($sortColumn, ['id', 'name', 'dofusdb_type_id', 'decision', 'seen_count', 'last_seen_at', 'resources_count', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $rows = $query->limit($limit)->get();

        return response()->json([
            'data' => ResourceTypeResource::collection($rows),
            'meta' => [
                'limit' => $limit,
                'returned' => $rows->count(),
            ],
        ]);
    }
}


