<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Entity\Concerns\ValidatesLeveledRelationSync;
use App\Models\Entity\Specialization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializationCapabilitiesRequest extends FormRequest
{
    use ValidatesLeveledRelationSync;

    public function authorize(): bool
    {
        $specialization = $this->route('specialization');

        return $specialization instanceof Specialization
            && $this->user()?->can('update', $specialization) === true;
    }

    protected function relationInputKey(): string
    {
        return 'capabilities';
    }

    protected function relationEntityTable(): string
    {
        return 'capabilities';
    }
}
