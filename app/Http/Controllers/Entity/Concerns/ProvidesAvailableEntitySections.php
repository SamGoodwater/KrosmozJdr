<?php

namespace App\Http\Controllers\Entity\Concerns;

use App\Services\Entity\EntityLeveledSectionsService;

/**
 * Payload Inertia {@code availableSections} pour les formulaires d’édition d’entités.
 */
trait ProvidesAvailableEntitySections
{
    /**
     * @return list<array<string, mixed>>
     */
    protected function availableSectionsPayload(): array
    {
        return app(EntityLeveledSectionsService::class)->availableSectionsForSelect(request());
    }
}
