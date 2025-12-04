<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCapabilityRequest;
use App\Http\Requests\Entity\UpdateCapabilityRequest;
use App\Models\Entity\Capability;
use App\Http\Resources\Entity\CapabilityResource;
use App\Services\PdfService;
use Inertia\Inertia;

class CapabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Capability::class);
        
        $query = Capability::with(['createdBy', 'specializations', 'creatures']);
        
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
        
        $capabilities = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/capability/Index', [
            'capabilities' => CapabilityResource::collection($capabilities),
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
    public function store(StoreCapabilityRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Capability $capability)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Capability $capability)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCapabilityRequest $request, Capability $capability)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Capability $capability)
    {
        //
    }

    /**
     * Télécharge un PDF pour un ou plusieurs capabilities.
     * 
     * @param Capability|null $capability La capability unique (si une seule)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Capability $capability = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $capabilities = Capability::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Capability::class);
                
                $pdf = PdfService::generateForEntities($capabilities, 'capability');
                $filename = 'capabilities-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$capability) {
            abort(404);
        }
        
        $this->authorize('view', $capability);
        
        $pdf = PdfService::generateForEntity($capability, 'capability');
        $filename = 'capability-' . $capability->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
