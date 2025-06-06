<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCreatureRequest;
use App\Http\Requests\Entity\UpdateCreatureRequest;
use App\Models\Entity\Creature;

class CreatureController extends Controller
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
    public function store(StoreCreatureRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Creature $creature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Creature $creature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreatureRequest $request, Creature $creature)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Creature $creature)
    {
        //
    }
}
