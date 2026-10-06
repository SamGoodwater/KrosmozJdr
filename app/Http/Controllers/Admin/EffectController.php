<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectGroupRequest;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Services\Effect\EffectGroupEditorDataService;
use App\Services\Effect\EffectGroupUpdateService;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin : définitions d’effets (liste, édition par degrés, duplication).
 */
class EffectController extends Controller
{
    public function __construct(
        private readonly EffectGroupEditorDataService $effectGroupEditorData,
        private readonly EffectGroupUpdateService $effectGroupUpdate,
    ) {}

    public function index(Request $request): InertiaResponse
    {
        $definitions = $this->sidebarDefinitions(
            q: trim((string) $request->query('q', '')),
        );

        return Inertia::render('Admin/effects/Index', [
            'effects' => [],
            'groups' => $definitions,
            'sidebarQuery' => trim((string) $request->query('q', '')),
            'selected' => null,
            'groupEffects' => null,
            // Éditeur fermé : pas de catalogue sous-effets (chargé sur show/create).
            'options' => [
                'effect_groups' => [],
                'sub_effects' => [],
                'characteristics' => [],
                'characteristics_object' => [],
                'monsters' => [],
                'conditions' => [],
                'scopes' => [],
            ],
        ]);
    }

    public function create(Request $request): InertiaResponse
    {
        $definitions = $this->sidebarDefinitions(
            q: trim((string) $request->query('q', '')),
        );

        return Inertia::render('Admin/effects/Index', [
            'effects' => [],
            'groups' => $definitions,
            'sidebarQuery' => trim((string) $request->query('q', '')),
            'selected' => 'new',
            'groupEffects' => null,
            'options' => $this->effectGroupEditorData->formOptions(),
        ]);
    }

