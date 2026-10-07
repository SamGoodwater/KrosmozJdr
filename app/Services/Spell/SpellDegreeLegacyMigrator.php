<?php

declare(strict_types=1);

namespace App\Services\Spell;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Models\SpellDegreeEffect;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Migration idempotente Effect/EffectDegree → SpellDegree pour les sorts.
 */
final class SpellDegreeLegacyMigrator
{
    /**
     * @return array{
     *     migrated: int,
     *     skipped: int,
     *     conflicts: list<array<string, mixed>>,
     *     shared_effects_duplicated: int
     * }
     */
    public function migrateAll(bool $dryRun = false): array
    {
        $report = [
            'migrated' => 0,
            'skipped' => 0,
            'conflicts' => [],
            'shared_effects_duplicated' => 0,
        ];

        Spell::query()
            ->with(['effects.degrees.effectSubEffects.subEffect'])
            ->orderBy('id')
            ->chunkById(50, function (Collection $spells) use (&$report, $dryRun): void {
                foreach ($spells as $spell) {
                    $result = $this->migrateSpell($spell, $dryRun);
                    $report['migrated'] += $result['migrated'] ? 1 : 0;
                    $report['skipped'] += $result['skipped'] ? 1 : 0;
                    $report['shared_effects_duplicated'] += $result['shared_effects_duplicated'];
                    foreach ($result['conflicts'] as $conflict) {
                        $report['conflicts'][] = $conflict;
                    }
                }
            });

        return $report;
    }

    /**
     * Remplace les degrés natifs d’un sort à partir des effets legacy (scraping / re-sync).
     *
     * @return array{migrated: bool, skipped: bool, shared_effects_duplicated: int, conflicts: list<array<string, mixed>>}
     */
    public function rebuildFromLegacy(Spell $spell, bool $dryRun = false): array
    {
        if (! $dryRun) {
            SpellDegree::query()->where('spell_id', $spell->id)->delete();
            $spell->unsetRelation('degrees');
        }

        return $this->migrateSpell($spell->fresh(['effects.degrees.effectSubEffects', 'degrees']) ?? $spell, $dryRun);
    }

