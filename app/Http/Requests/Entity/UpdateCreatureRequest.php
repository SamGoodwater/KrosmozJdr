<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'une Creature.
 *
 * Valide les champs principaux d'une créature.
 */
class UpdateCreatureRequest extends FormRequest
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
            'hostility' => ['nullable', 'integer', 'min:0', 'max:4'],
            'location' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'other_info' => ['nullable', 'string'],
            'life' => ['nullable', 'string', 'max:255'],
            'pa' => ['nullable', 'string', 'max:255'],
            'pm' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'ini' => ['nullable', 'string', 'max:255'],
            'invocation' => ['nullable', 'string', 'max:255'],
            'touch' => ['nullable', 'string', 'max:255'],
            'ca' => ['nullable', 'string', 'max:255'],
            'dodge_pa' => ['nullable', 'string', 'max:255'],
            'dodge_pm' => ['nullable', 'string', 'max:255'],
            'fuite' => ['nullable', 'string', 'max:255'],
            'tacle' => ['nullable', 'string', 'max:255'],
            'vitality' => ['nullable', 'string', 'max:255'],
            'sagesse' => ['nullable', 'string', 'max:255'],
            'strong' => ['nullable', 'string', 'max:255'],
            'intel' => ['nullable', 'string', 'max:255'],
            'agi' => ['nullable', 'string', 'max:255'],
            'chance' => ['nullable', 'string', 'max:255'],
            'kamas' => ['nullable', 'string', 'max:255'],
        ];
    }
}
