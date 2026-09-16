<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\HasCharacteristicValidation;
use App\Models\Entity\Consumable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'un Consumable.
 *
 * Valide les champs principaux d'un consommable.
 * Les min/max des champs liés aux caractéristiques sont dérivés de CharacteristicGetterService.
 */
class StoreConsumableRequest extends FormRequest
{
    use GuardsPlayableState;
    use HasCharacteristicValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Consumable::class) === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'recipe' => ['nullable', 'string'],
            'price_custom' => ['nullable', 'integer'],
            'rarity' => $this->characteristicRules('rarity', 'consumable') ?: ['nullable', 'integer', 'min:0'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
            'consumable_type_id' => ['nullable', 'integer', 'exists:type_consumable_types,id'],
        ];
    }

    protected function playableModelClass(): string
    {
        return Consumable::class;
    }
}
