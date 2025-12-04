<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'une Classe.
 *
 * Valide les champs principaux d'une classe.
 */
class UpdateClasseRequest extends FormRequest
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
            'description_fast' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'life' => ['nullable', 'string', 'max:255'],
            'life_dice' => ['nullable', 'string', 'max:255'],
            'specificity' => ['nullable', 'string'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'usable' => ['nullable', 'integer', 'in:0,1'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
