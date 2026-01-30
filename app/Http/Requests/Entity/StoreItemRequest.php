<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'un Item.
 *
 * Valide les champs principaux d'un objet/équipement.
 */
class StoreItemRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'bonus' => ['nullable', 'string'],
            'recipe' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'rarity' => ['nullable', 'integer', 'min:0'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'item_type_id' => ['nullable', 'integer', 'exists:type_item_types,id'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
