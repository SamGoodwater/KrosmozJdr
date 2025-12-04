<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreConsumableRequest;
use App\Http\Requests\Entity\UpdateConsumableRequest;
use App\Models\Entity\Consumable;
use App\Http\Resources\Entity\ConsumableResource;
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
}
