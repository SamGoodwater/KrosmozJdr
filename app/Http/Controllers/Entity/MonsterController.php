<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreMonsterRequest;
use App\Http\Requests\Entity\UpdateMonsterRequest;
use App\Models\Entity\Monster;
use App\Http\Resources\Entity\MonsterResource;
use Inertia\Inertia;

class MonsterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Monster::class);
        
        $query = Monster::with(['creature', 'monsterRace']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->whereHas('creature', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('size') && request()->size !== '') {
            $query->where('size', request()->size);
        }
        
        if (request()->has('is_boss') && request()->is_boss !== '') {
            $query->where('is_boss', request()->is_boss);
        }
        
        if (request()->has('monster_race_id') && request()->monster_race_id !== '') {
            $query->where('monster_race_id', request()->monster_race_id);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'size', 'is_boss', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $monsters = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/monster/Index', [
            'monsters' => MonsterResource::collection($monsters),
            'filters' => request()->only(['search', 'size', 'is_boss', 'monster_race_id']),
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
    public function store(StoreMonsterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Monster $monster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Monster $monster)
    {
        $this->authorize('update', $monster);
        
        $monster->load(['creature', 'monsterRace', 'scenarios', 'campaigns', 'spellInvocations']);
        
        // Charger toutes les entités disponibles pour la recherche
        $availableScenarios = \App\Models\Entity\Scenario::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        $availableCampaigns = \App\Models\Entity\Campaign::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        $availableSpells = \App\Models\Entity\Spell::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Pages/entity/monster/Edit', [
            'monster' => new MonsterResource($monster),
            'availableScenarios' => $availableScenarios,
            'availableCampaigns' => $availableCampaigns,
            'availableSpells' => $availableSpells,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMonsterRequest $request, Monster $monster)
    {
        $this->authorize('update', $monster);
        
        $monster->update($request->validated());
        
        $monster->load(['creature', 'monsterRace']);
        
        return redirect()->route('entities.monsters.show', $monster)
            ->with('success', 'Monstre mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Monster $monster)
    {
        //
    }

    /**
     * Update the scenarios of a monster.
     */
    public function updateScenarios(\Illuminate\Http\Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);
        
        $request->validate([
            'scenarios' => 'required|array',
            'scenarios.*' => 'exists:scenarios,id',
        ]);
        
        $monster->scenarios()->sync($request->scenarios);
        
        return redirect()->back()
            ->with('success', 'Scénarios du monstre mis à jour avec succès.');
    }

    /**
     * Update the campaigns of a monster.
     */
    public function updateCampaigns(\Illuminate\Http\Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);
        
        $request->validate([
            'campaigns' => 'required|array',
            'campaigns.*' => 'exists:campaigns,id',
        ]);
        
        $monster->campaigns()->sync($request->campaigns);
        
        return redirect()->back()
            ->with('success', 'Campagnes du monstre mises à jour avec succès.');
    }

    /**
     * Update the spell invocations of a monster.
     */
    public function updateSpellInvocations(\Illuminate\Http\Request $request, Monster $monster)
    {
        $this->authorize('update', $monster);
        
        $request->validate([
            'spellInvocations' => 'required|array',
            'spellInvocations.*' => 'exists:spells,id',
        ]);
        
        $monster->spellInvocations()->sync($request->spellInvocations);
        
        return redirect()->back()
            ->with('success', 'Sorts d\'invocation du monstre mis à jour avec succès.');
    }
}
