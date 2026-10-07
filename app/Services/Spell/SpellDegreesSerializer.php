<?php

declare(strict_types=1);

namespace App\Services\Spell;

use App\Models\Entity\Condition;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\SpellDegree;
use App\Models\SpellDegreeEffect;
use App\Support\DofusHyperlinkText;
use Illuminate\Support\Collection;

/**
 * Sérialise la progression des degrés d’un sort pour API / vues.
 */
final class SpellDegreesSerializer
{
    public function __construct(
        private readonly SpellDegreeResolver $resolver
    ) {}

    /**
     * @return array{
     *     degrees: list<array<string, mixed>>,
     *     default_degree_id: int|null
     * }
     */
    public function serialize(Spell $spell): array
    {
        $spell->loadMissing(['degrees.effects.subEffect']);
        $degrees = $spell->degrees->sortBy('position')->values();
        if ($degrees->isEmpty()) {
            return [
                'degrees' => [],
                'default_degree_id' => null,
            ];
        }

        $monsterIds = $this->collectParamInts($spell, $degrees, 'monster_id');
        $monstersById = $this->loadMonstersById($monsterIds);
        $creatureIds = $this->collectParamInts($spell, $degrees, 'creature_id');
        $creaturesById = $this->loadCreaturesById($creatureIds);
        $conditionIds = $this->collectParamInts($spell, $degrees, 'condition_id');
        $conditionDofusdbIds = $this->collectParamInts($spell, $degrees, 'condition_dofusdb_id');
        $conditionsById = $this->loadConditionsById($conditionIds);
        $conditionsByDofusdbId = $this->loadConditionsByDofusdbId($conditionDofusdbIds);

        $serialized = $degrees->map(function (SpellDegree $degree) use (
            $spell,
            $monstersById,
            $creaturesById,
            $conditionsById,
            $conditionsByDofusdbId
        ) {
            $properties = $this->resolver->resolveProperties($spell, $degree);
            $effects = $this->resolver->resolveEffects($spell, $degree);
            $source = $this->resolver->effectsSourceDegree($spell, $degree);

            $propertiesSource = in_array(
                (string) $degree->properties_source,
                SpellDegree::PROPERTIES_SOURCES,
                true
            )
                ? (string) $degree->properties_source
                : SpellDegree::PROPERTIES_SOURCE_OWN;

            return [
                'id' => $degree->id,
                'position' => $degree->position,
                'required_level' => $degree->required_level,
                'inherits_effects' => (bool) $degree->inherits_effects,
                'properties_source' => $propertiesSource,
                'effects_source_degree_id' => $source?->id,
                'properties' => $this->publicProperties($properties),
                'resolved_properties' => $this->publicProperties($properties),
                'property_sources' => $this->propertySources($properties),
                'area' => $properties['area'] ?? null,
                'rows' => $effects->map(function (SpellDegreeEffect $pivot) use (
                    $monstersById,
                    $creaturesById,
                    $conditionsById,
                    $conditionsByDofusdbId
                ) {
                    $base = $this->resolver->serializeEffectRow($pivot);
                    $params = $base['params'];

                    return array_merge($base, [
                        // Clé historique conservée pour les présentateurs existants.
                        'summon_monster' => $this->summonCreatureBrief($params, $creaturesById, $monstersById),
                        'condition' => $this->conditionBrief($params, $conditionsById, $conditionsByDofusdbId),
                    ]);
                })->values()->all(),
            ];
        })->all();

        return [
            'degrees' => $serialized,
            'default_degree_id' => $degrees->first()->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    private function publicProperties(array $properties): array
    {
        $out = [];
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            $out[$key] = $properties[$key] ?? null;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, string>
     */
    private function propertySources(array $properties): array
    {
        $out = [];
        foreach (SpellDegree::PROPERTY_KEYS as $key) {
            $out[$key] = (string) ($properties[$key.'_source'] ?? 'spell');
        }

        return $out;
    }

    /**
     * @param  Collection<int, SpellDegree>  $degrees
     * @return list<int>
     */
    private function collectParamInts(Spell $spell, Collection $degrees, string $paramKey): array
    {
        $ids = [];
        foreach ($degrees as $degree) {
            foreach ($this->resolver->resolveEffects($spell, $degree) as $pivot) {
                $raw = $pivot->params[$paramKey] ?? null;
                if ($raw !== null && $raw !== '' && is_numeric($raw)) {
                    $ids[(int) $raw] = true;
                }
            }
        }

        return array_keys($ids);
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Monster>
     */
    private function loadMonstersById(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return Monster::query()->with('creature')->whereIn('id', $ids)->get()->keyBy('id');
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Creature>
     */
    private function loadCreaturesById(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return Creature::query()
            ->with(['monster', 'npc'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Condition>
     */
    private function loadConditionsById(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return Condition::query()->with('canonical')->whereKey($ids)->get()->keyBy('id');
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Condition>
     */
    private function loadConditionsByDofusdbId(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return Condition::query()
            ->with('canonical')
            ->where(static function ($query) use ($ids): void {
                foreach ($ids as $id) {
                    $query->orWhere('dofusdb_id', $id);
                }
            })
            ->get()
            ->keyBy('dofusdb_id');
    }

    /**
     * @param  array<string, mixed>  $params
     * @param  Collection<int, Creature>  $creaturesById
     * @param  Collection<int, Monster>  $monstersById
     * @return array{id: int, creature_id?: int, entity_type?: string, name: string, image: string|null}|null
     */
    private function summonCreatureBrief(
        array $params,
        Collection $creaturesById,
        Collection $monstersById
    ): ?array {
        $creatureId = $params['creature_id'] ?? null;
        if ($creatureId !== null && $creatureId !== '' && is_numeric($creatureId)) {
            $id = (int) $creatureId;
            /** @var Creature|null $creature */
            $creature = $creaturesById->get($id);
            if ($creature === null) {
                return [
                    'id' => $id,
                    'creature_id' => $id,
                    'entity_type' => 'creature',
                    'name' => 'Créature #'.$id,
                    'image' => null,
                ];
            }

            if ($creature->monster !== null) {
                return [
                    'id' => $creature->monster->id,
                    'creature_id' => $creature->id,
                    'entity_type' => 'monster',
                    'name' => $creature->name,
                    'image' => $creature->image,
                ];
            }
            if ($creature->npc !== null) {
                return [
                    'id' => $creature->npc->id,
                    'creature_id' => $creature->id,
                    'entity_type' => 'npc',
                    'name' => $creature->name,
                    'image' => $creature->image,
                ];
            }

            return [
                'id' => $creature->id,
                'creature_id' => $creature->id,
                'entity_type' => 'creature',
                'name' => $creature->name,
                'image' => $creature->image,
            ];
        }

        $mid = $params['monster_id'] ?? null;
        if ($mid === null || $mid === '' || ! is_numeric($mid)) {
            return null;
        }
        $id = (int) $mid;
        $monster = $monstersById->get($id);
        if ($monster === null) {
            return ['id' => $id, 'name' => 'Monstre #'.$id, 'image' => null];
        }

        $creature = $monster->creature;
        if ($creature === null) {
            return ['id' => $monster->id, 'name' => 'Monstre #'.$monster->id, 'image' => null];
        }

        return [
            'id' => $monster->id,
            'creature_id' => $monster->creature_id,
            'entity_type' => 'monster',
            'name' => $creature->name,
            'image' => $creature->image,
        ];
    }

    /**
     * @param  array<string, mixed>  $params
     * @param  Collection<int, Condition>  $byId
     * @param  Collection<int, Condition>  $byDofusdbId
     * @return array{id: int|null, dofusdb_id: int|null, name: string, icon: string|null}|null
     */
    private function conditionBrief(array $params, Collection $byId, Collection $byDofusdbId): ?array
    {
        $sid = $params['condition_id'] ?? null;
        if ($sid !== null && $sid !== '' && is_numeric($sid)) {
            $st = $byId->get((int) $sid);
            if ($st !== null) {
                $display = $st->canonical instanceof Condition ? $st->canonical : $st;
                if ($display->state !== Condition::STATE_RAW) {
                    return $this->conditionBriefFromModel($display);
                }
            }
        }

        $dofusdbId = $params['condition_dofusdb_id'] ?? null;
        if ($dofusdbId === null || $dofusdbId === '' || ! is_numeric($dofusdbId)) {
            return null;
        }
        $st = $byDofusdbId->get((int) $dofusdbId);
        if ($st === null) {
            return null;
        }
        $display = $st->canonical instanceof Condition ? $st->canonical : $st;
        if ($display->state === Condition::STATE_RAW) {
            return null;
        }

        return $this->conditionBriefFromModel($display);
    }

    /**
     * @return array{id: int, dofusdb_id: int|null, name: string, icon: string|null}
     */
    private function conditionBriefFromModel(Condition $state): array
    {
        $name = is_string($state->name) ? DofusHyperlinkText::toDisplayLabel(trim($state->name)) : '';

        return [
            'id' => $state->id,
            'dofusdb_id' => $state->dofusdb_id,
            'name' => $name !== '' ? $name : ('Condition #'.$state->id),
            'icon' => $state->icon,
        ];
    }
}
