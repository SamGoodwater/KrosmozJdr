<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'une Specialization.
 *
 * Valide les champs principaux d'une spécialisation.
 */
class UpdateSpecializationRequest extends FormRequest
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
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
