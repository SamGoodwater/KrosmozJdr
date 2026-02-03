<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpellEffectType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * Administration des types d'effets de sort (référentiel).
 * Liste à gauche, panneau d'édition à droite.
 */
class SpellEffectTypeController extends Controller
{
    /**
     * Liste des types d'effets (page avec liste à gauche, panneau vide à droite).
     */
    public function index(): InertiaResponse
    {
        $list = SpellEffectType::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'category', 'sort_order']);

        return Inertia::render('Admin/spell-effect-types/Index', [
            'spellEffectTypes' => $list->map(fn (SpellEffectType $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'category' => $t->category,
                'sort_order' => $t->sort_order,
            ])->values()->all(),
            'selected' => null,
            'options' => $this->options(),
        ]);
    }

    /**
     * Affiche un type d'effet (même page, panneau à droite rempli).
     */
    public function show(SpellEffectType $spellEffectType): InertiaResponse
    {
        $list = SpellEffectType::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'category', 'sort_order']);

        $selected = [
            'id' => $spellEffectType->id,
            'name' => $spellEffectType->name,
            'slug' => $spellEffectType->slug,
            'category' => $spellEffectType->category,
            'description' => $spellEffectType->description,
            'value_type' => $spellEffectType->value_type,
            'element' => $spellEffectType->element,
            'unit' => $spellEffectType->unit,
            'is_positive' => $spellEffectType->is_positive,
            'sort_order' => $spellEffectType->sort_order,
            'dofusdb_effect_id' => $spellEffectType->dofusdb_effect_id,
        ];

        return Inertia::render('Admin/spell-effect-types/Index', [
            'spellEffectTypes' => $list->map(fn (SpellEffectType $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'category' => $t->category,
                'sort_order' => $t->sort_order,
            ])->values()->all(),
            'selected' => $selected,
            'options' => $this->options(),
        ]);
    }

    /**
     * Met à jour un type d'effet.
     */
    public function update(Request $request, SpellEffectType $spellEffectType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:64|unique:spell_effect_types,slug,' . $spellEffectType->id,
            'category' => 'required|string|in:' . implode(',', SpellEffectType::categories()),
            'description' => 'nullable|string|max:2000',
            'value_type' => 'required|string|in:' . implode(',', SpellEffectType::valueTypes()),
            'element' => 'nullable|string|max:16|in:' . implode(',', SpellEffectType::elements()),
            'unit' => 'nullable|string|max:32',
            'is_positive' => 'boolean',
            'sort_order' => 'integer',
            'dofusdb_effect_id' => 'nullable|integer|min:0',
        ]);

        if (($validated['element'] ?? '') === '') {
            $validated['element'] = null;
        }

        $spellEffectType->update($validated);

        return redirect()->route('admin.spell-effect-types.show', $spellEffectType)
            ->with('success', 'Type d’effet enregistré.');
    }

    /**
     * Options pour les listes déroulantes (catégories, value_type, élément).
     *
     * @return array{categories: list<array{id: string, name: string}>, value_types: list<array{id: string, name: string}>, elements: list<array{id: string, name: string}>}
     */
    private function options(): array
    {
        $categoryLabels = [
            'damage' => 'Dégâts',
            'heal' => 'Soin',
            'heal_over_time' => 'Soin dans le temps',
            'shield' => 'Bouclier',
            'ap' => 'PA',
            'pm' => 'PM',
            'range' => 'Portée',
            'buff_stat' => 'Buff caractéristique',
            'debuff_stat' => 'Debuff caractéristique',
            'buff_damage' => 'Buff dégâts',
            'debuff_damage' => 'Debuff dégâts',
            'resistance' => 'Résistance',
            'state' => 'État / Altération',
            'placement' => 'Placement',
            'teleport' => 'Téléportation',
            'summon' => 'Invocation',
            'glyph_trap' => 'Glyphe / Piège',
            'zone' => 'Zone',
            'critical' => 'Critique',
            'reflect' => 'Réflexion',
            'steal' => 'Vol',
            'damage_over_time' => 'Dégâts dans le temps',
            'lock' => 'Blocage',
            'line_of_sight' => 'Ligne de vue',
            'invisibility' => 'Invisibilité',
            'prospecting' => 'Prospection',
            'other' => 'Autre',
        ];

        return [
            'categories' => collect(SpellEffectType::categories())->map(fn (string $c) => [
                'value' => $c,
                'label' => $categoryLabels[$c] ?? $c,
            ])->values()->all(),
            'value_types' => [
                ['value' => 'fixed', 'label' => 'Valeur fixe'],
                ['value' => 'dice', 'label' => 'Dés'],
                ['value' => 'percent', 'label' => 'Pourcentage'],
            ],
            'elements' => [
                ['value' => '', 'label' => '— Aucun —'],
                ['value' => 'neutral', 'label' => 'Neutre'],
                ['value' => 'earth', 'label' => 'Terre'],
                ['value' => 'fire', 'label' => 'Feu'],
                ['value' => 'water', 'label' => 'Eau'],
                ['value' => 'air', 'label' => 'Air'],
            ],
        ];
    }
}
