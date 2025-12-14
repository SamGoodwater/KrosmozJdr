<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Type\StoreResourceTypeRequest;
use App\Http\Requests\Type\UpdateResourceTypeRequest;
use App\Http\Resources\Type\ResourceTypeResource;
use App\Models\Type\ResourceType;
use Inertia\Inertia;

/**
 * CRUD des types de ressources (ResourceType).
 *
 * @description
 * Gère les types métiers + la registry DofusDB (dofusdb_type_id, decision, seen_count).
 */
class ResourceTypeController extends Controller
{
    /**
     * Liste paginée des types de ressources.
     */
    public function index()
    {
        $this->authorize('viewAny', ResourceType::class);

        $query = ResourceType::query()->withCount('resources');

        if (request()->filled('search')) {
            $search = (string) request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('dofusdb_type_id', 'like', "%{$search}%");
            });
        }

        if (request()->filled('decision')) {
            $decision = (string) request()->get('decision');
            if (in_array($decision, [ResourceType::DECISION_PENDING, ResourceType::DECISION_ALLOWED, ResourceType::DECISION_BLOCKED], true)) {
                $query->where('decision', $decision);
            }
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        if (in_array($sortColumn, ['id', 'name', 'dofusdb_type_id', 'decision', 'seen_count', 'last_seen_at', 'resources_count', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $resourceTypes = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/resource-type/Index', [
            'resourceTypes' => ResourceTypeResource::collection($resourceTypes),
            'filters' => request()->only(['search', 'decision']),
        ]);
    }

    /**
     * Affiche un type de ressource (page show simple, utile pour lien partagé).
     */
    public function show(ResourceType $resourceType)
    {
        $this->authorize('view', $resourceType);

        $resourceType->loadCount('resources');

        return Inertia::render('Pages/entity/resource-type/Show', [
            'resourceType' => new ResourceTypeResource($resourceType),
        ]);
    }

    /**
     * Store a newly created resource type.
     */
    public function store(StoreResourceTypeRequest $request)
    {
        $this->authorize('create', ResourceType::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $type = ResourceType::create($data);

        return redirect()
            ->route('entities.resource-types.index')
            ->with('success', 'Type de ressource créé avec succès.');
    }

    /**
     * Update the specified resource type.
     */
    public function update(UpdateResourceTypeRequest $request, ResourceType $resourceType)
    {
        $this->authorize('update', $resourceType);

        $resourceType->update($request->validated());

        return redirect()
            ->route('entities.resource-types.index')
            ->with('success', 'Type de ressource mis à jour avec succès.');
    }

    /**
     * Remove the specified resource type.
     */
    public function delete(ResourceType $resourceType)
    {
        $this->authorize('delete', $resourceType);

        $resourceType->delete();

        return redirect()
            ->route('entities.resource-types.index')
            ->with('success', 'Type de ressource supprimé avec succès.');
    }
}


