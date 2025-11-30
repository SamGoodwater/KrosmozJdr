<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreNpcRequest;
use App\Http\Requests\Entity\UpdateNpcRequest;
use App\Models\Entity\Npc;
use App\Http\Resources\Entity\NpcResource;
use Inertia\Inertia;

class NpcController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeForUser(auth()->user(), 'viewAny', Npc::class);
        
        $query = Npc::with(['creature', 'classe', 'specialization']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->whereHas('creature', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('classe_id') && request()->classe_id !== '') {
            $query->where('classe_id', request()->classe_id);
        }
        
        if (request()->has('specialization_id') && request()->specialization_id !== '') {
            $query->where('specialization_id', request()->specialization_id);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $npcs = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/npc/Index', [
            'npcs' => NpcResource::collection($npcs),
            'filters' => request()->only(['search', 'classe_id', 'specialization_id']),
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
    public function store(StoreNpcRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Npc $npc)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Npc $npc)
    {
        $this->authorize('update', $npc);
        
        $npc->load(['creature', 'classe', 'specialization', 'panoplies', 'scenarios', 'campaigns']);
        
        // Charger toutes les entités disponibles pour la recherche
        $availablePanoplies = \App\Models\Entity\Panoply::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        $availableScenarios = \App\Models\Entity\Scenario::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        $availableCampaigns = \App\Models\Entity\Campaign::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Pages/entity/npc/Edit', [
            'npc' => new NpcResource($npc),
            'availablePanoplies' => $availablePanoplies,
            'availableScenarios' => $availableScenarios,
            'availableCampaigns' => $availableCampaigns,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNpcRequest $request, Npc $npc)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Npc $npc)
    {
        //
    }

    /**
     * Update the panoplies of an NPC.
     */
    public function updatePanoplies(\Illuminate\Http\Request $request, Npc $npc)
    {
        $this->authorize('update', $npc);
        
        $request->validate([
            'panoplies' => 'required|array',
            'panoplies.*' => 'exists:panoplies,id',
        ]);
        
        $npc->panoplies()->sync($request->panoplies);
        
        return redirect()->back()
            ->with('success', 'Panoplies du PNJ mises à jour avec succès.');
    }

    /**
     * Update the scenarios of an NPC.
     */
    public function updateScenarios(\Illuminate\Http\Request $request, Npc $npc)
    {
        $this->authorize('update', $npc);
        
        $request->validate([
            'scenarios' => 'required|array',
            'scenarios.*' => 'exists:scenarios,id',
        ]);
        
        $npc->scenarios()->sync($request->scenarios);
        
        return redirect()->back()
            ->with('success', 'Scénarios du PNJ mis à jour avec succès.');
    }

    /**
     * Update the campaigns of an NPC.
     */
    public function updateCampaigns(\Illuminate\Http\Request $request, Npc $npc)
    {
        $this->authorize('update', $npc);
        
        $request->validate([
            'campaigns' => 'required|array',
            'campaigns.*' => 'exists:campaigns,id',
        ]);
        
        $npc->campaigns()->sync($request->campaigns);
        
        return redirect()->back()
            ->with('success', 'Campagnes du PNJ mises à jour avec succès.');
    }
}
