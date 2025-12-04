<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreSpecializationRequest;
use App\Http\Requests\Entity\UpdateSpecializationRequest;
use App\Models\Entity\Specialization;
use App\Http\Resources\Entity\SpecializationResource;
use App\Services\PdfService;
use Inertia\Inertia;

class SpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Specialization::class);
        
        $query = Specialization::with(['createdBy', 'capabilities', 'npcs']);
        
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
        
        $specializations = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/specialization/Index', [
            'specializations' => SpecializationResource::collection($specializations),
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
    public function store(StoreSpecializationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Specialization $specialization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Specialization $specialization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecializationRequest $request, Specialization $specialization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Specialization $specialization)
    {
        //
    }

    /**
     * Télécharge un PDF pour un ou plusieurs specializations.
     * 
     * @param Specialization|null $specialization La specialization unique (si une seule)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Specialization $specialization = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $specializations = Specialization::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Specialization::class);
                
                $pdf = PdfService::generateForEntities($specializations, 'specialization');
                $filename = 'specializations-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$specialization) {
            abort(404);
        }
        
        $this->authorize('view', $specialization);
        
        $pdf = PdfService::generateForEntity($specialization, 'specialization');
        $filename = 'specialization-' . $specialization->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
