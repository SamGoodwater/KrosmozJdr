<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCreatureRequest;
use App\Http\Requests\Entity\UpdateCreatureRequest;
use App\Models\Entity\Creature;
use App\Http\Resources\Entity\CreatureResource;
use Inertia\Inertia;

class CreatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeForUser(auth()->user(), 'viewAny', Creature::class);
        
        $query = Creature::with(['createdBy', 'npc', 'monster']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $creatures = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/creature/Index', [
            'creatures' => CreatureResource::collection($creatures),
            'filters' => request()->only(['search']),
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
    public function store(StoreCreatureRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Creature $creature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Creature $creature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreatureRequest $request, Creature $creature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Creature $creature)
    {
        //
    }
}
