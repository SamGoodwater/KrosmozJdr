<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Characteristic;
use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectSubEffect;
use App\Models\Entity\Monster;
use App\Models\Entity\Condition;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;

/**
 * Moteur de résolution des effects.
 *
 * - Parcourt les lignes pivot effect_sub_effect (ordre, scope, params…)
 * - Évalue les formules de valeur et de durée
 * - Applique la logique AND / OR (avec condition numérique pour OR)
 * - Produit une structure machine + un texte résolu par sous-effet.
 *
 * @see EffectTextResolver
 * @see CharacteristicFormulaService
 */
final class EffectResolutionService
{
    /** Cache noms monstre par requête (évite requêtes répétées sur un même sort). */
    /** @var array<int, string> */
    private array $monsterNameCache = [];

    /** Cache brief monstre (id, name, image) aligné sur SpellEffectDefinitionsSerializer::summonMonsterBrief. */
    /** @var array<int, array{id: int, name: string, image: string|null}> */
    private array $monsterBriefCache = [];

    /** Cache libellé caractéristique (clé machine → name BDD) pour une résolution d’effect. */
    /** @var array<string, string> */
    private array $characteristicLabelByKeyCache = [];

    /** @var array<int, string> id conditions */
    private array $conditionNameByIdCache = [];

    /** @var array<int, string> dofusdb_id */
    private array $conditionNameByDofusdbIdCache = [];

    /** Aligné sur l’UI sorts (id élément → slug config). */
    private const ELEMENT_ID_TO_SLUG = [
        0 => 'neutral',
        1 => 'earth',
        2 => 'fire',
        3 => 'air',
        4 => 'water',
        5 => 'element_wisdom',
        6 => 'element_vitality',
    ];

    public function __construct(
        private readonly EffectTextResolver $textResolver,
        private readonly CharacteristicFormulaService $formulaService
    ) {}

    /**
     * Résout un effect pour un contexte donné.
     *
     * @param  array<string, int|float|string>  $baseContext  Variables disponibles (level, agi, etc.)
     * @param  bool  $isCrit  Si true, inclut les sous-effets « uniquement critique » et utilise value_formula_crit quand présent.
     * @return array{
     *     effect_id: int,
     *     effect_degree_id: int,
     *     sub_effects: list<array<string,mixed>>
     * }
     */
    public function resolveEffect(
        EffectDegree $degree,
        array $baseContext = [],
        ?string $scopeFilter = null,
        bool $formatDiceHuman = false,
        bool $isCrit = false
    ): array {
        $degree->loadMissing('effectSubEffects.subEffect', 'effect');

        $rows = $degree->effectSubEffects;

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
        $this->monsterNameCache = [];
        $this->monsterBriefCache = [];
        $this->characteristicLabelByKeyCache = [];
        $this->conditionNameByIdCache = [];
        $this->conditionNameByDofusdbIdCache = [];

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
            $params = is_array($row->params) ? $row->params : [];

            $text = '';
            $templateCtx = null;
            if ($sub !== null && $sub->template_text) {
                $templateCtx = $this->buildDisplayContextForTemplate($row, $ctx, $isCrit);
                $text = $this->textResolver->resolveEffectText($sub->template_text, $templateCtx);
                $text = $this->textResolver->formatDiceInText($text, $formatDiceHuman);
            }

            $conditionNameResolved = null;
            $cellsDisplay = null;
            if (is_array($templateCtx)) {
                if (isset($templateCtx['condition_name']) && is_string($templateCtx['condition_name'])) {
                    $sn = trim($templateCtx['condition_name']);
                    $conditionNameResolved = $sn !== '' ? $sn : null;
                }
                if (isset($templateCtx['cells']) && is_scalar($templateCtx['cells'])) {
                    $cd = trim((string) $templateCtx['cells']);
                    $cellsDisplay = $cd !== '' ? $cd : null;
                }
            }

            if ($sub !== null && $sub->slug === 'déplacer' && $text !== '' && $cellsDisplay !== null && $cellsDisplay !== '') {
                $text = $this->appendDisplacementMetersSuffix($text, $cellsDisplay);
            }

            $resolved[] = [
                'id' => $row->id,
                'sub_effect_id' => $row->sub_effect_id,
                'action_slug' => $sub?->slug,
                'characteristic' => $params['characteristic'] ?? null,
                'value' => $ctx['value'] ?? null,
                'value_formula' => $params['value_formula'] ?? null,
                'value_formula_crit' => $params['value_formula_crit'] ?? null,
                'life_steal_formula' => $params['life_steal_formula'] ?? null,
                'life_steal_heal' => $ctx['life_steal_heal'] ?? null,
                'crit_only' => (bool) ($row->crit_only ?? false),
                'duration' => $ctx['duration'] ?? null,
                'duration_formula' => $row->duration_formula,
                'scope' => $row->scope,
                'logic_group' => $row->logic_group,
                'logic_operator' => $row->logic_operator,
                'logic_condition' => $row->logic_condition,
                'text' => $text,
                'context' => $ctx,
                'summon_monster' => $this->summonMonsterBriefFromParams($params),
                'condition_name' => $conditionNameResolved,
                'cells_display' => $cellsDisplay,
                'movement_kind' => is_string($params['movement_kind'] ?? null) ? (string) $params['movement_kind'] : null,
                'teleport' => (bool) ($params['teleport'] ?? false),
            ];
        }

