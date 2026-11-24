<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreAptitudeRequest;
use App\Http\Requests\Entity\UpdateAptitudeRequest;
use App\Http\Resources\Entity\AptitudeResource;
use App\Models\Entity\Aptitude;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\EntityUpdateRedirect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class AptitudeController extends Controller
{
    use RedirectsAfterEntityCreate;

    public function index()
    {
        $this->authorize('viewAny', Aptitude::class);

        $query = Aptitude::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy']);

        if (request()->filled('search')) {
            $search = (string) request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('effect', 'like', "%{$search}%");
            });
        }

        if (request()->filled('state')) {
            $query->where('state', (string) request()->state);
        }

        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        if (in_array($sortColumn, ['id', 'name', 'state', 'reuse', 'duration', 'range', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $aptitudes = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/aptitude/Index', [
            'aptitudes' => AptitudeResource::collection($aptitudes),
            'filters' => request()->only(['search', 'state']),
        ]);
    }

    public function create()
    {
        //
    }

    public function store(StoreAptitudeRequest $request): RedirectResponse|Response|JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $aptitude = Aptitude::create($data);

        if ($request->header('X-Inertia')) {
            return $this->redirectAfterEntityStore(
                $request,
                $aptitude,
                'entities.aptitudes.edit',
                'entities.aptitudes.index',
                'Aptitude créée avec succès.',
            );
        }

        return response()->json($aptitude, 201);
    }

    public function show(Aptitude $aptitude)
    {
        $this->authorize('view', $aptitude);
        $aptitude->load(['createdBy', 'specializations']);

        return Inertia::render('Pages/entity/aptitude/Show', [
            'aptitude' => new AptitudeResource($aptitude),
        ]);
    }

    public function edit(Aptitude $aptitude)
    {
        $this->authorize('update', $aptitude);
        $aptitude->load(['createdBy', 'specializations']);

        return Inertia::render('Pages/entity/aptitude/Edit', [
            'aptitude' => new AptitudeResource($aptitude),
        ]);
    }

    public function update(UpdateAptitudeRequest $request, Aptitude $aptitude)
    {
        $data = $request->validated();
        unset($data['redirect_after_update']);
        $aptitude->update($data);

        if ($request->header('X-Inertia')) {
            return EntityUpdateRedirect::respond(
                $request,
                'Aptitude mise à jour avec succès.',
                fn () => back(),
                editRedirect: fn () => redirect()->route('entities.aptitudes.edit', $aptitude),
                indexRedirect: fn () => redirect()->route('entities.aptitudes.index'),
                showRedirect: fn () => redirect()->route('entities.aptitudes.show', $aptitude),
            );
        }

        return response()->json($aptitude);
    }

    public function delete(Request $request, Aptitude $aptitude, EntityDeletionService $deletionService): JsonResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);
        $deletionService->softDelete($aptitude, $actor);

        return response()->json(['message' => 'Entité placée en corbeille.']);
    }

    public function downloadPdf(?Aptitude $aptitude = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Aptitude::class);
                $aptitudes = Aptitude::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();
                $pdf = PdfService::generateForEntities($aptitudes, 'aptitude');

                return $pdf->download('aptitudes-'.now()->format('Y-m-d-His').'.pdf');
            }
        }

        if (! $aptitude) {
            abort(404);
        }

        $this->authorize('view', $aptitude);
        $pdf = PdfService::generateForEntity($aptitude, 'aptitude');

        return $pdf->download('aptitude-'.$aptitude->id.'-'.now()->format('Y-m-d-His').'.pdf');
    }
}
