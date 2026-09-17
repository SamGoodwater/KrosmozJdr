<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Capability;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCapabilityConditionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $capability = $this->route('capability');

        return $capability instanceof Capability && $this->user()?->can('update', $capability) === true;
    }

    public function rules(): array
    {
        return [
            'conditions' => ['nullable', 'array'],
            'conditions.*' => ['integer', 'exists:conditions,id'],
        ];
    }

    /** @return list<int> */
    public function validatedConditionIds(): array
    {
        $raw = $this->validated()['conditions'] ?? $this->input('conditions', []);

        return array_values(array_unique(array_map(static fn ($id) => (int) $id, is_array($raw) ? $raw : [])));
    }
}
