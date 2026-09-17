<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Entity\Concerns\ValidatesLeveledCreatureTraitSync;
use App\Models\Entity\Breed;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBreedCreatureTraitsRequest extends FormRequest
{
    use ValidatesLeveledCreatureTraitSync;

    public function authorize(): bool
    {
        $breed = $this->route('breed');

        return $breed instanceof Breed && $this->user()?->can('update', $breed) === true;
    }
}
