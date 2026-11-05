<?php

declare(strict_types=1);

namespace App\Http\Requests\Spell;

use App\Http\Requests\Spell\Concerns\AuthorizesSpellDegreeMutation;
use App\Http\Requests\Spell\Concerns\SpellDegreeEffectParamsRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Synchronisation des effets d’un degré.
 */
class SyncSpellDegreeEffectsRequest extends FormRequest
{
    use AuthorizesSpellDegreeMutation;
    use SpellDegreeEffectParamsRules;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'effects' => 'present|array',
            'effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'effects.*.order' => 'integer|min:0',
            'effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'effects.*.value_min' => 'nullable|integer',
            'effects.*.value_max' => 'nullable|integer',
            'effects.*.dice_num' => 'nullable|integer|min:0',
            'effects.*.dice_side' => 'nullable|integer|min:0',
            'effects.*.duration_formula' => 'nullable|string|max:255',
            'effects.*.logic_group' => 'nullable|string|max:64',
            'effects.*.logic_operator' => 'nullable|string|in:AND,OR',
            'effects.*.logic_condition' => 'nullable|string|max:255',
            'effects.*.crit_only' => 'nullable|boolean',
        ], $this->spellDegreeEffectParamsRules('effects.*.params'));
    }
}
