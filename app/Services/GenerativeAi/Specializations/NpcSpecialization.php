<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Enums\EntityState;
use App\Models\Entity\Creature;
use App\Models\Entity\Npc;
use App\Models\User;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Services\GenerativeAi\NpcKitCatalog;
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
                'spells' => $catalog['spells'],
            ],
            'consigne' => 'item_ids et spell_ids : uniquement des id de kit_catalog. Un objet par slot (deux anneaux max).',
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

        $breedId = is_numeric($npc['breed_id'] ?? null) ? (int) $npc['breed_id'] : null;
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
        foreach ($itemIds as $id) {
            if (! in_array((int) $id, $allowedItems, true)) {
                $errors[] = "Objet #{$id} hors pré-filtre playable.";
            }
        }
        foreach ($spellIds as $id) {
            if (! in_array((int) $id, $allowedSpells, true)) {
                $errors[] = "Sort #{$id} hors pré-filtre de classe.";
            }
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
            $creature->fill([
                'name' => (string) ($row['name'] ?? $creature->name ?? 'PNJ'),
                'description' => (string) ($row['concept'] ?? $creature->description ?? ''),
                'level' => $level,
                'state' => EntityState::Auto->value,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_GAME_MASTER,
            ]);
            $statKeys = ['life', 'pa', 'pm', 'ca', 'strong', 'intel', 'agi', 'chance', 'vitality', 'sagesse'];
            foreach ($statKeys as $key) {
                if (array_key_exists($key, $stats) && (is_string($stats[$key]) || is_numeric($stats[$key]))) {
                    $creature->setAttribute($key, (string) $stats[$key]);
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

            $itemIds = array_values(array_unique(array_filter(
                array_map('intval', is_array($payload['item_ids'] ?? null) ? $payload['item_ids'] : []),
                static fn (int $id): bool => $id > 0
            )));
            $spellIds = array_values(array_unique(array_filter(
                array_map('intval', is_array($payload['spell_ids'] ?? null) ? $payload['spell_ids'] : []),
                static fn (int $id): bool => $id > 0
            )));
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
        $creature = $npc?->creature;

        return [
            'official_id' => $npc?->official_id,
            'name' => $creature?->name ?? $model->getAttribute('name'),
            'story' => $npc?->story,
            'level' => $creature?->level,
            'breed_id' => $npc?->breed_id,
            'npc_role' => $npc?->npc_role,
        ];
    }

    public function taskPrompt(): string
    {
        $fromConfig = GenerationConfigLoader::default()->forEntity('npc')->extra['task_prompt'] ?? null;
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }

        return "Crée un PNJ JDR complet : nom, histoire, rôle, niveau, classe, kit.\n"
            ."Objets et sorts : uniquement des ids des listes préfiltrées playable.\n"
            .'Un objet par slot (deux anneaux max). Cohérence voie ↔ carac ↔ sorts.';
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
