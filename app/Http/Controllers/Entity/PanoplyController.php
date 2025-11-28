<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StorePanoplyRequest;
use App\Http\Requests\Entity\UpdatePanoplyRequest;
use App\Models\Entity\Panoply;
use App\Http\Resources\Entity\PanoplyResource;
use Inertia\Inertia;

class PanoplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeForUser(auth()->user(), 'viewAny', Panoply::class);
        
        $query = Panoply::with(['createdBy', 'items']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('bonus', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('usable') && request()->usable !== '') {
            $query->where('usable', request()->usable);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'dofusdb_id', 'usable', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $panoplies = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/panoply/Index', [
            'panoplies' => PanoplyResource::collection($panoplies),
            'filters' => request()->only(['search', 'usable']),
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
    public function store(StorePanoplyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Panoply $panoply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Panoply $panoply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePanoplyRequest $request, Panoply $panoply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Panoply $panoply)
    {
        //
    }
}
