<?php

declare(strict_types=1);

namespace App\Services\Spell;

use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Support\Facades\DB;

/**
 * CRUD transactionnel des degrés et effets d’un sort.
 */
final class SpellDegreeService
{
    public function __construct(
        private readonly SpellDegreeResolver $resolver
    ) {}

    /**
     * Crée un degré en copiant les propriétés effectives du précédent (ou du sort).
     *
     * @param  array<string, mixed>  $overrides
     */
    public function createDegree(Spell $spell, array $overrides = []): SpellDegree
    {
        return DB::transaction(function () use ($spell, $overrides): SpellDegree {
            $spell->loadMissing('degrees');
            $orderedDegrees = $spell->degrees->sortBy('position');
            $previous = $orderedDegrees->isEmpty() ? null : $orderedDegrees->last();
            $position = (int) ($previous !== null ? $previous->position : 0) + 1;

            $props = $this->resolver->resolveProperties($spell, $previous);
            $payload = [
                'spell_id' => $spell->id,
                'position' => $position,
                'required_level' => $overrides['required_level']
                    ?? (($previous !== null ? ($previous->required_level ?? 0) : 0) + 1),
                'inherits_effects' => array_key_exists('inherits_effects', $overrides)
                    ? (bool) $overrides['inherits_effects']
                    : ($previous !== null),
            ];

            foreach (SpellDegree::PROPERTY_KEYS as $key) {
                if (array_key_exists($key, $overrides)) {
                    $payload[$key] = $overrides[$key];
                } else {
                    $payload[$key] = $props[$key] ?? null;
                }
            }

            if ($previous === null) {
                $payload['inherits_effects'] = false;
            }

            $degree = SpellDegree::query()->create($payload);

            if (! $degree->inherits_effects && $previous !== null && empty($overrides['skip_copy_effects'])) {
                $this->copyEffectsFromSource($spell, $previous, $degree);
            }

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDegree(SpellDegree $degree, array $data): SpellDegree
    {
        return DB::transaction(function () use ($degree, $data): SpellDegree {
            $allowed = array_merge(['required_level', 'inherits_effects'], SpellDegree::PROPERTY_KEYS);
            $payload = [];
            foreach ($allowed as $key) {
                if (array_key_exists($key, $data)) {
                    $payload[$key] = $data[$key];
                }
            }

            if (array_key_exists('inherits_effects', $payload) && $payload['inherits_effects'] === true) {
                $degree->effects()->delete();
            }

            $degree->update($payload);

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    /**
     * Matérialise les effets hérités pour permettre l’édition locale.
     */
    public function materializeEffects(SpellDegree $degree): SpellDegree
    {
        return DB::transaction(function () use ($degree): SpellDegree {
            $spell = $degree->spell()->with('degrees.effects.subEffect')->firstOrFail();
            if (! $degree->inherits_effects) {
                return $degree->fresh(['effects.subEffect']) ?? $degree;
            }

            $source = $this->resolver->effectsSourceDegree($spell, $degree);
            $degree->update(['inherits_effects' => false]);
            $degree->effects()->delete();

            if ($source !== null && $source->id !== $degree->id) {
                $this->copyEffectsFromSource($spell, $source, $degree);
            }

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    public function syncEffects(SpellDegree $degree, array $rows): SpellDegree
    {
        return DB::transaction(function () use ($degree, $rows): SpellDegree {
            if ($degree->inherits_effects) {
                $this->materializeEffects($degree);
                $degree->refresh();
            }

            $degree->effects()->delete();
            $sanitizer = new EffectTextSanitizer;
            foreach ($rows as $i => $row) {
                $params = $row['params'] ?? null;
                if (is_array($params)) {
                    foreach (['value_formula', 'value_formula_crit', 'life_steal_formula', 'cells_formula'] as $fk) {
                        if (! empty($params[$fk]) && is_string($params[$fk])) {
                            $params[$fk] = $sanitizer->sanitize($params[$fk]);
                        }
                    }
                }
                $duration = $row['duration_formula'] ?? null;
                if (is_string($duration) && $duration !== '') {
                    $duration = $sanitizer->sanitize($duration);
                }
                $logicCondition = $row['logic_condition'] ?? null;
                if (is_string($logicCondition) && $logicCondition !== '') {
                    $logicCondition = $sanitizer->sanitize($logicCondition);
                }

                $degree->effects()->create([
                    'sub_effect_id' => $row['sub_effect_id'],
                    'order' => $row['order'] ?? $i,
                    'scope' => $row['scope'] ?? 'general',
                    'value_min' => $row['value_min'] ?? null,
                    'value_max' => $row['value_max'] ?? null,
                    'dice_num' => $row['dice_num'] ?? null,
                    'dice_side' => $row['dice_side'] ?? null,
                    'duration_formula' => $duration,
                    'logic_group' => $row['logic_group'] ?? null,
                    'logic_operator' => $row['logic_operator'] ?? null,
                    'logic_condition' => $logicCondition,
                    'crit_only' => (bool) ($row['crit_only'] ?? false),
                    'params' => $params,
                ]);
            }

            return $degree->fresh(['effects.subEffect']) ?? $degree;
        });
    }

    public function deleteDegree(SpellDegree $degree): void
    {
        DB::transaction(function () use ($degree): void {
            $spellId = $degree->spell_id;
            $degree->delete();
            $this->renumberPositions($spellId);
        });
    }

    public function renumberPositions(int $spellId): void
    {
        $degrees = SpellDegree::query()
            ->where('spell_id', $spellId)
            ->orderBy('position')
            ->orderBy('id')
            ->get();
        $pos = 1;
        foreach ($degrees as $degree) {
            if ((int) $degree->position !== $pos) {
                $degree->update(['position' => $pos]);
            }
            $pos++;
        }
    }

    private function copyEffectsFromSource(Spell $spell, SpellDegree $source, SpellDegree $target): void
    {
        $rows = $this->resolver->resolveEffects($spell, $source);
        foreach ($rows as $i => $row) {
            $target->effects()->create([
                'sub_effect_id' => $row->sub_effect_id,
                'order' => $row->order ?? $i,
                'scope' => $row->scope ?? 'general',
                'value_min' => $row->value_min,
                'value_max' => $row->value_max,
                'dice_num' => $row->dice_num,
                'dice_side' => $row->dice_side,
                'duration_formula' => $row->duration_formula,
                'logic_group' => $row->logic_group,
                'logic_operator' => $row->logic_operator,
                'logic_condition' => $row->logic_condition,
                'crit_only' => (bool) $row->crit_only,
                'params' => $row->params,
            ]);
        }
    }

    /**
     * Propriétés du sort à copier vers un premier degré.
     *
     * @return array<string, mixed>
     */
    public function propertiesFromSpell(Spell $spell): array
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
}
