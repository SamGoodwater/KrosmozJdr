<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Models\EffectUsage;
use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Model;

/**
 * Exige `view` sur la fiche parente d’un endpoint public d’effets.
 *
 * @example $this->authorizeEffectParentView('spell', 12);
 */
trait AuthorizesEffectParentView
{
    /**
     * Résout la fiche (sort / objet / conso / ressource) et refuse un brouillon invisible.
     *
     * @param  string  $entityType  spell|item|consumable|resource
     */
    protected function authorizeEffectParentView(string $entityType, int $entityId): Model
    {
        if ($entityType === 'spell') {
            $parent = Spell::query()->findOrFail($entityId);
            $this->authorize('view', $parent);

            return $parent;
        }

        $class = EffectUsage::entityTypeToClass($entityType);
        if ($class === null) {
            abort(422, 'Invalid entity_type');
        }

        $parent = $class::query()->findOrFail($entityId);
        $this->authorize('view', $parent);

        return $parent;
    }
}
