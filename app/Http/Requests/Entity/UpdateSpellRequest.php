<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\HasCharacteristicValidation;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Spell.
 *
 * Les min/max des champs liés aux caractéristiques (area, element, powerful, etc.)
 * sont dérivés de CharacteristicGetterService (entity spell).
 */
class UpdateSpellRequest extends FormRequest
{
    use HasCharacteristicValidation;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'area' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('area', 'spell') ?: ['min:0']
            ),
            'level' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'po_editable' => ['nullable', 'boolean'],
            'pa' => ['nullable', 'string', 'max:255'],
            'cast_per_turn' => ['nullable', 'string', 'max:255'],
            'cast_per_target' => ['nullable', 'string', 'max:255'],
            'sight_line' => ['nullable', 'boolean'],
            'number_between_two_cast' => ['nullable', 'string', 'max:255'],
            'number_between_two_cast_editable' => ['nullable', 'boolean'],
            'element' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('element', 'spell') ?: ['min:0', 'max:19']
            ),
            'category' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('category', 'spell')
            ),
            'is_magic' => ['nullable', 'boolean'],
            'powerful' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('powerful', 'spell')
            ),
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
        ];
    }
}
