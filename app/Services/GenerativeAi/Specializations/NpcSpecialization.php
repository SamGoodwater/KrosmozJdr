<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Enums\EntityState;
use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Npc;
use App\Models\Entity\Specialization as SpecializationModel;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\NpcKitCatalog;
use App\Services\GenerativeAi\NpcStatGabarit;
use App\Services\Npc\NpcEquipmentSlotValidator;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Spécialisation PNJ : fiche complète + ids du pré-filtre `NpcKitCatalog`.
 */
final class NpcSpecialization implements Specialization
{
    public function key(): string
    {
        return 'npc';
    }

    public function entityType(): string
    {
        return 'npc';
    }

    public function jsonSchema(EntityGenerationProfile $profile, ?ConversionRequest $request = null): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['npc', 'item_ids', 'spell_ids'],
            'properties' => [
                'npc' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['name', 'level'],
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'concept' => ['type' => 'string'],
                        'story' => ['type' => 'string'],
                        'level' => ['type' => 'integer'],
                        'breed_id' => ['type' => 'integer'],
                        'specialization_id' => ['type' => ['integer', 'null']],
                        'npc_role' => ['type' => 'string'],
                    ],
                ],
                'stats' => [
                    'type' => 'object',
                    'additionalProperties' => true,
                ],
                'item_ids' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                ],
                'spell_ids' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                ],
            ],
        ];
    }

    public function extraContext(ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        $npc = $this->sourceNpc($request);
        $level = $this->levelOf($npc);
        $breedId = $npc?->breed_id !== null ? (int) $npc->breed_id : null;
        $role = is_string($npc?->npc_role) && $npc->npc_role !== '' ? $npc->npc_role : 'other';

        $catalog = app(NpcKitCatalog::class)->assemble($level, null, $breedId, $role);

        return [
            'kit_catalog' => [
                'gabarit' => $catalog['gabarit'],
                'items' => $catalog['items'],
                'breeds' => $catalog['breeds'],
                'specializations' => $catalog['specializations'],
                'spells' => $catalog['spells'],
                'spells_by_breed' => $catalog['spells_by_breed'],
            ],
            'consigne' => 'item_ids : ids de kit_catalog.items. spell_ids : ids de kit_catalog.spells_by_breed pour la classe choisie (playable). breed_id / specialization_id : ids de kit_catalog.breeds / specializations (draft et auto autorisés). Un objet par slot (deux anneaux max). Privilégier les bonus d’équipement de la voie des sorts.',
        ];
    }

    public function preflight(ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        return [];
    }

    public function validate(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        $errors = [];
        $npc = $payload['npc'] ?? null;
        if (! is_array($npc)) {
            return ['Paquet PNJ : objet npc requis.'];
        }
        $name = is_string($npc['name'] ?? null) ? trim($npc['name']) : '';
        if ($name === '') {
            $errors[] = 'Nom du PNJ requis.';
        }
        $level = is_numeric($npc['level'] ?? null) ? (int) $npc['level'] : 0;
        if ($level < 1 || $level > 20) {
            $errors[] = 'Niveau PNJ entre 1 et 20.';
        }

        $breedId = is_numeric($npc['breed_id'] ?? null) && (int) $npc['breed_id'] > 0
            ? (int) $npc['breed_id']
            : null;
        if ($breedId !== null && ! $this->breedExists($breedId)) {
            $errors[] = "Classe #{$breedId} inconnue.";
        }
        $specId = is_numeric($npc['specialization_id'] ?? null) && (int) $npc['specialization_id'] > 0
            ? (int) $npc['specialization_id']
            : null;
        if ($specId !== null && ! $this->specializationExists($specId)) {
            $errors[] = "Spécialisation #{$specId} inconnue.";
        }

        $catalog = app(NpcKitCatalog::class)->assemble(
            max(1, $level),
            null,
            $breedId,
            is_string($npc['npc_role'] ?? null) ? (string) $npc['npc_role'] : 'other'
        );
        $allowedItems = array_map(static fn (array $row): int => (int) $row['id'], $catalog['items']);
        $allowedSpells = array_map(static fn (array $row): int => (int) $row['id'], $catalog['spells']);

        $itemIds = is_array($payload['item_ids'] ?? null) ? $payload['item_ids'] : [];
        $spellIds = is_array($payload['spell_ids'] ?? null) ? $payload['spell_ids'] : [];
        $intItemIds = [];
        foreach ($itemIds as $id) {
            $intId = (int) $id;
            $intItemIds[] = $intId;
            if (! in_array($intId, $allowedItems, true)) {
                $errors[] = "Objet #{$id} hors pré-filtre playable.";
            }
        }
        foreach ($spellIds as $id) {
            if (! in_array((int) $id, $allowedSpells, true)) {
                $errors[] = "Sort #{$id} hors pré-filtre de classe.";
            }
        }

        $role = is_string($npc['npc_role'] ?? null) && $npc['npc_role'] !== ''
            ? (string) $npc['npc_role']
            : 'other';
        $stats = is_array($payload['stats'] ?? null) ? $payload['stats'] : [];
        $errors = array_merge(
            $errors,
            app(NpcStatGabarit::class)->validateStats($stats, max(1, $level), $role)
        );

        if ($intItemIds !== []) {
            $items = Item::query()->with('itemType')->whereIn('id', array_values(array_unique($intItemIds)))->get();
            $errors = array_merge($errors, app(NpcEquipmentSlotValidator::class)->errorsForWornKit($items));
        }

        return $errors;
    }

    public function persist(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        return DB::transaction(function () use ($payload, $request): array {
            EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
            $row = is_array($payload['npc'] ?? null) ? $payload['npc'] : [];
            $stats = is_array($payload['stats'] ?? null) ? $payload['stats'] : [];

            $npc = $request->entityId !== null
                ? Npc::query()->with('creature')->findOrFail($request->entityId)
                : new Npc;
            $creature = $npc->creature ?? new Creature;
            $level = (string) ($row['level'] ?? $creature->level ?? '4');
            $itemIds = array_values(array_unique(array_filter(
                array_map('intval', is_array($payload['item_ids'] ?? null) ? $payload['item_ids'] : []),
                static fn (int $id): bool => $id > 0
            )));
            $spellIds = array_values(array_unique(array_filter(
                array_map('intval', is_array($payload['spell_ids'] ?? null) ? $payload['spell_ids'] : []),
                static fn (int $id): bool => $id > 0
            )));
            $creature->fill([
                'name' => (string) ($row['name'] ?? $creature->name ?? 'PNJ'),
                'description' => (string) ($row['concept'] ?? $creature->description ?? ''),
                'level' => $level,
                'state' => EntityState::Auto->value,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_GAME_MASTER,
            ]);
            $gabarit = app(NpcStatGabarit::class);
            $role = is_string($row['npc_role'] ?? null) && $row['npc_role'] !== ''
                ? (string) $row['npc_role']
                : (string) ($npc->npc_role ?: 'other');
            $expected = $gabarit->forLevelAndRole((int) $level, $role);
            $spells = Spell::query()->whereIn('id', $spellIds)->get(['id', 'element']);
            $expected = $gabarit->fillOmittedPrimaryFromElement(
                $expected,
                $stats,
                $gabarit->dominantPrimaryStatKey($spells)
            );
            foreach ($gabarit->creatureStatKeys() as $key) {
                if (array_key_exists($key, $stats) && (is_string($stats[$key]) || is_numeric($stats[$key]))) {
                    $creature->setAttribute($key, (string) $stats[$key]);
                } else {
                    $creature->setAttribute($key, $expected[$key]);
                }
            }
            $creature->save();

            $npc->creature_id = $creature->id;
            app(AllowlistWriter::class)->apply($npc, ['story', 'npc_role', 'breed_id', 'specialization_id'], [
                'story' => $row['story'] ?? $npc->story,
                'npc_role' => $row['npc_role'] ?? $npc->npc_role,
                'breed_id' => $row['breed_id'] ?? $npc->breed_id,
                'specialization_id' => $row['specialization_id'] ?? $npc->specialization_id,
            ]);

            $items = Item::query()->with('itemType')->whereIn('id', $itemIds)->get();
            app(NpcEquipmentSlotValidator::class)->assertWornKit($items);
            $sync = [];
            foreach ($itemIds as $id) {
                $sync[$id] = ['quantity' => 1];
            }
            $creature->items()->sync($sync);
            $creature->spells()->sync($spellIds);

            return [
                'entity_id' => (int) $npc->id,
                'related_ids' => array_values(array_unique([...$itemIds, ...$spellIds])),
            ];
        });
    }

    public function compactExample(Model $model): array
    {
        $npc = $model instanceof Npc ? $model : null;
        if ($npc !== null) {
            $npc->loadMissing(['creature.spells', 'specialization']);
        }
        $creature = $npc?->creature;

        return [
            'official_id' => $npc?->official_id,
            'name' => $creature?->name ?? $model->getAttribute('name'),
            'story' => $npc?->story,
            'level' => $creature?->level,
            'breed_id' => $npc?->breed_id,
            'specialization_id' => $npc?->specialization_id,
            'npc_role' => $npc?->npc_role,
            'spells' => $creature?->spells?->pluck('name')->filter()->values()->all() ?? [],
        ];
    }

    public function taskPrompt(): string
    {
        $fromConfig = GenerationConfigLoader::default()->forEntity('npc')->extra['task_prompt'] ?? null;
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return "Crée un PNJ JDR complet : nom, histoire, rôle, niveau, classe, spécialisation, kit.\n"
            ."Nom : invente un calembour ou jeu de mot façon Dofus (sonorité, métier, classe) ; n’emprunte pas un nom déjà connu du jeu.\n"
            ."Concept : une ou deux phrases qui collent au rôle, à la classe et à la voie des sorts choisis.\n"
            ."Objets et sorts : uniquement des ids des listes préfiltrées. Sorts playable de la classe choisie (spells_by_breed).\n"
            ."breed_id et specialization_id : choisis dans les listes ; une spécialisation draft ou auto est acceptée. Ne crée ni sort, ni capacité, ni aptitude.\n"
            .'Un objet par slot (deux anneaux max). Cohérence voie ↔ carac ↔ sorts. Privilégier le stuff de la même voie que les sorts.';
    }

    private function breedExists(int $breedId): bool
    {
        return Breed::query()
            ->whereKey($breedId)
            ->where('state', '!=', Breed::STATE_ARCHIVED)
            ->exists();
    }

    private function specializationExists(int $specId): bool
    {
        return SpecializationModel::query()
            ->whereKey($specId)
            ->where('state', '!=', SpecializationModel::STATE_ARCHIVED)
            ->exists();
    }

    private function sourceNpc(ConversionRequest $request): ?Npc
    {
        if ($request->entityId === null) {
            return null;
        }

        return Npc::query()->with('creature')->find($request->entityId);
    }

    private function levelOf(?Npc $npc): int
    {
        $raw = $npc?->creature?->level;
        $level = is_numeric($raw) ? (int) $raw : 4;

        return max(1, min(20, $level));
    }
}
