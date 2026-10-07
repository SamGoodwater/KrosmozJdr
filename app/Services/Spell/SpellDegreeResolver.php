<?php

declare(strict_types=1);

namespace App\Services\Spell;

use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Models\SpellDegreeEffect;
use Illuminate\Support\Collection;

/**
 * Résolution centralisée : degré actif, propriétés effectives, effets (héritage inclus).
 */
final class SpellDegreeResolver
{
    /**
     * Sélectionne un degré pour un sort.
     *
     * @param  int|null  $degreeId  Sélection manuelle (UI)
     * @param  int|null  $creatureLevel  Dernier degré dont required_level ≤ niveau
     */
    public function selectDegree(Spell $spell, ?int $degreeId = null, ?int $creatureLevel = null): ?SpellDegree
    {
        $spell->loadMissing(['degrees.effects.subEffect']);
        $degrees = $spell->degrees->sortBy('position')->values();
        if ($degrees->isEmpty()) {
            return null;
        }

        if ($degreeId !== null) {
            $manual = $degrees->firstWhere('id', $degreeId);
            if ($manual instanceof SpellDegree) {
                return $manual;
            }
        }

        if ($creatureLevel !== null) {
            $eligible = $degrees->filter(function (SpellDegree $d) use ($creatureLevel) {
                $req = $d->required_level;

                return $req === null || $creatureLevel >= $req;
            });
            if ($eligible->isNotEmpty()) {
                return $eligible->sortByDesc(fn (SpellDegree $d) => [$d->required_level ?? -1, $d->position])->first();
            }
        }

        return $degrees->first();
    }

    /**
     * Propriétés de lancement effectives selon `properties_source` du degré.
     *
     * - `spell` : valeurs du sort de base
     * - `previous` : résolution du degré précédent (ou sort si premier)
     * - `own` (défaut) : valeur du degré si non null, sinon repli sort
     *
     * @return array<string, mixed>
     */
    public function resolveProperties(Spell $spell, ?SpellDegree $degree): array
    {
        if ($degree === null) {
            return $this->propertiesFromSpell($spell, 'spell');
        }

        $source = $this->normalizePropertiesSource($degree->properties_source);

        if ($source === SpellDegree::PROPERTIES_SOURCE_SPELL) {
            return $this->propertiesFromSpell($spell, 'spell');
        }

        if ($source === SpellDegree::PROPERTIES_SOURCE_PREVIOUS) {
            $previous = $this->previousDegree($spell, $degree);
            if ($previous === null) {
                return $this->propertiesFromSpell($spell, 'spell');
            }

            $resolved = $this->resolveProperties($spell, $previous);

            return $this->retagSources($resolved, 'previous');
        }

        return $this->resolveOwnProperties($spell, $degree);
    }

    /**
     * Degré immédiatement précédent (position inférieure).
     */
    public function previousDegree(Spell $spell, SpellDegree $degree): ?SpellDegree
    {
        $spell->loadMissing('degrees');
        $ordered = $spell->degrees->sortBy('position')->values();
        $idx = $ordered->search(fn (SpellDegree $d) => $d->id === $degree->id);
        if ($idx === false || $idx < 1) {
            return null;
        }

        /** @var SpellDegree $previous */
        $previous = $ordered[(int) $idx - 1];

        return $previous;
    }

    /**
     * Effets effectivement affichés / joués pour le degré (héritage depuis le dernier degré personnalisé).
     *
     * @return Collection<int, SpellDegreeEffect>
     */
    public function resolveEffects(Spell $spell, ?SpellDegree $degree): Collection
    {
        if ($degree === null) {
            return collect();
        }

        $spell->loadMissing(['degrees.effects.subEffect']);
        $source = $this->effectsSourceDegree($spell, $degree);
        if ($source === null) {
            return collect();
        }

        $source->loadMissing('effects.subEffect');

        return $source->effects->sortBy('order')->values();
    }

