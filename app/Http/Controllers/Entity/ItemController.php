<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreItemRequest;
use App\Http\Requests\Entity\UpdateItemRequest;
use App\Http\Requests\Entity\UpdateItemResourcesRequest;
use App\Http\Resources\Entity\ItemResource;
use App\Models\Effect;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\User;
use App\Services\PdfService;
use App\Support\Entity\ObjectEffectEditOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ItemController extends Controller
{
    use RedirectsAfterEntityCreate;

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
            $query->where(function ($q) use ($search) {
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
    public function store(StoreItemRequest $request): RedirectResponse
    {
        $this->authorize('create', Item::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()?->id;
        $data['state'] = $data['state'] ?? Item::STATE_DRAFT;
        $data['rarity'] = array_key_exists('rarity', $data) && $data['rarity'] !== null ? (int) $data['rarity'] : 0;
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

        $item = Item::create($data);

        return $this->redirectAfterEntityStore(
            $request,
            $item,
            'entities.items.edit',
            'entities.items.index',
            'Objet créé avec succès.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $this->authorize('view', $item);

        $item->load([
            'itemType',
            'createdBy',
            'resources',
            'effectUsages.effectDegree.effect',
            'objectEffects',
        ]);

        return Inertia::render('Pages/entity/item/Show', [
            'item' => new ItemResource($item),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        $item->load(['itemType', 'createdBy', 'resources', 'effectUsages.effectDegree.effect', 'objectEffects']);

        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $effectUsages = $item->effectUsages()->with(['effectDegree.effect'])->get()->sortBy(fn ($u) => $u->effectDegree?->required_creature_level ?? 0)->values()->map(fn ($u) => [
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

        return Inertia::render('Pages/entity/item/Edit', array_merge([
            'item' => new ItemResource($item),
            'availableResources' => $availableResources,
            'effectUsages' => $effectUsages,
            'availableEffects' => $availableEffects,
            'effectEntityType' => 'item',
        ], ObjectEffectEditOptions::inertiaPropsFor($item)));
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

    /**
     * Télécharge un PDF pour un ou plusieurs items.
     *
     * @param  Item|null  $item  L'item unique (si un seul)
     * @return Response
     */
    public function downloadPdf(?Item $item = null)
    {
        // Si des IDs sont fournis dans la query string, on peut télécharger plusieurs items
        $ids = request()->get('ids');

        if (! empty($ids)) {
            // Convertir en tableau si c'est une chaîne
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                // Génération pour plusieurs items
                $items = Item::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Item::class);

                $pdf = PdfService::generateForEntities($items, 'item');
                $filename = 'items-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        // Génération pour un seul item
        if (! $item) {
            abort(404);
        }

        $this->authorize('view', $item);

        $pdf = PdfService::generateForEntity($item, 'item');
        $filename = 'item-'.$item->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
