<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectRequest;
use App\Models\Effect;
use App\Models\EffectGroup;
use App\Models\EffectSubEffect;
use App\Models\SubEffect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin : CRUD effects (liste à gauche, panneau à droite) + duplication degré.
 */
class EffectController extends Controller
{
    public function index(): InertiaResponse
    {
        $list = Effect::with('effectGroup')->orderBy('name')->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        return Inertia::render('Admin/effects/Index', [
            'effects' => $list->map(fn (Effect $e) => [
                'id' => $e->id,
                'name' => $e->name,
                'slug' => $e->slug,
                'effect_group_id' => $e->effect_group_id,
                'degree' => $e->degree,
            ])->values()->all(),
            'selected' => null,
            'options' => $this->options(),
        ]);
    }

    public function create(): InertiaResponse
    {
        $list = Effect::with('effectGroup')->orderBy('name')->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        return Inertia::render('Admin/effects/Index', [
            'effects' => $list->map(fn (Effect $e) => [
                'id' => $e->id,
                'name' => $e->name,
                'slug' => $e->slug,
                'effect_group_id' => $e->effect_group_id,
                'degree' => $e->degree,
            ])->values()->all(),
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
        $list = Effect::with('effectGroup')->orderBy('name')->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        $selected = [
            'id' => $effect->id,
            'name' => $effect->name,
            'slug' => $effect->slug,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $effect->degree,
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
            'selected' => $selected,
            'options' => $this->options(),
        ]);
    }

    public function update(UpdateEffectRequest $request, Effect $effect): RedirectResponse
    {
        $effect->update($request->only(['name', 'slug', 'description', 'effect_group_id', 'degree']));

        $validated = $request->validate([
            'effect_sub_effects' => 'present|array',
            'effect_sub_effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'effect_sub_effects.*.order' => 'integer|min:0',
            'effect_sub_effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'effect_sub_effects.*.value_min' => 'nullable|integer',
            'effect_sub_effects.*.value_max' => 'nullable|integer',
            'effect_sub_effects.*.dice_num' => 'nullable|integer|min:0',
            'effect_sub_effects.*.dice_side' => 'nullable|integer|min:0',
            'effect_sub_effects.*.params' => 'nullable|array',
            'effect_sub_effects.*.params.characteristic' => 'nullable|string|max:64',
            'effect_sub_effects.*.params.value_formula' => 'nullable|string|max:500',
        ]);

        $effect->effectSubEffects()->delete();
        $sanitizer = new \App\Services\Effect\EffectTextSanitizer();
        foreach ($validated['effect_sub_effects'] as $i => $row) {
            $params = $row['params'] ?? null;
            if ($params && ! empty($params['value_formula'])) {
                $params['value_formula'] = $sanitizer->sanitize($params['value_formula']);
            }
            $effect->effectSubEffects()->create([
                'sub_effect_id' => $row['sub_effect_id'],
                'order' => $row['order'] ?? $i,
                'scope' => $row['scope'] ?? 'general',
                'value_min' => $row['value_min'] ?? null,
                'value_max' => $row['value_max'] ?? null,
                'dice_num' => $row['dice_num'] ?? null,
                'dice_side' => $row['dice_side'] ?? null,
                'params' => $params,
            ]);
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

    public function duplicateDegree(Request $request, Effect $effect): RedirectResponse
    {
        $effect->load('effectSubEffects');
        $newDegree = ($effect->degree ?? 0) + 1;
        $newEffect = Effect::create([
            'name' => $effect->name,
            'slug' => $effect->slug ? $effect->slug . '-d' . $newDegree : null,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $newDegree,
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
                'params' => $p->params,
            ]);
        }
        return redirect()->route('admin.effects.show', $newEffect)
            ->with('success', 'Degré dupliqué. Ajustez les sous-effets si besoin.');
    }

    private function options(): array
    {
        $effectGroups = EffectGroup::orderBy('name')->get(['id', 'name', 'slug']);
        $subEffects = SubEffect::orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug', 'template_text', 'variables_allowed', 'param_schema']);
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
            'scopes' => [
                ['value' => 'general', 'label' => 'Général'],
                ['value' => 'combat', 'label' => 'Combat'],
                ['value' => 'out_of_combat', 'label' => 'Hors combat'],
            ],
        ];
    }
}