    /**
     * @return array{
     *     migrated: bool,
     *     skipped: bool,
     *     shared_effects_duplicated: int,
     *     conflicts: list<array<string, mixed>>
     * }
     */
    public function migrateSpell(Spell $spell, bool $dryRun = false): array
    {
        $spell->loadMissing(['effects.degrees.effectSubEffects', 'degrees']);

        if ($spell->degrees->isNotEmpty()) {
            return [
                'migrated' => false,
                'skipped' => true,
                'shared_effects_duplicated' => 0,
                'conflicts' => [],
            ];
        }

        $effects = $spell->effects;
        if ($effects->isEmpty()) {
            return [
                'migrated' => false,
                'skipped' => true,
                'shared_effects_duplicated' => 0,
                'conflicts' => [],
            ];
        }

        $conflicts = [];
        $shared = 0;
        foreach ($effects as $effect) {
            $spellCount = $effect->spells()->count();
            if ($spellCount > 1) {
                $shared++;
                $conflicts[] = [
                    'type' => 'shared_effect',
                    'spell_id' => $spell->id,
                    'effect_id' => $effect->id,
                    'linked_spells' => $spellCount,
                    'message' => 'Effet partagé entre plusieurs sorts — données dupliquées dans spell_degrees.',
                ];
            }
        }

        $levels = $this->collectLevelThresholds($effects);
        $built = $this->buildDegreePayloads($spell, $effects, $levels, $conflicts);

        if ($dryRun) {
            return [
                'migrated' => true,
                'skipped' => false,
                'shared_effects_duplicated' => $shared,
                'conflicts' => $conflicts,
            ];
        }

        DB::transaction(function () use ($built): void {
            foreach ($built as $payload) {
                $effectsRows = $payload['effects'];
                unset($payload['effects']);
                $degree = SpellDegree::query()->create($payload);
                foreach ($effectsRows as $i => $row) {
                    SpellDegreeEffect::query()->create(array_merge($row, [
                        'spell_degree_id' => $degree->id,
                        'order' => $row['order'] ?? $i,
                    ]));
                }
            }
        });

        Log::info('spell_degrees.migrated', [
            'spell_id' => $spell->id,
            'degrees' => count($built),
            'conflicts' => count($conflicts),
        ]);

        return [
            'migrated' => true,
            'skipped' => false,
            'shared_effects_duplicated' => $shared,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * @param  Collection<int, Effect>  $effects
     * @return list<int|null>
     */
    private function collectLevelThresholds(Collection $effects): array
    {
        $levels = [];
        foreach ($effects as $effect) {
            foreach ($effect->degrees as $degree) {
                $levels[$degree->required_creature_level === null ? 'null' : (string) $degree->required_creature_level]
                    = $degree->required_creature_level;
            }
        }
        $values = array_values($levels);
        usort($values, static function ($a, $b): int {
            if ($a === null && $b === null) {
                return 0;
            }
            if ($a === null) {
                return -1;
            }
            if ($b === null) {
                return 1;
            }

            return $a <=> $b;
        });

        return $values !== [] ? $values : [null];
    }

    /**
     * @param  Collection<int, Effect>  $effects
     * @param  list<int|null>  $levels
     * @param  list<array<string, mixed>>  $conflicts
     * @return list<array<string, mixed>>
     */
    private function buildDegreePayloads(Spell $spell, Collection $effects, array $levels, array &$conflicts): array
    {
        $payloads = [];
        $position = 1;
        $previousArea = null;

        foreach ($levels as $level) {
            $mergedEffects = [];
            $area = null;
            $order = 0;

            foreach ($effects as $effectIndex => $effect) {
                $picked = $this->pickLegacyDegree($effect, $level);
                if ($picked === null) {
                    continue;
                }
                if ($area === null && $picked->area) {
                    $area = $picked->area;
                } elseif ($picked->area && $area && $picked->area !== $area) {
                    $conflicts[] = [
                        'type' => 'area_mismatch',
                        'spell_id' => $spell->id,
                        'level' => $level,
                        'kept_area' => $area,
                        'ignored_area' => $picked->area,
                        'effect_id' => $effect->id,
                        'message' => 'Zones divergentes entre effets — zone du premier effet conservée.',
                    ];
                }

                foreach ($picked->effectSubEffects->sortBy('order') as $sub) {
                    $mergedEffects[] = [
                        'sub_effect_id' => $sub->sub_effect_id,
                        'order' => $order++,
                        'scope' => $sub->scope ?? 'general',
                        'value_min' => $sub->value_min,
                        'value_max' => $sub->value_max,
                        'dice_num' => $sub->dice_num,
                        'dice_side' => $sub->dice_side,
                        'duration_formula' => $sub->duration_formula,
                        'logic_group' => $sub->logic_group,
                        'logic_operator' => $effectIndex > 0 && $order === 1
                            ? ($sub->logic_operator ?: 'AND')
                            : $sub->logic_operator,
                        'logic_condition' => $sub->logic_condition,
                        'crit_only' => (bool) $sub->crit_only,
                        'params' => $sub->params,
                    ];
                }
            }

            $props = $this->propertiesFromSpell($spell);
            $props['area'] = $area;

            // target_type appartient au sort : renseigner depuis le premier effet legacy si absent.
            if (($spell->target_type === null || $spell->target_type === '') && $effects->isNotEmpty()) {
                $inferredTarget = $effects->first()->target_type ?: Effect::TARGET_DIRECT;
                $spell->forceFill(['target_type' => $inferredTarget])->save();
            }

            // Héritage : si mêmes effets que le degré précédent, cocher inherits.
            $inherits = false;
            if ($position > 1 && $previousArea === $area) {
                $prevEffects = $payloads[$position - 2]['effects'] ?? [];
                if ($this->effectsSignature($prevEffects) === $this->effectsSignature($mergedEffects)
                    && $mergedEffects !== []
                ) {
                    $inherits = true;
                    $mergedEffects = [];
                }
            }

            $payloads[] = array_merge($props, [
                'spell_id' => $spell->id,
                'position' => $position,
                'required_level' => $level,
                'inherits_effects' => $inherits || ($mergedEffects === [] && $position > 1),
                'effects' => $inherits ? [] : $mergedEffects,
            ]);
            if (! $inherits) {
                $previousArea = $area;
            }
            $position++;
        }

        // Premier degré ne doit jamais hériter.
        if ($payloads !== []) {
            $payloads[0]['inherits_effects'] = false;
        }

        return $payloads;
    }

    private function pickLegacyDegree(Effect $effect, ?int $level): ?EffectDegree
    {
        $degrees = $effect->degrees->sortBy('degree')->values();
        if ($degrees->isEmpty()) {
            return null;
        }

        if ($level === null) {
            return $degrees->first(fn (EffectDegree $d) => $d->required_creature_level === null)
                ?? $degrees->first();
        }

        $eligible = $degrees->filter(function (EffectDegree $d) use ($level) {
            $req = $d->required_creature_level;

            return $req === null || $level >= $req;
        });

        if ($eligible->isEmpty()) {
            return null;
        }

        return $eligible->sortByDesc(fn (EffectDegree $d) => [$d->required_creature_level ?? -1, $d->degree])->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function propertiesFromSpell(Spell $spell): array
    {
        $out = [];
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            if ($key === 'area') {
                $out[$key] = null;

                continue;
            }
            $out[$key] = $spell->getAttribute($key);
        }

        return $out;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function effectsSignature(array $rows): string
    {
        $normalized = array_map(static function (array $row): array {
            return [
                'sub_effect_id' => $row['sub_effect_id'] ?? null,
                'scope' => $row['scope'] ?? 'general',
                'params' => $row['params'] ?? null,
                'crit_only' => (bool) ($row['crit_only'] ?? false),
                'duration_formula' => $row['duration_formula'] ?? null,
                'logic_operator' => $row['logic_operator'] ?? null,
                'logic_condition' => $row['logic_condition'] ?? null,
                'value_min' => $row['value_min'] ?? null,
                'value_max' => $row['value_max'] ?? null,
            ];
        }, $rows);

        return hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE) ?: '');
    }
}
