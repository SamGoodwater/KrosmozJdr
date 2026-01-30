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
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'dofusdb_type_id' => ['nullable', 'integer', 'min:1', 'unique:resource_types,dofusdb_type_id,' . $id],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked'],
        ];
    }
}


