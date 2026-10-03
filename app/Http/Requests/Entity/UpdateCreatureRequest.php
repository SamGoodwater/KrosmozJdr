<?php

declare(strict_types=1);

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\HasCharacteristicValidation;
use App\Http\Requests\Concerns\ValidatesCreatureComposableCharacteristics;
use App\Http\Requests\Concerns\ValidatesCreatureSkillMasteries;
use App\Models\Entity\Creature;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * FormRequest pour la mise à jour d'une Creature.
 *
 * Valide identité, niveau (domaines autorisés) et caractéristiques composables (total + contexte).
 */
class UpdateCreatureRequest extends FormRequest
{
    use HasCharacteristicValidation;
    use ValidatesCreatureComposableCharacteristics;
    use ValidatesCreatureSkillMasteries;

    public function authorize(): bool
    {
        $model = $this->route('creature');

        return $model instanceof Creature && ($this->user()?->can('update', $model) === true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hostility' => ['nullable', 'integer', 'min:0', 'max:4'],
            'location' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string'],
            'other_info' => ['nullable', 'string'],
            'kamas' => ['nullable', 'string', 'max:255'],
            'critical_hit' => ['nullable', 'integer', 'min:0', 'max:3'],
            'heal_bonus' => ['nullable', 'integer', 'min:0', 'max:7'],
            'save_vitality_bonus' => ['nullable', 'integer', 'min:0', 'max:3'],
            'save_wisdom_bonus' => ['nullable', 'integer', 'min:0', 'max:3'],
            'save_strength_bonus' => ['nullable', 'integer', 'min:0', 'max:3'],
            'save_intelligence_bonus' => ['nullable', 'integer', 'min:0', 'max:3'],
            'save_chance_bonus' => ['nullable', 'integer', 'min:0', 'max:3'],
            'save_agility_bonus' => ['nullable', 'integer', 'min:0', 'max:3'],
            'save_vitality_mastery' => ['nullable', 'integer', 'min:0', 'max:1'],
            'save_wisdom_mastery' => ['nullable', 'integer', 'min:0', 'max:1'],
            'save_strength_mastery' => ['nullable', 'integer', 'min:0', 'max:1'],
            'save_intelligence_mastery' => ['nullable', 'integer', 'min:0', 'max:1'],
            'save_chance_mastery' => ['nullable', 'integer', 'min:0', 'max:1'],
            'save_agility_mastery' => ['nullable', 'integer', 'min:0', 'max:1'],
        ], $this->creatureComposableFieldRules(), $this->creatureSkillMasteryFieldRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateCreatureLevelField($validator);
            $this->validateCreatureComposableFields($validator);
            $this->validateCreatureSkillMasteries($validator);
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'level.*' => 'Le niveau est invalide (nombre, formule ou domaine autorisé).',
        ];
    }
}
