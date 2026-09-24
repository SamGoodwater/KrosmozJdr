<?php

namespace App\Http\Requests\Type;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Models\Type\ResourceType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation mise à jour ResourceType.
 */
class UpdateResourceTypeRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;

    public function authorize(): bool
    {
        $model = $this->route('resourceType');

        return $model instanceof ResourceType && ($this->user()?->can('update', $model) === true);
    }

    public function rules(): array
    {
        /** @var ResourceType|null $resourceType */
        $resourceType = $this->route('resourceType');
        $id = $resourceType?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'dofusdb_type_id' => ['nullable', 'integer', 'min:1', 'unique:resource_types,dofusdb_type_id,'.$id],
            'decision' => ['nullable', 'string', 'in:pending,allowed,blocked'],
            'show_in_catalog' => ['nullable', 'boolean'],
        ];
    }

    protected function playableModelClass(): string
    {
        return ResourceType::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
