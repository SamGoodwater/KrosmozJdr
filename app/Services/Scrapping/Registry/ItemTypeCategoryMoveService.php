<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Registry;

use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Type\ConsumableType;
use App\Models\Type\ItemType;
use App\Models\Type\ResourceType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Déplace un type d'une catégorie (resource/consumable/equipment) vers une autre.
 *
 * Un dofusdb_type_id ne doit figurer que dans une seule des trois tables.
 * Phase 1 : interdire le déplacement si des entités (resources, consumables, items) utilisent ce type.
 */
final class ItemTypeCategoryMoveService
{
    private const CACHE_KEYS = [
        'resource' => 'scrapping_allowed_type_ids_resource',
        'consumable' => 'scrapping_allowed_type_ids_consumable',
        'equipment' => 'scrapping_allowed_type_ids_equipment',
    ];

    /**
     * Déplace le type identifié par $id depuis la catégorie $from vers la catégorie $to.
     *
     * @param string $from 'resource' | 'consumable' | 'equipment'
     * @param int $id ID de l'enregistrement (ResourceType, ConsumableType ou ItemType)
     * @param string $to 'resource' | 'consumable' | 'equipment' (différent de $from)
     * @return array{success: bool, message: string, target_id?: int}
     */
    public function move(string $from, int $id, string $to): array
    {
        $from = strtolower(trim($from));
        $to = strtolower(trim($to));

        $allowed = ['resource', 'consumable', 'equipment'];
        if (!in_array($from, $allowed, true) || !in_array($to, $allowed, true) || $from === $to) {
            return ['success' => false, 'message' => 'Catégorie source ou cible invalide.'];
        }

        $model = $this->findSourceModel($from, $id);
        if ($model === null) {
            return ['success' => false, 'message' => 'Type introuvable.'];
        }

        $typeId = (int) $model->dofusdb_type_id;
        if ($typeId <= 0) {
            return ['success' => false, 'message' => 'Type sans dofusdb_type_id.'];
        }

        if ($this->targetHasType($to, $typeId)) {
            return ['success' => false, 'message' => 'Ce type existe déjà dans la catégorie cible.'];
        }

        if ($this->entitiesUseType($from, $id)) {
            return [
                'success' => false,
                'message' => 'Impossible de déplacer : des entités utilisent encore ce type. Aucune entité (ressource, consommable ou équipement) ne doit le référencer.',
            ];
        }

        try {
            DB::beginTransaction();

            $targetModel = $this->createInTarget($to, $model);
            $targetId = $targetModel->id;

            $this->deleteSource($from, $model);

            $this->invalidateCache();

            DB::commit();

            return [
                'success' => true,
                'message' => $this->moveSuccessMessage($from, $to),
                'target_id' => $targetId,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Erreur lors du déplacement : ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Déplace en masse les types identifiés par $ids depuis la catégorie $from vers $to.
     * Chaque déplacement est tenté individuellement ; les échecs (entités utilisent le type, etc.) sont collectés.
     *
     * @param string $from 'resource' | 'consumable' | 'equipment'
     * @param list<int> $ids IDs des enregistrements à déplacer
     * @param string $to 'resource' | 'consumable' | 'equipment'
     * @return array{moved: int, failed: int, errors: list<array{id: int, message: string}>}
     */
    public function moveBulk(string $from, array $ids, string $to): array
    {
        $from = strtolower(trim($from));
        $to = strtolower(trim($to));
        $allowed = ['resource', 'consumable', 'equipment'];
        if (!in_array($from, $allowed, true) || !in_array($to, $allowed, true) || $from === $to) {
            return [
                'moved' => 0,
                'failed' => 1,
                'errors' => [['id' => 0, 'message' => 'Catégorie source ou cible invalide.']],
            ];
        }

        $moved = 0;
        $errors = [];
        foreach (array_filter(array_map('intval', $ids)) as $id) {
            if ($id <= 0) {
                continue;
            }
            $result = $this->move($from, $id, $to);
            if ($result['success']) {
                $moved++;
            } else {
                $errors[] = ['id' => $id, 'message' => $result['message']];
            }
        }

        return ['moved' => $moved, 'failed' => count($errors), 'errors' => $errors];
    }

    /**
     * Libellé de la catégorie pour les messages (Ressources, Consommables, Équipements).
     */
    public function getTargetLabel(string $target): string
    {
        $labels = [
            'resource' => 'Ressources',
            'consumable' => 'Consommables',
            'equipment' => 'Équipements',
        ];
        return $labels[strtolower(trim($target))] ?? $target;
    }

    /**
     * Message utilisateur pour un résultat moveBulk (une seule source pour les 3 contrôleurs).
     *
     * @param array{moved: int, failed: int, errors: list<array{id: int, message: string}>} $result
     */
    public function formatBulkMoveMessage(array $result, string $target): string
    {
        $label = $this->getTargetLabel($target);
        $message = $result['moved'] > 0
            ? $result['moved'] . ' type(s) déplacé(s) vers ' . $label . '.'
            : 'Aucun type déplacé.';
        if ($result['failed'] > 0 && !empty($result['errors'])) {
            $first = $result['errors'][0]['message'] ?? '';
            $message .= ' ' . $result['failed'] . ' échec(s). Ex. : ' . $first;
        }
        return $message;
    }

    /**
     * @param string $from
     * @param int $id
     * @return ResourceType|ConsumableType|ItemType|null
     */
    private function findSourceModel(string $from, int $id)
    {
        return match ($from) {
            'resource' => ResourceType::find($id),
            'consumable' => ConsumableType::find($id),
            'equipment' => ItemType::find($id),
            default => null,
        };
    }

    private function targetHasType(string $to, int $dofusdbTypeId): bool
    {
        return match ($to) {
            'resource' => ResourceType::where('dofusdb_type_id', $dofusdbTypeId)->exists(),
            'consumable' => ConsumableType::where('dofusdb_type_id', $dofusdbTypeId)->exists(),
            'equipment' => ItemType::where('dofusdb_type_id', $dofusdbTypeId)->exists(),
            default => false,
        };
    }

    /** Vérifie si des entités (resources, consumables, items) référencent ce type. */
    private function entitiesUseType(string $from, int $typePk): bool
    {
        return match ($from) {
            'resource' => Resource::where('resource_type_id', $typePk)->exists(),
            'consumable' => Consumable::where('consumable_type_id', $typePk)->exists(),
            'equipment' => Item::where('item_type_id', $typePk)->exists(),
            default => true,
        };
    }

    /**
     * @param ResourceType|ConsumableType|ItemType $model
     * @return ResourceType|ConsumableType|ItemType
     */
    private function createInTarget(string $to, $model)
    {
        $attrs = [
            'name' => $model->name,
            'dofusdb_type_id' => $model->dofusdb_type_id,
            'decision' => $model->decision ?? 'pending',
            'seen_count' => $model->seen_count ?? 0,
            'last_seen_at' => $model->last_seen_at,
            'state' => $model->state ?? 'draft',
            'read_level' => $model->read_level ?? 0,
            'write_level' => $model->write_level ?? 3,
            'created_by' => $model->created_by,
        ];

        return match ($to) {
            'resource' => ResourceType::create($attrs),
            'consumable' => ConsumableType::create($attrs),
            'equipment' => ItemType::create($attrs),
        };
    }

    /**
     * @param ResourceType|ConsumableType|ItemType $model
     */
    private function deleteSource(string $from, $model): void
    {
        $model->forceDelete();
    }

    private function invalidateCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    private function moveSuccessMessage(string $from, string $to): string
    {
        return 'Type déplacé de ' . $this->getTargetLabel($from) . ' vers ' . $this->getTargetLabel($to) . '.';
    }
}
