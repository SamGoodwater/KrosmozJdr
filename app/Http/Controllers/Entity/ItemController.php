<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreItemRequest;
use App\Http\Requests\Entity\UpdateItemRequest;
use App\Http\Requests\Entity\UpdateItemResourcesRequest;
use App\Models\Entity\Item;
use App\Http\Resources\Entity\ItemResource;
use Inertia\Inertia;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Item::class);
        
        $query = Item::with(['createdBy', 'itemType', 'resources']);
        
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
        
        if (request()->has('rarity') && request()->rarity !== '') {
            $query->where('rarity', request()->rarity);
        }
        
        if (request()->has('item_type_id') && request()->item_type_id !== '') {
            $query->where('item_type_id', request()->item_type_id);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'level', 'rarity', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $items = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/item/Index', [
            'items' => ItemResource::collection($items),
            'filters' => request()->only(['search', 'level', 'rarity', 'item_type_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreItemRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);
        
        $item->load(['itemType', 'createdBy', 'resources']);
        
        // Charger toutes les ressources disponibles pour la recherche
        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Pages/entity/item/Edit', [
            'item' => new ItemResource($item),
            'availableResources' => $availableResources,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemRequest $request, Item $item)
    {
        $this->authorize('update', $item);
        
        $item->update($request->validated());
        
        $item->load(['itemType', 'createdBy']);
        
        return redirect()->route('entities.items.show', $item)
            ->with('success', 'Item mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Item $item)
    {
        //
    }

    /**
     * Update the resources of an item (recette de craft avec quantités).
     */
    public function updateResources(UpdateItemResourcesRequest $request, Item $item)
    {
        $this->authorize('update', $item);
        
        // Les données sont déjà normalisées et validées par la FormRequest
        $resources = $request->input('resources', []);
        $syncData = [];
        
        foreach ($resources as $resourceId => $pivotData) {
            $syncData[$resourceId] = ['quantity' => $pivotData['quantity']];
        }
        
        $item->resources()->sync($syncData);
        
        return redirect()->back()
            ->with('success', 'Ressources de l\'objet mises à jour avec succès.');
    }
}
