<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectUsageRequest;
use App\Http\Requests\Effect\UpdateEffectUsageRequest;
use App\Http\Resources\Effect\EffectUsageResource;
use App\Models\EffectUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EffectUsageController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'entity_type' => 'required|string|in:item,consumable,resource',
            'entity_id' => 'required|integer|min:1',
        ]);
        $class = EffectUsage::entityTypeToClass($validated['entity_type']);
        if ($class === null) {
            abort(422, 'Invalid entity_type');
        }

        $list = EffectUsage::query()
            ->where('entity_type', $class)
            ->where('entity_id', $validated['entity_id'])
            ->with(['effectDegree.effect'])
            ->get()
            ->sortBy(fn (EffectUsage $u) => $u->effectDegree?->required_creature_level ?? 0)
            ->values();

        return EffectUsageResource::collection($list);
    }

    public function store(StoreEffectUsageRequest $request): JsonResponse
    {
        $class = EffectUsage::entityTypeToClass($request->input('entity_type'));
        if ($class === null) {
            return response()->json(['message' => 'Invalid entity_type'], 422);
        }
        $usage = EffectUsage::create([
            'entity_type' => $class,
            'entity_id' => (int) $request->input('entity_id'),
            'effect_degree_id' => (int) $request->input('effect_degree_id'),
        ]);
        $usage->load(['effectDegree.effect']);

        return (new EffectUsageResource($usage))->response()->setStatusCode(201);
    }

    public function show(EffectUsage $effectUsage): EffectUsageResource
    {
        $effectUsage->load(['effectDegree.effect']);

        return new EffectUsageResource($effectUsage);
    }

    public function update(UpdateEffectUsageRequest $request, EffectUsage $effectUsage): EffectUsageResource
    {
        $effectUsage->update($request->validated());
        $effectUsage->load(['effectDegree.effect']);

        return new EffectUsageResource($effectUsage->fresh());
    }

    public function destroy(EffectUsage $effectUsage): JsonResponse
    {
        $effectUsage->delete();

        return response()->json(null, 204);
    }
}
