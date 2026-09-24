<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use InvalidArgumentException;

/**
 * Registre des spécialisations (sort, rencontre, PNJ, objet, conso).
 *
 * @example app(SpecializationRegistry::class)->forAction('encounter')
 */
final class SpecializationRegistry
{
    /** @var array<string, Specialization>|null */
    private ?array $byKey = null;

    public function forAction(string $action): Specialization
    {
        $map = $this->all();
        if (! isset($map[$action])) {
            throw new InvalidArgumentException("Spécialisation IA inconnue : {$action}");
        }

        return $map[$action];
    }

    public function forEntityType(string $entityType): Specialization
    {
        $key = match ($entityType) {
            'monster', 'monsters' => 'encounter',
            'spell', 'spells' => 'spell',
            'npc', 'npcs' => 'npc',
            'item', 'items' => 'item',
            'consumable', 'consumables' => 'consumable',
            default => $entityType,
        };

        return $this->forAction($key);
    }

    /**
     * Null si le type n’a pas de spécialisation LLM (injection générique fillable).
     */
    public function tryForEntityType(string $entityType): ?Specialization
    {
        try {
            return $this->forEntityType($entityType);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @return array<string, Specialization>
     */
    public function all(): array
    {
        if ($this->byKey !== null) {
            return $this->byKey;
        }

        $this->byKey = [
            'spell' => app(SpellSpecialization::class),
            'encounter' => app(MonsterSpecialization::class),
            'npc' => app(NpcSpecialization::class),
            'item' => app(ItemSpecialization::class),
            'consumable' => app(ConsumableSpecialization::class),
        ];

        return $this->byKey;
    }
}
