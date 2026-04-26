<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Aperçu léger d’une entité pour les infobulles des références riches (kref).
 */
class CmsKrefEntityPreviewController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $entityType = trim((string) $request->query('entityType', ''));
        $idRaw = $request->query('id');
        if ($entityType === '' || $idRaw === null || $idRaw === '') {
            return response()->json(['message' => 'Paramètres incomplets.'], 422);
        }

        $id = is_numeric($idRaw) ? (int) $idRaw : $idRaw;
        $model = $this->resolveEntity($entityType, $id);
        if ($model === null) {
            return response()->json(['message' => 'Entité introuvable.'], 404);
        }

        Gate::authorize('view', $model);

        return response()->json($this->serializeEntity($entityType, $model));
    }

    private function resolveEntity(string $entityType, mixed $id): ?Model
    {
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

    /**
     * @return array<string, mixed>
     */
    private function serializeEntity(string $entityType, Model $model): array
    {
        $name = (string) ($model->getAttribute('name') ?? '');
        $image = $model->getAttribute('image');
        $imageUrl = $image !== null && trim((string) $image) !== '' ? (string) $image : null;

        $meta = [];
        if ($entityType === 'spells') {
            $level = $model->getAttribute('level');
            if ($level !== null && $level !== '') {
                $meta[] = 'Niveau '.(string) $level;
            }
            $pa = $model->getAttribute('pa');
            if ($pa !== null && $pa !== '') {
                $meta[] = (string) $pa.' PA';
            }
        }
        if (in_array($entityType, ['items', 'consumables', 'resources', 'panoplies'], true)) {
            $level = $model->getAttribute('level');
            if ($level !== null && $level !== '') {
                $meta[] = 'Niveau '.(string) $level;
            }
        }
        if (in_array($entityType, ['monsters', 'npcs', 'creatures'], true)) {
            $level = $model->getAttribute('level');
            if ($level !== null && $level !== '') {
                $meta[] = 'Niveau '.(string) $level;
            }
        }

        return [
            'entityType' => $entityType,
            'name' => $name,
            'image' => $imageUrl,
            'meta' => array_values(array_filter($meta, static fn ($v) => is_string($v) && $v !== '')),
        ];
    }
}
