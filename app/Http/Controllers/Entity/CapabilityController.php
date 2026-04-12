<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCapabilityRequest;
use App\Http\Requests\Entity\UpdateCapabilityRequest;
use App\Http\Resources\Entity\CapabilityResource;
use App\Models\Entity\Capability;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

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
    public function store(StoreCapabilityRequest $request): RedirectResponse
    {
        $this->authorize('create', Capability::class);

        $data = $request->validated();

        if (! array_key_exists('description', $data) || $data['description'] === null) {
            $data['description'] = '';
        }

        foreach (['element', 'powerful'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === null) {
                unset($data[$key]);
            }
        }

        $data['created_by'] = $request->user()?->id;

        $capability = Capability::create($data);

        return redirect()->route('entities.capabilities.index')
            ->with('success', 'Capacité créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Capability $capability)
    {
        $this->authorize('view', $capability);

        $capability->load(['createdBy', 'specializations', 'creatures']);

        return Inertia::render('Pages/entity/capability/Show', [
            'capability' => new CapabilityResource($capability),
        ]);
    }

    /**
     * Données partagées page / modal d’édition.
     *
     * @return array{capability: Capability}
     */
    protected function buildCapabilityEditPayload(Capability $capability): array
    {
        $capability->load(['createdBy', 'specializations', 'creatures']);

        return [
            'capability' => $capability,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Capability $capability)
    {
        $this->authorize('update', $capability);

        $payload = $this->buildCapabilityEditPayload($capability);

        return Inertia::render('Pages/entity/capability/Edit', [
            'capability' => new CapabilityResource($payload['capability']),
        ]);
    }

    /**
     * Charge utile JSON pour l’éditeur en modal (liste des capacités).
     */
    public function editPayload(Capability $capability): JsonResponse
    {
        $this->authorize('update', $capability);

        $payload = $this->buildCapabilityEditPayload($capability);

        return response()->json([
            'capability' => (new CapabilityResource($payload['capability']))->toArray(request()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCapabilityRequest $request, Capability $capability): RedirectResponse
    {
        $this->authorize('update', $capability);

        $data = $request->validated();
        $redirectAfter = $data['redirect_after_update'] ?? null;
        unset($data['redirect_after_update']);

        $capability->update($data);

        $capability->load(['createdBy', 'specializations', 'creatures']);

        $successMessage = 'Capacité mise à jour avec succès.';

        if ($redirectAfter === 'stay') {
            return back()->with('success', $successMessage);
        }

        if ($redirectAfter === 'index') {
            return redirect()->route('entities.capabilities.index')
                ->with('success', $successMessage);
        }

        if ($redirectAfter === 'edit') {
            return redirect()->route('entities.capabilities.edit', $capability)
                ->with('success', $successMessage);
        }

        return redirect()->route('entities.capabilities.show', $capability)
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Capability $capability): RedirectResponse
    {
        $this->authorize('delete', $capability);
        $capability->delete();

        return redirect()->route('entities.capabilities.index')
            ->with('success', 'Capacité supprimée (corbeille).');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs capabilities.
     *
     * @param  Capability|null  $capability  La capability unique (si une seule)
     */
    public function downloadPdf(?Capability $capability = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $capabilities = Capability::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Capability::class);

                $pdf = PdfService::generateForEntities($capabilities, 'capability');
                $filename = 'capabilities-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $capability) {
            abort(404);
        }

        $this->authorize('view', $capability);

        $pdf = PdfService::generateForEntity($capability, 'capability');
        $filename = 'capability-'.$capability->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
