<?php

namespace App\Services\Entity;

use App\Http\Resources\SectionResource;
use App\Models\Concerns\HasLeveledSections;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Synchronisation et chargement des sections liées (pivot {@code level}).
 */
class EntityLeveledSectionsService
{
    /**
     * @param  array<int, array{level: int}>  $payload
     */
    public function sync(Model $entity, array $payload): void
    {
        if (! $this->supportsLeveledSections($entity)) {
            throw new \InvalidArgumentException(
                'L’entité '.get_class($entity).' ne supporte pas les sections avec pivot level.',
            );
        }

        /** @var Model&HasLeveledSections $entity */
        $entity->sections()->sync($payload);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function availableSectionsForSelect(?Request $request = null, int $limit = 5000): array
    {
        $request ??= request();

        return SectionResource::collection(
            Section::query()->orderBy('title')->limit($limit)->get(),
        )->toArray($request);
    }

    public function supportsLeveledSections(Model $entity): bool
    {
        return in_array(HasLeveledSections::class, class_uses_recursive($entity), true);
    }
}
