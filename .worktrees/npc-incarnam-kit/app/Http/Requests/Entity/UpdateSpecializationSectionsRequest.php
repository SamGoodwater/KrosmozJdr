<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\Specialization;
use Illuminate\Database\Eloquent\Model;

class UpdateSpecializationSectionsRequest extends UpdateEntityLeveledSectionsRequest
{
    protected function resolveAuthorizableEntity(): ?Model
    {
        $specialization = $this->route('specialization');

        return $specialization instanceof Specialization ? $specialization : null;
    }
}
