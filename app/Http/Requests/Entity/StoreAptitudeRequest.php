<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Http\Requests\Entity\Concerns\ValidatesAptitudeFields;
use App\Models\Entity\Aptitude;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAptitudeRequest extends FormRequest
{
    use GuardsPlayableState;
    use RestrictsAccessLevelMutation;
    use ValidatesAptitudeFields;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Aptitude::class) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge($this->aptitudeFieldRules(true), [
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
