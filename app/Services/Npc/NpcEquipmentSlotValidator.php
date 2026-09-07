<?php

declare(strict_types=1);

namespace App\Services\Npc;

use App\Models\Entity\Item;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Vérifie qu’un kit d’équipement PNJ respecte 1 objet par emplacement (2 anneaux).
 *
 * @example
 *   $validator->assertWornKit($items);
 */
final class NpcEquipmentSlotValidator
{
    public const SLOT_AMULET = 'amulet';

    public const SLOT_RING = 'ring';

    public const SLOT_BELT = 'belt';

    public const SLOT_BOOTS = 'boots';

    public const SLOT_HAT = 'hat';

    public const SLOT_CAPE = 'cape';

    public const SLOT_SHIELD = 'shield';

    public const SLOT_WEAPON = 'weapon';

    /** @var array<int, string> dofusdb_type_id → slot */
    public const DOFUSDB_TYPE_TO_SLOT = [
        1 => self::SLOT_AMULET,
        9 => self::SLOT_RING,
        10 => self::SLOT_BELT,
        11 => self::SLOT_BOOTS,
        16 => self::SLOT_HAT,
        17 => self::SLOT_CAPE,
        82 => self::SLOT_SHIELD,
        2 => self::SLOT_WEAPON,
        3 => self::SLOT_WEAPON,
        4 => self::SLOT_WEAPON,
        5 => self::SLOT_WEAPON,
        6 => self::SLOT_WEAPON,
        7 => self::SLOT_WEAPON,
        8 => self::SLOT_WEAPON,
        19 => self::SLOT_WEAPON,
        21 => self::SLOT_WEAPON,
        22 => self::SLOT_WEAPON,
        114 => self::SLOT_WEAPON,
        271 => self::SLOT_WEAPON,
    ];

    /** @var array<string, string> */
    public const SLOT_LABELS = [
        self::SLOT_AMULET => 'amulette',
        self::SLOT_RING => 'anneau',
        self::SLOT_BELT => 'ceinture',
        self::SLOT_BOOTS => 'bottes',
        self::SLOT_HAT => 'chapeau',
        self::SLOT_CAPE => 'cape',
        self::SLOT_SHIELD => 'bouclier',
        self::SLOT_WEAPON => 'arme',
    ];

    /**
     * @param  Collection<int, Item>|\Illuminate\Database\Eloquent\Collection<int, Item>  $items
     *
     * @throws ValidationException
     */
    public function assertWornKit($items): void
    {
        $counts = [];
        foreach ($items as $item) {
            $typeId = $item->itemType?->dofusdb_type_id;
            if ($typeId === null) {
                throw ValidationException::withMessages([
                    'items' => sprintf(
                        '« %s » n’est pas un équipement de kit PNJ (type DofusDB inconnu).',
                        $item->name ?: '#'.$item->id
                    ),
                ]);
            }
            $slot = self::DOFUSDB_TYPE_TO_SLOT[(int) $typeId] ?? null;
            if ($slot === null) {
                throw ValidationException::withMessages([
                    'items' => sprintf(
                        '« %s » n’est pas un équipement portable pour un PNJ.',
                        $item->name ?: '#'.$item->id
                    ),
                ]);
            }
            $counts[$slot] = ($counts[$slot] ?? 0) + 1;
            $max = $slot === self::SLOT_RING ? 2 : 1;
            if ($counts[$slot] > $max) {
                $label = self::SLOT_LABELS[$slot] ?? $slot;
                throw ValidationException::withMessages([
                    'items' => $slot === self::SLOT_RING
                        ? 'Un PNJ ne peut porter que deux anneaux.'
                        : sprintf('Un PNJ ne peut porter qu’un seul emplacement « %s ».', $label),
                ]);
            }
        }
    }
}
