<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreEffectRequest;
use App\Http\Requests\Effect\UpdateEffectRequest;
use App\Models\Effect;
use App\Models\EffectGroup;
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
        $effect->load(['effectGroup', 'subEffects']);
        $list = Effect::with('effectGroup')->orderBy('name')->get(['id', 'name', 'slug', 'effect_group_id', 'degree']);
        $selected = [
            'id' => $effect->id,
            'name' => $effect->name,
            'slug' => $effect->slug,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $effect->degree,
            'sub_effects' => $effect->subEffects->map(fn ($s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
                'template_text' => $s->template_text,
                'order' => $s->pivot->order ?? 0,
                'scope' => $s->pivot->scope ?? 'general',
                'value_min' => $s->pivot->value_min,
                'value_max' => $s->pivot->value_max,
                'dice_num' => $s->pivot->dice_num,
                'dice_side' => $s->pivot->dice_side,
                'params' => $s->pivot->params,
            ])->values()->all(),
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
        ]);

        $sync = [];
        foreach ($validated['effect_sub_effects'] as $i => $row) {
            $sync[$row['sub_effect_id']] = [
                'order' => $row['order'] ?? $i,
                'scope' => $row['scope'] ?? 'general',
                'value_min' => $row['value_min'] ?? null,
                'value_max' => $row['value_max'] ?? null,
                'dice_num' => $row['dice_num'] ?? null,
                'dice_side' => $row['dice_side'] ?? null,
                'params' => $row['params'] ?? null,
            ];
        }
        $effect->subEffects()->sync($sync);

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
        $effect->load('subEffects');
        $newDegree = ($effect->degree ?? 0) + 1;
        $newEffect = Effect::create([
            'name' => $effect->name,
            'slug' => $effect->slug ? $effect->slug . '-d' . $newDegree : null,
            'description' => $effect->description,
            'effect_group_id' => $effect->effect_group_id,
            'degree' => $newDegree,
        ]);
        $sync = [];
        foreach ($effect->subEffects as $i => $s) {
            $sync[$s->id] = [
                'order' => $s->pivot->order ?? $i,
                'scope' => $s->pivot->scope ?? 'general',
                'value_min' => $s->pivot->value_min,
                'value_max' => $s->pivot->value_max,
                'dice_num' => $s->pivot->dice_num,
                'dice_side' => $s->pivot->dice_side,
                'params' => $s->pivot->params,
            ];
        }
        $newEffect->subEffects()->sync($sync);
        return redirect()->route('admin.effects.show', $newEffect)
            ->with('success', 'Degré dupliqué. Ajustez les sous-effets si besoin.');
    }

    private function options(): array
    {
        $effectGroups = EffectGroup::orderBy('name')->get(['id', 'name', 'slug']);
        $subEffects = SubEffect::orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug']);
        return [
            'effect_groups' => $effectGroups->map(fn ($g) => ['value' => $g->id, 'label' => $g->name])->values()->all(),
            'sub_effects' => $subEffects->map(fn ($s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
            ])->values()->all(),
            'scopes' => [
                ['value' => 'general', 'label' => 'Général'],
                ['value' => 'combat', 'label' => 'Combat'],
                ['value' => 'out_of_combat', 'label' => 'Hors combat'],
            ],
        ];
    }
}
