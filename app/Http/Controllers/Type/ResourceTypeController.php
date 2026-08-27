<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Type\StoreResourceTypeRequest;
use App\Http\Requests\Type\UpdateResourceTypeRequest;
use App\Http\Resources\Type\ResourceTypeResource;
use App\Models\Type\ResourceType;
use Illuminate\Http\RedirectResponse;
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
     * Liste : registre commun `/admin/content/types/resource`.
     */
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        return redirect()->route('admin.content.types.show', ['kind' => 'resource']);
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
     * Affiche la page d'édition d'un type de ressource.
     */
    public function edit(ResourceType $resourceType)
    {
        $this->authorize('update', $resourceType);

        $resourceType->loadCount('resources');

        return Inertia::render('Pages/entity/resource-type/Edit', [
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

        $redirectAfter = (string) $request->input('redirect_after_update', '');

        $resourceType->update($request->validated());

        if ($redirectAfter === 'edit') {
            return redirect()
                ->route('entities.resource-types.edit', $resourceType)
                ->with('success', 'Type de ressource mis à jour avec succès.');
        }

        if ($redirectAfter === 'show') {
            return redirect()
                ->route('entities.resource-types.show', $resourceType)
                ->with('success', 'Type de ressource mis à jour avec succès.');
        }

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
