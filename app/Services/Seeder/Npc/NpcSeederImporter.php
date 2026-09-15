<?php

declare(strict_types=1);

namespace App\Services\Seeder\Npc;

use App\Enums\EntityState;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Language;
use App\Models\Entity\Npc;
use App\Models\Entity\Panoply;
use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\GenerativeAi\NpcStatGabarit;
use App\Services\Npc\NpcEquipmentSlotValidator;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Importe les PNJ JDR (JSON `entities/npcs/*.json`).
 *
 * Idempotent. Upsert sur `official_id` `jdr:npc:incarnam:…`. `auto_update = false`.
 *
 * @example $result = app(NpcSeederImporter::class)->import();
 */
final class NpcSeederImporter
{
    /** @var list<string> */
    private const LEGACY_SHELL_NAMES = [
        'Garde Iop d’Astrub',
        'Garde Iop d\'Astrub',
        'Marchande de Bonta',
        'Aubergiste du Coq de Brume',
    ];

    public function __construct(
        private readonly NpcStatGabarit $gabarit = new NpcStatGabarit,
        private readonly NpcEquipmentSlotValidator $slotValidator = new NpcEquipmentSlotValidator,
    ) {}

    /**
     * @return array{created: list<string>, updated: list<string>, skipped: list<string>, retired: list<string>}
     */
    public function import(?IncarnamNpcCatalog $catalog = null): array
    {
        $catalog ??= IncarnamNpcCatalog::load();
        $created = [];
        $updated = [];
        $skipped = [];

        foreach ($catalog->entries() as $entry) {
            $npc = $this->findNpc($entry['official_id']);
            $wasNew = $npc === null;
            $npc ??= new Npc;
            $creature = $npc->creature ?? new Creature;

            $attributes = $this->creatureAttributes($entry);
            if ($creature->exists && $creature->created_by !== null) {
                unset($attributes['created_by']);
            }
            $creature->fill($attributes);
            $creature->save();

            $npc->fill($this->npcAttributes($entry, $creature->id, $skipped));
            $npc->creature_id = $creature->id;
            $npc->save();

            $this->syncLanguages($npc, $entry['languages'], $entry['name'], $skipped);
            $this->syncPanoplies($npc, $entry['panoplies'], $entry['name'], $skipped);
            $this->syncItems($creature, $entry['items'], $entry['name'], $skipped);
            $this->syncSpells($creature, $entry['spells'], $entry['name'], $skipped);

            $wasNew ? $created[] = $entry['name'] : $updated[] = $entry['name'];
        }

        $retired = $this->retireLegacyShells();

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'retired' => $retired,
        ];
    }

    private function findNpc(string $officialId): ?Npc
    {
        return Npc::query()->where('official_id', $officialId)->with('creature')->first();
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    private function creatureAttributes(array $entry): array
    {
        $stats = $this->gabarit->forLevelAndRole($entry['level'], $entry['npc_role']);
        unset($stats['damage_dice'], $stats['band']);

        return array_merge($stats, [
            'name' => $entry['name'],
            'description' => $entry['description'],
            'other_info' => $entry['other_info'],
            'location' => $entry['location'],
            'hostility' => $entry['hostility'],
            'level' => (string) $entry['level'],
            'state' => Creature::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $this->createdById(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  list<string>  $skipped
     * @return array<string, mixed>
     */
    private function npcAttributes(array $entry, int $creatureId, array &$skipped): array
    {
        $breedId = null;
        if ($entry['breed'] !== null) {
            $breedId = Breed::query()->where('name', $entry['breed'])->value('id');
            if ($breedId === null) {
                $skipped[] = $entry['name'].' : classe « '.$entry['breed'].' » introuvable';
            }
        }

        $speId = null;
        if ($entry['specialization'] !== null) {
            $spe = Specialization::query()
                ->where('name', $entry['specialization'])
                ->where('state', EntityState::Playable->value)
                ->first();
            if ($spe === null) {
                $skipped[] = $entry['name'].' : spécialisation jouable « '.$entry['specialization'].' » introuvable';
            } else {
                $speId = $spe->id;
            }
        }

        return [
            'creature_id' => $creatureId,
            'official_id' => $entry['official_id'],
            'auto_update' => false,
            'npc_role' => $entry['npc_role'],
            'size' => $entry['size'],
            'age' => $entry['age'],
            'story' => $entry['story'],
            'historical' => $entry['historical'],
            'breed_id' => $breedId,
            'specialization_id' => $speId,
            'state' => Npc::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ];
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $skipped
     */
    private function syncLanguages(Npc $npc, array $names, string $npcName, array &$skipped): void
    {
        $sync = [];
        $order = 0;
        foreach ($names as $name) {
            $id = Language::query()->where('name', $name)->value('id');
            if ($id === null) {
                $skipped[] = $npcName.' : langue « '.$name.' » introuvable';

                continue;
            }
            $sync[(int) $id] = ['sort_order' => $order];
            $order++;
        }
        $npc->languages()->sync($sync);
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $skipped
     */
    private function syncPanoplies(Npc $npc, array $names, string $npcName, array &$skipped): void
    {
        $ids = [];
        foreach ($names as $name) {
            $id = Panoply::query()
                ->where('name', $name)
                ->where('state', EntityState::Playable->value)
                ->value('id');
            if ($id === null) {
                $skipped[] = $npcName.' : panoplie « '.$name.' » introuvable';

                continue;
            }
            $ids[] = (int) $id;
        }
        $npc->panoplies()->sync($ids);
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $skipped
     */
    private function syncItems(Creature $creature, array $names, string $npcName, array &$skipped): void
    {
        /** @var Collection<int, Item> $picked */
        $picked = new Collection;
        foreach ($names as $name) {
            $item = Item::query()
                ->with('itemType')
                ->where('name', $name)
                ->where('state', EntityState::Playable->value)
                ->first();
            if ($item === null) {
                $skipped[] = $npcName.' : objet « '.$name.' » introuvable';

                continue;
            }
            $candidate = $picked->concat([$item]);
            try {
                $this->slotValidator->assertWornKit($candidate);
            } catch (ValidationException) {
                $skipped[] = $npcName.' : objet « '.$name.' » refusé (emplacement)';

                continue;
            }
            $picked->push($item);
        }

        $sync = [];
        foreach ($picked as $item) {
            $sync[$item->id] = ['quantity' => 1];
        }
        $creature->items()->sync($sync);
    }

    /**
     * @param  list<string>  $names
     * @param  list<string>  $skipped
     */
    private function syncSpells(Creature $creature, array $names, string $npcName, array &$skipped): void
    {
        $ids = [];
        foreach ($names as $name) {
            $id = Spell::query()
                ->where('name', $name)
                ->where('state', EntityState::Playable->value)
                ->value('id');
            if ($id === null) {
                $skipped[] = $npcName.' : sort « '.$name.' » introuvable';

                continue;
            }
            $ids[] = (int) $id;
        }
        $creature->spells()->sync($ids);
    }

    /**
     * @return list<string>
     */
    private function retireLegacyShells(): array
    {
        $retired = [];
        $npcs = Npc::query()
            ->whereHas('creature', fn ($q) => $q->whereIn('name', self::LEGACY_SHELL_NAMES))
            ->where(function ($q): void {
                $q->whereNull('official_id')->orWhere('official_id', '');
            })
            ->with(['creature', 'campaigns'])
            ->get();

        foreach ($npcs as $npc) {
            if ($npc->campaigns->isNotEmpty()) {
                continue;
            }
            $name = (string) ($npc->creature?->name ?? '#'.$npc->id);
            $npc->delete();
            $retired[] = $name;
        }

        return $retired;
    }

    private function createdById(): int
    {
        $id = User::getSystemUser()?->id ?? User::query()->orderBy('id')->value('id');
        if ($id !== null) {
            return (int) $id;
        }

        return User::factory()->create()->id;
    }
}
