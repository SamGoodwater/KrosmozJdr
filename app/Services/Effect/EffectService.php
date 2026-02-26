<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Effect;
use App\Models\EffectUsage;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use Illuminate\Support\Collection;

/**
 * Service métier : effets pour une entité/niveau, rendu texte (template + formules).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/PLAN_MISE_EN_OEUVRE_EFFECTS.md Phase 2
 */
final class EffectService
{
    public function __construct(
        private readonly EffectTextResolver $textResolver,
        private readonly CharacteristicFormulaService $formulaService
    ) {
    }

    /**
     * Retourne les effects applicables pour une entité à un niveau donné.
     *
     * @param string $entity_type spell, item, consumable, resource
     * @param int $entity_id
     * @param int $level Niveau du sort/objet/personnage
     * @param string|null $context combat | out_of_combat | null (tous)
     * @return Collection<int, Effect> Effects avec sous-effets chargés (filtrés par scope si context fourni)
     */
    public function getEffectsForEntity(
        string $entity_type,
        int $entity_id,
        int $level,
        ?string $context = null
    ): Collection {
        $usages = EffectUsage::query()
            ->where('entity_type', $entity_type)
            ->where('entity_id', $entity_id)
            ->where(function ($q) use ($level) {
                $q->whereNull('level_min')->orWhere('level_min', '<=', $level);
            })
            ->where(function ($q) use ($level) {
                $q->whereNull('level_max')->orWhere('level_max', '>=', $level);
            })
            ->with(['effect.subEffects'])
            ->orderBy('level_min')
            ->get();

        $effects = $usages->pluck('effect')->filter()->unique('id')->values();

        if ($context !== null) {
            $scopes = $context === 'combat'
                ? [Effect::SCOPE_GENERAL, Effect::SCOPE_COMBAT]
                : [Effect::SCOPE_GENERAL, Effect::SCOPE_OUT_OF_COMBAT];
            foreach ($effects as $effect) {
                $effect->setRelation('subEffects', $effect->subEffects->filter(
                    fn ($sub) => in_array($sub->pivot->scope ?? Effect::SCOPE_GENERAL, $scopes, true)
                )->values());
            }
        }

        return $effects;
    }

    /**
     * Rendu texte complet d'un effect : résout template + formula pour chaque sous-effet, concatène.
     *
     * @param array<string, int|float|string> $context Variables (level, agi, value, element…)
     * @param string|null $scope_filter combat | out_of_combat | null (tous)
     * @param bool $format_dice_human "2d6" → "2 dés à 6 faces"
     */
    public function renderEffectText(
        Effect $effect,
        array $context = [],
        ?string $scope_filter = null,
        bool $format_dice_human = false
    ): string {
        $effect->loadMissing('subEffects');
        $subEffects = $effect->subEffects;

        if ($scope_filter !== null) {
            $scopes = $scope_filter === 'combat'
                ? [Effect::SCOPE_GENERAL, Effect::SCOPE_COMBAT]
                : [Effect::SCOPE_GENERAL, Effect::SCOPE_OUT_OF_COMBAT];
            $subEffects = $subEffects->filter(
                fn ($sub) => in_array($sub->pivot->scope ?? Effect::SCOPE_GENERAL, $scopes, true)
            );
        }

        $parts = [];
        foreach ($subEffects as $sub) {
            $line = $this->renderSubEffectLine($sub, $context);
            if ($line !== '') {
                $line = $this->textResolver->formatDiceInText($line, $format_dice_human);
                $parts[] = $line;
            }
        }

        return implode(' ', $parts);
    }

    /**
     * Construit le contexte pour un sous-effet (pivot + formule évaluée).
     *
     * @param \Illuminate\Database\Eloquent\Model $sub SubEffect avec pivot
     * @param array<string, int|float|string> $base_context
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
                $ctx['dice'] = $pivot->dice_num . 'd' . $pivot->dice_side;
            }
            if (is_array($pivot->params ?? null)) {
                foreach ($pivot->params as $k => $v) {
                    if (is_scalar($v)) {
                        $ctx[$k] = $v;
                    }
                }
            }
        }

        if ($sub->formula !== null && trim($sub->formula) !== '') {
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
