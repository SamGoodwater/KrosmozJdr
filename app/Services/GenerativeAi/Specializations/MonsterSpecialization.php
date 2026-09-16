<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi\Specializations;

use App\Enums\EntityState;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\GenerativeAi\AllowlistWriter;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\CreationGuideCatalog;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\GenerationConfigLoader;
use App\Support\ElementBitmask;
use App\Support\Entity\EntityStateGate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Spécialisation rencontre : `{ monster, spells: [2-3] }` persisté en `auto`.
 */
final class MonsterSpecialization implements Specialization
{
    public function key(): string
    {
        return 'encounter';
    }

    public function entityType(): string
    {
        return 'monster';
    }

    public function jsonSchema(EntityGenerationProfile $profile, ?ConversionRequest $request = null): array
    {
        $monsterProps = [];
        foreach ($profile->writableFields as $field) {
            $monsterProps[$field] = ['type' => ['string', 'number', 'boolean', 'null']];
        }

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['spells'],
            'properties' => [
                'monster' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => $monsterProps === [] ? new \stdClass : $monsterProps,
                ],
                'spells' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 3,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['name', 'effect'],
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'effect' => ['type' => 'string'],
                            'pa' => ['type' => 'string'],
                            'po_min' => ['type' => 'string'],
                            'po_max' => ['type' => 'string'],
                            'element' => ['type' => 'string'],
                            'kind' => ['type' => 'string'],
                            'attack_characteristic_key' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function extraContext(ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        return [];
    }

    public function preflight(ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        return [];
    }

    public function validate(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        $errors = [];
        $spells = $payload['spells'] ?? null;
        if (! is_array($spells)) {
            return ['Le paquet doit contenir spells (2 à 3).'];
        }
        $count = count($spells);
        if ($count < 2 || $count > 3) {
            $errors[] = 'Une rencontre a 2 ou 3 sorts-créature.';
        }

        $maxEffects = (int) (GenerationConfigLoader::default()->get('generation.max_effects_per_spell', 3) ?: 3);
        $creature = $this->creatureFor($request);
        $paBudget = $creature !== null && is_numeric($creature->pa) ? (int) $creature->pa : 6;
        $paSum = 0;

        foreach ($spells as $index => $spell) {
            if (! is_array($spell)) {
                $errors[] = "Sort {$index} invalide.";

                continue;
            }
            $name = is_string($spell['name'] ?? null) ? trim($spell['name']) : '';
            $effect = is_string($spell['effect'] ?? null) ? trim($spell['effect']) : '';
            if ($name === '') {
                $errors[] = "Sort {$index} : nom requis.";
            }
            if ($effect === '') {
                $errors[] = "Sort {$index} : effect requis.";
            }
            $parts = preg_split('/[\n;•]+/u', $effect) ?: [];
            $parts = array_values(array_filter(array_map('trim', $parts)));
            if (count($parts) > $maxEffects) {
                $errors[] = "Sort « {$name} » : trop d’effets ({$maxEffects} max).";
            }
            $pa = is_numeric($spell['pa'] ?? null) ? (int) $spell['pa'] : 3;
            $paSum += max(0, $pa);

            $element = is_string($spell['element'] ?? null) ? strtolower(trim($spell['element'])) : '';
            if ($creature !== null && $element !== '') {
                $mismatch = $this->elementMismatch($creature, $element);
                if ($mismatch !== null) {
                    $errors[] = "Sort « {$name} » : {$mismatch}";
                }
            }
        }

        if ($paBudget > 0 && $paSum > $paBudget * 2) {
            $errors[] = "Budget PA trop élevé ({$paSum} pour {$paBudget} PA de créature).";
        }

        $monster = $payload['monster'] ?? [];
        if ($monster !== [] && ! is_array($monster)) {
            $errors[] = 'monster doit être un objet.';
        } elseif (is_array($monster)) {
            foreach (array_keys($monster) as $key) {
                if (! in_array($key, $profile->writableFields, true)) {
                    $errors[] = "Champ monstre non autorisé : {$key}.";
                }
            }
        }

        return $errors;
    }

    public function persist(array $payload, ConversionRequest $request, EntityGenerationProfile $profile): array
    {
        if ($request->entityId === null) {
            throw new RuntimeException('Monstre source requis pour une rencontre.');
        }

        return DB::transaction(function () use ($payload, $request, $profile): array {
            EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);

            $monster = Monster::query()->with('creature')->findOrFail($request->entityId);
            $creature = $monster->creature;
            if ($creature === null) {
                throw new RuntimeException('Le monstre n’a pas de créature liée.');
            }

            $spellIds = [];
            foreach ($payload['spells'] as $index => $row) {
                if (! is_array($row)) {
                    continue;
                }
                $spellIds[] = $this->createCreatureSpell($monster, $creature, $row, (int) $index)->id;
            }

            $creature->spells()->sync($spellIds);
            $creature->state = EntityState::Auto->value;
            $creature->save();

            $monsterPayload = is_array($payload['monster'] ?? null) ? $payload['monster'] : [];
            app(AllowlistWriter::class)->apply($monster, $profile->writableFields, $monsterPayload);

            return [
                'entity_id' => (int) $monster->id,
                'related_ids' => array_map(static fn ($id): int => (int) $id, $spellIds),
            ];
        });
    }

