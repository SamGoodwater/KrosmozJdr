<?php

namespace App\Http\Controllers\Type;

use App\Http\Controllers\Controller;
use App\Http\Requests\Type\StoreResourceTypeRequest;
use App\Http\Requests\Type\UpdateResourceTypeRequest;
use App\Models\Type\ResourceType;
use Illuminate\Http\RedirectResponse;

/**
 * CRUD des types de ressources (ResourceType).
 *
 * Liste, fiche et édition redirigent vers le registre commun
 * `/admin/content/types/resource`.
 */
class ResourceTypeController extends Controller
{
    /**
     * Liste : registre commun `/admin/content/types/resource`.
     */
    public function index(): RedirectResponse
    {
        $this->authorize('viewAny', ResourceType::class);

        return $this->redirectToRegistry();
    }

    /**
     * Fiche unitaire : même registre commun.
     */
    public function show(ResourceType $resourceType): RedirectResponse
    {
        $this->authorize('view', $resourceType);

        return $this->redirectToRegistry();
    }

    /**
     * Édition unitaire : même registre commun.
     */
    public function edit(ResourceType $resourceType): RedirectResponse
    {
        $this->authorize('update', $resourceType);

        return $this->redirectToRegistry();
    }

    /**
     * Store a newly created resource type.
     */
    public function store(StoreResourceTypeRequest $request): RedirectResponse
    {
        $this->authorize('create', ResourceType::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        ResourceType::create($data);

        return $this->redirectToRegistry()
            ->with('success', 'Type de ressource créé avec succès.');
    }

    /**
     * Update the specified resource type.
     */
    public function update(UpdateResourceTypeRequest $request, ResourceType $resourceType): RedirectResponse
    {
        $this->authorize('update', $resourceType);

        $resourceType->update($request->validated());

        return $this->redirectToRegistry()
            ->with('success', 'Type de ressource mis à jour avec succès.');
    }

    /**
     * Remove the specified resource type.
     */
    public function delete(ResourceType $resourceType): RedirectResponse
    {
        $this->authorize('delete', $resourceType);

        $resourceType->delete();

        return $this->redirectToRegistry()
            ->with('success', 'Type de ressource supprimé avec succès.');
    }

    private function redirectToRegistry(): RedirectResponse
    {
        return redirect()->route('admin.content.types.show', ['kind' => 'resource']);
    }
}
