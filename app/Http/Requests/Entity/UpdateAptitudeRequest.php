<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Http\Requests\Entity\Concerns\ValidatesAptitudeFields;
use App\Models\Entity\Aptitude;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAptitudeRequest extends FormRequest
{
    use GuardsPlayableState;
    use RestrictsAccessLevelMutation;
    use ValidatesAptitudeFields;

    public function authorize(): bool
    {
        $model = $this->route('aptitude');

        return $model instanceof Aptitude && ($this->user()?->can('update', $model) === true);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->aptitudeFieldRules(false), [
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
        ]);
    }

    protected function playableModelClass(): string
    {
        return Aptitude::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
