<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Characteristic;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\SpellState;
use App\Models\SubEffect;
use Illuminate\Support\Collection;

/**
 * Données pour l’éditeur (définition d’effet + degrés) — admin et fiche sort.
 */
final class EffectGroupEditorDataService
{
    /** @var list<string> */
    private const EXCLUDED_OBJECT_CHARACTERISTIC_KEYS = [
        'name_object',
        'level_object',
        'price_object',
        'weight_object',
        'rarity_object',
    ];

    /**
     * Caractéristiques du groupe « object » (BDD) pour booster / retirer / voler-caractéristiques.
     *
     * @return list<array{key: string, label: string, category: string, helper: string|null, descriptions: mixed}>
     */
    public function characteristicsObjectForEffectEditor(): array
    {
        return Characteristic::query()
            ->where('group', 'object')
            ->whereNull('linked_to_characteristic_id')
            ->whereNotIn('key', self::EXCLUDED_OBJECT_CHARACTERISTIC_KEYS)
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->map(function (Characteristic $c): array {
                $eff = $c->effectiveCharacteristic();

                return [
                    'key' => $eff->key,
                    'label' => $eff->name ?? $eff->short_name ?? $eff->key,
                    'category' => 'object',
                    'helper' => $eff->helper,
                    'descriptions' => $eff->descriptions,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function formOptions(): array
    {
        /** @deprecated Retiré du référentiel : vol de vie = frapper + life_steal_formula. Exclure tant que d’anciennes lignes BDD peuvent subsister. */
        $subEffects = SubEffect::query()
            ->whereNotIn('slug', ['voler-vie'])
            ->orderBy('type_slug')
            ->orderBy('slug')
            ->get(['id', 'slug', 'type_slug', 'template_text', 'variables_allowed', 'param_schema']);
        $monsters = Monster::with('creature:id,name')->orderBy('id')->get()->map(fn ($m) => [
            'value' => $m->id,
            'label' => $m->creature?->name ?? (string) $m->id,
        ])->values()->all();

        $spellStates = SpellState::query()
            ->orderBy('name')
            ->get(['id', 'dofusdb_id', 'name', 'icon'])
            ->map(fn ($st) => [
                'id' => $st->id,
                'dofusdb_id' => $st->dofusdb_id,
                'name' => $st->name,
                'icon' => $st->icon,
            ])
            ->values()
            ->all();

        return [
            'effect_groups' => [],
            'sub_effects' => $subEffects->map(fn ($s) => [
                'id' => $s->id,
                'slug' => $s->slug,
                'type_slug' => $s->type_slug,
                'template_text' => $s->template_text,
                'variables_allowed' => $s->variables_allowed,
                'param_schema' => $s->param_schema,
            ])->values()->all(),
            'characteristics' => config('effect_sub_effects.characteristics', []),
            'characteristics_object' => $this->characteristicsObjectForEffectEditor(),
            'monsters' => $monsters,
            'spell_states' => $spellStates,
            'scopes' => [
                ['value' => 'general', 'label' => 'Général'],
                ['value' => 'combat', 'label' => 'Combat'],
                ['value' => 'out_of_combat', 'label' => 'Hors combat'],
            ],
        ];
    }

    /**
     * Degrés d’une définition d’effet, triés.
     *
     * @return Collection<int, EffectDegree>
     */
    public function degreesForEffect(Effect $effect): Collection
    {
        return $effect->degrees()
            ->with(['effectSubEffects.subEffect'])
            ->orderBy('degree')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return Collection<int, int>
     */
    public function degreeIdsForEffect(Effect $effect): Collection
    {
        return $this->degreesForEffect($effect)->pluck('id')->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeDegreeForEditor(EffectDegree $degree): array
    {
        $degree->loadMissing(['effectSubEffects.subEffect', 'effect']);

        return [
            'id' => $degree->id,
            'effect_id' => $degree->effect_id,
            'name' => $degree->effect->name,
            'slug' => $degree->slug,
            'description' => $degree->effect->description,
            'effect_group_id' => null,
            'degree' => $degree->degree,
            'target_type' => $degree->effect->target_type ?? Effect::TARGET_DIRECT,
            'area' => $degree->area,
            'required_creature_level' => $degree->required_creature_level,
            'sub_effects' => $degree->effectSubEffects->map(function (EffectSubEffect $p) {
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
    }

    public function spellLinksToEffect(Spell $spell, Effect $effect): bool
    {
        return $spell->effects()->whereKey($effect->id)->exists();
    }

    /**
     * @return list<array{label: string, anchor_effect_id: int, group_effects: list<array<string, mixed>>}>
     */
    public function distinctGroupsForSpell(Spell $spell): array
    {
        $spell->loadMissing(['effects.degrees']);

        $result = [];
        foreach ($spell->effects as $effect) {
            $label = $effect->name ?: ($effect->slug ?: 'Effet #'.$effect->id);
            $degrees = $this->degreesForEffect($effect);
            if ($degrees->isEmpty()) {
                continue;
            }
            $result[] = [
                'label' => $label,
                'anchor_effect_id' => $effect->id,
                'group_effects' => $degrees
                    ->map(fn (EffectDegree $d) => $this->serializeDegreeForEditor($d))
                    ->values()
                    ->all(),
            ];
        }

        return $result;
    }
}
