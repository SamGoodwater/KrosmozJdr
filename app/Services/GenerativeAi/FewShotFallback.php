<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;

/**
 * Replis few-shot par official_id / nom quand la config n’a pas d’example_ids.
 *
 * @example $ids = app(FewShotFallback::class)->idsFor('monster');
 */
final class FewShotFallback
{
    /**
     * @return list<int>
     */
    public function idsFor(string $entity): array
    {
        $limit = (int) (GenerationConfigLoader::default()->get('generation.few_shot_count', 8) ?: 8);
        $limit = max(1, min(30, $limit));

        return match ($entity) {
            'npc' => app(NpcKitCatalog::class)->exampleIds(),
            'monster' => $this->idsByOfficialPrefix(Monster::class, 'jdr:bestiary:', $limit)
                ?: $this->idsByOfficialPrefix(Monster::class, 'jdr:summon:', $limit),
            'spell' => $this->named(Spell::class, ['Pression', 'Attaque Naturelle', 'Intimidation', 'Fendoir', 'Bond', 'Concentration', 'Puissance', 'Épée Divine'], $limit),
            'item' => $this->named(Item::class, [
                'Cape du Piou Vert',
                'Cape du Piou Rouge',
                'Cape du Piou Bleu',
                'Cape du Piou Jaune',
                'Anneau du Piou Vert',
                'Anneau du Tofu',
                'Anneau du Sanglier',
                'Anneau du Mulou',
            ], $limit),
            'consumable' => $this->consumableIds($limit),
            default => [],
        };
    }

    /**
     * @param  class-string  $modelClass
     * @return list<int>
     */
    private function idsByOfficialPrefix(string $modelClass, string $prefix, int $limit): array
    {
        return $modelClass::query()
            ->where('state', EntityState::Playable->value)
            ->where('official_id', 'like', $prefix.'%')
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param  class-string  $modelClass
     * @param  list<string>  $names
     * @return list<int>
     */
    private function named(string $modelClass, array $names, int $limit): array
    {
        $ids = [];
        foreach ($names as $name) {
            $id = $modelClass::query()
                ->where('state', EntityState::Playable->value)
                ->where('name', $name)
                ->value('id');
            if ($id !== null) {
                $ids[] = (int) $id;
            }
            if (count($ids) >= $limit) {
                break;
            }
        }

        return $ids;
    }

    /**
     * @return list<int>
     */
    private function consumableIds(int $limit): array
    {
        $named = $this->named(Consumable::class, ['Pain d\'Incarnam'], $limit);
        $official = Consumable::query()
            ->where('state', EntityState::Playable->value)
            ->where(static function ($query): void {
                $query->where('official_id', 'like', 'jdr:heal:%')
                    ->orWhere('official_id', 'like', 'jdr:antidote%');
            })
            ->orderBy('id')
            ->limit($limit)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();

        return array_values(array_unique([...$named, ...$official]));
    }
}
