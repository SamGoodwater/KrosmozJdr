<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Entity\Concerns\ValidatesLeveledRelationSync;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Synchronisation des sections liées à une entité (pivot {@code level}).
 */
abstract class UpdateEntityLeveledSectionsRequest extends FormRequest
{
    use ValidatesLeveledRelationSync;

    public function authorize(): bool
    {
        $entity = $this->resolveAuthorizableEntity();

        return $entity instanceof Model
            && $this->user()?->can('update', $entity) === true;
    }

    abstract protected function resolveAuthorizableEntity(): ?Model;

    protected function relationInputKey(): string
    {
        return 'sections';
    }

    protected function relationEntityTable(): string
    {
        return 'sections';
    }
}
