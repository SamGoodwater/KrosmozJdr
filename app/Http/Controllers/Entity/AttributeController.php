<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreAttributeRequest;
use App\Http\Requests\Entity\UpdateAttributeRequest;
use App\Models\Entity\Attribute;
use App\Http\Resources\Entity\AttributeResource;
use Inertia\Inertia;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Attribute::class);
        
        $query = Attribute::with(['createdBy', 'creatures']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('usable') && request()->usable !== '') {
            $query->where('usable', request()->usable);
        }
        
        if (request()->has('is_visible') && request()->is_visible !== '') {
            $query->where('is_visible', request()->is_visible);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'usable', 'is_visible', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $attributes = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/attribute/Index', [
            'attributes' => AttributeResource::collection($attributes),
            'filters' => request()->only(['search', 'usable', 'is_visible']),
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
    public function store(StoreAttributeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $attribute = Attribute::create($data);
        return response()->json($attribute, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        $this->authorize('view', $attribute);
        return response()->json($attribute);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $data = $request->validated();
        $attribute->update($data);
        return response()->json($attribute);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Attribute $attribute)
    {
        $this->authorize('delete', $attribute);
        $attribute->delete();
        return response()->json(['message' => 'Deleted'], 204);
    }
}
