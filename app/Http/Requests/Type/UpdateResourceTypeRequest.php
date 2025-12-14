<?php

namespace App\Http\Requests\Type;

use App\Models\Type\ResourceType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation mise à jour ResourceType.
 */
class UpdateResourceTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var ResourceType|null $resourceType */
        $resourceType = $this->route('resourceType');
        $id = $resourceType?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'usable' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'string', 'max:255'],
            'dofusdb_type_id' => ['nullable', 'integer', 'min:1', 'unique:resource_types,dofusdb_type_id,' . $id],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked'],
        ];
    }
}


