<?php

namespace App\Http\Requests\Type;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation création ResourceType.
 */
class StoreResourceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'usable' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'dofusdb_type_id' => ['nullable', 'integer', 'min:1', 'unique:resource_types,dofusdb_type_id'],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked'],
            'seen_count' => ['nullable', 'integer', 'min:0'],
            'last_seen_at' => ['nullable', 'date'],
        ];
    }
}


