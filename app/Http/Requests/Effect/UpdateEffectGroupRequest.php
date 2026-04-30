<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Models\EffectDegree;
use App\Rules\ValidAreaNotation;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateEffectGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->verifyRole('game_master') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'common' => 'required|array',
            'common.name' => 'nullable|string|max:255',
            'common.description' => 'nullable|string|max:65535',
            'common.target_type' => 'nullable|string|in:direct,trap,glyph',

            'degrees' => 'required|array|min:1',
            'degrees.*.id' => 'required|integer|exists:effect_degrees,id',
            'degrees.*.slug' => 'nullable|string|max:64',
            'degrees.*.area' => ['nullable', 'string', 'max:64', new ValidAreaNotation],
            'degrees.*.required_creature_level' => 'nullable|integer|min:0',
            'degrees.*.effect_sub_effects' => 'present|array',
            'degrees.*.effect_sub_effects.*.sub_effect_id' => 'required|integer|exists:sub_effects,id',
            'degrees.*.effect_sub_effects.*.order' => 'integer|min:0',
            'degrees.*.effect_sub_effects.*.scope' => 'string|in:general,combat,out_of_combat',
            'degrees.*.effect_sub_effects.*.value_min' => 'nullable|integer',
            'degrees.*.effect_sub_effects.*.value_max' => 'nullable|integer',
            'degrees.*.effect_sub_effects.*.dice_num' => 'nullable|integer|min:0',
            'degrees.*.effect_sub_effects.*.dice_side' => 'nullable|integer|min:0',
            'degrees.*.effect_sub_effects.*.duration_formula' => 'nullable|string|max:255',
            'degrees.*.effect_sub_effects.*.logic_group' => 'nullable|string|max:64',
            'degrees.*.effect_sub_effects.*.logic_operator' => 'nullable|string|in:AND,OR',
            'degrees.*.effect_sub_effects.*.logic_condition' => 'nullable|string|max:255',
            'degrees.*.effect_sub_effects.*.params' => 'nullable|array',
            'degrees.*.effect_sub_effects.*.params.characteristic' => 'nullable|string|max:64',
            'degrees.*.effect_sub_effects.*.params.value_formula' => 'nullable|string|max:500',
            'degrees.*.effect_sub_effects.*.params.value_formula_crit' => 'nullable|string|max:500',
            'degrees.*.effect_sub_effects.*.params.life_steal_formula' => 'nullable|string|max:500',
            'degrees.*.effect_sub_effects.*.params.cells_formula' => 'nullable|string|max:500',
            'degrees.*.effect_sub_effects.*.params.movement_kind' => 'nullable|string|in:movement,jump,teleport,push,pull',
            'degrees.*.effect_sub_effects.*.params.effect_direction' => 'nullable|string|in:bonus,malus,steal,action',
            'degrees.*.effect_sub_effects.*.params.element' => 'nullable|integer|min:0|max:6',
            'degrees.*.effect_sub_effects.*.params.dofus_element_id' => 'nullable|integer|min:0|max:6',
            'degrees.*.effect_sub_effects.*.params.value_converted' => 'nullable',
            'degrees.*.effect_sub_effects.*.params.life_steal_value_converted' => 'nullable',
            'degrees.*.effect_sub_effects.*.params.dice_formula' => 'nullable|string|max:64',
            'degrees.*.effect_sub_effects.*.params.monster_id' => 'nullable|integer|exists:monsters,id',
            'degrees.*.effect_sub_effects.*.params.spell_state_id' => 'nullable|integer|exists:spell_states,id',
            'degrees.*.effect_sub_effects.*.params.state_dofusdb_id' => 'nullable|integer|min:0',
            'degrees.*.effect_sub_effects.*.params.state_name' => 'nullable|string|max:255',
            'degrees.*.effect_sub_effects.*.params.teleport' => 'nullable|boolean',
            'degrees.*.effect_sub_effects.*.params.dispellable' => 'nullable|boolean',
            'degrees.*.effect_sub_effects.*.crit_only' => 'nullable|boolean',
        ];
    }

    protected function passedValidation(): void
    {
        $common = $this->input('common', []);
        if (! empty($common['description'])) {
            $common['description'] = (new EffectTextSanitizer)->sanitize((string) $common['description']);
            $this->merge(['common' => $common]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $degrees = $this->input('degrees', []);
            foreach ($degrees as $i => $row) {
                $id = $row['id'] ?? null;
                $slug = $row['slug'] ?? null;
                if ($slug === null || $slug === '') {
                    continue;
                }
                $q = EffectDegree::query()->where('slug', $slug);
                if ($id) {
                    $q->where('id', '!=', $id);
                }
                if ($q->exists()) {
                    $v->errors()->add('degrees.'.$i.'.slug', 'Ce slug est déjà utilisé.');
                }
            }
        });
    }
}
