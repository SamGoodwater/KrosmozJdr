<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectRequest;
use App\Http\Resources\Effect\EffectResource;
use App\Models\Effect;
use App\Models\EffectUsage;
use App\Models\User;
use App\Services\Effect\EffectResolutionService;
use App\Services\Effect\EffectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EffectController extends Controller
{
    public function __construct(
        private readonly EffectService $effectService,
        private readonly EffectResolutionService $effectResolutionService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        // Effets réservés aux utilisateurs ayant au moins le rôle "player"
        if (! $user || ! $user->verifyRole(User::ROLE_PLAYER)) {
            abort(403);
        }

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
        $entityType = $validated['entity_type']; // short type: spell, item, consumable, resource
        $entityId = (int) $validated['entity_id'];
        $level = (int) $validated['level'];
        $context = $validated['context'] ?? null;
        $formatDiceHuman = (bool) ($validated['format_dice_human'] ?? false);

        $class = EffectUsage::entityTypeToClass($entityType);
        if ($class === null) {
            return response()->json(['message' => 'Invalid entity_type'], 422);
        }

        // On utilise le short type pour la requête (convention côté EffectUsage)
        $effects = $this->effectService->getEffectsForEntity($entityType, $entityId, $level, $context);
        $baseContext = ['level' => $level];

        $payload = $effects->map(function (Effect $e) use ($request, $baseContext, $context, $formatDiceHuman) {
            $resolved = $this->effectResolutionService->resolveEffect($e, $baseContext, $context, $formatDiceHuman, false);
            $resolvedCrit = $this->effectResolutionService->resolveEffect($e, $baseContext, $context, $formatDiceHuman, true);

            return [
                'effect' => (new EffectResource($e))->toArray($request),
                // Texte global (compat) basé sur l’ancien rendu agrégé
                'resolved_text' => $this->effectService->renderEffectText($e, $baseContext, $context, $formatDiceHuman),
                // Résolution détaillée par sous-effet (normal)
                'resolved' => $resolved,
                // Résolution en cas de critique (sous-effets crit_only + value_formula_crit)
                'resolved_crit' => $resolvedCrit,
                'description' => $e->description,
            ];
        })->values()->all();

        return response()->json(['data' => $payload]);
    }
}
