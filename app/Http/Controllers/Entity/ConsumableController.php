<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreConsumableRequest;
use App\Http\Requests\Entity\UpdateConsumableRequest;
use App\Models\Effect;
use App\Models\Entity\Consumable;
use App\Http\Resources\Entity\ConsumableResource;
use App\Services\PdfService;
use Inertia\Inertia;

class ConsumableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Consumable::class);
        
        $query = Consumable::with(['createdBy', 'consumableType', 'resources']);
        
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
    public function store(StoreConsumableRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Consumable $consumable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Consumable $consumable)
    {
        $this->authorize('update', $consumable);

        $consumable->load(['createdBy', 'consumableType', 'resources', 'effectUsages.effect.subEffects']);

        $availableConsumableTypes = \App\Models\Type\ConsumableType::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();

        $effectUsages = $consumable->effectUsages()->with('effect.subEffects')->orderBy('level_min')->get()->map(fn ($u) => [
            'id' => $u->id,
            'effect_id' => $u->effect_id,
            'effect' => $u->effect ? [
                'id' => $u->effect->id,
                'name' => $u->effect->name,
                'slug' => $u->effect->slug,
                'degree' => $u->effect->degree,
                'target_type' => $u->effect->target_type ?? \App\Models\Effect::TARGET_DIRECT,
                'area' => $u->effect->area,
            ] : null,
            'level_min' => $u->level_min,
            'level_max' => $u->level_max,
        ])->values()->all();

        $availableEffects = Effect::orderBy('name')->get(['id', 'name', 'slug', 'degree', 'target_type', 'area'])->map(fn ($e) => [
            'id' => $e->id,
            'name' => $e->name ?? $e->slug ?? 'Effet #' . $e->id,
            'slug' => $e->slug,
            'degree' => $e->degree,
            'target_type' => $e->target_type ?? \App\Models\Effect::TARGET_DIRECT,
            'area' => $e->area,
        ])->values()->all();

        return Inertia::render('Pages/entity/consumable/Edit', [
            'consumable' => new ConsumableResource($consumable),
            'availableConsumableTypes' => $availableConsumableTypes,
            'effectUsages' => $effectUsages,
            'availableEffects' => $availableEffects,
            'effectEntityType' => 'consumable',
        ]);
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
    public function delete(Consumable $consumable)
    {
        //
    }

    /**
     * Télécharge un PDF pour un ou plusieurs consumables.
     * 
     * @param Consumable|null $consumable Le consumable unique (si un seul)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Consumable $consumable = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $consumables = Consumable::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Consumable::class);
                
                $pdf = PdfService::generateForEntities($consumables, 'consumable');
                $filename = 'consumables-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$consumable) {
            abort(404);
        }
        
        $this->authorize('view', $consumable);
        
        $pdf = PdfService::generateForEntity($consumable, 'consumable');
        $filename = 'consumable-' . $consumable->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
