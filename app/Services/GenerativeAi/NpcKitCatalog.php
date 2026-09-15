<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentBonusDecoder;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridDefinition;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentVoieClassifier;
use App\Services\Npc\NpcEquipmentSlotValidator;
use App\Support\ElementBitmask;

/**
 * Pré-filtre compact (équipement, sorts, gabarit) à envoyer plus tard à un LLM.
 *
 * Pas d’appel IA : liste courte `playable`, 1–2 options par slot, max 40 objets.
 *
 * @example
 * $payload = app(NpcKitCatalog::class)->assemble(4, 'terre', $iopId, 'guard');
 */
final class NpcKitCatalog
{
    public const MAX_ITEMS = 40;

    public const LEVEL_BAND = 2;

    public const MAX_PER_SLOT = 5;

    public const OFFICIAL_ID_PREFIX = 'jdr:npc:incarnam:';

    public function __construct(
        private readonly EquipmentBonusDecoder $decoder = new EquipmentBonusDecoder,
        private readonly NpcStatGabarit $gabarit = new NpcStatGabarit,
    ) {}

    /**
     * @return array{
     *     gabarit: array<string, string>,
     *     items: list<array<string, mixed>>,
     *     spells: list<array<string, mixed>>,
     *     example_ids: list<int>
     * }
     */
    public function assemble(int $level, ?string $voie = null, ?int $breedId = null, string $role = 'other'): array
    {
        return [
            'gabarit' => $this->gabarit->forLevelAndRole($level, $role),
            'items' => $this->equipment($level, $voie),
            'spells' => $breedId !== null ? $this->spells($breedId, $level) : [],
            'example_ids' => $this->exampleIds(),
        ];
    }

    /**
     * @return list<array{id: int, name: string, type: string, slot: string, level: int, bonuses: array<string, float>}>
     */
    public function equipment(int $level, ?string $voie = null): array
    {
        $level = max(1, min(20, $level));
        $minLevel = max(1, $level - self::LEVEL_BAND);
        $definition = EquipmentGridDefinition::loadDefault();
        $classifier = new EquipmentVoieClassifier($definition);
        $voie = $voie !== null && $voie !== '' ? strtolower(trim($voie)) : null;

        $rows = Item::query()
            ->with('itemType:id,name,dofusdb_type_id')
            ->where('state', EntityState::Playable->value)
            ->whereHas('itemType', static function ($query): void {
                $query->whereIn('dofusdb_type_id', array_keys(NpcEquipmentSlotValidator::DOFUSDB_TYPE_TO_SLOT));
            })
            ->get(['id', 'name', 'level', 'bonus', 'effect', 'item_type_id']);

        $bySlot = [];
        foreach ($rows as $item) {
            $itemLevel = is_numeric($item->level) ? (int) $item->level : 1;
            if ($itemLevel < $minLevel || $itemLevel > $level) {
                continue;
            }
            $typeId = $item->itemType?->dofusdb_type_id;
            if ($typeId === null) {
                continue;
            }
            $slot = NpcEquipmentSlotValidator::DOFUSDB_TYPE_TO_SLOT[(int) $typeId] ?? null;
            if ($slot === null) {
                continue;
            }
            $bonuses = $this->decoder->decode($item->effect, $item->bonus);
            $itemVoie = $classifier->classify($bonuses);
            if ($voie !== null && $itemVoie !== null && $itemVoie !== $voie) {
                continue;
            }

            $bySlot[$slot][] = [
                'id' => (int) $item->id,
                'name' => (string) $item->name,
                'type' => (string) ($item->itemType?->name ?: (NpcEquipmentSlotValidator::SLOT_LABELS[$slot] ?? $slot)),
                'slot' => $slot,
                'level' => $itemLevel,
                'bonuses' => $this->compactBonuses($bonuses),
                '_voie_match' => $itemVoie === $voie ? 1 : 0,
            ];
        }

        $out = [];
        foreach ($bySlot as $slot => $candidates) {
            usort($candidates, static function (array $a, array $b): int {
                return [$b['_voie_match'], $b['level'], $a['id']] <=> [$a['_voie_match'], $a['level'], $b['id']];
            });
            $limit = $slot === NpcEquipmentSlotValidator::SLOT_RING
                ? max(2, self::MAX_PER_SLOT)
                : self::MAX_PER_SLOT;
            foreach (array_slice($candidates, 0, $limit) as $row) {
                unset($row['_voie_match']);
                $out[] = $row;
            }
        }

        usort($out, static fn (array $a, array $b): int => [$a['slot'], -$a['level'], $a['name']] <=> [$b['slot'], -$b['level'], $b['name']]);

        return array_slice($out, 0, self::MAX_ITEMS);
    }

    /**
     * @return list<array{id: int, name: string, character_level: int, element: string, pa: string}>
     */
    public function spells(int $breedId, int $maxLevel): array
    {
        $maxLevel = max(1, min(20, $maxLevel));

        $spells = Spell::query()
            ->where('state', EntityState::Playable->value)
            ->whereHas('breeds', static function ($query) use ($breedId, $maxLevel): void {
                $query->where('breeds.id', $breedId)
                    ->where('breed_spell.character_level', '>', 0)
                    ->where('breed_spell.character_level', '<=', $maxLevel);
            })
            ->with(['breeds' => static function ($query) use ($breedId): void {
                $query->where('breeds.id', $breedId);
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'element', 'pa']);

        $out = [];
        foreach ($spells as $spell) {
            $characterLevel = (int) ($spell->breeds->first()?->pivot?->character_level ?? 1);
            $element = is_numeric($spell->element) ? ElementBitmask::label((int) $spell->element) : '—';
            $out[] = [
                'id' => (int) $spell->id,
                'name' => (string) $spell->name,
                'character_level' => $characterLevel,
                'element' => $element,
                'pa' => (string) ($spell->pa ?? ''),
            ];
        }

        return $out;
    }

    /**
     * @return list<int>
     */
    public function exampleIds(): array
    {
        return Npc::query()
            ->where('state', Npc::STATE_PLAYABLE)
            ->where('official_id', 'like', self::OFFICIAL_ID_PREFIX.'%')
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->all();
    }

    /**
     * @param  array<string, float>  $bonuses
     * @return array<string, float>
     */
    private function compactBonuses(array $bonuses): array
    {
        $out = [];
        foreach ($bonuses as $key => $value) {
            if (! is_numeric($value) || (float) $value == 0.0) {
                continue;
            }
            $out[$key] = round((float) $value, 2);
        }

        return $out;
    }
}
