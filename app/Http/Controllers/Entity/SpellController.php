<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\UpdateSpellEffectGroupRequest;
use App\Http\Requests\Entity\StoreSpellRequest;
use App\Http\Requests\Entity\UpdateSpellRequest;
use App\Http\Resources\Entity\SpellResource;
use App\Models\Effect;
use App\Models\Entity\Spell;
use App\Models\Type\SpellType;
use App\Services\Effect\EffectGroupEditorDataService;
use App\Services\Effect\EffectGroupUpdateService;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
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
            $query->where(function ($q) use ($search) {
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
        $spellTypes = SpellType::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Pages/entity/spell/Index', [
            'spells' => SpellResource::collection($spells),
            'filters' => request()->only(['search', 'level', 'pa']),
            'spellTypes' => $spellTypes,
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
    public function store(StoreSpellRequest $request): RedirectResponse
    {
        $this->authorize('create', Spell::class);

        $data = $request->validated();
        $spellTypes = $data['spellTypes'] ?? null;
        unset($data['spellTypes']);

        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = '';
        }

        foreach (['element', 'category', 'powerful'] as $intKey) {
            if (array_key_exists($intKey, $data) && $data[$intKey] === null) {
                unset($data[$intKey]);
            }
        }

        $data['created_by'] = $request->user()?->id;

        $spell = Spell::create($data);

        if (is_array($spellTypes)) {
            $spell->spellTypes()->sync($spellTypes);
        }

        return redirect()->route('entities.spells.index')
            ->with('success', 'Sort créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Spell $spell)
    {
        $this->authorize('view', $spell);

        $spell->load([
            'createdBy',
            'spellTypes',
            'effects.degrees.effectSubEffects.subEffect',
        ]);

        return Inertia::render('Pages/entity/spell/Show', [
            'spell' => new SpellResource($spell),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Spell $spell)
    {
        $this->authorize('update', $spell);

        $spell->load(['createdBy', 'creatures', 'breeds', 'spellTypes', 'effects.degrees']);

        $availableBreeds = \App\Models\Entity\Breed::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        $availableSpellTypes = \App\Models\Type\SpellType::select('id', 'name', 'description', 'color')
            ->orderBy('name')
            ->get();

        $availableEffects = Effect::with('degrees')
            ->orderBy('name')
            ->get()
            ->flatMap(function (Effect $e) {
                return $e->degrees->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => ($e->name ?? $e->slug ?? 'Effet #'.$e->id).' · D'.$d->degree,
                    'slug' => $d->slug,
                    'degree' => $d->degree,
                    'target_type' => $e->target_type ?? Effect::TARGET_DIRECT,
                    'area' => $d->area,
                    'effect_definition_id' => $e->id,
                ]);
            })
            ->values()
            ->all();

        $editorData = app(EffectGroupEditorDataService::class);

        return Inertia::render('Pages/entity/spell/Edit', [
            'spell' => new SpellResource($spell),
            'availableBreeds' => $availableBreeds,
            'availableSpellTypes' => $availableSpellTypes,
            'availableEffects' => $availableEffects,
            'effectEntityType' => 'spell',
            'effectFormOptions' => $editorData->formOptions(),
            'spellEffectGroups' => $editorData->distinctGroupsForSpell($spell),
        ]);
    }

    /**
     * Enregistre un groupe d’effets depuis la fiche sort (même charge utile que l’admin).
     */
    public function updateEffectGroup(UpdateSpellEffectGroupRequest $request, Spell $spell, Effect $effect): RedirectResponse
    {
        $this->authorize('update', $spell);

        app(EffectGroupUpdateService::class)->updateGroup($effect, $request->validated());

        return redirect()->route('entities.spells.edit', $spell)
            ->with('success', 'Effets du groupe enregistrés.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpellRequest $request, Spell $spell)
    {
        $this->authorize('update', $spell);

        $data = $request->validated();
        $spellTypes = $data['spellTypes'] ?? null;
        unset($data['spellTypes']);

        $spell->update($data);

        if (is_array($spellTypes)) {
            $spell->spellTypes()->sync($spellTypes);
        }

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
     * Remove the specified resource from storage.
     */
    public function delete(Spell $spell)
    {
        //
    }

    /**
     * Télécharge un PDF pour un ou plusieurs spells.
     *
     * @param  Spell|null  $spell  Le spell unique (si un seul)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Spell $spell = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $spells = Spell::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Spell::class);

                $pdf = PdfService::generateForEntities($spells, 'spell');
                $filename = 'spells-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $spell) {
            abort(404);
        }

        $this->authorize('view', $spell);

        $pdf = PdfService::generateForEntity($spell, 'spell');
        $filename = 'spell-'.$spell->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
