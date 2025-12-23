<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreResourceRequest;
use App\Http\Requests\Entity\UpdateResourceRequest;
use App\Models\Entity\Resource;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Shop;
use App\Models\Entity\Scenario;
use App\Models\Entity\Campaign;
use App\Models\Type\ResourceType;
use App\Http\Resources\Entity\ResourceResource;
use App\Services\PdfService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Resource::class);

        $user = request()->user();
        
        $query = Resource::with(['createdBy', 'resourceType', 'consumables']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('level') && request()->level !== '') {
            $query->where('level', request()->level);
        }
        
        if (request()->has('resource_type_id') && request()->resource_type_id !== '') {
            $query->where('resource_type_id', request()->resource_type_id);
        }

        if (request()->has('rarity') && request()->rarity !== '') {
            $query->where('rarity', (int) request()->rarity);
        }

        if (request()->has('usable') && request()->usable !== '') {
            $query->where('usable', (int) request()->usable);
        }

        if (request()->has('auto_update') && request()->auto_update !== '') {
            $query->where('auto_update', (int) request()->auto_update);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'level', 'rarity', 'price', 'weight', 'usable', 'auto_update', 'dofusdb_id', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $resources = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/resource/Index', [
            'resources' => ResourceResource::collection($resources),
            'filters' => request()->only(['search', 'level', 'resource_type_id', 'rarity', 'usable', 'auto_update']),
            'resourceTypes' => ResourceType::query()->select('id', 'name')->orderBy('name')->get(),
            'can' => [
                'create' => $user ? $user->can('create', Resource::class) : false,
                'updateAny' => $user ? $user->can('updateAny', Resource::class) : false,
                'deleteAny' => $user ? $user->can('deleteAny', Resource::class) : false,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Resource::class);

        return Inertia::render('Pages/entity/resource/Edit', [
            'resource' => null,
            'resourceTypes' => ResourceType::query()->select('id', 'name')->orderBy('name')->get(),
            'availableItems' => Item::query()->select('id', 'name', 'description', 'level')->orderBy('name')->get(),
            'availableConsumables' => Consumable::query()->select('id', 'name', 'description', 'level')->orderBy('name')->get(),
            'availableCreatures' => Creature::query()->select('id', 'name', 'description', 'level')->orderBy('name')->get(),
            'availableShops' => Shop::query()->select('id', 'name', 'description')->orderBy('name')->get(),
            'availableScenarios' => Scenario::query()->select('id', 'name', 'description')->orderBy('name')->get(),
            'availableCampaigns' => Campaign::query()->select('id', 'name', 'description')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResourceRequest $request)
    {
        $this->authorize('create', Resource::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        // Valeurs par défaut / normalisation (éviter d'insérer explicitement NULL sur des colonnes NOT NULL)
        $data['rarity'] = array_key_exists('rarity', $data) && $data['rarity'] !== null ? (int) $data['rarity'] : 0;

        // Normaliser les booléens
        if (array_key_exists('usable', $data)) {
            $data['usable'] = (int) ((bool) $data['usable']);
        } else {
            $data['usable'] = 0;
        }

        if (array_key_exists('auto_update', $data)) {
            $data['auto_update'] = (bool) $data['auto_update'];
        } else {
            $data['auto_update'] = false;
        }

        $resource = Resource::create($data);

        return redirect()
            ->route('entities.resources.index')
            ->with('success', 'Ressource créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $this->authorize('view', $resource);

        $resource->load([
            'createdBy',
            'resourceType',
            'consumables',
            'creatures',
            'items',
            'scenarios',
            'campaigns',
            'shops',
        ]);

        return Inertia::render('Pages/entity/resource/Show', [
            'resource' => new ResourceResource($resource),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        $this->authorize('update', $resource);

        $resource->load([
            'createdBy',
            'resourceType',
            'consumables',
            'creatures',
            'items',
            'scenarios',
            'campaigns',
            'shops',
        ]);

        return Inertia::render('Pages/entity/resource/Edit', [
            'resource' => new ResourceResource($resource),
            'resourceTypes' => ResourceType::query()->select('id', 'name')->orderBy('name')->get(),
            'availableItems' => Item::query()->select('id', 'name', 'description', 'level')->orderBy('name')->get(),
            'availableConsumables' => Consumable::query()->select('id', 'name', 'description', 'level')->orderBy('name')->get(),
            'availableCreatures' => Creature::query()->select('id', 'name', 'description', 'level')->orderBy('name')->get(),
            'availableShops' => Shop::query()->select('id', 'name', 'description')->orderBy('name')->get(),
            'availableScenarios' => Scenario::query()->select('id', 'name', 'description')->orderBy('name')->get(),
            'availableCampaigns' => Campaign::query()->select('id', 'name', 'description')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        $this->authorize('update', $resource);

        $data = $request->validated();

        if (array_key_exists('rarity', $data) && $data['rarity'] === null) {
            $data['rarity'] = 0;
        }

        if (array_key_exists('usable', $data)) {
            $data['usable'] = (int) ((bool) $data['usable']);
        }

        if (array_key_exists('auto_update', $data)) {
            $data['auto_update'] = (bool) $data['auto_update'];
        }

        $resource->update($data);

        return redirect()
            ->route('entities.resources.show', $resource)
            ->with('success', 'Ressource mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Resource $resource)
    {
        $this->authorize('delete', $resource);

        $resource->delete();

        return redirect()
            ->route('entities.resources.index')
            ->with('success', 'Ressource supprimée avec succès.');
    }

    /**
     * Met à jour la relation many-to-many Resource <-> Item (pivot: quantity).
     */
    public function updateItems(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sync = $this->buildPivotSyncData($validated['items'] ?? []);
        $resource->items()->sync($sync);

        return back()->with('success', 'Objets liés mis à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Consumable (pivot: quantity).
     */
    public function updateConsumables(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'consumables' => ['nullable', 'array'],
            'consumables.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sync = $this->buildPivotSyncData($validated['consumables'] ?? []);
        $resource->consumables()->sync($sync);

        return back()->with('success', 'Consommables liés mis à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Creature (pivot: quantity).
     */
    public function updateCreatures(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'creatures' => ['nullable', 'array'],
            'creatures.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sync = $this->buildPivotSyncData($validated['creatures'] ?? []);
        $resource->creatures()->sync($sync);

        return back()->with('success', 'Créatures liées mises à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Shop (pivot: quantity, price, comment).
     */
    public function updateShops(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'shops' => ['nullable', 'array'],
            'shops.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'shops.*.price' => ['nullable', 'numeric', 'min:0'],
            'shops.*.comment' => ['nullable', 'string', 'max:255'],
        ]);

        $sync = [];
        foreach (($validated['shops'] ?? []) as $shopId => $pivot) {
            $shopId = (int) $shopId;
            $sync[$shopId] = [
                'quantity' => (int) ($pivot['quantity'] ?? 0),
                'price' => (int) ($pivot['price'] ?? 0),
                'comment' => $pivot['comment'] ?? null,
            ];
        }

        $resource->shops()->sync($sync);

        return back()->with('success', 'Boutiques liées mises à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Scenario (sans pivot).
     */
    public function updateScenarios(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'scenarios' => ['nullable', 'array'],
            'scenarios.*' => ['integer', 'exists:scenarios,id'],
        ]);

        $resource->scenarios()->sync($validated['scenarios'] ?? []);

        return back()->with('success', 'Scénarios liés mis à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Campaign (sans pivot).
     */
    public function updateCampaigns(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'campaigns' => ['nullable', 'array'],
            'campaigns.*' => ['integer', 'exists:campaigns,id'],
        ]);

        $resource->campaigns()->sync($validated['campaigns'] ?? []);

        return back()->with('success', 'Campagnes liées mises à jour.');
    }

    /**
     * Construit un payload compatible sync() pour des pivots type quantity.
     *
     * @param array $data Format: { id: { quantity: X } }
     * @return array<int, array{quantity:int}>
     */
    private function buildPivotSyncData(array $data): array
    {
        $sync = [];

        foreach ($data as $id => $pivot) {
            $id = (int) $id;
            $sync[$id] = [
                'quantity' => (int) ($pivot['quantity'] ?? 0),
            ];
        }

        return $sync;
    }

    /**
     * Télécharge un PDF pour un ou plusieurs resources.
     * 
     * @param Resource|null $resource La resource unique (si une seule)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Resource $resource = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $resources = Resource::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Resource::class);
                
                $pdf = PdfService::generateForEntities($resources, 'resource');
                $filename = 'resources-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$resource) {
            abort(404);
        }
        
        $this->authorize('view', $resource);
        
        $pdf = PdfService::generateForEntity($resource, 'resource');
        $filename = 'resource-' . $resource->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
