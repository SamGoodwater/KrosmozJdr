<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Effect;
use App\Models\EffectSubEffect;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;

/**
 * Moteur de résolution des effects.
 *
 * - Parcourt les lignes pivot effect_sub_effect (ordre, scope, params…)
 * - Évalue les formules de valeur et de durée
 * - Applique la logique AND / OR (avec condition numérique pour OR)
 * - Produit une structure machine + un texte résolu par sous-effet.
 *
 * @see \App\Services\Effect\EffectTextResolver
 * @see \App\Services\Characteristic\Formula\CharacteristicFormulaService
 */
final class EffectResolutionService
{
    public function __construct(
        private readonly EffectTextResolver $textResolver,
        private readonly CharacteristicFormulaService $formulaService
    ) {
    }

    /**
     * Résout un effect pour un contexte donné.
     *
     * @param array<string, int|float|string> $baseContext Variables disponibles (level, agi, etc.)
     * @param bool $isCrit Si true, inclut les sous-effets « uniquement critique » et utilise value_formula_crit quand présent.
     * @return array{
     *     effect_id: int,
     *     sub_effects: list<array<string,mixed>>
     * }
     */
    public function resolveEffect(
        Effect $effect,
        array $baseContext = [],
        ?string $scopeFilter = null,
        bool $formatDiceHuman = false,
        bool $isCrit = false
    ): array {
        $effect->loadMissing('effectSubEffects.subEffect');

        $rows = $effect->effectSubEffects;

        if ($scopeFilter !== null) {
            $scopes = $scopeFilter === Effect::SCOPE_COMBAT
                ? [Effect::SCOPE_GENERAL, Effect::SCOPE_COMBAT]
                : [Effect::SCOPE_GENERAL, Effect::SCOPE_OUT_OF_COMBAT];

            $rows = $rows->filter(
                fn (EffectSubEffect $row) => in_array($row->scope ?? Effect::SCOPE_GENERAL, $scopes, true)
            )->values();
        }

        // En mode non-critique : exclure les lignes réservées au critique
        if (! $isCrit) {
            $rows = $rows->filter(fn (EffectSubEffect $row) => ! ($row->crit_only ?? false))->values();
        }

        $resolved = [];
        $lastApplied = true;
        $lastGroup = null;

        foreach ($rows as $row) {
            // Nouveau groupe logique → on réinitialise l'état précédent
            if ($row->logic_group !== null && $row->logic_group !== $lastGroup) {
                $lastApplied = true;
                $lastGroup = $row->logic_group;
            }

            $ctx = $this->buildContextForRow($row, $baseContext, $isCrit);

            [$applied, $lastApplied] = $this->evaluateLogic($row, $ctx, $lastApplied);
            if (! $applied) {
                continue;
            }

            $sub = $row->subEffect;
            $text = '';
            if ($sub !== null && $sub->template_text) {
                $text = $this->textResolver->resolveEffectText($sub->template_text, $ctx);
                $text = $this->textResolver->formatDiceInText($text, $formatDiceHuman);
            }

            $resolved[] = [
                'id' => $row->id,
                'sub_effect_id' => $row->sub_effect_id,
                'action_slug' => $sub?->slug,
                'characteristic' => $row->params['characteristic'] ?? null,
                'value' => $ctx['value'] ?? null,
                'value_formula' => $row->params['value_formula'] ?? null,
                'value_formula_crit' => $row->params['value_formula_crit'] ?? null,
                'crit_only' => (bool) ($row->crit_only ?? false),
                'duration' => $ctx['duration'] ?? null,
                'duration_formula' => $row->duration_formula,
                'scope' => $row->scope,
                'logic_group' => $row->logic_group,
                'logic_operator' => $row->logic_operator,
                'logic_condition' => $row->logic_condition,
                'text' => $text,
                'context' => $ctx,
            ];
        }

        return [
            'effect_id' => $effect->id,
            'sub_effects' => $resolved,
        ];
    }

    /**
     * Construit le contexte pour une ligne pivot (sous-effet attaché).
     *
     * @param array<string, int|float|string> $baseContext
     * @param bool $isCrit Si true, utilise value_formula_crit pour la valeur quand elle est définie.
     * @return array<string, int|float|string>
     */
    private function buildContextForRow(EffectSubEffect $row, array $baseContext, bool $isCrit = false): array
    {
        $ctx = $baseContext;

        // Paramètres explicites (characteristic, value_formula…)
        $params = is_array($row->params ?? null) ? $row->params : [];
        foreach ($params as $k => $v) {
            if (is_scalar($v)) {
                $ctx[$k] = $v;
            }
        }

        // Valeur : en critique, priorité à value_formula_crit si présente ; sinon value_formula
        $valueFormula = $isCrit && ($params['value_formula_crit'] ?? null) !== null && trim((string) $params['value_formula_crit']) !== ''
            ? $params['value_formula_crit']
            : ($params['value_formula'] ?? null);
        if (is_string($valueFormula) && trim($valueFormula) !== '') {
            $numeric = $this->formulaService->evaluate($valueFormula, $this->toNumericContext($ctx));
            if ($numeric !== null) {
                // On garde la valeur telle quelle (float) dans le contexte ; charge au moteur de combat de trancher int/float
                $ctx['value'] = $numeric;
            }
        }

        // Durée : expr numérique, interprétée selon le contexte (tours / secondes)
        if (is_string($row->duration_formula) && trim($row->duration_formula) !== '') {
            $durationNumeric = $this->formulaService->evaluate($row->duration_formula, $this->toNumericContext($ctx));
            if ($durationNumeric !== null) {
                $ctx['duration'] = $durationNumeric;
            }
        }

        return $ctx;
    }

    /**
     * Applique la logique AND / OR à partir de l'état précédent.
     *
     * - AND : appliqué seulement si le précédent du groupe était appliqué.
     * - OR  : appliqué si la condition numérique > 0 (formule logic_condition), indépendant de l'état précédent.
     *
     * @param array<string, int|float|string> $ctx
     * @return array{0: bool, 1: bool} [applied, newLastApplied]
     */
    private function evaluateLogic(EffectSubEffect $row, array $ctx, bool $lastApplied): array
    {
        $op = strtoupper((string) ($row->logic_operator ?? ''));
        if ($op === '') {
            // Premier de la chaîne (ou pas de logique déclarée) : appliqué.
            return [true, true];
        }

        if ($op === 'AND') {
            $applied = $lastApplied;
            return [$applied, $applied];
        }

        if ($op === 'OR') {
            $condition = $row->logic_condition ?? null;
            if (! is_string($condition) || trim($condition) === '') {
                // Sans condition explicite, on n'applique pas par défaut.
                return [false, false];
            }

            $numeric = $this->formulaService->evaluate($condition, $this->toNumericContext($ctx));
            $applied = $numeric !== null && $numeric > 0;

            return [$applied, $applied];
        }

        // Opérateur inconnu → on applique par défaut.
        return [true, true];
    }

    /**
     * @param array<string, int|float|string> $ctx
     * @return array<string, int|float>
     */
    private function toNumericContext(array $ctx): array
    {
        $out = [];
        foreach ($ctx as $k => $v) {
            if (is_int($v) || is_float($v)) {
                $out[$k] = $v;
            } elseif (is_string($v) && is_numeric($v)) {
                $out[$k] = str_contains($v, '.') ? (float) $v : (int) $v;
            }
        }

        return $out;
    }
}

