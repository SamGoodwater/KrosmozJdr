<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreScenarioRequest;
use App\Http\Requests\Entity\UpdateScenarioRequest;
use App\Models\Entity\Scenario;

class ScenarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreScenarioRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Scenario $scenario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scenario $scenario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScenarioRequest $request, Scenario $scenario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scenario $scenario)
    {
        //
    }

    /**
     * Associe un utilisateur au scénario.
     */
    public function attachUser(\Illuminate\Http\Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $scenario->users()->attach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Dissocie un utilisateur du scénario.
     */
    public function detachUser(\Illuminate\Http\Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);
        $request->validate(['user_id' => 'required|exists:users,id']);
        $scenario->users()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    /**
     * Synchronise la liste des utilisateurs associés au scénario.
     */
    public function syncUsers(\Illuminate\Http\Request $request, Scenario $scenario)
    {
        $this->authorize('update', $scenario);
        $request->validate(['user_ids' => 'array', 'user_ids.*' => 'exists:users,id']);
        $scenario->users()->sync($request->user_ids);
        return response()->json(['success' => true]);
    }

    /**
     * Liste les utilisateurs associés au scénario.
     */
    public function users(Scenario $scenario)
    {
        $this->authorize('view', $scenario);
        return response()->json($scenario->users);
    }
}
