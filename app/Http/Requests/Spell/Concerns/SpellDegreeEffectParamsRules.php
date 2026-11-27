<?php

declare(strict_types=1);

namespace App\Http\Requests\Spell\Concerns;

use App\Rules\EffectConditionSideFormula;
use App\Support\Spell\EffectConditionParams;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Règles partagées pour {@code params} des effets de degré de sort.
 *
 * Laravel omet les clés d’un tableau sans règle dédiée ({@code validated()}).
 * {@see SpellDegreeService::syncEffects} recrée les lignes : une clé absente
 * est une perte (texte « autre » en {@code value}, {@code dice_formula}, …).
 */
trait SpellDegreeEffectParamsRules
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function spellDegreeEffectParamsRules(string $prefix): array
    {
        $p = $prefix;

        return [
            "{$p}" => 'nullable|array',
            // Catch-all : round-trip du JSON params (value, dice_formula, dofus_effect_id…).
            "{$p}.*" => 'nullable',
            "{$p}.characteristic" => 'nullable|string|max:64',
            "{$p}.value" => 'nullable',
            "{$p}.value_formula" => 'nullable|string|max:500',
            "{$p}.value_formula_crit" => 'nullable|string|max:500',
            "{$p}.dice_formula" => 'nullable|string|max:500',
            "{$p}.life_steal_formula" => 'nullable|string|max:500',
            "{$p}.cells_formula" => 'nullable|string|max:500',
            "{$p}.duration_formula" => 'nullable|string|max:255',
            "{$p}.movement_kind" => 'nullable|string|in:movement,jump,teleport,push,pull',
            "{$p}.effect_direction" => 'nullable|string|in:bonus,malus,steal,action',
            "{$p}.element" => 'nullable|integer|min:0|max:6',
            "{$p}.dofus_element_id" => 'nullable|integer|min:0|max:6',
            "{$p}.dofus_effect_id" => 'nullable|integer|min:0',
            "{$p}.value_converted" => 'nullable',
            "{$p}.life_steal_value_converted" => 'nullable',
            "{$p}.creature_id" => 'nullable|integer|exists:creatures,id',
            "{$p}.monster_id" => 'nullable|integer|exists:monsters,id',
            "{$p}.condition_id" => 'nullable|integer|exists:conditions,id',
            "{$p}.condition_dofusdb_id" => 'nullable|integer|min:0',
            "{$p}.condition_name" => 'nullable|string|max:255',
            "{$p}.teleport" => 'nullable|boolean',
            "{$p}.dispellable" => 'nullable|boolean',
            "{$p}.applies_to" => 'nullable|string|in:'.implode(',', EffectConditionParams::APPLIES_TO),
            "{$p}.value_up_to" => 'nullable|boolean',
            "{$p}.trigger_turn" => 'nullable|integer|min:0|max:'.EffectConditionParams::TRIGGER_TURN_MAX,
            "{$p}.conditions" => 'nullable|array|max:'.EffectConditionParams::MAX_CONDITIONS,
            "{$p}.conditions.*.left" => ['nullable', 'string', 'max:'.EffectConditionParams::SIDE_MAX_LENGTH, new EffectConditionSideFormula],
            "{$p}.conditions.*.op" => 'nullable|string|in:'.implode(',', EffectConditionParams::OPERATORS),
            "{$p}.conditions.*.right" => ['nullable', 'string', 'max:'.EffectConditionParams::SIDE_MAX_LENGTH, new EffectConditionSideFormula],
            "{$p}.conditions.*.join" => 'nullable|string|in:'.implode(',', EffectConditionParams::JOINS),
            "{$p}.value_rules" => 'nullable|array|max:'.EffectConditionParams::MAX_VALUE_RULES,
            "{$p}.value_rules.*.action" => 'nullable|string|in:'.implode(',', EffectConditionParams::ACTIONS),
            "{$p}.value_rules.*.formula" => ['nullable', 'string', 'max:'.EffectConditionParams::SIDE_MAX_LENGTH, new EffectConditionSideFormula],
            "{$p}.value_rules.*.conditions" => 'nullable|array|max:'.EffectConditionParams::MAX_CONDITIONS,
            "{$p}.value_rules.*.conditions.*.left" => ['nullable', 'string', 'max:'.EffectConditionParams::SIDE_MAX_LENGTH, new EffectConditionSideFormula],
            "{$p}.value_rules.*.conditions.*.op" => 'nullable|string|in:'.implode(',', EffectConditionParams::OPERATORS),
            "{$p}.value_rules.*.conditions.*.right" => ['nullable', 'string', 'max:'.EffectConditionParams::SIDE_MAX_LENGTH, new EffectConditionSideFormula],
            "{$p}.value_rules.*.conditions.*.join" => 'nullable|string|in:'.implode(',', EffectConditionParams::JOINS),
        ];
    }
}
