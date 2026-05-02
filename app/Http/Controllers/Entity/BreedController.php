<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreBreedRequest;
use App\Http\Requests\Entity\UpdateBreedRequest;
use App\Http\Requests\Entity\UpdateBreedSpellsRequest;
use App\Http\Resources\Entity\BreedResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BreedController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Breed::class);

        $query = Breed::query()
            ->visibleToUser(request()->user())
            ->with([
                'createdBy',
                'npcs',
                'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                    ->orderBy('breed_spell.slot_index')
                    ->orderBy('breed_spell.choice_order')
                    ->orderBy('spells.name'),
            ]);

        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('specificity', 'like', "%{$search}%");
            });
        }

        if (request()->has('life') && request()->life !== '') {
            $query->where('life', request()->life);
        }

        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'life', 'life_dice', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $breeds = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/breed/Index', [
            'breeds' => BreedResource::collection($breeds),
            'filters' => request()->only(['search', 'life']),
        ]);
    }

    /**
     * La création se fait surtout via la modal sur la liste ; cette route évite une 404 pour les favoris / liens.
     */
    public function create(): RedirectResponse
    {
        $this->authorize('create', Breed::class);

        return redirect()->route('entities.breeds.index');
    }

    public function store(StoreBreedRequest $request): RedirectResponse
    {
        $this->authorize('create', Breed::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()?->id;

        $breed = Breed::create($data);

        return redirect()->route('entities.breeds.edit', $breed)
            ->with('success', 'Classe créée avec succès.');
    }

    public function show(Breed $breed): Response
    {
        $this->authorize('view', $breed);

        $breed->load([
            'createdBy',
            'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                ->orderBy('breed_spell.slot_index')
                ->orderBy('breed_spell.choice_order')
                ->orderBy('spells.name'),
            'npcs' => fn ($q) => $q->limit(100),
        ]);

        return Inertia::render('Pages/entity/breed/Show', [
            'breed' => new BreedResource($breed),
        ]);
    }

    public function edit(Breed $breed): Response
    {
        $this->authorize('update', $breed);

        $breed->load([
            'createdBy',
            'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                ->orderBy('breed_spell.slot_index')
                ->orderBy('breed_spell.choice_order')
                ->orderBy('spells.name'),
        ]);

        $availableSpells = Spell::query()
            ->select(['id', 'name', 'level', 'description'])
            ->orderBy('name')
            ->limit(8000)
            ->get();

        return Inertia::render('Pages/entity/breed/Edit', [
            'breed' => new BreedResource($breed),
            'availableSpells' => $availableSpells,
        ]);
    }

    public function update(UpdateBreedRequest $request, Breed $breed): RedirectResponse
    {
        $this->authorize('update', $breed);

        $breed->update($request->validated());

        return redirect()->route('entities.breeds.show', $breed)
            ->with('success', 'Classe mise à jour avec succès.');
    }

    /**
     * Synchronise les sorts liés à la classe (pivot breed_spell avec emplacements).
     */
    public function updateSpells(UpdateBreedSpellsRequest $request, Breed $breed): RedirectResponse
    {
        $breed->spells()->sync($request->validatedSpellsSyncPayload());

        return redirect()->back()
            ->with('success', 'Sorts de la classe mis à jour.');
    }

    public function delete(Breed $breed): RedirectResponse
    {
        $this->authorize('delete', $breed);

        $breed->delete();

        return redirect()->route('entities.breeds.index')
            ->with('success', 'Classe supprimée (corbeille).');
    }

    /**
     * Télécharge un PDF pour une ou plusieurs breeds (affichées « Classes »).
     */
    public function downloadPdf(?Breed $breed = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $breeds = Breed::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Breed::class);

                $pdf = PdfService::generateForEntities($breeds, 'breed');
                $filename = 'breeds-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $breed) {
            abort(404);
        }

        $this->authorize('view', $breed);

        $pdf = PdfService::generateForEntity($breed, 'breed');
        $filename = 'breed-'.$breed->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
