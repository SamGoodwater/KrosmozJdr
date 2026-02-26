<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectRequest;
use App\Http\Resources\Effect\EffectResource;
use App\Models\Effect;
use App\Models\EffectUsage;
use App\Services\Effect\EffectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EffectController extends Controller
{
    public function __construct(
        private readonly EffectService $effectService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $list = Effect::with('subEffects')->orderBy('name')->get();
        return EffectResource::collection($list);
    }

    public function store(StoreEffectRequest $request): JsonResponse
    {
        $effect = Effect::create($request->validated());
        $effect->load('subEffects');
        return (new EffectResource($effect))->response()->setStatusCode(201);
    }

    public function show(Effect $effect): EffectResource
    {
        $effect->load('subEffects');
        return new EffectResource($effect);
    }

    public function update(UpdateEffectRequest $request, Effect $effect): EffectResource
    {
        $effect->update($request->validated());
        $effect->load('subEffects');
        return new EffectResource($effect->fresh());
    }

    public function destroy(Effect $effect): JsonResponse
    {
        $effect->delete();
        return response()->json(null, 204);
    }

    public function forEntity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'entity_type' => 'required|string|in:spell,item,consumable,resource',
            'entity_id' => 'required|integer|min:1',
            'level' => 'required|integer|min:0',
            'context' => 'nullable|string|in:combat,out_of_combat',
            'format_dice_human' => 'boolean',
        ]);
        $entityType = $validated['entity_type'];
        $entityId = (int) $validated['entity_id'];
        $level = (int) $validated['level'];
        $context = $validated['context'] ?? null;
        $formatDiceHuman = (bool) ($validated['format_dice_human'] ?? false);

        $class = EffectUsage::entityTypeToClass($entityType);
        if ($class === null) {
            return response()->json(['message' => 'Invalid entity_type'], 422);
        }

        $effects = $this->effectService->getEffectsForEntity($class, $entityId, $level, $context);
        $baseContext = ['level' => $level];

        $payload = $effects->map(fn (Effect $e) => [
            'effect' => (new EffectResource($e))->toArray($request),
            'resolved_text' => $this->effectService->renderEffectText($e, $baseContext, $context, $formatDiceHuman),
            'description' => $e->description,
        ])->values()->all();

        return response()->json(['data' => $payload]);
    }
}
