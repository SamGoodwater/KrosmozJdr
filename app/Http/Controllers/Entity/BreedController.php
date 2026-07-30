<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Entity\Concerns\ProvidesAvailableEntitySections;
use App\Http\Controllers\Entity\Concerns\SyncsLeveledEntitySections;
use App\Http\Requests\Entity\StoreBreedRequest;
use App\Http\Requests\Entity\UpdateBreedCapabilitiesRequest;
use App\Http\Requests\Entity\UpdateBreedCreatureTraitsRequest;
use App\Http\Requests\Entity\UpdateBreedLanguagesRequest;
use App\Http\Requests\Entity\UpdateBreedRequest;
use App\Http\Requests\Entity\UpdateBreedSectionsRequest;
use App\Http\Requests\Entity\UpdateBreedSpellsRequest;
use App\Http\Resources\Entity\BreedResource;
use App\Http\Resources\Entity\CapabilityResource;
use App\Http\Resources\Entity\CreatureTraitResource;
use App\Http\Resources\Entity\LanguageResource;
use App\Http\Resources\Entity\SpellResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Language;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\Entity\SyncBreedElementOrientations;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class BreedController extends Controller
{
    use ProvidesAvailableEntitySections;
    use RedirectsAfterEntityCreate;
    use SyncsLeveledEntitySections;

    public function index()
    {
        $this->authorize('viewAny', Breed::class);

        $query = Breed::query()
            ->visibleToUser(request()->user())
            ->with([
                'createdBy',
                'npcs',
                'elementOrientations',
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

        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'life_dice', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $breeds = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/breed/Index', [
            'breeds' => BreedResource::collection($breeds),
            'filters' => request()->only(['search']),
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

        $validated = $request->validated();
        $orientations = $validated['element_orientations'] ?? null;
        unset($validated['element_orientations']);
        $validated['created_by'] = $request->user()?->id;

        $breed = Breed::create($validated);

        app(SyncBreedElementOrientations::class)->sync($breed, $orientations);

        return $this->redirectAfterEntityStore(
            $request,
            $breed,
            'entities.breeds.edit',
            'entities.breeds.index',
            'Classe créée avec succès.',
        );
    }

    public function show(Breed $breed): Response
    {
        $this->authorize('view', $breed);

        $breed->load([
            'createdBy',
            'elementOrientations',
            'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                ->orderBy('breed_spell.slot_index')
                ->orderBy('breed_spell.choice_order')
                ->orderBy('spells.name'),
            'npcs' => fn ($q) => $q->limit(100),
            'capabilities' => fn ($q) => $q->orderBy('name'),
            'creatureTraits' => fn ($q) => $q->orderBy('name'),
            'languages',
            'sections' => Breed::orderedSectionsEagerLoadConstraint(),
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
            'elementOrientations',
            'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                ->orderBy('breed_spell.slot_index')
                ->orderBy('breed_spell.choice_order')
                ->orderBy('spells.name'),
            'capabilities' => fn ($q) => $q->orderBy('name'),
            'creatureTraits' => fn ($q) => $q->orderBy('name'),
            'languages',
            'sections' => Breed::orderedSectionsEagerLoadConstraint(),
        ]);

        $spellTable = (new Spell)->getTable();
        $levelOrder = Schema::getConnection()->getDriverName() === 'sqlite'
            ? "CAST({$spellTable}.level AS INTEGER)"
            : "CAST({$spellTable}.level AS UNSIGNED)";

        $req = request();
        $availableSpells = SpellResource::collection(
            Spell::query()
                ->orderByRaw("{$levelOrder} ASC")
                ->orderBy("{$spellTable}.name")
                ->limit(8000)
                ->get()
        )->toArray($req);

        $availableCapabilities = CapabilityResource::collection(
            Capability::query()->orderBy('name')->limit(5000)->get()
        )->toArray($req);

        $availableCreatureTraits = CreatureTraitResource::collection(
            CreatureTrait::query()->orderBy('name')->limit(5000)->get()
        )->toArray($req);

        $availableLanguages = LanguageResource::collection(
            Language::query()->orderBy('name')->limit(5000)->get()
        )->toArray($req);

        return Inertia::render('Pages/entity/breed/Edit', [
            'breed' => new BreedResource($breed),
            'availableSpells' => $availableSpells,
            'availableCapabilities' => $availableCapabilities,
            'availableCreatureTraits' => $availableCreatureTraits,
            'availableLanguages' => $availableLanguages,
            'breedOrientationKeys' => config('breed_element_orientations.allowed_orientation_keys', []),
            'availableSections' => $this->availableSectionsPayload(),
        ]);
    }

    public function updateSections(UpdateBreedSectionsRequest $request, Breed $breed): RedirectResponse
    {
        return $this->syncLeveledEntitySections(
            $breed,
            $request,
            'Sections de la classe mises à jour.',
        );
    }

    public function update(UpdateBreedRequest $request, Breed $breed): RedirectResponse
    {
        $this->authorize('update', $breed);

        $validated = $request->validated();
        $hasOrientationPayload = array_key_exists('element_orientations', $validated);
        $orientations = $validated['element_orientations'] ?? null;
        unset($validated['element_orientations']);

        if (count($validated) > 0) {
            $breed->update($validated);
        }

        if ($hasOrientationPayload) {
            app(SyncBreedElementOrientations::class)->sync($breed, $orientations);
        }

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

    public function updateCapabilities(UpdateBreedCapabilitiesRequest $request, Breed $breed): RedirectResponse
    {
        $breed->capabilities()->sync($request->validatedCapabilityIds());

        return redirect()->back()
            ->with('success', 'Capacités de la classe mises à jour.');
    }

    public function updateCreatureTraits(UpdateBreedCreatureTraitsRequest $request, Breed $breed): RedirectResponse
    {
        $breed->creatureTraits()->sync($request->validatedCreatureTraitSyncPayload());

        return redirect()->back()
            ->with('success', 'Traits de la classe mis à jour.');
    }

    public function updateLanguages(UpdateBreedLanguagesRequest $request, Breed $breed): RedirectResponse
    {
        $sync = [];
        foreach ($request->validatedLanguageIdsOrdered() as $index => $id) {
            $sync[$id] = ['sort_order' => $index];
        }
        $breed->languages()->sync($sync);

        return redirect()->back()
            ->with('success', 'Langues de la classe mises à jour.');
    }

    public function delete(Request $request, Breed $breed, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($breed, $actor);

        return redirect()->route('entities.breeds.index')
            ->with('success', 'Classe placée en corbeille.');
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
