<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Effect;
use App\Models\Entity\Monster;
use App\Models\SpellState;
use Illuminate\Support\Collection;

/**
 * Sérialise les définitions d’effets d’un sort (degrés, pivots, monstres invoqués) pour API / table.
 */
final class SpellEffectDefinitionsSerializer
{
    /**
     * @param  Collection<int, Effect>|\Illuminate\Database\Eloquent\Collection<int, Effect>|iterable<Effect>  $effects
     * @return list<array<string, mixed>>
     */
    public function serialize(iterable $effects): array
    {
        $effects = Collection::make($effects)->map(function (Effect $effect) {
            $effect->loadMissing(['degrees.effectSubEffects.subEffect']);

            return $effect;
        });

        $monsterIds = $this->collectMonsterIdsFromPivots($effects);
        $monstersById = $this->loadMonstersById($monsterIds);
        $spellStateIds = $this->collectSpellStateIdsFromPivots($effects);
        $spellStatesById = $this->loadSpellStatesById($spellStateIds);

        return $effects->map(function (Effect $effect) use ($monstersById, $spellStatesById) {
            return [
                'id' => $effect->id,
                'name' => $effect->name,
                'description' => $effect->description,
                'target_type' => $effect->target_type ?? Effect::TARGET_DIRECT,
                'degrees' => $effect->degrees->sortBy('degree')->values()->map(function ($degree) use ($monstersById, $spellStatesById) {
                    return [
                        'id' => $degree->id,
                        'degree' => $degree->degree,
                        'required_creature_level' => $degree->required_creature_level,
                        'area' => $degree->area,
                        'rows' => $degree->effectSubEffects->sortBy('order')->values()->map(function ($pivot) use ($monstersById, $spellStatesById) {
                            $sub = $pivot->subEffect;
                            $params = is_array($pivot->params) ? $pivot->params : [];

                            return [
                                'order' => $pivot->order,
                                'scope' => $pivot->scope,
                                'value_min' => $pivot->value_min,
                                'value_max' => $pivot->value_max,
                                'dice_num' => $pivot->dice_num,
                                'dice_side' => $pivot->dice_side,
                                'duration_formula' => $pivot->duration_formula,
                                'logic_group' => $pivot->logic_group,
                                'logic_operator' => $pivot->logic_operator,
                                'logic_condition' => $pivot->logic_condition,
                                'crit_only' => (bool) ($pivot->crit_only ?? false),
                                'params' => $params,
                                'summon_monster' => $this->summonMonsterBrief($params, $monstersById),
                                'spell_state' => $this->spellStateBrief($params, $spellStatesById),
                                'sub_effect' => $sub ? [
                                    'id' => $sub->id,
                                    'slug' => $sub->slug,
                                    'type_slug' => $sub->type_slug,
                                    'template_text' => $sub->template_text,
                                ] : null,
                            ];
                        })->all(),
                    ];
                })->all(),
            ];
        })->values()->all();
    }

    /**
     * @param  Collection<int, Effect>  $effects
     * @return list<int>
     */
    private function collectMonsterIdsFromPivots(Collection $effects): array
    {
        $monsterIds = [];
        foreach ($effects as $effect) {
            foreach ($effect->degrees as $degree) {
                foreach ($degree->effectSubEffects as $pivot) {
                    $params = $pivot->params;
                    if (! is_array($params)) {
                        continue;
                    }
                    $mid = $params['monster_id'] ?? null;
                    if ($mid === null || $mid === '' || ! is_numeric($mid)) {
                        continue;
                    }
                    $monsterIds[(int) $mid] = true;
                }
            }
        }

        return array_keys($monsterIds);
    }

    /**
     * @param  Collection<int, Effect>  $effects
     * @return list<int>
     */
    private function collectSpellStateIdsFromPivots(Collection $effects): array
    {
        $ids = [];
        foreach ($effects as $effect) {
            foreach ($effect->degrees as $degree) {
                foreach ($degree->effectSubEffects as $pivot) {
                    $params = $pivot->params;
                    if (! is_array($params)) {
                        continue;
                    }
                    $sid = $params['spell_state_id'] ?? null;
                    if ($sid === null || $sid === '' || ! is_numeric($sid)) {
                        continue;
                    }
                    $ids[(int) $sid] = true;
                }
            }
        }

        return array_keys($ids);
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, SpellState>
     */
    private function loadSpellStatesById(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return SpellState::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  array<string, mixed>  $params
     * @param  Collection<int, SpellState>  $byId
     * @return array{id: int, name: string, icon: string|null}|null
     */
    private function spellStateBrief(array $params, Collection $byId): ?array
    {
        $sid = $params['spell_state_id'] ?? null;
        if ($sid === null || $sid === '' || ! is_numeric($sid)) {
            return null;
        }
        $id = (int) $sid;
        $st = $byId->get($id);
        if ($st === null) {
            return [
                'id' => $id,
                'name' => 'État #'.$id,
                'icon' => null,
            ];
        }

        return [
            'id' => $st->id,
            'name' => $st->name !== '' ? $st->name : ('État #'.$st->id),
            'icon' => $st->icon,
        ];
    }

    /**
     * @param  list<int>  $monsterIds
     * @return Collection<int, Monster>
     */
    private function loadMonstersById(array $monsterIds): Collection
    {
        if ($monsterIds === []) {
            return collect();
        }

        return Monster::query()
            ->with('creature')
            ->whereIn('id', $monsterIds)
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  array<string, mixed>  $params
     * @param  Collection<int, Monster>  $monstersById
     * @return array{id: int, name: string, image: string|null}|null
     */
    private function summonMonsterBrief(array $params, Collection $monstersById): ?array
    {
        $mid = $params['monster_id'] ?? null;
        if ($mid === null || $mid === '' || ! is_numeric($mid)) {
            return null;
        }
        $id = (int) $mid;
        $monster = $monstersById->get($id);
        if ($monster === null) {
            return [
                'id' => $id,
                'name' => 'Monstre #'.$id,
                'image' => null,
            ];
        }

        return [
            'id' => $monster->id,
            'name' => $monster->creature?->name ?? ('Monstre #'.$monster->id),
            'image' => $monster->creature?->image,
        ];
    }
}
