<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Entity\Concerns\NormalizesCapabilityStringDefaults;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'une Capability.
 *
 * Valide les champs principaux d'une capacité.
 */
class StoreCapabilityRequest extends FormRequest
{
    use NormalizesCapabilityStringDefaults;

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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'effect' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'pa' => ['nullable', 'string', 'max:255'],
            'po' => ['nullable', 'string', 'max:255'],
            'po_editable' => ['nullable', 'boolean'],
            'time_before_use_again' => ['nullable', 'string', 'max:255'],
            'casting_time' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:255'],
            'element' => ['nullable', 'integer', 'min:0', 'max:127'],
            'is_magic' => ['nullable', 'boolean'],
            'ritual_available' => ['nullable', 'boolean'],
            'is_passive' => ['nullable', 'boolean'],
            'powerful' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeCapabilityNotNullDefaultsForDatabase();
    }
}
