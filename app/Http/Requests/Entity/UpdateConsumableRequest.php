<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Consumable.
 *
 * Valide les champs principaux d'un consommable.
 */
class UpdateConsumableRequest extends FormRequest
{
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
            'level' => ['nullable', 'string', 'max:255'],
            'recipe' => ['nullable', 'string'],
            'price' => ['nullable', 'string', 'max:255'],
            'rarity' => ['nullable', 'integer', 'min:0'],
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
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
}
