<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Entity\Concerns\ValidatesLeveledCreatureTraitSync;
use App\Models\Entity\Specialization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecializationCreatureTraitsRequest extends FormRequest
{
    use ValidatesLeveledCreatureTraitSync;

    public function authorize(): bool
    {
        $specialization = $this->route('specialization');

        return $specialization instanceof Specialization && $this->user()?->can('update', $specialization) === true;
    }
}