    public function compactExample(Model $model): array
    {
        $monster = $model instanceof Monster ? $model : null;
        $creature = $monster?->creature;
        $spells = [];
        if ($creature !== null) {
            foreach ($creature->spells as $spell) {
                $spells[] = [
                    'name' => $spell->name,
                    'effect' => $spell->effect,
                    'pa' => $spell->pa,
                ];
            }
        }

        return [
            'official_id' => $monster?->official_id,
            'name' => $creature?->name ?? $model->getAttribute('name'),
            'level' => $creature?->level,
            'life' => $creature?->life,
            'pa' => $creature?->pa,
            'pm' => $creature?->pm,
            'strong' => $creature?->strong,
            'intel' => $creature?->intel,
            'agi' => $creature?->agi,
            'chance' => $creature?->chance,
            'spells' => $spells,
        ];
    }

    public function taskPrompt(): string
    {
        $fromConfig = GenerationConfigLoader::default()->forEntity('monster')->extra['task_prompt'] ?? null;
        if (is_string($fromConfig) && trim($fromConfig) !== '') {
            return trim($fromConfig);
        }
        $guide = app(CreationGuideCatalog::class)->promptFor('monster');

        return trim((string) $guide."\n\nGénère UNE rencontre : la fiche monstre (clés writable seulement) et 2 à 3 sorts-créature cohérents avec ses caracs. Pas de sorts de classe.");
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function createCreatureSpell(Monster $monster, Creature $creature, array $row, int $index): Spell
    {
        $name = trim((string) ($row['name'] ?? 'Sort'));
        $slug = Str::slug($name) ?: 'sort';
        $officialId = 'ia:encounter:'.$monster->id.':'.$slug.':'.$index;
        $elementSlug = is_string($row['element'] ?? null) ? (string) $row['element'] : '';
        $element = $elementSlug !== '' ? ElementBitmask::fromSlug($elementSlug) : null;
        $isHeal = strtolower((string) ($row['kind'] ?? '')) === 'soigner';

        $spell = new Spell;
        $spell->fill([
            'name' => $name,
            'description' => 'Sort de créature généré pour « '.($creature->name ?: $name).' ».',
            'effect' => trim((string) ($row['effect'] ?? '')),
            'level' => (string) ($creature->level ?: '1'),
            'pa' => (string) ($row['pa'] ?? '3'),
            'po_min' => (string) ($row['po_min'] ?? '1'),
            'po_max' => (string) ($row['po_max'] ?? '1'),
            'po_editable' => false,
            'sight_line' => true,
            'cast_per_turn' => '1',
            'cast_per_target' => '0',
            'number_between_two_cast' => '0',
            'cast_in_line' => false,
            'cast_in_diagonal' => false,
            'target_type' => 'direct',
            'max_stack' => 0,
            'global_cooldown' => 0,
            'element' => $element ?? 0,
            'category' => Spell::CATEGORY_CREATURE,
            'is_magic' => ! $isHeal,
            'powerful' => 0,
            'resolution_mode' => 'attack_roll',
            'attack_characteristic_key' => $isHeal ? null : (is_string($row['attack_characteristic_key'] ?? null) ? $row['attack_characteristic_key'] : $this->attackKeyForElement($elementSlug)),
            'allows_reaction' => false,
            'official_id' => $officialId,
            'state' => EntityState::Auto->value,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'auto_update' => false,
        ]);
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);
        $spell->save();

        return $spell;
    }

    private function creatureFor(ConversionRequest $request): ?Creature
    {
        if ($request->entityId === null) {
            return null;
        }
        $monster = Monster::query()->with('creature')->find($request->entityId);

        return $monster?->creature;
    }

    private function elementMismatch(Creature $creature, string $element): ?string
    {
        $stat = match (true) {
            str_contains($element, 'earth') || str_contains($element, 'terre') => 'strong',
            str_contains($element, 'fire') || str_contains($element, 'feu') => 'intel',
            str_contains($element, 'water') || str_contains($element, 'eau') => 'chance',
            str_contains($element, 'air') => 'agi',
            default => null,
        };
        if ($stat === null) {
            return null;
        }
        $value = is_numeric($creature->{$stat}) ? (int) $creature->{$stat} : 0;
        if ($value > 0) {
            return null;
        }

        return "élément {$element} incohérent avec {$stat}={$value}.";
    }

    private function attackKeyForElement(string $element): ?string
    {
        return match (true) {
            str_contains($element, 'earth') || str_contains($element, 'terre') => 'strong',
            str_contains($element, 'fire') || str_contains($element, 'feu') => 'intel',
            str_contains($element, 'water') || str_contains($element, 'eau') => 'chance',
            str_contains($element, 'air') => 'agi',
            default => 'strong',
        };
    }
}
