<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\EffectUsage;
use App\Models\Entity\Spell;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use Illuminate\Support\Collection;

/**
 * Service métier : degrés d’effet applicables pour une entité/niveau, rendu texte.
 */
final class EffectService
{
    public function __construct(
        private readonly EffectTextResolver $textResolver,
        private readonly CharacteristicFormulaService $formulaService
    ) {}

    /**
     * Retourne les {@see EffectDegree} applicables pour une entité au niveau de créature donné.
     *
     * Pour chaque **définition** liée (sort : pivot effect_spell ; item etc. : usages → effect_degree_id),
     * on ne retient que le degré **numérique** le plus élevé dont le seuil
     * {@see EffectDegree::required_creature_level} est atteint (null = toujours éligible).
     *
     * @param  string  $entity_type  spell, item, consumable, resource
     * @return Collection<int, EffectDegree> Degrés avec `effect` et `effectSubEffects.subEffect` chargés si besoin
     */
    public function getEffectDegreesForEntity(
        string $entity_type,
        int $entity_id,
        int $level,
        ?string $context = null
    ): Collection {
        if ($entity_type === 'spell') {
            return $this->pickDegreesForSpell($entity_id, $level, $context);
        }

        return $this->pickDegreesFromUsages($entity_type, $entity_id, $level, $context);
    }

    /**
     * @return Collection<int, EffectDegree>
     */
    private function pickDegreesForSpell(int $spellId, int $level, ?string $context): Collection
    {
        $spell = Spell::query()
            ->with(['effects.degrees' => fn ($q) => $q->orderBy('degree')])
            ->find($spellId);
        if ($spell === null) {
            return collect();
        }

        $picked = collect();
        foreach ($spell->effects as $definition) {
            $degrees = $definition->degrees->sortBy('degree')->values();
            if ($degrees->isEmpty()) {
                continue;
            }
            $eligible = $degrees->filter(function (EffectDegree $d) use ($level) {
                $req = $d->required_creature_level;

                return $req === null || $level >= $req;
            });
            if ($eligible->isEmpty()) {
                continue;
            }
            /** @var EffectDegree $best */
            $best = $eligible->sortByDesc(fn (EffectDegree $d) => $d->degree)->first();
            $picked->push($best);
        }

        return $this->filterDegreesByContext($picked->unique('id')->values(), $context);
    }

    /**
     * @return Collection<int, EffectDegree>
     */
    private function pickDegreesFromUsages(string $entity_type, int $entity_id, int $level, ?string $context): Collection
    {
        $class = EffectUsage::entityTypeToClass($entity_type);
        if ($class === null) {
            return collect();
        }

        $usages = EffectUsage::query()
            ->where('entity_type', $class)
            ->where('entity_id', $entity_id)
            ->with(['effectDegree.effect'])
            ->get();

        $byDefinition = $usages->groupBy(fn (EffectUsage $u) => $u->effectDegree?->effect_id ?? 0);
        $picked = collect();

        foreach ($byDefinition as $groupUsages) {
            if (! $groupUsages instanceof Collection) {
                continue;
            }
            $eligible = $groupUsages->filter(function (EffectUsage $usage) use ($level) {
                $deg = $usage->effectDegree;
                if ($deg === null) {
                    return false;
                }
                $req = $deg->required_creature_level;

                return $req === null || $level >= $req;
            });
            if ($eligible->isEmpty()) {
                continue;
            }
            /** @var EffectUsage $bestUsage */
            $bestUsage = $eligible->sortByDesc(fn (EffectUsage $u) => $u->effectDegree?->degree ?? 0)->first();
            $deg = $bestUsage->effectDegree;
            if ($deg !== null) {
                $picked->push($deg);
            }
        }

        return $this->filterDegreesByContext($picked->unique('id')->values(), $context);
    }

