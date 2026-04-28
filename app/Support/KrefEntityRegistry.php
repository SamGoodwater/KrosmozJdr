<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Entity\Campaign;
use App\Models\Entity\Capability;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Spell;
use Illuminate\Database\Eloquent\Model;

/**
 * Registre central des types d'entités kref côté backend.
 */
class KrefEntityRegistry
{
    /**
     * @return array<int, string>
     */
    public static function allowedTypes(): array
    {
        return [
            'campaigns',
            'scenarios',
            'spells',
            'items',
            'resources',
            'consumables',
            'monsters',
            'npcs',
            'panoplies',
            'capabilities',
            'creatures',
        ];
    }

    public static function isAllowedType(string $entityType): bool
    {
        return in_array($entityType, self::allowedTypes(), true);
    }

    public static function resolveModel(string $entityType, mixed $id): ?Model
    {
        $id = is_numeric($id) ? (int) $id : $id;

        return match ($entityType) {
            'spells' => Spell::query()->find($id),
            'items' => Item::query()->find($id),
            'resources' => Resource::query()->find($id),
            'consumables' => Consumable::query()->find($id),
            'monsters' => Monster::query()->find($id),
            'npcs' => Npc::query()->find($id),
            'campaigns' => Campaign::query()->find($id),
            'scenarios' => Scenario::query()->find($id),
            'panoplies' => Panoply::query()->find($id),
            'capabilities' => Capability::query()->find($id),
            'creatures' => Creature::query()->find($id),
            default => null,
        };
    }
}
