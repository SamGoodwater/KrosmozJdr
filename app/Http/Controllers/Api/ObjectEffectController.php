<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreObjectEffectRequest;
use App\Http\Requests\Api\UpdateObjectEffectRequest;
use App\Http\Resources\ObjectEffectResource;
use App\Models\ObjectEffect;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ObjectEffectController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'entity_type' => 'required|string|in:item,consumable,resource',
            'entity_id' => 'required|integer|min:1',
        ]);
        $class = ObjectEffect::entityTypeToClass($validated['entity_type']);
        if ($class === null) {
            abort(422, 'Invalid entity_type');
        }
        if (! $class::query()->whereKey($validated['entity_id'])->exists()) {
            abort(404);
        }

        $list = ObjectEffect::query()
            ->where('object_effectable_type', $class)
            ->where('object_effectable_id', $validated['entity_id'])
            ->with(['characteristic', 'monster.creature'])
            ->orderBy('id')
            ->get();

        return ObjectEffectResource::collection($list);
    }

    public function store(StoreObjectEffectRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $class = ObjectEffect::entityTypeToClass($validated['entity_type']);
        if ($class === null) {
            return response()->json(['message' => 'Invalid entity_type'], 422);
        }
        $parent = $class::query()->findOrFail((int) $validated['entity_id']);
        $this->authorize('update', $parent);

        unset($validated['entity_type'], $validated['entity_id']);

        $effect = $parent->objectEffects()->create($validated);
        $effect->load(['characteristic', 'monster.creature']);

        return (new ObjectEffectResource($effect))->response()->setStatusCode(201);
    }

    public function update(UpdateObjectEffectRequest $request, ObjectEffect $objectEffect): JsonResponse
    {
        $parent = $objectEffect->objectEffectable;
        if ($parent === null) {
            abort(404);
        }
        $this->authorize('update', $parent);

        $objectEffect->update($request->validated());
        $objectEffect->load(['characteristic', 'monster.creature']);

        return (new ObjectEffectResource($objectEffect->fresh()))->response();
    }

    public function destroy(ObjectEffect $objectEffect): JsonResponse
    {
        $parent = $objectEffect->objectEffectable;
        if ($parent === null) {
            abort(404);
        }
        $this->authorize('update', $parent);
        $objectEffect->delete();

        return response()->json(null, 204);
    }
}
