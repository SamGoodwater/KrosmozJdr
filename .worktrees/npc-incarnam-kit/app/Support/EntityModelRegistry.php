<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Entity\Breed;
use App\Models\Entity\Campaign;
use App\Models\Entity\Capability;
use App\Models\Entity\Condition;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Shop;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Registre backend des entités JDR manipulables par les API génériques.
 *
 * @example EntityModelRegistry::resolveModel('spells', 12, withTrashed: true)
 */
final class EntityModelRegistry
{
    /** @return array<string, class-string<Model>> */
    public static function modelMap(): array
    {
        return [
            'breeds' => Breed::class,
            'campaigns' => Campaign::class,
            'capabilities' => Capability::class,
            'conditions' => Condition::class,
            'consumables' => Consumable::class,
            'creatures' => Creature::class,
            'creature-traits' => CreatureTrait::class,
            'items' => Item::class,
            'monsters' => Monster::class,
            'npcs' => Npc::class,
            'panoplies' => Panoply::class,
            'resources' => Resource::class,
            'scenarios' => Scenario::class,
            'shops' => Shop::class,
            'specializations' => Specialization::class,
            'spells' => Spell::class,
        ];
    }

    public static function resolveModel(string $entityType, int $id, bool $withTrashed = false): ?Model
    {
        $class = self::modelMap()[self::normalizeType($entityType)] ?? null;
        if ($class === null) {
            return null;
        }

        $query = $class::query();
        if ($withTrashed && in_array(SoftDeletes::class, class_uses_recursive($class), true)) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    public static function normalizeType(string $entityType): string
    {
        return match (trim($entityType)) {
            'breed', 'class', 'classes' => 'breeds',
            'campaign' => 'campaigns',
            'capability' => 'capabilities',
            'condition' => 'conditions',
            'consumable' => 'consumables',
            'creature' => 'creatures',
            'creature-trait' => 'creature-traits',
            'item' => 'items',
            'monster' => 'monsters',
            'npc' => 'npcs',
            'panoply' => 'panoplies',
            'resource' => 'resources',
            'scenario' => 'scenarios',
            'shop' => 'shops',
            'specialization' => 'specializations',
            'spell' => 'spells',
            default => trim($entityType),
        };
    }
}
