<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreClasseRequest;
use App\Http\Requests\Entity\UpdateClasseRequest;
use App\Models\Entity\Classe;
use App\Http\Resources\Entity\ClasseResource;
use App\Services\PdfService;
use Inertia\Inertia;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Classe::class);
        
        $query = Classe::with(['createdBy', 'npcs', 'spells']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specificity', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('life') && request()->life !== '') {
            $query->where('life', request()->life);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'life', 'life_dice', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $classes = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/classe/Index', [
            'classes' => ClasseResource::collection($classes),
            'filters' => request()->only(['search', 'life']),
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
    public function store(StoreClasseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Classe $classe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Classe $classe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClasseRequest $request, Classe $classe)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Classe $classe)
    {
        //
    }

    /**
     * Télécharge un PDF pour un ou plusieurs classes.
     * 
     * @param Classe|null $classe La classe unique (si une seule)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Classe $classe = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $classes = Classe::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Classe::class);
                
                $pdf = PdfService::generateForEntities($classes, 'classe');
                $filename = 'classes-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$classe) {
            abort(404);
        }
        
        $this->authorize('view', $classe);
        
        $pdf = PdfService::generateForEntity($classe, 'classe');
        $filename = 'classe-' . $classe->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
