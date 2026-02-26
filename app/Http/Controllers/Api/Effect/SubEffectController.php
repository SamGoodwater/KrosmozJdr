<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreSubEffectRequest;
use App\Http\Requests\Effect\UpdateSubEffectRequest;
use App\Http\Resources\Effect\SubEffectResource;
use App\Models\SubEffect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubEffectController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $list = SubEffect::orderBy('type_slug')->orderBy('slug')->get();
        return SubEffectResource::collection($list);
    }

    public function store(StoreSubEffectRequest $request): JsonResponse
    {
        $sub = SubEffect::create($request->validated());
        return (new SubEffectResource($sub))->response()->setStatusCode(201);
    }

    public function show(SubEffect $subEffect): SubEffectResource
    {
        return new SubEffectResource($subEffect);
    }

    public function update(UpdateSubEffectRequest $request, SubEffect $subEffect): SubEffectResource
    {
        $subEffect->update($request->validated());
        return new SubEffectResource($subEffect->fresh());
    }

    public function destroy(SubEffect $subEffect): JsonResponse
    {
        $subEffect->delete();
        return response()->json(null, 204);
    }
}
