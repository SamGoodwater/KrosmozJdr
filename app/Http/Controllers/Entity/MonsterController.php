<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreMonsterRequest;
use App\Http\Requests\Entity\UpdateMonsterRequest;
use App\Models\Entity\Monster;
use App\Http\Resources\Entity\MonsterResource;
use Inertia\Inertia;

class MonsterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeForUser(auth()->user(), 'viewAny', Monster::class);
        
        $query = Monster::with(['creature', 'monsterRace']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->whereHas('creature', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('size') && request()->size !== '') {
            $query->where('size', request()->size);
        }
        
        if (request()->has('is_boss') && request()->is_boss !== '') {
            $query->where('is_boss', request()->is_boss);
        }
        
        if (request()->has('monster_race_id') && request()->monster_race_id !== '') {
            $query->where('monster_race_id', request()->monster_race_id);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'size', 'is_boss', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $monsters = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/monster/Index', [
            'monsters' => MonsterResource::collection($monsters),
            'filters' => request()->only(['search', 'size', 'is_boss', 'monster_race_id']),
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
    public function store(StoreMonsterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Monster $monster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Monster $monster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMonsterRequest $request, Monster $monster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Monster $monster)
    {
        //
    }
}
