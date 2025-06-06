<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StorePanoplyRequest;
use App\Http\Requests\Entity\UpdatePanoplyRequest;
use App\Models\Entity\Panoply;

class PanoplyController extends Controller
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
    public function store(StorePanoplyRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Panoply $panoply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Panoply $panoply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePanoplyRequest $request, Panoply $panoply)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Panoply $panoply)
    {
        //
    }
}
