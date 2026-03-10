<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectRequest;
use App\Models\Effect;
use App\Models\EffectGroup;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Models\EffectSubEffect;
use App\Models\Entity\Monster;
use App\Models\SubEffect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin : CRUD effects (liste à gauche, panneau à droite) + duplication degré.
 */
class EffectController extends Controller
{
    public function index(): InertiaResponse
    {
        $list = Effect::with('effectGroup')
            ->orderBy('effect_group_id')
            ->orderBy('degree')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        return Inertia::render('Admin/effects/Index', [
            'effects' => $list->map(fn (Effect $e) => [
                'id' => $e->id,
                'name' => $e->name,
                'slug' => $e->slug,
                'effect_group_id' => $e->effect_group_id,
                'degree' => $e->degree,
            ])->values()->all(),
            'groups' => $this->buildSidebarGroups($list),
            'selected' => null,
            'options' => $this->options(),
        ]);
    }

    public function create(): InertiaResponse
    {
        $list = Effect::with('effectGroup')
            ->orderBy('effect_group_id')
            ->orderBy('degree')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        return Inertia::render('Admin/effects/Index', [
            'effects' => $list->map(fn (Effect $e) => [
                'id' => $e->id,
                'name' => $e->name,
                'slug' => $e->slug,
                'effect_group_id' => $e->effect_group_id,
                'degree' => $e->degree,
            ])->values()->all(),
            'groups' => $this->buildSidebarGroups($list),
            'selected' => 'new',
            'options' => $this->options(),
        ]);
    }

    public function store(StoreEffectRequest $request): RedirectResponse
    {
        $effect = Effect::create($request->validated());
        return redirect()->route('admin.effects.show', $effect)
            ->with('success', 'Effet créé.');
    }

    public function show(Effect $effect): InertiaResponse
    {
        $effect->load(['effectGroup', 'effectSubEffects.subEffect']);
        $list = Effect::with('effectGroup')
            ->orderBy('effect_group_id')
            ->orderBy('degree')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        $selected = [
            'id' => $effect->id,
            'name' => $effect->name,
            'slug' => $effect->slug,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $effect->degree,
            'target_type' => $effect->target_type ?? \App\Models\Effect::TARGET_DIRECT,
            'area' => $effect->area,
            'sub_effects' => $effect->effectSubEffects->map(function (EffectSubEffect $p) {
                $params = $p->params ?? [];
                if (! isset($params['characteristic'])) {
                    $params['characteristic'] = $params['element'] ?? $params['caracteristic'] ?? '';
                }
                return [
                    'id' => $p->subEffect->id,
                    'slug' => $p->subEffect->slug,
                    'type_slug' => $p->subEffect->type_slug,
                    'template_text' => $p->subEffect->template_text,
                    'param_schema' => $p->subEffect->param_schema,
                    'order' => $p->order,
                    'scope' => $p->scope ?? 'general',
                    'value_min' => $p->value_min,
                    'value_max' => $p->value_max,
                    'dice_num' => $p->dice_num,
                    'dice_side' => $p->dice_side,
                    'duration_formula' => $p->duration_formula,
                    'logic_group' => $p->logic_group,
                    'logic_operator' => $p->logic_operator,
                    'logic_condition' => $p->logic_condition,
                    'crit_only' => (bool) ($p->crit_only ?? false),
                    'params' => $params,
                ];
            })->values()->all(),
        ];
        return Inertia::render('Admin/effects/Index', [
            'effects' => $list->map(fn (Effect $e) => [
                'id' => $e->id,
                'name' => $e->name,
                'slug' => $e->slug,
                'effect_group_id' => $e->effect_group_id,
                'degree' => $e->degree,
            ])->values()->all(),
            'groups' => $this->buildSidebarGroups($list),
            'selected' => $selected,
            'options' => $this->options(),
        ]);
    }