    /**
     * @param  Collection<int, EffectDegree>  $degrees
     * @return Collection<int, EffectDegree>
     */
    private function filterDegreesByContext(Collection $degrees, ?string $context): Collection
    {
        if ($context === null) {
            return $degrees;
        }

        $scopes = $context === 'combat'
            ? [Effect::SCOPE_GENERAL, Effect::SCOPE_COMBAT]
            : [Effect::SCOPE_GENERAL, Effect::SCOPE_OUT_OF_COMBAT];

        foreach ($degrees as $degree) {
            $degree->loadMissing('effectSubEffects');
            $filtered = $degree->effectSubEffects->filter(
                fn (EffectSubEffect $row) => in_array(
                    $row->scope ?? Effect::SCOPE_GENERAL,
                    $scopes,
                    true
                )
            )->values();
            $degree->setRelation('effectSubEffects', $filtered);
        }

        return $degrees;
    }

    /**
     * Rendu texte pour un degré : résout chaque sous-effet (pivot), concatène.
     *
     * @param  array<string, int|float|string>  $context  Variables (level, agi, value, element…)
     */
    public function renderEffectText(
        EffectDegree $degree,
        array $context = [],
        ?string $scope_filter = null,
        bool $format_dice_human = false
    ): string {
        $degree->loadMissing('effectSubEffects.subEffect');

        $rows = $degree->effectSubEffects;
        if ($scope_filter !== null) {
            $scopes = $scope_filter === 'combat'
                ? [Effect::SCOPE_GENERAL, Effect::SCOPE_COMBAT]
                : [Effect::SCOPE_GENERAL, Effect::SCOPE_OUT_OF_COMBAT];
            $rows = $rows->filter(
                fn ($row) => in_array($row->scope ?? Effect::SCOPE_GENERAL, $scopes, true)
            );
        }

        $parts = [];
        foreach ($rows as $row) {
            $sub = $row->subEffect;
            if ($sub === null) {
                continue;
            }
            $sub->setRelation('pivot', $row);
            $line = $this->renderSubEffectLine($sub, $context);
            if ($line !== '') {
                $line = $this->textResolver->formatDiceInText($line, $format_dice_human);
                $parts[] = $line;
            }
        }

        return implode(' ', $parts);
    }

    /**
     * @param  array<string, int|float|string>  $base_context
     * @return array<string, int|float|string>
     */
    private function buildSubEffectContext($sub, array $base_context): array
    {
        $ctx = $base_context;
        $pivot = $sub->pivot ?? null;

        if ($pivot) {
            if ($pivot->value_min !== null) {
                $ctx['value_min'] = $pivot->value_min;
            }
            if ($pivot->value_max !== null) {
                $ctx['value_max'] = $pivot->value_max;
            }
            $ctx['value'] = $pivot->value_min ?? $pivot->value_max ?? ($base_context['value'] ?? null);
            if ($pivot->dice_num !== null && $pivot->dice_side !== null) {
                $ctx['dice'] = $pivot->dice_num.'d'.$pivot->dice_side;
            }
            if (is_array($pivot->params ?? null)) {
                foreach ($pivot->params as $k => $v) {
                    if (is_scalar($v)) {
                        $ctx[$k] = $v;
                    }
                }
            }
        }

        if ($sub->formula !== null && trim((string) $sub->formula) !== '') {
            $numeric = $this->formulaService->evaluate($sub->formula, $this->toNumericContext($ctx));
            if ($numeric !== null) {
                $ctx['value'] = (int) round($numeric);
            }
        }

        return $ctx;
    }

    /** @return array<string, int|float> */
    private function toNumericContext(array $ctx): array
    {
        $out = [];
        foreach ($ctx as $k => $v) {
            if (is_int($v) || is_float($v)) {
                $out[$k] = $v;
            }
            if (is_string($v) && is_numeric($v)) {
                $out[$k] = str_contains($v, '.') ? (float) $v : (int) $v;
            }
        }

        return $out;
    }

    private function renderSubEffectLine($sub, array $base_context): string
    {
        $template = $sub->template_text ?? '';
        if ($template === '') {
            return '';
        }
        $ctx = $this->buildSubEffectContext($sub, $base_context);
        $resolved = $this->textResolver->resolveEffectText($template, $ctx);
        $dice = $ctx['dice'] ?? null;
        if ($dice !== null && $resolved !== '') {
            $resolved = preg_replace('/\bndX\b/i', $dice, $resolved);
        }

        return trim($resolved);
    }
}
