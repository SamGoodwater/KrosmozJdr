<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreSpecializationRequest;
use App\Http\Requests\Entity\UpdateSpecializationCapabilitiesRequest;
use App\Http\Requests\Entity\UpdateSpecializationConsumablesRequest;
use App\Http\Requests\Entity\UpdateSpecializationCreatureTraitsRequest;
use App\Http\Requests\Entity\UpdateSpecializationItemsRequest;
use App\Http\Requests\Entity\UpdateSpecializationRequest;
use App\Http\Requests\Entity\UpdateSpecializationResourcesRequest;
use App\Http\Requests\Entity\UpdateSpecializationSectionsRequest;
use App\Http\Requests\Entity\UpdateSpecializationSpellsRequest;
use App\Http\Resources\Entity\CapabilityResource;
use App\Http\Resources\Entity\ConsumableResource;
use App\Http\Resources\Entity\CreatureTraitResource;
use App\Http\Resources\Entity\ItemResource;
use App\Http\Resources\Entity\ResourceResource;
use App\Http\Resources\Entity\SpecializationResource;
use App\Http\Resources\Entity\SpellResource;
use App\Http\Resources\SectionResource;
use App\Models\Entity\Capability;
use App\Models\Entity\Consumable;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\Section;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SpecializationController extends Controller
{
    use RedirectsAfterEntityCreate;
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Specialization::class);

        $query = Specialization::query()
            ->with([
                'createdBy',
                'npcs',
                'capabilities' => fn ($q) => $q->orderBy('name'),
                'spells' => fn ($q) => $q->orderBy('name'),
            ])
            ->withCount(['capabilities', 'spells']);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'capabilities_count', 'spells_count', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $specializations = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/specialization/Index', [
            'specializations' => SpecializationResource::collection($specializations),
            'filters' => request()->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        $this->authorize('create', Specialization::class);

        return redirect()->route('entities.specializations.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecializationRequest $request): RedirectResponse
    {
        $this->authorize('create', Specialization::class);

        $validated = $request->validated();
        $validated['created_by'] = $request->user()?->id;

        $specialization = Specialization::create($validated);

        return $this->redirectAfterEntityStore(
            $request,
            $specialization,
            'entities.specializations.edit',
            'entities.specializations.index',
            'Spécialisation créée avec succès.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization): Response
    {
        $this->authorize('view', $specialization);

        $specialization->load([
            'createdBy',
            'npcs' => fn ($q) => $q->limit(100),
            'capabilities' => fn ($q) => $q->orderBy('name'),
            'spells' => fn ($q) => $q->orderBy('name'),
            'creatureTraits' => fn ($q) => $q->orderBy('name'),
            'consumables' => fn ($q) => $q->orderBy('name'),
            'resources' => fn ($q) => $q->orderBy('name'),
            'items' => fn ($q) => $q->orderBy('name'),
            'sections' => fn ($q) => $q->orderByPivot('level')->orderBy('title'),
        ]);

        return Inertia::render('Pages/entity/specialization/Show', [
            'specialization' => new SpecializationResource($specialization),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialization $specialization): Response
    {
        $this->authorize('update', $specialization);

        $specialization->load([
            'createdBy',
            'capabilities' => fn ($q) => $q->orderBy('name'),
            'spells' => fn ($q) => $q->orderBy('name'),
            'creatureTraits' => fn ($q) => $q->orderBy('name'),
            'consumables' => fn ($q) => $q->orderBy('name'),
            'resources' => fn ($q) => $q->orderBy('name'),
            'items' => fn ($q) => $q->orderBy('name'),
            'sections' => fn ($q) => $q->orderByPivot('level')->orderBy('title'),
        ]);

        $request = request();

        $availableSpells = SpellResource::collection(
            Spell::query()->orderBy('name')->limit(8000)->get()
        )->toArray($request);

        $availableCapabilities = CapabilityResource::collection(
            Capability::query()->orderBy('name')->limit(5000)->get()
        )->toArray($request);

        $availableCreatureTraits = CreatureTraitResource::collection(
            CreatureTrait::query()->orderBy('name')->limit(5000)->get()
        )->toArray($request);

        $availableConsumables = ConsumableResource::collection(
            Consumable::query()->orderBy('name')->limit(5000)->get()
        )->toArray($request);

        $availableResources = ResourceResource::collection(
            Resource::query()->orderBy('name')->limit(5000)->get()
        )->toArray($request);

        $availableItems = ItemResource::collection(
            Item::query()->orderBy('name')->limit(5000)->get()
        )->toArray($request);

        $availableSections = SectionResource::collection(
            Section::query()->orderBy('title')->limit(5000)->get()
        )->toArray($request);

        return Inertia::render('Pages/entity/specialization/Edit', [
            'specialization' => new SpecializationResource($specialization),
            'availableSpells' => $availableSpells,
            'availableCapabilities' => $availableCapabilities,
            'availableCreatureTraits' => $availableCreatureTraits,
            'availableConsumables' => $availableConsumables,
            'availableResources' => $availableResources,
            'availableItems' => $availableItems,
            'availableSections' => $availableSections,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecializationRequest $request, Specialization $specialization): RedirectResponse
    {
        $this->authorize('update', $specialization);

        $validated = $request->validated();
        if (count($validated) > 0) {
            $specialization->update($validated);
        }

        return redirect()->route('entities.specializations.show', $specialization)
            ->with('success', 'Spécialisation mise à jour avec succès.');
    }

    public function updateSpells(UpdateSpecializationSpellsRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->spells()->sync($request->validatedLeveledSyncPayload());

        return redirect()->back()->with('success', 'Sorts de la spécialisation mis à jour.');
    }

    public function updateCapabilities(UpdateSpecializationCapabilitiesRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->capabilities()->sync($request->validatedLeveledSyncPayload());

        return redirect()->back()->with('success', 'Capacités de la spécialisation mises à jour.');
    }

    public function updateCreatureTraits(UpdateSpecializationCreatureTraitsRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->creatureTraits()->sync($request->validatedCreatureTraitSyncPayload());

        return redirect()->back()
            ->with('success', 'Traits de la spécialisation mis à jour.');
    }

    public function updateConsumables(UpdateSpecializationConsumablesRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->consumables()->sync($request->validatedLeveledQuantitySyncPayload());

        return redirect()->back()->with('success', 'Consommables de la spécialisation mis à jour.');
    }

    public function updateResources(UpdateSpecializationResourcesRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->resources()->sync($request->validatedLeveledQuantitySyncPayload());

        return redirect()->back()->with('success', 'Ressources de la spécialisation mises à jour.');
    }

    public function updateItems(UpdateSpecializationItemsRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->items()->sync($request->validatedLeveledQuantitySyncPayload());

        return redirect()->back()->with('success', 'Items de la spécialisation mis à jour.');
    }

    public function updateSections(UpdateSpecializationSectionsRequest $request, Specialization $specialization): RedirectResponse
    {
        $specialization->sections()->sync($request->validatedLeveledSyncPayload());

        return redirect()->back()->with('success', 'Sections de la spécialisation mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Specialization $specialization): RedirectResponse
    {
        $this->authorize('delete', $specialization);

        $specialization->delete();

        return redirect()->route('entities.specializations.index')
            ->with('success', 'Spécialisation supprimée (corbeille).');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs specializations.
     *
     * @param  Specialization|null  $specialization  La specialization unique (si une seule)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Specialization $specialization = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $specializations = Specialization::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Specialization::class);

                $pdf = PdfService::generateForEntities($specializations, 'specialization');
                $filename = 'specializations-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $specialization) {
            abort(404);
        }

        $this->authorize('view', $specialization);

        $pdf = PdfService::generateForEntity($specialization, 'specialization');
        $filename = 'specialization-'.$specialization->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
