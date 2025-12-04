<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreSpellRequest;
use App\Http\Requests\Entity\UpdateSpellRequest;
use App\Models\Entity\Spell;
use App\Http\Resources\Entity\SpellResource;
use App\Services\PdfService;
use Inertia\Inertia;

class SpellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Spell::class);
        
        $query = Spell::with(['createdBy', 'creatures', 'classes', 'spellTypes']);
        
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
        
        if (request()->has('pa') && request()->pa !== '') {
            $query->where('pa', request()->pa);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'level', 'pa', 'po', 'area', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $spells = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/spell/Index', [
            'spells' => SpellResource::collection($spells),
            'filters' => request()->only(['search', 'level', 'pa']),
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
    public function store(StoreSpellRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Spell $spell)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Spell $spell)
    {
        $this->authorize('update', $spell);
        
        $spell->load(['createdBy', 'creatures', 'classes', 'spellTypes']);
        
        // Charger toutes les classes disponibles pour la recherche
        $availableClasses = \App\Models\Entity\Classe::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        // Charger tous les types de sorts disponibles pour la recherche
        $availableSpellTypes = \App\Models\Type\SpellType::select('id', 'name', 'description', 'color')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Pages/entity/spell/Edit', [
            'spell' => new SpellResource($spell),
            'availableClasses' => $availableClasses,
            'availableSpellTypes' => $availableSpellTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpellRequest $request, Spell $spell)
    {
        $this->authorize('update', $spell);
        
        $spell->update($request->validated());
        
        $spell->load(['createdBy', 'creatures', 'classes', 'spellTypes']);
        
        return redirect()->route('entities.spells.show', $spell)
            ->with('success', 'Sort mis à jour avec succès.');
    }

    /**
     * Update the classes of a spell.
     */
    public function updateClasses(\Illuminate\Http\Request $request, Spell $spell)
    {
        $this->authorize('update', $spell);
        
        $request->validate([
            'classes' => 'present|array',
            'classes.*' => 'exists:classes,id',
        ]);
        
        $spell->classes()->sync($request->classes);
        
        $spell->load(['createdBy', 'creatures', 'classes', 'spellTypes']);
        
        return redirect()->back()
            ->with('success', 'Classes du sort mises à jour avec succès.');
    }

    /**
     * Update the spell types of a spell.
     */
    public function updateSpellTypes(\Illuminate\Http\Request $request, Spell $spell)
    {
        $this->authorize('update', $spell);
        
        $request->validate([
            'spellTypes' => 'present|array',
            'spellTypes.*' => 'exists:spell_types,id',
        ]);
        
        $spell->spellTypes()->sync($request->spellTypes);
        
        $spell->load(['createdBy', 'creatures', 'classes', 'spellTypes']);
        
        return redirect()->back()
            ->with('success', 'Types de sort mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Spell $spell)
    {
        //
    }

    /**
     * Télécharge un PDF pour un ou plusieurs spells.
     * 
     * @param Spell|null $spell Le spell unique (si un seul)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Spell $spell = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $spells = Spell::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Spell::class);
                
                $pdf = PdfService::generateForEntities($spells, 'spell');
                $filename = 'spells-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$spell) {
            abort(404);
        }
        
        $this->authorize('view', $spell);
        
        $pdf = PdfService::generateForEntity($spell, 'spell');
        $filename = 'spell-' . $spell->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
