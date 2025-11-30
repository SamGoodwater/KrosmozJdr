<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCreatureRequest;
use App\Http\Requests\Entity\UpdateCreatureRequest;
use App\Models\Entity\Creature;
use App\Http\Resources\Entity\CreatureResource;
use Inertia\Inertia;

class CreatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeForUser(auth()->user(), 'viewAny', Creature::class);
        
        $query = Creature::with(['createdBy', 'npc', 'monster']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $creatures = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/creature/Index', [
            'creatures' => CreatureResource::collection($creatures),
            'filters' => request()->only(['search']),
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
    public function store(StoreCreatureRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Creature $creature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Creature $creature)
    {
        $this->authorize('update', $creature);
        
        $creature->load([
            'createdBy', 
            'items', 
            'resources', 
            'consumables', 
            'spells'
        ]);
        
        // Charger toutes les entités disponibles pour la recherche
        $availableItems = \App\Models\Entity\Item::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availableConsumables = \App\Models\Entity\Consumable::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availableSpells = \App\Models\Entity\Spell::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Pages/entity/creature/Edit', [
            'creature' => new CreatureResource($creature),
            'availableItems' => $availableItems,
            'availableResources' => $availableResources,
            'availableConsumables' => $availableConsumables,
            'availableSpells' => $availableSpells,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreatureRequest $request, Creature $creature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Creature $creature)
    {
        //
    }

    /**
     * Update the items of a creature (avec quantités).
     */
    public function updateItems(\Illuminate\Http\Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);
        
        $request->validate([
            'items' => 'array',
        ]);
        
        $syncData = [];
        foreach ($request->items as $itemId => $pivotData) {
            $itemId = (int)$itemId; // S'assurer que l'ID est un entier
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $syncData[$itemId] = ['quantity' => (int)$pivotData['quantity']];
            }
        }
        
        if (!empty($syncData)) {
            $itemIds = array_keys($syncData);
            $existingItems = \App\Models\Entity\Item::whereIn('id', $itemIds)->pluck('id')->toArray();
            $invalidIds = array_diff($itemIds, $existingItems);
            
            if (!empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['items' => 'Certains objets n\'existent pas.'])
                    ->withInput();
            }
        }
        
        $creature->items()->sync($syncData);
        
        return redirect()->back()
            ->with('success', 'Objets de la créature mis à jour avec succès.');
    }

    /**
     * Update the resources of a creature (avec quantités).
     */
    public function updateResources(\Illuminate\Http\Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);
        
        $request->validate([
            'resources' => 'array',
        ]);
        
        $syncData = [];
        foreach ($request->resources as $resourceId => $pivotData) {
            $resourceId = (int)$resourceId; // S'assurer que l'ID est un entier
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $syncData[$resourceId] = ['quantity' => (int)$pivotData['quantity']];
            }
        }
        
        if (!empty($syncData)) {
            $resourceIds = array_keys($syncData);
            $existingResources = \App\Models\Entity\Resource::whereIn('id', $resourceIds)->pluck('id')->toArray();
            $invalidIds = array_diff($resourceIds, $existingResources);
            
            if (!empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['resources' => 'Certaines ressources n\'existent pas.'])
                    ->withInput();
            }
        }
        
        $creature->resources()->sync($syncData);
        
        return redirect()->back()
            ->with('success', 'Ressources de la créature mises à jour avec succès.');
    }

    /**
     * Update the consumables of a creature (avec quantités).
     */
    public function updateConsumables(\Illuminate\Http\Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);
        
        $request->validate([
            'consumables' => 'array',
        ]);
        
        $syncData = [];
        foreach ($request->consumables as $consumableId => $pivotData) {
            $consumableId = (int)$consumableId; // S'assurer que l'ID est un entier
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $syncData[$consumableId] = ['quantity' => (int)$pivotData['quantity']];
            }
        }
        
        if (!empty($syncData)) {
            $consumableIds = array_keys($syncData);
            $existingConsumables = \App\Models\Entity\Consumable::whereIn('id', $consumableIds)->pluck('id')->toArray();
            $invalidIds = array_diff($consumableIds, $existingConsumables);
            
            if (!empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['consumables' => 'Certains consommables n\'existent pas.'])
                    ->withInput();
            }
        }
        
        $creature->consumables()->sync($syncData);
        
        return redirect()->back()
            ->with('success', 'Consommables de la créature mis à jour avec succès.');
    }

    /**
     * Update the spells of a creature.
     */
    public function updateSpells(\Illuminate\Http\Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);
        
        $request->validate([
            'spells' => 'array',
            'spells.*' => 'exists:spells,id',
        ]);
        
        $creature->spells()->sync($request->spells);
        
        return redirect()->back()
            ->with('success', 'Sorts de la créature mis à jour avec succès.');
    }
}
