<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Models\Entity\Scenario;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Scenario.
 *
 * Valide les champs principaux d'un scénario.
 */
class UpdateScenarioRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $model = $this->route('scenario');

        return $model instanceof Scenario && ($this->user()?->can('update', $model) === true);
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

    protected function playableModelClass(): string
    {
        return Scenario::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
