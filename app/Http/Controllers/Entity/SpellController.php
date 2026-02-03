<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreSpellRequest;
use App\Http\Requests\Entity\UpdateSpellRequest;
use App\Models\Entity\Spell;
use App\Models\SpellEffect;
use App\Models\SpellEffectType;
use App\Http\Resources\Entity\SpellResource;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Spell::class);
        
        $query = Spell::with(['createdBy', 'creatures', 'breeds', 'spellTypes']);
        
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
        
        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes', 'spellEffects.spellEffectType']);

        $availableBreeds = \App\Models\Entity\Breed::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        $availableSpellTypes = \App\Models\Type\SpellType::select('id', 'name', 'description', 'color')
            ->orderBy('name')
            ->get();

        $availableSpellEffectTypes = SpellEffectType::orderBy('sort_order')->orderBy('name')
            ->get(['id', 'name', 'slug', 'category', 'unit', 'value_type']);

        return Inertia::render('Pages/entity/spell/Edit', [
            'spell' => new SpellResource($spell),
            'availableBreeds' => $availableBreeds,
            'availableSpellTypes' => $availableSpellTypes,
            'availableSpellEffectTypes' => $availableSpellEffectTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpellRequest $request, Spell $spell)
    {
        $this->authorize('update', $spell);
        
        $spell->update($request->validated());
        
        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes']);

        return redirect()->route('entities.spells.show', $spell)
            ->with('success', 'Sort mis à jour avec succès.');
    }

    /**
     * Update the breeds (affichées « Classes ») of a spell.
     */
    public function updateBreeds(\Illuminate\Http\Request $request, Spell $spell)
    {
        $this->authorize('update', $spell);

        $request->validate([
            'breeds' => 'present|array',
            'breeds.*' => 'exists:breeds,id',
        ]);

        $spell->breeds()->sync($request->breeds);

        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes']);

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

        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes']);

        return redirect()->back()
            ->with('success', 'Types de sort mis à jour avec succès.');
    }

    /**
     * Update the spell effects (liste d'effets avec type, valeurs, durée, cible, etc.).
     */
    public function updateEffects(Request $request, Spell $spell)
    {
        $this->authorize('update', $spell);

        $validated = $request->validate([
            'spell_effects' => 'present|array',
            'spell_effects.*.id' => 'nullable|integer|exists:spell_effects,id',
            'spell_effects.*.spell_effect_type_id' => 'required|integer|exists:spell_effect_types,id',
            'spell_effects.*.value_min' => 'nullable|integer',
            'spell_effects.*.value_max' => 'nullable|integer',
            'spell_effects.*.dice_num' => 'nullable|integer|min:0',
            'spell_effects.*.dice_side' => 'nullable|integer|min:0',
            'spell_effects.*.duration' => 'nullable|integer|min:0',
            'spell_effects.*.target_scope' => 'required|string|in:self,ally,enemy,cell,zone',
            'spell_effects.*.zone_shape' => 'nullable|string|max:32',
            'spell_effects.*.dispellable' => 'boolean',
            'spell_effects.*.order' => 'integer|min:0',
            'spell_effects.*.raw_description' => 'nullable|string|max:1000',
            'spell_effects.*.summon_monster_id' => 'nullable|integer|exists:monsters,id',
        ]);

        $idsToKeep = [];
        foreach ($validated['spell_effects'] as $index => $row) {
            $data = [
                'spell_effect_type_id' => $row['spell_effect_type_id'],
                'value_min' => $row['value_min'] ?? null,
                'value_max' => $row['value_max'] ?? null,
                'dice_num' => $row['dice_num'] ?? null,
                'dice_side' => $row['dice_side'] ?? null,
                'duration' => $row['duration'] ?? null,
                'target_scope' => $row['target_scope'],
                'zone_shape' => $row['zone_shape'] ?? null,
                'dispellable' => $row['dispellable'] ?? true,
                'order' => $row['order'] ?? $index,
                'raw_description' => $row['raw_description'] ?? null,
                'summon_monster_id' => $row['summon_monster_id'] ?? null,
            ];
            if (!empty($row['id'])) {
                $effect = SpellEffect::where('id', $row['id'])->where('spell_id', $spell->id)->first();
                if ($effect) {
                    $effect->update($data);
                    $idsToKeep[] = $effect->id;
                }
            } else {
                $effect = $spell->spellEffects()->create($data);
                $idsToKeep[] = $effect->id;
            }
        }

        $spell->spellEffects()->whereNotIn('id', $idsToKeep)->delete();

        $spell->load(['spellEffects.spellEffectType']);

        return redirect()->back()
            ->with('success', 'Effets du sort mis à jour.');
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
