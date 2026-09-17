<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Breed;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Synchronisation des capacités liées à une classe (pivot breed_capability).
 */
class UpdateBreedCapabilitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        $breed = $this->route('breed');

        return $breed instanceof Breed && $this->user()?->can('update', $breed) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'capabilities' => ['nullable', 'array'],
            'capabilities.*' => ['integer', 'exists:capabilities,id'],
        ];
    }

    /**
     * @return list<int>
     */
    public function validatedCapabilityIds(): array
    {
        $raw = $this->validated()['capabilities'] ?? $this->input('capabilities', []);

        return array_values(array_unique(array_map(static fn ($id) => (int) $id, is_array($raw) ? $raw : [])));
    }
}