    /**
     * Degré qui porte réellement les lignes d’effets (celui-ci ou un ancêtre non héritant).
     */
    public function effectsSourceDegree(Spell $spell, SpellDegree $degree): ?SpellDegree
    {
        $spell->loadMissing('degrees');
        $ordered = $spell->degrees->sortBy('position')->values();
        $idx = $ordered->search(fn (SpellDegree $d) => $d->id === $degree->id);
        if ($idx === false) {
            return $degree->inherits_effects ? null : $degree;
        }

        for ($i = (int) $idx; $i >= 0; $i--) {
            /** @var SpellDegree $candidate */
            $candidate = $ordered[$i];
            if (! $candidate->inherits_effects) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Snapshot complet pour API / UI.
     *
     * @return array{
     *     degree: array<string, mixed>|null,
     *     properties: array<string, mixed>,
     *     effects: list<array<string, mixed>>,
     *     inherits_effects: bool,
     *     effects_source_degree_id: int|null
     * }
     */
    public function snapshot(Spell $spell, ?int $degreeId = null, ?int $creatureLevel = null): array
    {
        $degree = $this->selectDegree($spell, $degreeId, $creatureLevel);
        $source = $degree !== null ? $this->effectsSourceDegree($spell, $degree) : null;
        $effects = $this->resolveEffects($spell, $degree);

        return [
            'degree' => $degree === null ? null : [
                'id' => $degree->id,
                'position' => $degree->position,
                'required_level' => $degree->required_level,
                'inherits_effects' => (bool) $degree->inherits_effects,
                'properties_source' => $this->normalizePropertiesSource($degree->properties_source),
            ],
            'properties' => $this->resolveProperties($spell, $degree),
            'effects' => $effects->map(fn (SpellDegreeEffect $row) => $this->serializeEffectRow($row))->all(),
            'inherits_effects' => $degree !== null && (bool) $degree->inherits_effects,
            'effects_source_degree_id' => $source?->id,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeEffectRow(SpellDegreeEffect $row): array
    {
        $row->loadMissing('subEffect');
        $sub = $row->subEffect;

        return [
            'id' => $row->id,
            'order' => $row->order,
            'scope' => $row->scope,
            'value_min' => $row->value_min,
            'value_max' => $row->value_max,
            'dice_num' => $row->dice_num,
            'dice_side' => $row->dice_side,
            'duration_formula' => $row->duration_formula,
            'logic_group' => $row->logic_group,
            'logic_operator' => $row->logic_operator,
            'logic_condition' => $row->logic_condition,
            'crit_only' => (bool) $row->crit_only,
            'params' => is_array($row->params) ? $row->params : [],
            'sub_effect' => $sub ? [
                'id' => $sub->id,
                'slug' => $sub->slug,
                'type_slug' => $sub->type_slug,
                'template_text' => $sub->template_text,
                'param_schema' => $sub->param_schema,
            ] : null,
            'sub_effect_id' => $row->sub_effect_id,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveOwnProperties(Spell $spell, SpellDegree $degree): array
    {
        $out = [];
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            if ($key === 'area') {
                $value = $degree->area;
                if ($value === null || $value === '') {
                    $storedArea = $spell->getRawOriginal('area');
                    $value = is_string($storedArea) && $storedArea !== '' ? $storedArea : null;
                }
                $out[$key] = $value;
                $out[$key.'_source'] = $degree->area !== null && $degree->area !== ''
                    ? 'degree'
                    : ($value !== null ? 'spell' : 'none');

                continue;
            }

            $degreeValue = $degree->getAttribute($key);
            if ($degreeValue !== null) {
                $out[$key] = $degreeValue;
                $out[$key.'_source'] = 'degree';

                continue;
            }

            $out[$key] = $spell->getAttribute($key);
            $out[$key.'_source'] = 'spell';
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    private function propertiesFromSpell(Spell $spell, string $sourceTag): array
    {
        $out = [];
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            if ($key === 'area') {
                $storedArea = $spell->getRawOriginal('area');
                $value = is_string($storedArea) && $storedArea !== '' ? $storedArea : null;
                $out[$key] = $value;
                $out[$key.'_source'] = $value !== null ? $sourceTag : 'none';

                continue;
            }

            $out[$key] = $spell->getAttribute($key);
            $out[$key.'_source'] = $sourceTag;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    private function retagSources(array $properties, string $sourceTag): array
    {
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            $srcKey = $key.'_source';
            if (($properties[$srcKey] ?? null) === 'none') {
                continue;
            }
            $properties[$srcKey] = $sourceTag;
        }

        return $properties;
    }

    private function normalizePropertiesSource(mixed $raw): string
    {
        $value = is_string($raw) ? trim($raw) : SpellDegree::PROPERTIES_SOURCE_OWN;
        if (! in_array($value, SpellDegree::PROPERTIES_SOURCES, true)) {
            return SpellDegree::PROPERTIES_SOURCE_OWN;
        }

        return $value;
    }
}
