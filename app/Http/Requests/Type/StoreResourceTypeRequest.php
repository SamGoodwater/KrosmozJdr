<?php

namespace App\Http\Requests\Type;

use App\Enums\EntityState;
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
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'dofusdb_type_id' => ['nullable', 'integer', 'min:1', 'unique:resource_types,dofusdb_type_id'],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked'],
            'show_in_catalog' => ['nullable', 'boolean'],
            'seen_count' => ['nullable', 'integer', 'min:0'],
            'last_seen_at' => ['nullable', 'date'],
        ];
    }
}
