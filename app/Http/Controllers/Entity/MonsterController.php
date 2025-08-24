<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreMonsterRequest;
use App\Http\Requests\Entity\UpdateMonsterRequest;
use App\Models\Entity\Monster;

class MonsterController extends Controller
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
    public function store(StoreMonsterRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Monster $monster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Monster $monster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMonsterRequest $request, Monster $monster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Monster $monster)
    {
        //
    }
}
