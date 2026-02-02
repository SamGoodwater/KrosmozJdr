<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreBreedRequest;
use App\Http\Requests\Entity\UpdateBreedRequest;
use App\Models\Entity\Breed;
use App\Http\Resources\Entity\BreedResource;
use App\Services\PdfService;
use Inertia\Inertia;

class BreedController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Breed::class);

        $query = Breed::with(['createdBy', 'npcs', 'spells']);

        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specificity', 'like', "%{$search}%");
            });
        }

        if (request()->has('life') && request()->life !== '') {
            $query->where('life', request()->life);
        }

        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'life', 'life_dice', 'dofusdb_id', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $breeds = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/breed/Index', [
            'breeds' => BreedResource::collection($breeds),
            'filters' => request()->only(['search', 'life']),
        ]);
    }

    public function create()
    {
        //
    }

    public function store(StoreBreedRequest $request)
    {
        //
    }

    public function show(Breed $breed)
    {
        //
    }

    public function edit(Breed $breed)
    {
        //
    }

    public function update(UpdateBreedRequest $request, Breed $breed)
    {
        //
    }

    public function delete(Breed $breed)
    {
        //
    }

    /**
     * Télécharge un PDF pour une ou plusieurs breeds (affichées « Classes »).
     */
    public function downloadPdf(?Breed $breed = null)
    {
        $ids = request()->get('ids');

        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $breeds = Breed::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Breed::class);

                $pdf = PdfService::generateForEntities($breeds, 'breed');
                $filename = 'breeds-' . now()->format('Y-m-d-His') . '.pdf';

                return $pdf->download($filename);
            }
        }

        if (!$breed) {
            abort(404);
        }

        $this->authorize('view', $breed);

        $pdf = PdfService::generateForEntity($breed, 'breed');
        $filename = 'breed-' . $breed->id . '-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }
}
