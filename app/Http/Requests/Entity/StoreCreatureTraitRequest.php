<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Models\Entity\CreatureTrait;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCreatureTraitRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', CreatureTrait::class) === true;
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
            'description' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function playableModelClass(): string
    {
        return CreatureTrait::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