    public function update(UpdateEffectRequest $request, Effect $effect): RedirectResponse
    {
        $effect->update($request->only([
            'name', 'slug', 'description', 'effect_group_id', 'degree',
            'target_type', 'area',
        ]));

        // Normaliser params.monster_id (chaîne vide → null) pour la validation
        $subEffects = $request->input('effect_sub_effects', []);
        foreach ($subEffects as $i => $row) {
            $params = $row['params'] ?? [];
            if (array_key_exists('monster_id', $params) && $params['monster_id'] === '') {
                $subEffects[$i]['params']['monster_id'] = null;
            }
        }
        $request->merge(['effect_sub_effects' => $subEffects]);

        $validated = $request->validate([
            'effect_sub_effects' => 'present|array',
            'effect_sub_effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'effect_sub_effects.*.order' => 'integer|min:0',
            'effect_sub_effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'effect_sub_effects.*.value_min' => 'nullable|integer',
            'effect_sub_effects.*.value_max' => 'nullable|integer',
            'effect_sub_effects.*.dice_num' => 'nullable|integer|min:0',
            'effect_sub_effects.*.dice_side' => 'nullable|integer|min:0',
            'effect_sub_effects.*.duration_formula' => 'nullable|string|max:255',
            'effect_sub_effects.*.logic_group' => 'nullable|string|max:64',
            'effect_sub_effects.*.logic_operator' => 'nullable|string|in:AND,OR',
            'effect_sub_effects.*.logic_condition' => 'nullable|string|max:255',
            'effect_sub_effects.*.params' => 'nullable|array',
            'effect_sub_effects.*.params.characteristic' => 'nullable|string|max:64',
            'effect_sub_effects.*.params.value_formula' => 'nullable|string|max:500',
            'effect_sub_effects.*.params.value_formula_crit' => 'nullable|string|max:500',
            'effect_sub_effects.*.params.monster_id' => 'nullable|integer|exists:monsters,id',
            'effect_sub_effects.*.crit_only' => 'nullable|boolean',
        ]);

        $effect->effectSubEffects()->delete();
        $sanitizer = new \App\Services\Effect\EffectTextSanitizer();
        foreach ($validated['effect_sub_effects'] as $i => $row) {
            $params = $row['params'] ?? null;
            if ($params && ! empty($params['value_formula'])) {
                $params['value_formula'] = $sanitizer->sanitize($params['value_formula']);
            }
            if ($params && ! empty($params['value_formula_crit'])) {
                $params['value_formula_crit'] = $sanitizer->sanitize($params['value_formula_crit']);
            }
            $durationFormula = $row['duration_formula'] ?? null;
            if ($durationFormula) {
                $durationFormula = $sanitizer->sanitize($durationFormula);
            }
            $logicCondition = $row['logic_condition'] ?? null;
            if ($logicCondition) {
                $logicCondition = $sanitizer->sanitize($logicCondition);
            }
            $effect->effectSubEffects()->create([
                'sub_effect_id' => $row['sub_effect_id'],
                'order' => $row['order'] ?? $i,
                'scope' => $row['scope'] ?? 'general',
                'value_min' => $row['value_min'] ?? null,
                'value_max' => $row['value_max'] ?? null,
                'dice_num' => $row['dice_num'] ?? null,
                'dice_side' => $row['dice_side'] ?? null,
                'duration_formula' => $durationFormula,
                'logic_group' => $row['logic_group'] ?? null,
                'logic_operator' => $row['logic_operator'] ?? null,
                'logic_condition' => $logicCondition,
                'crit_only' => (bool) ($row['crit_only'] ?? false),
                'params' => $params,
            ]);
        }

        $effect->load('effectSubEffects');
        $newSignature = app(IntegrationService::class)->rebuildConfigSignatureForEffect($effect);
        if ($newSignature !== null) {
            $effect->update(['config_signature' => $newSignature]);
        }

        return redirect()->route('admin.effects.show', $effect)
            ->with('success', 'Effet enregistré.');
    }

    public function destroy(Effect $effect): RedirectResponse
    {
        $effect->delete();
        return redirect()->route('admin.effects.index')
            ->with('success', 'Effet supprimé.');
    }

