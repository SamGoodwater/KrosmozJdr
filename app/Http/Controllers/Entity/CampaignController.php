<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCampaignRequest;
use App\Http\Requests\Entity\UpdateCampaignRequest;
use App\Models\Entity\Campaign;
use App\Http\Resources\Entity\CampaignResource;
use App\Services\PdfService;
use Inertia\Inertia;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Campaign::class);
        
        $query = Campaign::with(['createdBy', 'users', 'scenarios']);
        
        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtres
        if (request()->has('state') && request()->state !== '') {
            $query->where('state', request()->state);
        }
        
        if (request()->has('is_public') && request()->is_public !== '') {
            $query->where('is_public', request()->is_public);
        }
        
        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');
        
        if (in_array($sortColumn, ['id', 'name', 'slug', 'state', 'is_public', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }
        
        $campaigns = $query->paginate(20)->withQueryString();
        
        return Inertia::render('Pages/entity/campaign/Index', [
            'campaigns' => CampaignResource::collection($campaigns),
            'filters' => request()->only(['search', 'state', 'is_public']),
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
    public function store(StoreCampaignRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $campaign = Campaign::create($data);
        return response()->json($campaign, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        return response()->json($campaign);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $campaign->load([
            'createdBy', 
            'users',
            'scenarios',
            'items', 
            'consumables', 
            'resources', 
            'spells', 
            'panoplies'
        ]);
        
        // Charger toutes les entités disponibles pour la recherche
        $availableUsers = \App\Models\User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
        
        $availableScenarios = \App\Models\Entity\Scenario::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        $availableItems = \App\Models\Entity\Item::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availableConsumables = \App\Models\Entity\Consumable::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availableSpells = \App\Models\Entity\Spell::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();
        
        $availablePanoplies = \App\Models\Entity\Panoply::select('id', 'name', 'description')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('Pages/entity/campaign/Edit', [
            'campaign' => new CampaignResource($campaign),
            'availableUsers' => $availableUsers,
            'availableScenarios' => $availableScenarios,
            'availableItems' => $availableItems,
            'availableConsumables' => $availableConsumables,
            'availableResources' => $availableResources,
            'availableSpells' => $availableSpells,
            'availablePanoplies' => $availablePanoplies,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $data = $request->validated();
        $campaign->update($data);
        return response()->json($campaign);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);
        $campaign->delete();
        return response()->json(['message' => 'Deleted'], 204);
    }

    /**
     * Associe un utilisateur à la campagne.
     */
    public function attachUser(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $campaign->users()->attach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur de la campagne.
     */
    public function detachUser(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $campaign->users()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés à la campagne.
     */
    public function syncUsers(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $campaign->users()->sync($request->user_ids);
        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés à la campagne.
     */
    public function users(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        return response()->json($campaign->users);
    }

    /**
     * Update the users of a campaign.
     */
    public function updateUsers(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);
        
        $campaign->users()->sync($request->users);
        
        return redirect()->back()
            ->with('success', 'Utilisateurs de la campagne mis à jour avec succès.');
    }

    /**
     * Update the scenarios of a campaign.
     */
    public function updateScenarios(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'scenarios' => 'required|array',
            'scenarios.*' => 'exists:scenarios,id',
        ]);
        
        $campaign->scenarios()->sync($request->scenarios);
        
        return redirect()->back()
            ->with('success', 'Scénarios de la campagne mis à jour avec succès.');
    }

    /**
     * Update the items of a campaign.
     */
    public function updateItems(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:items,id',
        ]);
        
        $campaign->items()->sync($request->items);
        
        return redirect()->back()
            ->with('success', 'Objets de la campagne mis à jour avec succès.');
    }

    /**
     * Update the consumables of a campaign.
     */
    public function updateConsumables(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'consumables' => 'required|array',
            'consumables.*' => 'exists:consumables,id',
        ]);
        
        $campaign->consumables()->sync($request->consumables);
        
        return redirect()->back()
            ->with('success', 'Consommables de la campagne mis à jour avec succès.');
    }

    /**
     * Update the resources of a campaign.
     */
    public function updateResources(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'resources' => 'required|array',
            'resources.*' => 'exists:resources,id',
        ]);
        
        $campaign->resources()->sync($request->resources);
        
        return redirect()->back()
            ->with('success', 'Ressources de la campagne mises à jour avec succès.');
    }

    /**
     * Update the spells of a campaign.
     */
    public function updateSpells(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'spells' => 'required|array',
            'spells.*' => 'exists:spells,id',
        ]);
        
        $campaign->spells()->sync($request->spells);
        
        return redirect()->back()
            ->with('success', 'Sorts de la campagne mis à jour avec succès.');
    }

    /**
     * Update the panoplies of a campaign.
     */
    public function updatePanoplies(\Illuminate\Http\Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);
        
        $request->validate([
            'panoplies' => 'required|array',
            'panoplies.*' => 'exists:panoplies,id',
        ]);
        
        $campaign->panoplies()->sync($request->panoplies);
        
        return redirect()->back()
            ->with('success', 'Panoplies de la campagne mises à jour avec succès.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs campaigns.
     * 
     * @param Campaign|null $campaign Le campaign unique (si un seul)
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(?Campaign $campaign = null)
    {
        $ids = request()->get('ids');
        
        if (!empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            
            if (is_array($ids) && count($ids) > 0) {
                $campaigns = Campaign::whereIn('id', $ids)->get();
                $this->authorize('viewAny', Campaign::class);
                
                $pdf = PdfService::generateForEntities($campaigns, 'campaign');
                $filename = 'campaigns-' . now()->format('Y-m-d-His') . '.pdf';
                
                return $pdf->download($filename);
            }
        }
        
        if (!$campaign) {
            abort(404);
        }
        
        $this->authorize('view', $campaign);
        
        $pdf = PdfService::generateForEntity($campaign, 'campaign');
        $filename = 'campaign-' . $campaign->id . '-' . now()->format('Y-m-d-His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
