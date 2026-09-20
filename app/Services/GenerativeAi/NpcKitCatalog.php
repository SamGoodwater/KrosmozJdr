<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Breed;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentBonusDecoder;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentGridDefinition;
use App\Services\GenerativeAi\EquipmentGrid\EquipmentVoieClassifier;
use App\Services\Npc\NpcEquipmentSlotValidator;
use App\Support\ElementBitmask;

/**
 * Pré-filtre compact (équipement, classes, spés, sorts, gabarit) à envoyer à un LLM.
 *
 * Pas d’appel IA : objets `playable`, classes/spés hors archive, sorts `playable` plafonnés par classe.
 *
 * @example
 * $payload = app(NpcKitCatalog::class)->assemble(4, 'terre', $iopId, 'guard');
 */
final class NpcKitCatalog
{
    public const MAX_ITEMS = 40;

    public const LEVEL_BAND = 2;

    public const MAX_PER_SLOT = 5;

    public const MAX_SPELLS_PER_BREED = 12;

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
     *     spells_by_breed: array<int, list<array<string, mixed>>>,
     *     breeds: list<array{id: int, name: string, state: string}>,
     *     specializations: list<array{id: int, name: string, state: string, short_description?: string}>,
     *     example_ids: list<int>
     * }
     */
    public function assemble(int $level, ?string $voie = null, ?int $breedId = null, string $role = 'other'): array
    {
        return [
            'gabarit' => $this->gabarit->forLevelAndRole($level, $role),
            'items' => $this->equipment($level, $voie),
            'spells' => $breedId !== null ? $this->spells($breedId, $level) : [],
            'spells_by_breed' => $this->spellsByBreed($level),
            'breeds' => $this->breeds(),
            'specializations' => $this->specializations(),
            'example_ids' => $this->requiredExampleIds(),
        ];
    }

    /**
     * Classes hors archive (draft / auto / playable) pour le choix d’ids.
     *
     * @return list<array{id: int, name: string, state: string}>
     *
     * @example $rows = (new NpcKitCatalog)->breeds();
     */
    public function breeds(): array
    {
        return Breed::query()
            ->where('state', '!=', Breed::STATE_ARCHIVED)
            ->orderBy('name')
            ->get(['id', 'name', 'state'])
            ->map(static fn (Breed $breed): array => [
                'id' => (int) $breed->id,
                'name' => (string) $breed->name,
                'state' => (string) $breed->state,
            ])
            ->values()
            ->all();
    }

    /**
     * Spécialisations hors archive, sans dump des capacités / aptitudes.
     *
     * @return list<array{id: int, name: string, state: string, short_description?: string}>
     *
     * @example $rows = (new NpcKitCatalog)->specializations();
     */
    public function specializations(): array
    {
        $out = [];
        $rows = Specialization::query()
            ->where('state', '!=', Specialization::STATE_ARCHIVED)
            ->orderBy('name')
            ->get(['id', 'name', 'state', 'short_description']);

        foreach ($rows as $spec) {
            $row = [
                'id' => (int) $spec->id,
                'name' => (string) $spec->name,
                'state' => (string) $spec->state,
            ];
            $short = is_string($spec->short_description) ? trim($spec->short_description) : '';
            if ($short !== '') {
                $row['short_description'] = $short;
            }
            $out[] = $row;
        }

        return $out;
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
     * Sorts playable groupés par classe, plafonnés, même sans `breed_id` source.
     *
     * @return array<int, list<array{id: int, name: string, character_level: int, element: string, pa: string}>>
     *
     * @example $byBreed = (new NpcKitCatalog)->spellsByBreed(4);
     */
    public function spellsByBreed(int $maxLevel): array
    {
        $maxLevel = max(1, min(20, $maxLevel));

        $spells = Spell::query()
            ->where('state', EntityState::Playable->value)
            ->whereHas('breeds', static function ($query) use ($maxLevel): void {
                $query->where('breeds.state', '!=', Breed::STATE_ARCHIVED)
                    ->where('breed_spell.character_level', '>', 0)
                    ->where('breed_spell.character_level', '<=', $maxLevel);
            })
            ->with(['breeds' => static function ($query) use ($maxLevel): void {
                $query->where('breeds.state', '!=', Breed::STATE_ARCHIVED)
                    ->where('breed_spell.character_level', '>', 0)
                    ->where('breed_spell.character_level', '<=', $maxLevel);
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'element', 'pa']);

        $byBreed = [];
        foreach ($spells as $spell) {
            $element = is_numeric($spell->element) ? ElementBitmask::label((int) $spell->element) : '—';
            $row = [
                'id' => (int) $spell->id,
                'name' => (string) $spell->name,
                'element' => $element,
                'pa' => (string) ($spell->pa ?? ''),
            ];
            foreach ($spell->breeds as $breed) {
                $breedId = (int) $breed->id;
                $byBreed[$breedId][] = array_merge($row, [
                    'character_level' => (int) ($breed->pivot?->character_level ?? 1),
                ]);
            }
        }

        $out = [];
        foreach ($byBreed as $breedId => $rows) {
            usort($rows, static fn (array $a, array $b): int => [$a['character_level'], $a['name'], $a['id']] <=> [$b['character_level'], $b['name'], $b['id']]);
            $out[$breedId] = array_values(array_slice($rows, 0, self::MAX_SPELLS_PER_BREED));
        }
        ksort($out);

        return $out;
    }

    /**
     * @return list<int>
     */
    public function exampleIds(): array
    {
        $configured = $this->configuredExampleRefs();
        if ($configured !== []) {
            return app(FewShotExamplePool::class)->resolvePlayableIds('npc', $configured);
        }

        return $this->incarnamFallbackIds();
    }

    /**
     * @return list<int>
     */
    public function requiredExampleIds(): array
    {
        return app(FewShotExamplePool::class)->requireIds(
            'npc',
            $this->configuredExampleRefs(),
            $this->incarnamFallbackIds()
        );
    }

    /**
     * @return list<int|string>
     */
    private function configuredExampleRefs(): array
    {
        try {
            return GenerationConfigLoader::default()->forEntity('npc')->exampleIds;
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @return list<int>
     */
    private function incarnamFallbackIds(): array
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
