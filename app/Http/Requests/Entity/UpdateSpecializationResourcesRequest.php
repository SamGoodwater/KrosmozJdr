<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Entity\Concerns\ValidatesLeveledQuantifiedRelationSync;
use App\Models\Entity\Specialization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializationResourcesRequest extends FormRequest
{
    use ValidatesLeveledQuantifiedRelationSync;

    public function authorize(): bool
    {
        $specialization = $this->route('specialization');

        return $specialization instanceof Specialization
            && $this->user()?->can('update', $specialization) === true;
    }

    protected function relationInputKey(): string
    {
        return 'resources';
    }

    protected function relationEntityTable(): string
    {
        return 'resources';
    }
}
