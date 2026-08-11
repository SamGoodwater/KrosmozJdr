<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Effect;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectRequest;
use App\Http\Resources\Effect\EffectResource;
use App\Http\Resources\Effect\ResolvedEffectDegreeResource;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectUsage;
use App\Models\Entity\Spell;
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
    ) {}

    /**
     * Liste paginée / filtrée des définitions d’effet (évite le dump massif).
     *
     * Query : `q` (nom/slug), `per_page` (1–100, défaut 30).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        if (! $user || ! $user->verifyRole(User::ROLE_PLAYER)) {
            abort(403);
        }

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        $perPage = (int) ($validated['per_page'] ?? 30);

        $query = Effect::query()->with('degrees')->orderBy('name');
        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        return EffectResource::collection($query->paginate($perPage));
    }

    /**
     * Recherche légère de définitions d’effet (liaison sort / pickers).
     * Évite d’embarquer ~10k+ effets dans le payload d’édition.
     */
    public function searchDefinitions(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            abort(401);
        }

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'exclude_spell_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $q = trim((string) ($validated['q'] ?? ''));
        $limit = (int) ($validated['limit'] ?? 30);

        $query = Effect::query()->orderBy('name');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        $excludeSpellId = isset($validated['exclude_spell_id']) ? (int) $validated['exclude_spell_id'] : 0;
        if ($excludeSpellId > 0) {
            $spell = Spell::query()->find($excludeSpellId);
            if ($spell === null || ! $user->can('update', $spell)) {
                abort(403);
            }
            $query->whereDoesntHave('spells', function ($builder) use ($excludeSpellId): void {
                $builder->where('spells.id', $excludeSpellId);
            });
        }

        $rows = $query
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'target_type']);

        return response()->json([
            'data' => $rows->map(static function (Effect $e): array {
                return [
                    'id' => $e->id,
                    'effect_definition_id' => $e->id,
                    'name' => $e->name ?? $e->slug ?? ('Effet #'.$e->id),
                    'slug' => $e->slug,
                    'target_type' => $e->target_type ?? Effect::TARGET_DIRECT,
                    'degree' => null,
                    'area' => null,
                ];
            })->values()->all(),
        ]);
    }

    public function store(StoreEffectRequest $request): JsonResponse
    {
        $effect = Effect::create($request->safe()->only(['name', 'slug', 'description', 'target_type'])->toArray());
        EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'area' => $request->input('initial_area'),
            'required_creature_level' => $request->input('initial_required_creature_level'),
            'slug' => $request->input('initial_degree_slug'),
        ]);
        $effect->load('degrees');

        return (new EffectResource($effect))->response()->setStatusCode(201);
    }

    public function show(Effect $effect): EffectResource
    {
        $effect->load('degrees');

        return new EffectResource($effect);
    }

    public function update(UpdateEffectRequest $request, Effect $effect): EffectResource
    {
        $effect->update($request->safe()->only(['name', 'slug', 'description', 'target_type'])->toArray());
        $effect->load('degrees');

        return new EffectResource($effect->fresh());
    }

    public function destroy(Effect $effect): JsonResponse
    {
        $effect->delete();

        return response()->json(null, 204);
    }

    /**
     * Prévisualisation : degrés d’effet applicables selon le niveau du porteur.
     */
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

        if ($entityType === 'spell') {
            if (! Spell::query()->whereKey($entityId)->exists()) {
                return response()->json(['message' => 'Sort introuvable.'], 422);
            }
        } else {
            $class = EffectUsage::entityTypeToClass($entityType);
            if ($class === null) {
                return response()->json(['message' => 'Invalid entity_type'], 422);
            }
        }

        $degrees = $this->effectService->getEffectDegreesForEntity($entityType, $entityId, $level, $context);
        $baseContext = ['level' => $level];

        $payload = $degrees->map(function (EffectDegree $d) use ($request, $baseContext, $context, $formatDiceHuman) {
            $d->loadMissing('effect');
            $resolved = $this->effectResolutionService->resolveEffect($d, $baseContext, $context, $formatDiceHuman, false);
            $resolvedCrit = $this->effectResolutionService->resolveEffect($d, $baseContext, $context, $formatDiceHuman, true);

            return [
                'effect' => (new ResolvedEffectDegreeResource($d))->toArray($request),
                'resolved_text' => $this->effectService->renderEffectText($d, $baseContext, $context, $formatDiceHuman),
                'resolved' => $resolved,
                'resolved_crit' => $resolvedCrit,
                'description' => $d->effect?->description,
            ];
        })->values()->all();

        return response()->json(['data' => $payload]);
    }
}
