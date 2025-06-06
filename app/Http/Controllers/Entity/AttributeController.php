<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreAttributeRequest;
use App\Http\Requests\Entity\UpdateAttributeRequest;
use App\Models\Entity\Attribute;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Attribute::class);
        $attributes = Attribute::query()->latest()->paginate(20);
        return response()->json($attributes);
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
    public function store(StoreAttributeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $attribute = Attribute::create($data);
        return response()->json($attribute, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        $this->authorize('view', $attribute);
        return response()->json($attribute);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $data = $request->validated();
        $attribute->update($data);
        return response()->json($attribute);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        $this->authorize('delete', $attribute);
        $attribute->delete();
        return response()->json(['message' => 'Deleted'], 204);
    }
}
