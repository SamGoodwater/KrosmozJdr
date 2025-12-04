<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'une Resource.
 *
 * Valide les champs principaux d'une ressource.
 */
class StoreResourceRequest extends FormRequest
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
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'string', 'max:255'],
            'rarity' => ['nullable', 'integer', 'min:0', 'max:5'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'usable' => ['nullable', 'integer', 'in:0,1'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
            'official_id' => ['nullable', 'integer'],
            'resource_type_id' => ['nullable', 'integer', 'exists:type_resource_types,id'],
        ];
    }
}