    public function duplicate(Request $request, Effect $effect): RedirectResponse
    {
        $effect->load('effectSubEffects');

        $newEffect = Effect::create([
            'name' => $effect->name,
            'slug' => $effect->slug ? $effect->slug . '-copy' : null,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $effect->degree,
            'target_type' => $effect->target_type ?? Effect::TARGET_DIRECT,
            'area' => $effect->area,
        ]);

        foreach ($effect->effectSubEffects as $p) {
            $newEffect->effectSubEffects()->create([
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

        $newSignature = app(IntegrationService::class)->rebuildConfigSignatureForEffect($newEffect->load('effectSubEffects'));
        if ($newSignature !== null) {
            $newEffect->update(['config_signature' => $newSignature]);
        }

        return redirect()->route('admin.effects.show', $newEffect)
            ->with('success', 'Effet dupliqué. Ajustez le nom, le slug et les sous-effets si besoin.');
    }

    public function duplicateDegree(Request $request, Effect $effect): RedirectResponse
    {
        $effect->load(['effectGroup', 'effectSubEffects']);

        // S'assurer qu'un groupe existe pour cet effet et ses degrés
        if (! $effect->effect_group_id) {
            $baseName = $effect->name ?: ($effect->slug ?: 'Groupe effet #' . $effect->id);
            $baseSlug = $effect->slug ? $effect->slug . '-group' : Str::slug($baseName);

            $slug = $baseSlug;
            $i = 1;
            while (EffectGroup::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $i;
                $i++;
            }

            $group = EffectGroup::create([
                'name' => $baseName,
                'slug' => $slug,
            ]);

            $effect->effect_group_id = $group->id;
            $effect->save();
        }

        $newDegree = ($effect->degree ?? 0) + 1;
        $newEffect = Effect::create([
            'name' => $effect->name,
            'slug' => $effect->slug ? $effect->slug . '-d' . $newDegree : null,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $newDegree,
            'target_type' => $effect->target_type ?? Effect::TARGET_DIRECT,
            'area' => $effect->area,
        ]);
        foreach ($effect->effectSubEffects as $p) {
            $newEffect->effectSubEffects()->create([
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

        $newSignature = app(IntegrationService::class)->rebuildConfigSignatureForEffect($newEffect->load('effectSubEffects'));
        if ($newSignature !== null) {
            $newEffect->update(['config_signature' => $newSignature]);
        }

        return redirect()->route('admin.effects.show', $newEffect)
            ->with('success', 'Degré dupliqué. Ajustez les sous-effets si besoin.');
    }

    private function options(): array
    {
        $effectGroups = EffectGroup::orderBy('name')->get(['id', 'name', 'slug']);
        $subEffects = SubEffect::orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug', 'template_text', 'variables_allowed', 'param_schema']);
        $monsters = Monster::with('creature:id,name')->orderBy('id')->get()->map(fn ($m) => [
            'value' => $m->id,
            'label' => $m->creature?->name ?? (string) $m->id,
        ])->values()->all();

        return [
            'effect_groups' => $effectGroups->map(fn ($g) => ['value' => $g->id, 'label' => $g->name])->values()->all(),
            'sub_effects' => $subEffects->map(fn ($s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
                'template_text' => $s->template_text,
                'variables_allowed' => $s->variables_allowed,
                'param_schema' => $s->param_schema,
            ])->values()->all(),
            'characteristics' => config('effect_sub_effects.characteristics', []),
            'monsters' => $monsters,
            'scopes' => [
                ['value' => 'general', 'label' => 'Général'],
                ['value' => 'combat', 'label' => 'Combat'],
                ['value' => 'out_of_combat', 'label' => 'Hors combat'],
            ],
        ];
    }

    /**
     * Regroupe les effets par groupe pour l'affichage dans le menu latéral.
     *
     * @param \Illuminate\Support\Collection<int,Effect> $effects
     * @return array<int,array<string,mixed>>
     */
    private function buildSidebarGroups($effects): array
    {
        $groups = [];

        foreach ($effects as $effect) {
            $groupId = $effect->effect_group_id;

            if ($groupId) {
                $key = 'group-' . $groupId;
                if (! isset($groups[$key])) {
                    $label = $effect->effectGroup?->name
                        ?? ($effect->name ?: ($effect->slug ?: 'Groupe #' . $groupId));

                    $groups[$key] = [
                        'id' => $groupId,
                        'label' => $label,
                        'effects' => [],
                    ];
                }

                $groups[$key]['effects'][] = [
                    'id' => $effect->id,
                    'name' => $effect->name,
                    'slug' => $effect->slug,
                    'degree' => $effect->degree,
                ];
            } else {
                // Effet isolé sans groupe → groupe virtuel 1:1
                $key = 'single-' . $effect->id;
                if (! isset($groups[$key])) {
                    $label = $effect->name ?: ($effect->slug ?: 'Effet #' . $effect->id);
                    $groups[$key] = [
                        'id' => null,
                        'label' => $label,
                        'effects' => [[
                            'id' => $effect->id,
                            'name' => $effect->name,
                            'slug' => $effect->slug,
                            'degree' => $effect->degree,
                        ]],
                    ];
                }
            }
        }

        return array_values($groups);
    }
}
