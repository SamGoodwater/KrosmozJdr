<?php

namespace App\Http\Controllers\Api;

use App\Enums\EntityState;
use App\Http\Controllers\Controller;
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
use App\Models\Type\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Met à jour l'état d'une seule entité via la barre d'actions.
 *
 * @description
 * Utilise les policies `update` par entité, contrairement aux endpoints bulk qui
 * vérifient `updateAny`. Cela conserve les droits propriétaire quand ils existent.
 *
 * @example
 * PATCH /api/entities/items/12/state
 * { "state": "playable" }
 */
class EntityStateController extends Controller
{
    /** @var array<string, class-string<Model>> */
    private const MODELS = [
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
        'resource-types' => ResourceType::class,
        'scenarios' => Scenario::class,
        'shops' => Shop::class,
        'specializations' => Specialization::class,
        'spells' => Spell::class,
    ];

    public function update(Request $request, string $entityType, int $id): JsonResponse
    {
        $modelClass = self::MODELS[$entityType] ?? null;
        if ($modelClass === null) {
            return response()->json([
                'success' => false,
                'message' => "Type d'entité non pris en charge.",
            ], 404);
        }

        $validated = $request->validate([
            'state' => ['required', 'string', EntityState::rule()],
        ]);

        /** @var Model $model */
        $model = $modelClass::query()->findOrFail($id);
        $this->authorize('update', $model);

        if (! array_key_exists('state', $model->getAttributes()) && ! $model->isFillable('state')) {
            return response()->json([
                'success' => false,
                'message' => "Cette entité ne possède pas d'état modifiable.",
            ], 422);
        }

        $model->setAttribute('state', $validated['state']);
        $model->save();

        return response()->json([
            'success' => true,
            'entity' => [
                'id' => $model->getKey(),
                'type' => $entityType,
                'state' => $model->getAttribute('state'),
            ],
        ]);
    }
}
