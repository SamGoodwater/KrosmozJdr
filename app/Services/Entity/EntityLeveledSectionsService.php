<?php

namespace App\Services\Entity;

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
     * Options de sélection pour l’édition : id + title + slug uniquement
     * (pas de {@see \App\Http\Resources\SectionResource} / data / settings / can[]).
     *
     * @return list<array{id: int, title: ?string, slug: ?string}>
     */
    public function availableSectionsForSelect(?Request $request = null, int $limit = 500): array
    {
        unset($request);

        return Section::query()
            ->orderBy('title')
            ->limit($limit)
            ->get(['id', 'title', 'slug'])
            ->map(static fn (Section $section): array => [
                'id' => (int) $section->id,
                'title' => $section->title,
                'slug' => $section->slug,
            ])
            ->values()
            ->all();
    }

    public function supportsLeveledSections(Model $entity): bool
    {
        return in_array(HasLeveledSections::class, class_uses_recursive($entity), true);
    }
}