        return [
            'effect_id' => (int) $degree->effect_id,
            'effect_degree_id' => (int) $degree->id,
            'sub_effects' => $resolved,
        ];
    }

    /**
     * Construit le contexte pour une ligne pivot (sous-effet attaché).
     *
     * @param  array<string, int|float|string>  $baseContext
     * @param  bool  $isCrit  Si true, utilise value_formula_crit pour la valeur quand elle est définie.
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

        // Dégâts primaires (sans résistances) pour le vol de vie : identiques à la valeur résolue pour l’instant.
        if (isset($ctx['value']) && is_numeric($ctx['value'])) {
            $ctx['dgt'] = (float) $ctx['value'];
        }

        $lifeStealRaw = $params['life_steal_formula'] ?? null;
        if (is_string($lifeStealRaw) && trim($lifeStealRaw) !== '') {
            $lifeStealNorm = LifeStealFormulaNormalizer::normalize($lifeStealRaw);
            if (is_string($lifeStealNorm) && trim($lifeStealNorm) !== '') {
                $numCtx = $this->toNumericContext($ctx);
                if (isset($ctx['dgt'])) {
                    $numCtx['dgt'] = (float) $ctx['dgt'];
                }
                $heal = $this->formulaService->evaluate($lifeStealNorm, $numCtx);
                if ($heal !== null) {
                    $ctx['life_steal_heal'] = $heal;
                }
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
     * Complète le contexte pour {@see EffectTextResolver::resolveEffectText} : libellés
     * ([characteristic], [element]), [cells] depuis cells_formula, [monster] depuis monster_id.
     *
     * @param  array<string, int|float|string>  $ctx
     * @return array<string, int|float|string>
     */
    private function buildDisplayContextForTemplate(EffectSubEffect $row, array $ctx, bool $isCrit = false): array
    {
        $params = is_array($row->params ?? null) ? $row->params : [];
        $out = $ctx;

        $charLabel = $this->lookupCharacteristicLabelFromParams($params);
        if ($charLabel !== '') {
            $out['characteristic'] = $charLabel;
        }

        $elementLabel = $this->lookupElementLabelForTemplate($params);
        if ($elementLabel !== '') {
            $out['element'] = $elementLabel;
        }

        $subSlug = $row->subEffect !== null ? (string) ($row->subEffect->slug ?? '') : '';
        $cells = $this->resolveCellsTemplateValue($params, $ctx, $subSlug, $isCrit);
        if ($cells !== null) {
            $out['cells'] = $cells;
        }

        $monster = $this->resolveMonsterTemplateValue($params);
        if ($monster !== null) {
            $out['monster'] = $monster;
        }

        $conditionName = $this->resolveConditionNameForTemplate($params, $out);
        if ($conditionName !== null && $conditionName !== '') {
            $out['condition_name'] = $conditionName;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function lookupCharacteristicLabelFromParams(array $params): string
    {
        $k = isset($params['characteristic']) ? trim((string) $params['characteristic']) : '';
        if ($k !== '') {
            $label = $this->labelForEffectCharacteristicKey($k);

            return $label !== '' ? $label : '';
        }

        $el = $params['element'] ?? null;
        if ($el !== null && $el !== '' && is_numeric($el)) {
            $slug = $this->elementIdToSlug((int) $el);
            if ($slug !== '') {
                $label = $this->labelForEffectCharacteristicKey($slug);

                return $label !== '' ? $label : '';
            }
        }

        return '';
    }

    /**
     * Libellé pour la variable [element] dans le template.
     *
     * @param  array<string, mixed>  $params
     */
    private function lookupElementLabelForTemplate(array $params): string
    {
        $el = $params['element'] ?? null;
        if ($el === null || $el === '') {
            return '';
        }
        if (is_numeric($el)) {
            $slug = $this->elementIdToSlug((int) $el);
            if ($slug === '') {
                return '';
            }

            return $this->labelForEffectCharacteristicKey($slug);
        }

        $k = trim((string) $el);

        return $this->labelForEffectCharacteristicKey($k) ?: $k;
    }

    private function labelForEffectCharacteristicKey(string $key): string
    {
        if ($key === '') {
            return '';
        }
        $list = config('effect_sub_effects.characteristics', []);
        foreach ($list as $row) {
            if (isset($row['key']) && $row['key'] === $key) {
                return (string) ($row['label'] ?? '');
            }
        }

        if (array_key_exists($key, $this->characteristicLabelByKeyCache)) {
            return $this->characteristicLabelByKeyCache[$key];
        }

        $name = Characteristic::query()->where('key', $key)->value('name');
        $label = ($name !== null && $name !== '') ? (string) $name : '';
        $this->characteristicLabelByKeyCache[$key] = $label;

        return $label;
    }

    /**
     * Nom de condition pour [condition_name] : params explicites ou résolution conditions.
     *
     * @param  array<string, mixed>  $params
     * @param  array<string, int|float|string>  $displayCtx
     */
    private function resolveConditionNameForTemplate(array $params, array $displayCtx): ?string
    {
        $existing = isset($displayCtx['condition_name']) ? trim((string) $displayCtx['condition_name']) : '';
        if ($existing !== '') {
            return $existing;
        }

        $sid = $params['condition_id'] ?? null;
        if ($sid !== null && $sid !== '' && is_numeric($sid)) {
            $id = (int) $sid;
            if ($id > 0) {
                if (! array_key_exists($id, $this->conditionNameByIdCache)) {
                    $n = Condition::query()->whereKey($id)->value('name');
                    $this->conditionNameByIdCache[$id] = ($n !== null && $n !== '') ? (string) $n : '';
                }
                $resolved = $this->conditionNameByIdCache[$id];

                return $resolved !== '' ? $resolved : null;
            }
        }

        $dofusId = $params['condition_dofusdb_id'] ?? null;
        if ($dofusId !== null && $dofusId !== '' && is_numeric($dofusId)) {
            $dId = (int) $dofusId;
            if ($dId > 0) {
                if (! array_key_exists($dId, $this->conditionNameByDofusdbIdCache)) {
                    $n = Condition::query()->where('dofusdb_id', $dId)->value('name');
                    $this->conditionNameByDofusdbIdCache[$dId] = ($n !== null && $n !== '') ? (string) $n : '';
                }
                $resolved = $this->conditionNameByDofusdbIdCache[$dId];

                return $resolved !== '' ? $resolved : null;
            }
        }

        return null;
    }

    private function elementIdToSlug(int $elementId): string
    {
        return self::ELEMENT_ID_TO_SLUG[$elementId] ?? '';
    }

    /**
     * @param  array<string, mixed>  $params
     * @param  array<string, int|float|string>  $ctx
     */
    private function resolveCellsTemplateValue(array $params, array $ctx, string $subEffectSlug = '', bool $isCrit = false): ?string
    {
        $formula = isset($params['cells_formula']) ? trim((string) $params['cells_formula']) : '';
        if ($formula === '' && $subEffectSlug === 'déplacer') {
            if ($isCrit) {
                $crit = isset($params['value_formula_crit']) ? trim((string) $params['value_formula_crit']) : '';
                if ($crit !== '') {
                    $formula = $crit;
                }
            }
            if ($formula === '') {
                $formula = isset($params['value_formula']) ? trim((string) $params['value_formula']) : '';
            }
        }
        if ($formula === '') {
            return null;
        }
        $evaluated = $this->formulaService->evaluate($formula, $this->toNumericContext($ctx));
        if ($evaluated !== null) {
            return $this->formatNumericForTemplate($evaluated);
        }

        return $formula;
    }

    private function formatNumericForTemplate(int|float $n): string
    {
        if (is_int($n) || $n === floor($n)) {
            return (string) (int) $n;
        }

        return rtrim(rtrim(sprintf('%.4F', $n), '0'), '.');
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function resolveMonsterTemplateValue(array $params): ?string
    {
        $mid = $params['monster_id'] ?? null;
        if ($mid === null || $mid === '' || ! is_numeric($mid)) {
            return null;
        }
        $id = (int) $mid;
        if ($id <= 0) {
            return null;
        }

        $this->ensureMonsterCaches($id);

        return $this->monsterNameCache[$id];
    }

    /**
     * Résumé monstre invoqué pour l’UI (chips sous-effets, aligné serializer sorts).
     *
     * @param  array<string, mixed>  $params
     * @return array{id: int, name: string, image: string|null}|null
     */
    private function summonMonsterBriefFromParams(array $params): ?array
    {
        $mid = $params['monster_id'] ?? null;
        if ($mid === null || $mid === '' || ! is_numeric($mid)) {
            return null;
        }
        $id = (int) $mid;
        if ($id <= 0) {
            return null;
        }

        $this->ensureMonsterCaches($id);

        return $this->monsterBriefCache[$id];
    }

    /**
     * Charge créature + remplit caches nom / brief pour un id monstre.
     */
    private function ensureMonsterCaches(int $id): void
    {
        if (array_key_exists($id, $this->monsterNameCache)) {
            return;
        }

        $monster = Monster::query()->with('creature:id,name,image')->find($id);
        if ($monster === null) {
            $this->monsterNameCache[$id] = 'Monstre #'.$id;
            $this->monsterBriefCache[$id] = [
                'id' => $id,
                'name' => 'Monstre #'.$id,
                'image' => null,
            ];

            return;
        }

        $name = $monster->creature?->name ?? ('Monstre #'.$monster->id);
        $this->monsterNameCache[$id] = $name;
        $this->monsterBriefCache[$id] = [
            'id' => $monster->id,
            'name' => $name,
            'image' => $monster->creature?->image,
        ];
    }

    /**
     * Applique la logique AND / OR à partir de l'état précédent.
     *
     * - AND : appliqué seulement si le précédent du groupe était appliqué.
     * - OR  : appliqué si la condition numérique > 0 (formule logic_condition), indépendant de l'état précédent.
     *
     * @param  array<string, int|float|string>  $ctx
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
     * @param  array<string, int|float|string>  $ctx
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

    /**
     * Ajoute « (X m) » au texte résolu du déplacement quand le nombre de cases affiché est un littéral numérique.
     * Règle Krosmoz : 1 case = 1,5 m ; une décimale pour les mètres.
     */
    private function appendDisplacementMetersSuffix(string $text, string $cellsDisplay): string
    {
        $n = $this->parseFloatLiteralCellCount($cellsDisplay);
        if ($n === null) {
            return $text;
        }
        $meters = round($n * 1.5, 1);

        return $text.' ('.$this->formatFrenchDisplacementMeters($meters).' m)';
    }

    /**
     * @return float|null Littéral positif/négatif sans dés ni variable (aligné sur le front).
     */
    private function parseFloatLiteralCellCount(string $cellsDisplay): ?float
    {
        $t = trim(str_replace(' ', '', $cellsDisplay));
        if ($t === '') {
            return null;
        }
        if (preg_match('/[dD\[]/', $t) || preg_match('/\p{L}/u', $t)) {
            return null;
        }
        $normalized = str_replace(',', '.', $t);
        if (! is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }

    private function formatFrenchDisplacementMeters(float $meters): string
    {
        $r = round($meters, 1);
        if (abs($r - (float) (int) $r) < 1e-6) {
            return (string) (int) $r;
        }

        return str_replace('.', ',', sprintf('%.1F', $r));
    }
}
