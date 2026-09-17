<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Breed;
use Illuminate\Database\Eloquent\Model;

class UpdateBreedSectionsRequest extends UpdateEntityLeveledSectionsRequest
{
    protected function resolveAuthorizableEntity(): ?Model
    {
        $breed = $this->route('breed');

        return $breed instanceof Breed ? $breed : null;
    }
}
