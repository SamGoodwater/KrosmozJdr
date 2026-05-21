<?php

namespace App\Services;

use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Synchronise les sous-pages CMS « Bibliothèques » pour chaque classe et spécialisation.
 *
 * Chaque entité jouable reçoit une page enfant (menu déroulant) pointant vers la fiche
 * via {@code settings.linked_entity}.
 */
class BibliothequeEntityPageService
{
    public const PARENT_SLUG_BREED = 'bibliotheque-breed';

    public const PARENT_SLUG_SPECIALIZATION = 'bibliotheque-specialization';

    /**
     * @return array{breeds: int, specializations: int, removed: int}
     */
    public function syncAll(?int $creatorId = null): array
    {
        $creatorId ??= User::query()->value('id');

        $breeds = $this->syncEntityType(Breed::class, 'breed', self::PARENT_SLUG_BREED, $creatorId);
        $specs = $this->syncEntityType(Specialization::class, 'specialization', self::PARENT_SLUG_SPECIALIZATION, $creatorId);

        PageService::clearMenuCache();

        return [
            'breeds' => $breeds['synced'],
            'specializations' => $specs['synced'],
            'removed' => $breeds['removed'] + $specs['removed'],
        ];
    }

    /**
     * @param  class-string<Breed|Specialization>  $modelClass
     * @return array{synced: int, removed: int}
     */
    private function syncEntityType(string $modelClass, string $entityType, string $parentSlug, ?int $creatorId): array
    {
        $parent = Page::query()->where('slug', $parentSlug)->first();
        if (! $parent) {
            return ['synced' => 0, 'removed' => 0];
        }

        $synced = 0;
        $activeSlugs = [];
        $order = 0;

        $modelClass::query()
            ->orderBy('name')
            ->each(function ($entity) use ($parent, $entityType, $creatorId, &$synced, &$activeSlugs, &$order): void {
                $slug = $this->buildChildSlug($entityType, (string) $entity->name);
                $activeSlugs[] = $slug;

                $pageAttributes = [
                    'title' => (string) $entity->name,
                    'in_menu' => true,
                    'state' => Page::STATE_PLAYABLE,
                    'read_level' => (int) ($entity->read_level ?? User::ROLE_GUEST),
                    'write_level' => (int) ($entity->write_level ?? User::ROLE_ADMIN),
                    'parent_id' => $parent->id,
                    'menu_order' => $order++,
                    'menu_group' => null,
                    'entity_key' => $entityType,
                    'menu_item_css_classes' => 'color-'.$entityType.'-500',
                    'created_by' => $creatorId,
                    'settings' => [
                        'linked_entity' => [
                            'type' => $entityType,
                            'id' => (int) $entity->id,
                        ],
                    ],
                ];

                if ($entityType === 'breed' && $entity instanceof Breed) {
                    $menuIcon = PageService::resolveBreedMenuIconForSync($entity);
                    $pageAttributes['icon'] = $menuIcon;
                }

                Page::query()->updateOrCreate(
                    ['slug' => $slug],
                    $pageAttributes
                );
                $synced++;
            });

        $removed = Page::query()
            ->where('parent_id', $parent->id)
            ->whereNotIn('slug', $activeSlugs)
            ->update(['in_menu' => false]);

        return ['synced' => $synced, 'removed' => $removed];
    }

    public function buildChildSlug(string $entityType, string $name): string
    {
        $prefix = $entityType === 'breed' ? 'classe' : 'specialisation';
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'entite';
        }

        return $prefix.'-'.$base;
    }

    /**
     * @param  array{type?: string, id?: int}  $linked
     */
    public function resolveLinkedEntity(array $linked): Breed|Specialization|null
    {
        $type = (string) ($linked['type'] ?? '');
        $id = (int) ($linked['id'] ?? 0);
        if ($id < 1) {
            return null;
        }

        return match ($type) {
            'breed' => Breed::query()->find($id),
            'specialization' => Specialization::query()->find($id),
            default => null,
        };
    }
}