    public function store(StoreEffectRequest $request): RedirectResponse
    {
        $effect = Effect::create($request->safe()->only(['name', 'slug', 'description', 'target_type'])->toArray());
        EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'area' => $request->input('initial_area'),
            'required_creature_level' => $request->input('initial_required_creature_level'),
            'slug' => $request->input('initial_degree_slug'),
        ]);

        return redirect()->route('admin.effects.show', $effect)
            ->with('success', 'Effet créé.');
    }

    public function show(Effect $effect): InertiaResponse
    {
        $degrees = $this->effectGroupEditorData->degreesForEffect($effect);

        $groupEffects = $degrees
            ->map(fn (EffectDegree $d) => $this->effectGroupEditorData->serializeDegreeForEditor($d))
            ->values()
            ->all();

        $selected = [
            'id' => $effect->id,
            'name' => $effect->name,
            'slug' => $effect->slug,
            'description' => $effect->description,
            'target_type' => $effect->target_type ?? Effect::TARGET_DIRECT,
        ];

        $definitions = $this->sidebarDefinitions(
            q: trim((string) request()->query('q', '')),
            ensureId: $effect->id,
        );

        return Inertia::render('Admin/effects/Index', [
            'effects' => [],
            'groups' => $definitions,
            'sidebarQuery' => trim((string) request()->query('q', '')),
            'selected' => $selected,
            'groupEffects' => $groupEffects,
            'options' => $this->effectGroupEditorData->formOptions(),
        ]);
    }

    public function updateGroup(UpdateEffectGroupRequest $request, Effect $effect): JsonResponse|RedirectResponse
    {
        $this->effectGroupUpdate->updateGroup($effect, $request->validated());

        $message = 'Groupe d’effets enregistré.';

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back(fallback: route('admin.effects.show', $effect))
            ->with('success', $message);
    }

    public function destroy(Effect $effect): RedirectResponse
    {
        $effect->delete();

        return redirect()->route('admin.effects.index')
            ->with('success', 'Effet supprimé.');
    }

    /**
     * Supprime un degré précis (sous-effets en cascade). Interdit si dernier degré ou usages liés.
     */
    public function destroyDegree(Effect $effect, EffectDegree $degree): RedirectResponse
    {
        abort_unless($degree->effect_id === $effect->id, 404);

        if ($effect->degrees()->count() <= 1) {
            return redirect()->route('admin.effects.show', $effect)
                ->with('error', 'Impossible de supprimer le dernier degré. Pour retirer toute la définition, utilisez « Supprimer la définition ».');
        }

        if ($degree->effectUsages()->exists()) {
            return redirect()->route('admin.effects.show', $effect)
                ->with('error', 'Ce degré est encore utilisé (objets, consommables…). Retirez ou réassignez ces usages avant suppression.');
        }

        $degree->delete();

        return redirect()->route('admin.effects.show', $effect)
            ->with('success', 'Degré supprimé.');
    }

    public function duplicate(Effect $effect): RedirectResponse
    {
        $effect->load(['degrees.effectSubEffects']);

        $newEffect = Effect::create([
            'name' => $effect->name,
            'slug' => $effect->slug ? $effect->slug.'-copy' : null,
            'description' => $effect->description,
            'target_type' => $effect->target_type ?? Effect::TARGET_DIRECT,
        ]);

        foreach ($effect->degrees as $deg) {
            $slug = $deg->slug ? $this->uniqueDegreeSlug($deg->slug.'-copy') : null;
            $newDeg = EffectDegree::create([
                'effect_id' => $newEffect->id,
                'degree' => $deg->degree,
                'required_creature_level' => $deg->required_creature_level,
                'area' => $deg->area,
                'slug' => $slug,
            ]);
            foreach ($deg->effectSubEffects as $p) {
                $newDeg->effectSubEffects()->create([
                    'sub_effect_id' => $p->sub_effect_id,
                    'order' => $p->order,
                    'scope' => $p->scope,
                    'value_min' => $p->value_min,
                    'value_max' => $p->value_max,
                    'dice_num' => $p->dice_num,
                    'dice_side' => $p->dice_side,
                    'duration_formula' => $p->duration_formula,
                    'logic_group' => $p->logic_group,
                    'logic_operator' => $p->logic_operator,
                    'logic_condition' => $p->logic_condition,
                    'crit_only' => (bool) ($p->crit_only ?? false),
                    'params' => $p->params,
                ]);
            }
            $newDeg->load(['effectSubEffects', 'effect']);
            $sig = app(IntegrationService::class)->rebuildConfigSignatureForEffectDegree($newDeg);
            if ($sig !== null) {
                $newDeg->update(['config_signature' => $sig]);
            }
        }

        return redirect()->route('admin.effects.show', $newEffect)
            ->with('success', 'Effet dupliqué. Ajustez le nom, le slug et les sous-effets si besoin.');
    }

    public function duplicateDegree(Effect $effect): RedirectResponse
    {
        $effect->load(['degrees.effectSubEffects']);
        $template = $effect->degrees->sortByDesc('degree')->first();
        if ($template === null) {
            $slug = $this->uniqueDegreeSlug($effect->slug ? $effect->slug.'-d1' : 'effect-'.$effect->id.'-d1');
            EffectDegree::create([
                'effect_id' => $effect->id,
                'degree' => 1,
                'area' => null,
                'slug' => $slug,
            ]);

            return redirect()->route('admin.effects.show', $effect)
                ->with('success', 'Premier degré créé.');
        }

        $newDegreeNum = (int) $effect->degrees()->max('degree') + 1;
        $baseSlug = ($template->slug ?? $effect->slug ?? 'deg').'-d'.$newDegreeNum;
        $newSlug = $this->uniqueDegreeSlug(Str::limit($baseSlug, 64, ''));

        $newDeg = EffectDegree::create([
            'effect_id' => $effect->id,
            'degree' => $newDegreeNum,
            'required_creature_level' => $template->required_creature_level,
            'area' => $template->area,
            'slug' => $newSlug,
        ]);

        foreach ($template->effectSubEffects as $p) {
            $newDeg->effectSubEffects()->create([
                'sub_effect_id' => $p->sub_effect_id,
                'order' => $p->order,
                'scope' => $p->scope,
                'value_min' => $p->value_min,
                'value_max' => $p->value_max,
                'dice_num' => $p->dice_num,
                'dice_side' => $p->dice_side,
                'duration_formula' => $p->duration_formula,
                'logic_group' => $p->logic_group,
                'logic_operator' => $p->logic_operator,
                'logic_condition' => $p->logic_condition,
                'crit_only' => (bool) ($p->crit_only ?? false),
                'params' => $p->params,
            ]);
        }

        $newDeg->load(['effectSubEffects', 'effect']);
        $sig = app(IntegrationService::class)->rebuildConfigSignatureForEffectDegree($newDeg);
        if ($sig !== null) {
            $newDeg->update(['config_signature' => $sig]);
        }

        return redirect()->route('admin.effects.show', $effect)
            ->with('success', 'Degré dupliqué. Ajustez les sous-effets si besoin.');
    }

    /**
    /**
     * Sidebar admin : échantillon limité + recherche (évite ~15k lignes dans le HTML Inertia).
     *
     * @return list<array{id:int, label:string, degrees_count:int}>
     */
    private function sidebarDefinitions(string $q = '', ?int $ensureId = null, int $limit = 50): array
    {
        $limit = max(1, min(100, $limit));
        $q = trim($q);

        $query = Effect::query()
            ->select(['effects.id', 'effects.name', 'effects.slug'])
            ->withCount('degrees')
            ->orderBy('name');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(static function ($builder) use ($like): void {
                $builder->where('name', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        $rows = $query
            ->limit($limit)
            ->get()
            ->map(static fn (Effect $effect): array => [
                'id' => (int) $effect->id,
                'label' => $effect->name ?: ($effect->slug ?: 'Effet #'.$effect->id),
                'degrees_count' => (int) $effect->degrees_count,
            ])
            ->all();

        if ($ensureId !== null && ! collect($rows)->contains(static fn (array $row): bool => $row['id'] === $ensureId)) {
            $ensured = Effect::query()
                ->select(['id', 'name', 'slug'])
                ->withCount('degrees')
                ->whereKey($ensureId)
                ->first();
            if ($ensured !== null) {
                array_unshift($rows, [
                    'id' => (int) $ensured->id,
                    'label' => $ensured->name ?: ($ensured->slug ?: 'Effet #'.$ensured->id),
                    'degrees_count' => (int) $ensured->degrees_count,
                ]);
            }
        }

        return $rows;
    }

    private function uniqueDegreeSlug(string $preferred): ?string
    {
        $preferred = trim($preferred);
        if ($preferred === '') {
            return null;
        }
        $slug = Str::limit($preferred, 64, '');
        $n = 0;
        while (EffectDegree::query()->where('slug', $slug)->exists()) {
            $n++;
            $suffix = '-'.$n;
            $slug = Str::limit(substr($preferred, 0, 64 - strlen($suffix)).$suffix, 64, '');
        }

        return $slug;
    }
}
