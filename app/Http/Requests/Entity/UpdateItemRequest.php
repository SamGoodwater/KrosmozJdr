<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\HasCharacteristicValidation;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Item.
 *
 * Les min/max des champs liés aux caractéristiques (level, rarity, etc.)
 * sont dérivés de CharacteristicGetterService (entity item).
 */
class UpdateItemRequest extends FormRequest
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
            'level' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('level', 'item') ?: ['min:0']
            ),
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'bonus' => ['nullable', 'string'],
            'recipe' => ['nullable', 'string'],
            'price' => array_merge(
                ['nullable', 'numeric'],
                $this->characteristicMinMaxRules('price', 'item') ?: ['min:0']
            ),
            'rarity' => array_merge(
                ['nullable', 'integer'],
                $this->characteristicMinMaxRules('rarity', 'item')
            ),
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'item_type_id' => ['nullable', 'integer', 'exists:type_item_types,id'],
        ];
    }
}
