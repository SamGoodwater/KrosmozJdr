<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreAttributeRequest;
use App\Http\Requests\Entity\UpdateAttributeRequest;
use App\Models\Entity\Attribute;
use App\Http\Resources\Entity\AttributeResource;
use App\Services\PdfService;
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
        if (request()->has('state') && request()->state !== '') {
            $query->where('state', (string) request()->state);
        }
        if (request()->has('read_level') && request()->read_level !== '') {
            $query->where('read_level', (int) request()->read_level);
        }
        if (request()->has('write_level') && request()->write_level !== '') {
            $query->where('write_level', (int) request()->write_level);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'state', 'read_level', 'write_level', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $attributes = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/attribute/Index', [
            'attributes' => AttributeResource::collection($attributes),
            'filters' => request()->only(['search', 'state', 'read_level', 'write_level']),
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

    /**
     * Télécharge un PDF pour un ou plusieurs attributes.
     * 
     * @param Attribute|null $attribute L'attribute unique (si un seul)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Attribute $attribute = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $attributes = Attribute::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Attribute::class);
                
                $pdf = PdfService::generateForEntities($attributes, 'attribute');
                $filename = 'attributes-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$attribute) {
            abort(404);
        }
        
        $this->authorize('view', $attribute);
        
        $pdf = PdfService::generateForEntity($attribute, 'attribute');
        $filename = 'attribute-' . $attribute->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
