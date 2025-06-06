<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCampaignRequest;
use App\Http\Requests\Entity\UpdateCampaignRequest;
use App\Models\Entity\Campaign;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Campaign::class);
        $campaigns = Campaign::query()->latest()->paginate(20);
        return response()->json($campaigns);
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
        //
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
    public function destroy(Campaign $campaign)
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
}
