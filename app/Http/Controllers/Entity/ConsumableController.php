<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreConsumableRequest;
use App\Http\Requests\Entity\UpdateConsumableRequest;
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsumableRequest $request, Consumable $consumable)
    {
        //
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
