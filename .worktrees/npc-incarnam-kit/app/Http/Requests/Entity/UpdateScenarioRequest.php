<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Scenario.
 *
 * Valide les champs principaux d'un scénario.
 */
class UpdateScenarioRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'unique:scenarios,slug,'.$this->route('scenario')],
            'keyword' => ['nullable', 'string', 'max:255'],
            'is_public' => ['sometimes', 'required', 'boolean'],
            'progress_state' => ['sometimes', 'required', 'integer', 'in:0,1,2,3'],
            'state' => ['sometimes', 'nullable', 'string', EntityState::rule()],
            'read_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
