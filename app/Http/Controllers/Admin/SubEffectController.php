<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Effect\StoreSubEffectRequest;
use App\Http\Requests\Effect\UpdateSubEffectRequest;
use App\Models\SubEffect;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Admin : CRUD sous-effets (liste à gauche, panneau à droite).
 */
class SubEffectController extends Controller
{
    public function index(): InertiaResponse
    {
        $list = SubEffect::orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug']);
        return Inertia::render('Admin/sub-effects/Index', [
            'subEffects' => $list->map(fn (SubEffect $s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
            ])->values()->all(),
            'selected' => null,
            'options' => $this->options(),
        ]);
    }

    public function create(): InertiaResponse
    {
        $list = SubEffect::orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug']);
        return Inertia::render('Admin/sub-effects/Index', [
            'subEffects' => $list->map(fn (SubEffect $s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
            ])->values()->all(),
            'selected' => 'new',
            'options' => $this->options(),
        ]);
    }

    public function store(StoreSubEffectRequest $request): RedirectResponse
    {
        $sub = SubEffect::create($request->validated());
        return redirect()->route('admin.sub-effects.show', $sub)
            ->with('success', 'Sous-effet créé.');
    }

    public function show(SubEffect $sub_effect): InertiaResponse
    {
        $list = SubEffect::orderBy('type_slug')->orderBy('slug')->get(['id', 'slug', 'type_slug']);
        $s = $sub_effect;
        $selected = [
            'id' => $s->id,
            'slug' => $s->slug,
            'type_slug' => $s->type_slug,
            'template_text' => $s->template_text,
            'formula' => $s->formula,
            'variables_allowed' => $s->variables_allowed,
            'dofusdb_effect_id' => $s->dofusdb_effect_id,
        ];
        return Inertia::render('Admin/sub-effects/Index', [
            'subEffects' => $list->map(fn (SubEffect $s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
            ])->values()->all(),
            'selected' => $selected,
            'options' => $this->options(),
        ]);
    }

    public function update(UpdateSubEffectRequest $request, SubEffect $sub_effect): RedirectResponse
    {
        $sub_effect->update($request->validated());
        return redirect()->route('admin.sub-effects.show', $sub_effect)
            ->with('success', 'Sous-effet enregistré.');
    }

    public function destroy(SubEffect $sub_effect): RedirectResponse
    {
        $sub_effect->delete();
        return redirect()->route('admin.sub-effects.index')
            ->with('success', 'Sous-effet supprimé.');
    }

    private function options(): array
    {
        return [
            'type_slugs' => [
                ['value' => 'taper', 'label' => 'Taper (dégâts)'],
                ['value' => 'soigner', 'label' => 'Soigner'],
                ['value' => 'vol_pa', 'label' => 'Vol PA'],
                ['value' => 'vol_pm', 'label' => 'Vol PM'],
                ['value' => 'buff', 'label' => 'Buff'],
            ],
            'syntax_help_url' => '/docs/50-Fonctionnalités/Spell-Effects/SYNTAXE_EFFETS.md',
        ];
    }
}
