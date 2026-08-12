<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreConsumableRequest;
use App\Http\Requests\Entity\UpdateConsumableRequest;
use App\Http\Resources\Entity\ConsumableResource;
use App\Models\Effect;
use App\Models\Entity\Consumable;
use App\Models\Type\ConsumableType;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\ObjectEffectEditOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ConsumableController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Consumable::class);

        $query = Consumable::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy', 'consumableType', 'resources']);

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

        if (request()->has('consumable_type_id') && request()->consumable_type_id !== '') {
            $query->where('consumable_type_id', request()->consumable_type_id);
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'level', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $consumables = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/consumable/Index', [
            'consumables' => ConsumableResource::collection($consumables),
            'filters' => request()->only(['search', 'level', 'consumable_type_id']),
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
    public function store(StoreConsumableRequest $request): RedirectResponse
    {
        $this->authorize('create', Consumable::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()?->id;
        $data['state'] = $data['state'] ?? Consumable::STATE_DRAFT;
        $data['read_level'] = array_key_exists('read_level', $data) && $data['read_level'] !== null
            ? (int) $data['read_level'] : User::ROLE_GUEST;
        $data['write_level'] = array_key_exists('write_level', $data) && $data['write_level'] !== null
            ? (int) $data['write_level'] : User::ROLE_GAME_MASTER;
        if ((int) $data['write_level'] < (int) $data['read_level']) {
            $data['write_level'] = (int) $data['read_level'];
        }
        $data['auto_update'] = array_key_exists('auto_update', $data) ? (bool) $data['auto_update'] : false;

        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = '';
        }

        $consumable = Consumable::create($data);

        return $this->redirectAfterEntityStore(
            $request,
            $consumable,
            'entities.consumables.edit',
            'entities.consumables.index',
            'Consommable créé avec succès.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Consumable $consumable)
    {
        $this->authorize('view', $consumable);

        $consumable->load([
            'consumableType',
            'createdBy',
            'resources',
            'effectUsages.effectDegree.effect',
            'objectEffects',
        ]);

        return Inertia::render('Pages/entity/consumable/Show', [
            'consumable' => new ConsumableResource($consumable),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consumable $consumable)
    {
        $this->authorize('update', $consumable);

        $consumable->load(['createdBy', 'consumableType', 'resources', 'effectUsages.effectDegree.effect', 'objectEffects']);

        $availableConsumableTypes = ConsumableType::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        $effectUsages = $consumable->effectUsages()->with(['effectDegree.effect'])->get()->sortBy(fn ($u) => $u->effectDegree?->required_creature_level ?? 0)->values()->map(fn ($u) => [
            'id' => $u->id,
            'effect_degree_id' => $u->effect_degree_id,
            'effect' => $u->effectDegree ? [
                'id' => $u->effectDegree->id,
                'name' => ($u->effectDegree->effect->name ?? $u->effectDegree->effect->slug ?? 'Effet #'.$u->effectDegree->effect_id).' · D'.$u->effectDegree->degree,
                'slug' => $u->effectDegree->slug,
                'degree' => $u->effectDegree->degree,
                'target_type' => $u->effectDegree->effect->target_type ?? Effect::TARGET_DIRECT,
                'area' => $u->effectDegree->area,
                'effect_definition_id' => $u->effectDegree->effect_id,
            ] : null,
            'required_creature_level' => $u->effectDegree?->required_creature_level,
        ])->values()->all();

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

        return Inertia::render('Pages/entity/consumable/Edit', array_merge([
            'consumable' => new ConsumableResource($consumable),
            'availableConsumableTypes' => $availableConsumableTypes,
            'effectUsages' => $effectUsages,
            'availableEffects' => $availableEffects,
            'effectEntityType' => 'consumable',
        ], ObjectEffectEditOptions::inertiaPropsFor($consumable)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsumableRequest $request, Consumable $consumable)
    {
        $this->authorize('update', $consumable);

        $consumable->update($request->validated());

        $consumable->load(['createdBy', 'consumableType']);

        return redirect()->route('entities.consumables.show', $consumable)
            ->with('success', 'Consommable mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Place le consommable en corbeille (soft delete).
     */
    public function delete(Request $request, Consumable $consumable, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($consumable, $actor);

        return redirect()->route('entities.consumables.index')
            ->with('success', 'Consommable placé en corbeille.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs consumables.
     *
     * @param  Consumable|null  $consumable  Le consumable unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Consumable $consumable = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $consumables = Consumable::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Consumable::class);

                $pdf = PdfService::generateForEntities($consumables, 'consumable');
                $filename = 'consumables-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $consumable) {
            abort(404);
        }

        $this->authorize('view', $consumable);

        $pdf = PdfService::generateForEntity($consumable, 'consumable');
        $filename = 'consumable-'.$consumable->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
