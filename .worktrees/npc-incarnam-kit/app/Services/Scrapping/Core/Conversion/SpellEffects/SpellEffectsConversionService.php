<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Models\Effect;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Effect\SpellActionBudgetService;
use App\Services\Jdr\DiceNotationService;
use App\Services\Scrapping\Config\DofusDbConditionCatalog;
use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use App\Support\DofusHyperlinkText;
use App\Support\KrosmozGameTerms;
use Illuminate\Support\Str;

/**
 * Sous-service de conversion dédié aux effets de sorts DofusDB vers KrosmozJDR.
 *
 * Prend en entrée les données brutes du sort et les spell-levels (déjà récupérés),
 * résout chaque effectId via DofusDbEffectCatalog, applique le mapping effectId → SubEffect
 * et produit une structure prête pour l'intégration (EffectGroup + Effects + sous-effets).
 * Phase 3 : conversion des valeurs via characteristic_spell (value_converted).
 *
 * @see docs/features/effects/README.md
 * @see docs/features/effects/README.md
 */
final class SpellEffectsConversionService
{
    /** @var list<array{level:string,code:string,message:string,context:array<string,mixed>}> */
    private array $diagnostics = [];

    private const SUB_EFFECT_SLUG_APPLY_STATE = 'appliquer-etat';

    private const SUB_EFFECT_SLUG_SELF_APPLY_STATE = 's-appliquer-etat';

    /**
     * effectId Dofus → stateId Dofus (catalogue conditions) quand la description
     * n’est pas « État #N » mais l’effet applique quand même un état fixe.
     *
     * @var array<int, int>
     */
    private const FORCED_STATE_ID_BY_EFFECT_ID = [
        150 => 250, // Rend la cible invisible → Invisible
    ];

    private SpellActionBudgetService $actionBudgetService;

    private SpellResolutionInferenceService $resolutionInferenceService;

    public function __construct(
        private DofusDbEffectCatalog $effectCatalog,
        private DofusDbConditionCatalog $conditionCatalog,
        private DofusdbEffectMappingService $mappingService,
        private SpellEffectConversionFormulaResolver $formulaResolver,
        private DofusConversionService $dofusConversion,
        private CharacteristicGetterService $characteristicGetter,
        private DiceNotationService $diceNotationService,
        ?SpellActionBudgetService $actionBudgetService = null,
        ?SpellResolutionInferenceService $resolutionInferenceService = null,
    ) {
        $this->actionBudgetService = $actionBudgetService ?? new SpellActionBudgetService;
        $this->resolutionInferenceService = $resolutionInferenceService ?? new SpellResolutionInferenceService;
    }

    /**
     * Convertit les effets d'un sort DofusDB (sort brut + spell-levels) en structure KrosmozJDR.
     *
     * @param  array<string, mixed>  $spellRaw  Réponse GET /spells/{id} (doit contenir id, name, spellLevels)
     * @param  list<array<string, mixed>>  $spellLevelsData  Liste des réponses GET /spell-levels/{levelId} (grade, effects[], criticalEffect[])
     * @param  array{lang?: string}  $options  lang pour le catalogue d'effets (défaut fr)
     */
    public function convert(
        array $spellRaw,
        array $spellLevelsData,
        array $options = []
    ): SpellEffectsConversionResult {
        $this->diagnostics = [];
        $lang = (string) ($options['lang'] ?? 'fr');
        $spellName = $this->extractSpellName($spellRaw, $lang);
        $spellId = isset($spellRaw['id']) ? (int) $spellRaw['id'] : 0;
        $baseSlug = $this->buildSlug($spellName, $spellId);

        $effectGroup = [
            'name' => $spellName,
            'slug' => $baseSlug,
        ];

        $effects = [];
        foreach ($spellLevelsData as $levelData) {
            $grade = isset($levelData['grade']) ? (int) $levelData['grade'] : 0;
            $effects[] = $this->convertOneLevel(
                $levelData,
                $spellName,
                $baseSlug,
                $grade,
                $lang
            );
        }

        usort($effects, static fn (array $a, array $b) => ($a['degree'] ?? 0) <=> ($b['degree'] ?? 0));

        $resolution = $this->resolutionInferenceService->infer($effects, $spellRaw);

        return new SpellEffectsConversionResult($effectGroup, $effects, $resolution, $this->diagnostics);
    }

    /**
     * @param  array<string, mixed>  $levelData  Un spell-level (effects[], criticalEffect[])
     * @return array{degree: int, name: string, slug: string, description: string|null, sub_effects: list<array>}
     */
    private function convertOneLevel(
        array $levelData,
        string $spellName,
        string $baseSlug,
        int $grade,
        string $lang
    ): array {
        $degree = $grade > 0 ? $grade : 1;
        $slug = $baseSlug.'-'.$degree;

        $subEffects = [];
        $effectsList = $levelData['effects'] ?? [];
        $criticalList = $this->indexCriticalEffectsByOrder($levelData['criticalEffect'] ?? []);

        foreach ($effectsList as $index => $instance) {
            if (! is_array($instance)) {
                continue;
            }
            $effectId = isset($instance['effectId']) ? (int) $instance['effectId'] : 0;
            if ($effectId === 0) {
                continue;
            }

            $definition = $this->effectCatalog->get($effectId, $lang);
            $stateData = $this->resolveConditionData($instance, $definition, $lang)
                ?? $this->resolveForcedStateData($effectId, $lang);

            if ($stateData !== null) {
                $subEffects[] = [
                    'order' => isset($instance['order']) ? (int) $instance['order'] : $index,
                    'sub_effect_slug' => $this->resolveStateSubEffectSlug($instance),
                    'params' => $this->buildParamsForState($instance, $stateData, $effectId),
                    'crit_only' => false,
                ];

                continue;
            }

            $mapping = $this->mappingService->getSubEffectForEffectId($effectId);
            $subEffectSlug = null;
            $charSource = null;
            $mappedCharacteristicKey = null;

            if ($mapping !== null) {
                $subEffectSlug = isset($mapping[0]) && is_string($mapping[0]) && $mapping[0] !== ''
                    ? $mapping[0]
                    : DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER;
                $charSource = isset($mapping[1]) && is_string($mapping[1]) && $mapping[1] !== ''
                    ? $mapping[1]
                    : 'none';
                $mappedCharacteristicKey = isset($mapping[2]) && is_string($mapping[2]) && $mapping[2] !== ''
                    ? $mapping[2]
                    : null;
            } else {
                $subEffectSlug = DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER;
                $this->diagnostics[] = [
                    'level' => 'manual_review',
                    'code' => 'unmapped_spell_effect',
                    'message' => "Effet DofusDB #{$effectId} conservé dans le sous-effet autre.",
                    'context' => [
                        'dofus_effect_id' => $effectId,
                        'grade' => $grade,
                        'raw_effect' => $instance,
                    ],
                ];
            }

            $order = isset($instance['order']) ? (int) $instance['order'] : $index;
            $params = $subEffectSlug === DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER
                ? $this->buildParamsForOther($instance, $definition, $lang)
                : $this->buildParams($instance, $definition, $charSource ?? 'none', $subEffectSlug, $mappedCharacteristicKey, $degree);
            $params['dofus_effect_id'] = $effectId;
            $critOnly = false;

            $criticalInstance = $criticalList[$order] ?? null;
            if ($criticalInstance !== null && is_array($criticalInstance)) {
                $critFormula = $this->buildValueFormula($criticalInstance);
                if ($critFormula !== null && $critFormula !== '') {
                    $criticalParams = $params;
                    $criticalParams['value_formula'] = $critFormula;
                    $this->applyValueConversion($criticalInstance, $subEffectSlug, $criticalParams, $degree);
                    $params['dofus_value_formula_crit'] = $critFormula;
                    $params['value_formula_crit'] = $criticalParams['value_formula'] ?? $critFormula;
                    if (isset($criticalParams['value_converted'])) {
                        $params['value_converted_crit'] = $criticalParams['value_converted'];
                    }
                    if (isset($criticalParams['dice_formula'])) {
                        $params['dice_formula_crit'] = $criticalParams['dice_formula'];
                    }
                }
            }

            $subEffects[] = [
                'order' => $order,
                'sub_effect_slug' => $subEffectSlug,
                'params' => $params,
                'crit_only' => $critOnly,
            ];
        }

        $area = $this->extractAreaNotationFromLevel($levelData);
        $this->applyActionBudgets($subEffects, $levelData, $area);
        $targetType = $this->extractTargetTypeFromLevel($levelData);

        return [
            'degree' => $degree,
            'name' => $spellName,
            'slug' => $slug,
            'description' => null,
            'target_type' => $targetType,
            'area' => $area,
            'sub_effects' => $subEffects,
        ];
    }

    /**
     * Répartit les budgets par tour du référentiel « Creation sort » entre les lignes d'un lancement.
     *
     * @param  list<array<string, mixed>>  $subEffects
     * @param  array<string, mixed>  $levelData
     */
    private function applyActionBudgets(array &$subEffects, array $levelData, ?string $area): void
    {
        $level = $this->krosmozLevelFromDofusLevelData($levelData);
        $actionPointCost = isset($levelData['apCost']) && is_numeric($levelData['apCost'])
            ? (int) $levelData['apCost']
            : null;
        if ($level === null || $actionPointCost === null || $actionPointCost <= 0) {
            return;
        }

        $damageIndices = [];
        $healIndices = [];
        $shieldIndices = [];
        $tempHpIndices = [];
        $allDamageIsLifeSteal = true;
        foreach ($subEffects as $index => $subEffect) {
            $slug = (string) ($subEffect['sub_effect_slug'] ?? '');
            $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
            if ($slug === 'frapper' && isset($params['value_converted']) && is_numeric($params['value_converted'])) {
                $damageIndices[] = $index;
                $allDamageIsLifeSteal = $allDamageIsLifeSteal
                    && isset($params['life_steal_formula'])
                    && trim((string) $params['life_steal_formula']) !== '';
            }
            if ($slug === 'soigner' && isset($params['value_converted']) && is_numeric($params['value_converted'])) {
                $healIndices[] = $index;
            }
            if ($slug === 'protéger' && isset($params['value_converted']) && is_numeric($params['value_converted'])) {
                $shieldIndices[] = $index;
            }
            if ($slug === 'donner-pv-temporaires' && isset($params['value_converted']) && is_numeric($params['value_converted'])) {
                $tempHpIndices[] = $index;
            }
        }

        // Bouclier et PV temporaires partagent l’enveloppe survie (même groupe hybride).
        $survivabilityIndices = array_values(array_merge($shieldIndices, $tempHpIndices));
        $groups = array_values(array_filter(
            [$damageIndices, $healIndices, $survivabilityIndices],
            static fn (array $group): bool => $group !== []
        ));
        $hybridDivisor = max(1, count($groups));
        $powerIndex = $this->actionPowerIndex($levelData, $area);

        if ($damageIndices !== []) {
            $action = $allDamageIsLifeSteal
                ? SpellActionBudgetService::ACTION_LIFE_STEAL
                : SpellActionBudgetService::ACTION_DAMAGE;
            $this->distributeActionBudget(
                $subEffects,
                $damageIndices,
                $action,
                $level,
                $actionPointCost,
                $powerIndex,
                $hybridDivisor
            );
        }
        if ($healIndices !== []) {
            $this->distributeActionBudget(
                $subEffects,
                $healIndices,
                SpellActionBudgetService::ACTION_HEAL,
                $level,
                $actionPointCost,
                $powerIndex,
                $hybridDivisor
            );
        }
        if ($survivabilityIndices !== []) {
            // Même enveloppe budget que les soins ; métadonnée d’action distincte pour les PV temp.
            $this->distributeActionBudget(
                $subEffects,
                $survivabilityIndices,
                SpellActionBudgetService::ACTION_SHIELD,
                $level,
                $actionPointCost,
                $powerIndex,
                $hybridDivisor
            );
            foreach ($tempHpIndices as $index) {
                $params = is_array($subEffects[$index]['params'] ?? null) ? $subEffects[$index]['params'] : [];
                if (! is_array($params['action_budget'] ?? null)) {
                    continue;
                }
                $params['action_budget']['action'] = SpellActionBudgetService::ACTION_TEMP_HP;
                $subEffects[$index]['params'] = $params;
            }
        }
    }

    /**
     * @param  list<array<string, mixed>>  $subEffects
     * @param  list<int>  $indices
     */
    private function distributeActionBudget(
        array &$subEffects,
        array $indices,
        string $action,
        int $level,
        int $actionPointCost,
        int $powerIndex,
        int $hybridDivisor
    ): void {
        $castBudget = $this->actionBudgetService->budgetForCast($action, $level, $actionPointCost, $powerIndex);
        $castBudget = max(count($indices), (int) round($castBudget / max(1, $hybridDivisor)));
        $weights = [];
        foreach ($indices as $index) {
            $params = is_array($subEffects[$index]['params'] ?? null) ? $subEffects[$index]['params'] : [];
            $weights[] = isset($params['value_converted']) && is_numeric($params['value_converted'])
                ? (float) $params['value_converted']
                : 1.0;
        }
        $allocations = $this->actionBudgetService->distribute($castBudget, $weights);

        foreach ($indices as $position => $index) {
            $params = is_array($subEffects[$index]['params'] ?? null) ? $subEffects[$index]['params'] : [];
            $oldValue = isset($params['value_converted']) && is_numeric($params['value_converted'])
                ? max(1.0, (float) $params['value_converted'])
                : 1.0;
            $newValue = $allocations[$position];
            $params['value_converted'] = $newValue;
            $this->replaceActionFormula($params, $newValue);

            if (isset($params['value_converted_crit']) && is_numeric($params['value_converted_crit'])) {
                $criticalRatio = max(1.0, (float) $params['value_converted_crit'] / $oldValue);
                $criticalValue = max($newValue, (int) round($newValue * $criticalRatio));
                $params['value_converted_crit'] = $criticalValue;
                $this->replaceCriticalActionFormula($params, $criticalValue);
            }
            if (isset($params['life_steal_formula'])) {
                $params['life_steal_value_converted'] = $newValue;
            }

            $params['action_budget'] = [
                'source' => 'creation-sort-pv',
                'action' => $action,
                'level' => $level,
                'pa_cost' => $actionPointCost,
                'max_pa' => $this->actionBudgetService->maxActionPoints($level),
                'power' => SpellActionBudgetService::POWER_LEVELS[$powerIndex],
                'turn_budget' => $this->actionBudgetService->turnBudget($action, $level, $powerIndex),
                'cast_budget' => $castBudget,
                'hybrid_divisor' => $hybridDivisor,
            ];
            $subEffects[$index]['params'] = $params;
        }
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function replaceActionFormula(array &$params, int $value): void
    {
        $params['dice_formula'] = $this->diceNotationService->toDiceNotation($value);
        $params['value_formula'] = $params['dice_formula'];
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function replaceCriticalActionFormula(array &$params, int $value): void
    {
        $params['dice_formula_crit'] = $this->diceNotationService->toDiceNotation($value);
        $params['value_formula_crit'] = $params['dice_formula_crit'];
    }

    /**
     * Le palier neutre représente le second rôle moyen.
     * La mêlée monte d'un palier ; longue portée et zone descendent chacune d'un palier.
     *
     * @param  array<string, mixed>  $levelData
     */
    private function actionPowerIndex(array $levelData, ?string $area): int
    {
        $powerIndex = 2;
        $range = isset($levelData['range']) && is_numeric($levelData['range']) ? (int) $levelData['range'] : null;
        if ($range !== null && $range <= 1) {
            $powerIndex++;
        } elseif ($range !== null && $range >= 7) {
            $powerIndex--;
        }
        if ($area !== null && $area !== 'point') {
            $powerIndex--;
        }

        return max(0, min(4, $powerIndex));
    }

    /**
     * @param  array<string, mixed>  $levelData
     */
    private function krosmozLevelFromDofusLevelData(array $levelData): ?int
    {
        if (! isset($levelData['minPlayerLevel']) || ! is_numeric($levelData['minPlayerLevel'])) {
            return null;
        }

        return max(1, min(20, (int) round((float) $levelData['minPlayerLevel'] / 10)));
    }

    /**
     * @param  array<string, mixed>  $instance
     * @param  array<string, mixed>  $stateData
     * @return array<string, mixed>
     */
    private function buildParamsForState(array $instance, array $stateData, int $effectId): array
    {
        $params = [
            'condition_dofusdb_id' => (int) ($stateData['id'] ?? 0),
            'condition_name' => DofusHyperlinkText::toDisplayLabel(
                $this->extractLocalizedValue($stateData['name'] ?? null, 'fr')
            ) ?: null,
            'condition_icon' => isset($stateData['icon']) ? (string) $stateData['icon'] : null,
            'condition_image' => isset($stateData['img']) ? (string) $stateData['img'] : null,
            'dispellable' => isset($instance['dispellable']) ? (bool) $instance['dispellable'] : null,
            'target_mask' => isset($instance['targetMask']) ? (string) $instance['targetMask'] : null,
            'target_id' => isset($instance['targetId']) && is_numeric($instance['targetId']) ? (int) $instance['targetId'] : null,
            'dofus_effect_id' => $effectId,
            'condition_flags' => [
                'prevents_spell_cast' => (bool) ($stateData['preventsSpellCast'] ?? false),
                'prevents_fight' => (bool) ($stateData['preventsFight'] ?? false),
                'cant_be_moved' => (bool) ($stateData['cantBeMoved'] ?? false),
                'cant_be_pushed' => (bool) ($stateData['cantBePushed'] ?? false),
                'cant_deal_damage' => (bool) ($stateData['cantDealDamage'] ?? false),
                'invulnerable' => (bool) ($stateData['invulnerable'] ?? false),
                'cant_switch_position' => (bool) ($stateData['cantSwitchPosition'] ?? false),
                'incurable' => (bool) ($stateData['incurable'] ?? false),
                'invulnerable_melee' => (bool) ($stateData['invulnerableMelee'] ?? false),
                'invulnerable_range' => (bool) ($stateData['invulnerableRange'] ?? false),
                'cant_tackle' => (bool) ($stateData['cantTackle'] ?? false),
                'cant_be_tackled' => (bool) ($stateData['cantBeTackled'] ?? false),
                'display_turn_remaining' => (bool) ($stateData['displayTurnRemaining'] ?? false),
                'is_main_state' => (bool) ($stateData['isMainState'] ?? false),
            ],
        ];
        $this->addDurationToParams($instance, $params);
        $this->attachDofusElementIdFromSpellEffectInstance($instance, $params);

        return $params;
    }

    /**
     * Extrait la notation zone (point, line-WxL, cross-N, circle-N, rect-WxH) depuis le premier zoneDescr du niveau.
     *
     * @param  array<string, mixed>  $levelData  spell-level (effects[].zoneDescr)
     */
    private function extractAreaNotationFromLevel(array $levelData): ?string
    {
        $effectsList = $levelData['effects'] ?? [];
        foreach ($effectsList as $inst) {
            if (! is_array($inst)) {
                continue;
            }
            $zone = $inst['zoneDescr'] ?? null;
            if (! is_array($zone)) {
                continue;
            }
            $notation = self::zoneDescrToNotation($zone);
            if ($notation !== null) {
                return $notation;
            }
        }

        return null;
    }

    /**
     * Convertisseur zoneDescr DofusDB (shape, param1, param2) → notation KrosmozJDR.
     * Shapes DofusDB : 80 = case unique, 67 = cercle, 79 = anneau sans centre, 76 = ligne,
     * 88 = croix pleine, 81 = croix sans centre, 71 = carré.
     *
     * @see docs/features/effects/README.md
     *
     * @param  array{shape?: int, param1?: int, param2?: int}  $zoneDescr
     */
    public static function zoneDescrToNotation(array $zoneDescr): ?string
    {
        $shape = isset($zoneDescr['shape']) ? (int) $zoneDescr['shape'] : 0;
        $p1 = isset($zoneDescr['param1']) ? (int) $zoneDescr['param1'] : 0;
        $p2 = isset($zoneDescr['param2']) ? (int) $zoneDescr['param2'] : 0;

        return match (true) {
            $shape === 0, $shape === 80 => 'point',  // case unique (CAC)
            $shape === 67, $shape === 79 => self::circleNotation($p1, $p2),  // 67 cercle, 79 anneau sans centre
            $shape === 76 => 'line-1x'.max(1, $p1 ?: 1),  // ligne
            $shape === 88 => self::crossNotation(0, $p1 ?: 1),  // croix pleine (min=0)
            $shape === 81 => self::crossNotation(1, $p1 ?: 1),  // croix sans centre (min=1)
            $shape === 71 => self::rectNotation($p1, $p2),  // carré (ou rect si p2)
            // Anciens IDs (rétrocompat)
            $shape === 1 => 'line-1x'.max(1, $p1 ?: 1),
            $shape === 2, $shape === 4 => self::crossNotation(0, $p1 ?: 1),
            $shape === 3 => self::circleNotation($p1, $p2),
            default => $shape > 0 ? 'shape-'.$shape.($p1 !== 0 || $p2 !== 0 ? '-'.$p1.'-'.$p2 : '') : null,
        };
    }

    /**
     * Notation cercle : circle-{min}-{max} (rayon intérieur, rayon extérieur).
     */
    private static function circleNotation(int $p1, int $p2): string
    {
        if ($p2 <= 0) {
            $radius = max(1, $p1);

            return 'circle-0-'.$radius;
        }
        $min = max(0, $p1);
        $max = max($min, $p2);

        return 'circle-'.$min.'-'.$max;
    }

    /**
     * Notation croix : cross-{min}-{max}. 0-N = pleine, 1-N = sans centre.
     */
    private static function crossNotation(int $min, int $max): string
    {
        $max = max(1, $max);
        $min = max(0, min($min, $max));

        return 'cross-'.$min.'-'.$max;
    }

    /**
     * Notation rectangle / carré : rect-{W}x{H}. Si param2 = 0, carré NxN.
     */
    private static function rectNotation(int $p1, int $p2): string
    {
        $w = max(1, $p1 ?: 1);
        $h = $p2 > 0 ? max(1, $p2) : $w;

        return 'rect-'.$w.'x'.$h;
    }

    /**
     * Déduit target_type (direct / trap / glyph) depuis le niveau (Dofus : triggers, etc.).
     *
     * @param  array<string, mixed>  $levelData
     */
    private function extractTargetTypeFromLevel(array $levelData): string
    {
        $effectsList = $levelData['effects'] ?? [];
        foreach ($effectsList as $inst) {
            if (! is_array($inst)) {
                continue;
            }
            $triggers = $inst['triggers'] ?? null;
            if (is_string($triggers) && str_contains(strtoupper($triggers), 'P')) {
                return Effect::TARGET_TRAP;
            }
            if (is_string($triggers) && str_contains(strtoupper($triggers), 'G')) {
                return Effect::TARGET_GLYPH;
            }
        }

        return Effect::TARGET_DIRECT;
    }

    /**
     * @param  list<array<string, mixed>>  $criticalEffect
     * @return array<int, array<string, mixed>> order => instance
     */
    private function indexCriticalEffectsByOrder(array $criticalEffect): array
    {
        $indexed = [];
        foreach ($criticalEffect as $inst) {
            if (! is_array($inst)) {
                continue;
            }
            $order = isset($inst['order']) ? (int) $inst['order'] : count($indexed);
            $indexed[$order] = $inst;
        }

        return $indexed;
    }

    /**
     * Params pour le sous-effet "autre" : valeur (formule dés/valeur) + description DofusDB (pour affichage / sous-effets personnalisés).
     *
     * @param  array<string, mixed>  $instance  Instance d'effet (diceNum, diceSide, value)
     * @param  array<string, mixed>  $definition  Définition GET /effects/{id} (description multilingue)
     * @return array{value_formula: ?string, value: string, value_formula_crit: null}
     */
    private function buildParamsForOther(array $instance, array $definition, string $lang): array
    {
        $valueFormula = $this->buildValueFormula($instance);
        $description = $this->extractEffectDescription($definition, $lang);
        $value = $description !== '' ? $description : ($valueFormula ?? 'Effet non mappé');
        $params = [
            'value_formula' => $valueFormula,
            'value' => $value,
            'value_formula_crit' => null,
        ];
        $this->addDurationToParams($instance, $params);
        $this->attachDofusElementIdFromSpellEffectInstance($instance, $params);

        return $params;
    }

    /**
     * @param  array<string, mixed>  $instance
     * @param  array<string, mixed>  $definition
     * @return array<string, mixed>|null
     */
    private function resolveConditionData(array $instance, array $definition, string $lang): ?array
    {
        if (! $this->isStateEffectDefinition($definition)) {
            return null;
        }

        $stateId = $this->extractStateIdFromInstance($instance);
        if ($stateId <= 0) {
            return null;
        }

        $state = $this->conditionCatalog->get($stateId, $lang);
        if ($state === []) {
            return ['id' => $stateId, 'name' => null];
        }

        return $state;
    }

    /**
     * États forcés pour des effectId connus (ex. invisibilité 150 → state 250).
     *
     * @return array<string, mixed>|null
     */
    private function resolveForcedStateData(int $effectId, string $lang): ?array
    {
        $stateId = self::FORCED_STATE_ID_BY_EFFECT_ID[$effectId] ?? null;
        if ($stateId === null || $stateId <= 0) {
            return null;
        }

        $state = $this->conditionCatalog->get($stateId, $lang);
        if ($state === []) {
            return ['id' => $stateId, 'name' => null];
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    private function isStateEffectDefinition(array $definition): bool
    {
        $description = $this->normalizeDecisionText($this->extractEffectDescription($definition, 'fr'));
        if ($description === '') {
            return false;
        }

        return str_contains($description, 'etat #') || str_contains($description, 'state #');
    }

    /**
     * @param  array<string, mixed>  $instance
     */
    private function extractStateIdFromInstance(array $instance): int
    {
        foreach (['value', 'diceNum', 'diceSide'] as $candidateKey) {
            if (isset($instance[$candidateKey]) && is_numeric($instance[$candidateKey])) {
                $value = (int) $instance[$candidateKey];
                if ($value > 0) {
                    return $value;
                }
            }
        }

        return 0;
    }

    /**
     * @param  array<string, mixed>  $instance
     */
    private function resolveStateSubEffectSlug(array $instance): string
    {
        $targetMask = strtoupper((string) ($instance['targetMask'] ?? ''));
        if ($targetMask !== '' && str_contains($targetMask, 'C')) {
            return self::SUB_EFFECT_SLUG_SELF_APPLY_STATE;
        }

        return self::SUB_EFFECT_SLUG_APPLY_STATE;
    }

    private function extractLocalizedValue(mixed $value, string $lang): ?string
    {
        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed !== '' ? $trimmed : null;
        }
        if (is_array($value)) {
            if (isset($value[$lang]) && is_string($value[$lang])) {
                $trimmed = trim((string) $value[$lang]);

                return $trimmed !== '' ? $trimmed : null;
            }
            if (isset($value['fr']) && is_string($value['fr'])) {
                $trimmed = trim((string) $value['fr']);

                return $trimmed !== '' ? $trimmed : null;
            }
            $first = reset($value);
            if (is_string($first)) {
                $trimmed = trim($first);

                return $trimmed !== '' ? $trimmed : null;
            }
        }

        return null;
    }

    /**
     * Ajoute la durée (tours) aux params si présente dans l'instance (ex. "2 durée" sur la carte sort).
     *
     * @param  array<string, mixed>  $instance  Instance d'effet (duration)
     * @param  array<string, mixed>  $params  Params à enrichir — modifié par référence
     */
    private function addDurationToParams(array $instance, array &$params): void
    {
        if (! array_key_exists('duration', $instance)) {
            return;
        }
        $duration = $instance['duration'];
        if (is_numeric($duration)) {
            $turns = max(0, (int) $duration);
            $params['duration'] = $turns;
            $params['duration_formula'] = (string) $turns;
        }
    }

    /**
     * Extrait la description d'une définition d'effet DofusDB (champ description multilingue).
     */
    private function extractEffectDescription(array $definition, string $lang): string
    {
        $desc = $definition['description'] ?? null;
        if (is_string($desc)) {
            $text = $desc;
        } elseif (is_array($desc) && isset($desc[$lang])) {
            $text = (string) $desc[$lang];
        } elseif (is_array($desc) && isset($desc['fr'])) {
            $text = (string) $desc['fr'];
        } elseif (is_array($desc)) {
            $first = reset($desc);
            $text = $first !== false ? (string) $first : '';
        } else {
            $text = '';
        }

        return KrosmozGameTerms::replaceDesenvoutableWithDissipable($text);
    }

    /**
     * @param  array<string, mixed>  $instance  Instance d'effet (diceNum, diceSide, value, effectElement)
     * @param  array<string, mixed>  $definition  Définition /effects/{id} (elementId, characteristic)
     * @param  int  $spellGrade  Grade du spell-level Dofus (1–6+), injecté comme [level] dans les formules conversion.
     * @return array<string, mixed> params pour le pivot (value_formula, characteristic, value_converted, value_formula_crit si fourni ailleurs)
     */
    private function buildParams(
        array $instance,
        array $definition,
        string $charSource,
        string $subEffectSlug,
        ?string $mappedCharacteristicKey = null,
        int $spellGrade = 1
    ): array {
        $dofusValueFormula = $this->buildValueFormula($instance);
        $params = [
            'value_formula' => $dofusValueFormula,
            'dofus_value_formula' => $dofusValueFormula,
            'value_formula_crit' => null,
        ];
        $this->addEffectDirectionToParams($subEffectSlug, $params);
        $this->addMovementKindToParams($subEffectSlug, $instance, $definition, $params);
        $this->addDurationToParams($instance, $params);

        if ($charSource === 'element') {
            $elementId = isset($instance['effectElement']) && is_numeric($instance['effectElement'])
                ? (int) $instance['effectElement']
                : (isset($definition['elementId']) && is_numeric($definition['elementId']) ? (int) $definition['elementId'] : null);
            $key = DofusDbEffectMapping::elementIdToCharacteristicKey($elementId);
            if ($key !== null) {
                $params['characteristic'] = $key;
            }
        }
        if ($charSource === 'characteristic') {
            $key = $mappedCharacteristicKey;
            if (($key === null || $key === '') && isset($definition['characteristic']) && is_numeric($definition['characteristic'])) {
                $dofusdbCharacteristicId = (int) $definition['characteristic'];
                $key = $this->characteristicGetter->getCharacteristicKeyByDofusdbCharacteristicId(
                    $dofusdbCharacteristicId,
                    SpellEffectConversionFormulaResolver::ENTITY_SPELL
                );
                if ($key === null || $key === '') {
                    $key = $this->resolveSpellCharacteristicKeyFromConfig($dofusdbCharacteristicId);
                }
            }
            if (is_string($key) && $key !== '') {
                $params['characteristic'] = $key;
            }
        }

        $this->maybeAttachLifeStealFormulaFromDofusDefinition($subEffectSlug, $definition, $params);
        $this->applyValueConversion($instance, $subEffectSlug, $params, $spellGrade);
        $this->applyLifeStealValueConversion($instance, $params, $spellGrade);
        $this->attachDofusElementIdFromSpellEffectInstance($instance, $params);
        $this->syncCellsFormulaForDeplacement($subEffectSlug, $params);

        return $params;
    }

    /**
     * Le sous-effet « déplacer » attend {@see cells_formula} pour le template [cells] et l’UI ;
     * le scrapping ne remplissait que value_formula — on aligne les deux.
     *
     * @param  array<string, mixed>  $params
     */
    private function syncCellsFormulaForDeplacement(string $subEffectSlug, array &$params): void
    {
        if ($subEffectSlug !== 'déplacer') {
            return;
        }
        $cf = isset($params['cells_formula']) ? trim((string) $params['cells_formula']) : '';
        if ($cf !== '') {
            return;
        }
        if (isset($params['value_converted']) && is_numeric($params['value_converted'])) {
            $params['cells_formula'] = (string) $params['value_converted'];

            return;
        }
        $vf = isset($params['value_formula']) ? trim((string) $params['value_formula']) : '';
        if ($vf === '') {
            return;
        }
        $params['cells_formula'] = $vf;
    }

    /**
     * Renseigne dofus_element_id dans params uniquement si l’instance d’effet DofusDB expose effectElement (0–4).
     * Pas de repli sur la définition /effects (évite les biais type elementId par défaut).
     *
     * @param  array<string, mixed>  $params
     */
    private function attachDofusElementIdFromSpellEffectInstance(array $instance, array &$params): void
    {
        if (! isset($instance['effectElement']) || ! is_numeric($instance['effectElement'])) {
            return;
        }
        $el = (int) $instance['effectElement'];
        if ($el < 0 || $el > 4) {
            return;
        }
        $params['dofus_element_id'] = $el;
    }

    /**
     * Détecte un effet « vol de vie » Dofus (texte) et ajoute life_steal_formula = [dgt] si absent.
     *
     * @param  array<string, mixed>  $definition  Définition /effects/{id}
     * @param  array<string, mixed>  $params  Modifié par référence
     */
    private function maybeAttachLifeStealFormulaFromDofusDefinition(string $subEffectSlug, array $definition, array &$params): void
    {
        if ($subEffectSlug !== 'frapper') {
            return;
        }
        $existing = $params['life_steal_formula'] ?? null;
        if (is_string($existing) && trim($existing) !== '') {
            return;
        }

        $desc = $definition['description'] ?? null;
        $text = '';
        if (is_string($desc)) {
            $text = $desc;
        } elseif (is_array($desc)) {
            $text = (string) ($desc['fr'] ?? $desc['en'] ?? '');
            if ($text === '' && $desc !== []) {
                $first = reset($desc);
                $text = is_string($first) ? $first : '';
            }
        }
        $low = mb_strtolower($text);
        if ($low === '') {
            return;
        }

        $volVie = str_contains($low, 'vol de vie')
            || (str_contains($low, 'vole') && str_contains($low, 'vie'));
        if ($volVie) {
            $params['life_steal_formula'] = '[dgt]';
        }
    }

    /**
     * Conversion Dofus → Krosmoz pour les PV volés (même base « d » que les dommages).
     *
     * @param  array<string, mixed>  $params  Modifié par référence
     */
    private function applyLifeStealValueConversion(array $instance, array &$params, int $spellGrade = 1): void
    {
        $key = $this->formulaResolver->resolveLifeStealCharacteristicKeyForConversion($params);
        if ($key === null) {
            return;
        }

        $d = $this->computeDofusValueForConversion($instance);
        if ($d === null) {
            return;
        }

        $fallback = (float) round($d);
        $context = ['raw' => $instance];
        $converted = $this->dofusConversion->convert(
            $key,
            $this->conversionVariablesForSpell($d, $spellGrade),
            SpellEffectConversionFormulaResolver::ENTITY_SPELL,
            $fallback,
            $context
        );
        $params['life_steal_value_converted'] = $converted;
    }

    /**
     * Variables pour les formules conversion (spell) : [d] brut Dofus, [level] = grade du sort-level.
     *
     * @return array<string, float|int>
     */
    private function conversionVariablesForSpell(float $d, int $spellGrade): array
    {
        return [
            'd' => $d,
            'level' => max(1, $spellGrade),
        ];
    }

    /**
     * Résout la clé caractéristique sort depuis la config JSON de référence (fallback runtime).
     * Retourne une clé courte (ex. po, pa), ensuite normalisée vers *_spell par le resolver.
     */
    private function resolveSpellCharacteristicKeyFromConfig(int $dofusdbCharacteristicId): ?string
    {
        static $map = null;

        if ($map === null) {
            $map = [];
            $path = resource_path('scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz_spell.json');
            if (is_file($path)) {
                $content = @file_get_contents($path);
                if ($content !== false) {
                    $decoded = json_decode($content, true);
                    $mapping = is_array($decoded['mapping'] ?? null) ? $decoded['mapping'] : [];
                    foreach ($mapping as $id => $key) {
                        if (is_numeric($id) && is_string($key) && $key !== '') {
                            $map[(int) $id] = $key;
                        }
                    }
                }
            }
        }

        return $map[$dofusdbCharacteristicId] ?? null;
    }

    /**
     * Calcule la valeur Dofus « d » (moyenne de la plage ou valeur fixe) pour la conversion.
     * Quand diceSide est 0, diceNum porte souvent la valeur (ex. 10 = 10%, 50 = 50).
     *
     * @param  array<string, mixed>  $instance  Instance d'effet (diceNum, diceSide, value)
     * @return float|null Moyenne de diceNum–diceSide, ou diceNum si diceSide=0, ou value, ou null
     */
    private function computeDofusValueForConversion(array $instance): ?float
    {
        $bounds = $this->computeDofusEffectDiceBounds($instance);

        return $bounds !== null ? $bounds['mean'] : null;
    }

    /**
     * Borne min / max / moyenne Dofus pour un effet.
     * DofusDB nomme historiquement ces bornes `diceNum` et `diceSide`, mais elles ne représentent pas NdX.
     * Sert à convertir séparément min et max puis à produire une notation dés réaliste (écart fort = n petit, X grand).
     *
     * @return array{min: float, max: float, mean: float}|null
     */
    private function computeDofusEffectDiceBounds(array $instance): ?array
    {
        $diceNum = isset($instance['diceNum']) && is_numeric($instance['diceNum']) ? (int) $instance['diceNum'] : null;
        $diceSide = isset($instance['diceSide']) && is_numeric($instance['diceSide']) ? (int) $instance['diceSide'] : null;

        if ($diceNum !== null && $diceSide !== null && $diceNum > 0 && $diceSide > 0) {
            $min = min($diceNum, $diceSide);
            $max = max($diceNum, $diceSide);

            return [
                'min' => (float) $min,
                'max' => (float) $max,
                'mean' => ($min + $max) / 2.0,
            ];
        }
        if ($diceNum !== null && $diceNum > 0 && ($diceSide === null || $diceSide === 0)) {
            $v = (float) $diceNum;

            return ['min' => $v, 'max' => $v, 'mean' => $v];
        }
        $value = isset($instance['value']) && is_numeric($instance['value']) ? (float) $instance['value'] : null;
        if ($value !== null) {
            return ['min' => $value, 'max' => $value, 'mean' => $value];
        }

        return null;
    }

    /**
     * Applique la conversion BDD (characteristic_spell) et remplit params.value_converted si possible.
     *
     * @param  array<string, mixed>  $instance  Instance d'effet DofusDB
     * @param  array<string, mixed>  $params  Params déjà remplis (value_formula, characteristic) — modifié par référence
     */
    private function applyValueConversion(array $instance, string $subEffectSlug, array &$params, int $spellGrade = 1): void
    {
        $characteristicKey = $this->formulaResolver->resolveCharacteristicKeyForConversion($subEffectSlug, $params);
        if ($characteristicKey === null) {
            return;
        }
        $d = $this->computeDofusValueForConversion($instance);
        if ($d === null) {
            return;
        }
        $fallback = (float) round($d);
        $context = ['raw' => $instance];
        $converted = $this->dofusConversion->convert(
            $characteristicKey,
            $this->conversionVariablesForSpell($d, $spellGrade),
            SpellEffectConversionFormulaResolver::ENTITY_SPELL,
            $fallback,
            $context
        );
        $params['value_converted'] = $converted;
        $this->applyDirectionalBalanceToConvertedValue($subEffectSlug, $params);

        $conversionFunctionId = $this->characteristicGetter->getConversionFunctionId(
            $characteristicKey,
            SpellEffectConversionFormulaResolver::ENTITY_SPELL
        );
        if ($conversionFunctionId !== 'convertToDice') {
            $params['value_formula'] = (string) $params['value_converted'];

            return;
        }

        $bounds = $this->computeDofusEffectDiceBounds($instance);
        if ($bounds !== null) {
            $kMin = $this->dofusConversion->convert(
                $characteristicKey,
                $this->conversionVariablesForSpell($bounds['min'], $spellGrade),
                SpellEffectConversionFormulaResolver::ENTITY_SPELL,
                (float) round($bounds['min']),
                $context
            );
            $kMax = $this->dofusConversion->convert(
                $characteristicKey,
                $this->conversionVariablesForSpell($bounds['max'], $spellGrade),
                SpellEffectConversionFormulaResolver::ENTITY_SPELL,
                (float) round($bounds['max']),
                $context
            );
            if ($kMin > $kMax) {
                [$kMin, $kMax] = [$kMax, $kMin];
            }
            if ($this->isRestrictiveEffect($subEffectSlug)) {
                $kMin = $this->scaleRestrictiveEffectValue((float) $kMin);
                $kMax = $this->scaleRestrictiveEffectValue((float) $kMax);
            }
            $params['dice_formula'] = $this->diceNotationService->toDiceNotation((float) $kMin, (float) $kMax);
            $params['value_formula'] = $params['dice_formula'];

            return;
        }

        $params['dice_formula'] = $this->diceNotationService->toDiceNotation((float) $params['value_converted']);
        $params['value_formula'] = $params['dice_formula'];
    }

    /**
     * Ajoute une indication lisible par l'UI et les outils d'équilibrage.
     *
     * @param  array<string, mixed>  $params
     */
    private function addEffectDirectionToParams(string $subEffectSlug, array &$params): void
    {
        $params['effect_direction'] = match ($subEffectSlug) {
            'booster', 'soigner', 'protéger', 'donner-pv-temporaires', 'invoquer' => 'bonus',
            'retirer' => 'malus',
            'voler-caracteristiques' => 'steal',
            default => 'action',
        };
    }

    /**
     * Précise le type de déplacement afin de choisir la bonne norme/conversion :
     * saut, téléportation, repousse, attirance ou déplacement générique.
     *
     * @param  array<string, mixed>  $instance
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $params
     */
    private function addMovementKindToParams(string $subEffectSlug, array $instance, array $definition, array &$params): void
    {
        if ($subEffectSlug !== 'déplacer') {
            return;
        }

        $effectId = isset($instance['effectId']) && is_numeric($instance['effectId'])
            ? (int) $instance['effectId']
            : (isset($params['dofus_effect_id']) && is_numeric($params['dofus_effect_id'])
                ? (int) $params['dofus_effect_id']
                : 0);

        // Identifiants Dofus stables prioritaires sur le texte (traductions / formulations variables).
        $kindFromId = match ($effectId) {
            5, 1021, 1041, 4001, 4002, 1103, 4003 => 'push',
            6, 1022, 1042, 1043 => 'pull',
            4 => 'teleport',
            default => null,
        };

        $text = $this->normalizeDecisionText(
            implode(' ', array_filter([
                $this->extractEffectDescription($definition, 'fr'),
                $this->extractLocalizedValue($instance['description'] ?? null, 'fr') ?? '',
                $this->extractLocalizedValue($instance['raw_description'] ?? null, 'fr') ?? '',
            ]))
        );

        $kind = $kindFromId ?? 'movement';
        if ($kindFromId === null) {
            if (preg_match('/\b(attire|attirer|rapproche|vers le lanceur|avance)\b/u', $text) === 1) {
                $kind = 'pull';
            } elseif (preg_match('/\b(repousse|repousser|pousse|eloigne|recule)\b/u', $text) === 1) {
                $kind = 'push';
            } elseif (preg_match('/\b(teleporte|teleportation|echange de position|transpose)\b/u', $text) === 1) {
                $kind = 'teleport';
            } elseif (preg_match('/\b(saute|saut|bond|bondit)\b/u', $text) === 1) {
                $kind = 'jump';
            }
        }

        if ($kind === 'teleport') {
            $params['teleport'] = true;
        }

        $params['movement_kind'] = $kind;
    }

    /**
     * Les retraits et vols de caractéristiques sont plus forts qu'un simple bonus :
     * ils réduisent l'économie d'action ou la bounded accuracy adverse. On applique
     * donc une conversion effective plus basse, sans créer une seconde famille de
     * caractéristiques pour chaque stat.
     *
     * @param  array<string, mixed>  $params
     */
    private function applyDirectionalBalanceToConvertedValue(string $subEffectSlug, array &$params): void
    {
        if (! $this->isRestrictiveEffect($subEffectSlug)) {
            return;
        }
        if (! isset($params['value_converted']) || ! is_numeric($params['value_converted'])) {
            return;
        }

        if ($this->isRelativeResistanceCharacteristic($params['characteristic'] ?? null)) {
            $value = (int) $params['value_converted'];
            $params['value_converted'] = $value > 0 ? min(50, $value) : 0;

            return;
        }

        $params['value_converted'] = $this->scaleRestrictiveEffectValue((float) $params['value_converted']);
    }

    private function isRelativeResistanceCharacteristic(mixed $characteristic): bool
    {
        if (! is_string($characteristic)) {
            return false;
        }
        if (str_ends_with($characteristic, '_spell')) {
            $characteristic = substr($characteristic, 0, -6);
        }

        return in_array($characteristic, [
            'res_neutre',
            'res_terre',
            'res_feu',
            'res_eau',
            'res_air',
            'res_sagesse',
            'res_vitalite',
        ], true);
    }

    private function isRestrictiveEffect(string $subEffectSlug): bool
    {
        return in_array($subEffectSlug, ['retirer', 'voler-caracteristiques'], true);
    }

    private function scaleRestrictiveEffectValue(float $value): int
    {
        if ($value <= 0) {
            return 0;
        }

        return max(1, (int) floor($value / 2));
    }

    /**
     * Construit une formule exécutable depuis les bornes DofusDB.
     * `diceNum` et `diceSide` sont respectivement le minimum et le maximum, pas une notation NdX.
     * La moyenne sert uniquement de repli lorsque la caractéristique ne dispose d'aucune conversion.
     *
     * @param  array<string, mixed>  $instance
     */
    private function buildValueFormula(array $instance): ?string
    {
        $diceNum = isset($instance['diceNum']) && is_numeric($instance['diceNum']) ? (int) $instance['diceNum'] : null;
        $diceSide = isset($instance['diceSide']) && is_numeric($instance['diceSide']) ? (int) $instance['diceSide'] : null;
        if ($diceNum !== null && $diceSide !== null && $diceNum > 0 && $diceSide > 0) {
            $min = min($diceNum, $diceSide);
            $max = max($diceNum, $diceSide);

            return $min === $max ? (string) $min : "({$min} + {$max}) / 2";
        }
        // diceSide 0 ou absent : valeur fixe dans diceNum (ex. 10% bouclier) ou value
        if ($diceNum !== null && $diceNum > 0 && ($diceSide === null || $diceSide === 0)) {
            return (string) $diceNum;
        }
        $value = isset($instance['value']) && is_numeric($instance['value']) ? (int) $instance['value'] : null;
        if ($value !== null) {
            return (string) $value;
        }

        return null;
    }

    private function normalizeDecisionText(string $text): string
    {
        $value = trim(mb_strtolower($text));
        if ($value === '') {
            return '';
        }

        $value = strip_tags($value);
        $value = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c'],
            $value
        );
        $value = preg_replace('/<[^>]+>/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    private function extractSpellName(array $spellRaw, string $lang): string
    {
        $name = $spellRaw['name'] ?? null;
        if (is_array($name) && isset($name[$lang])) {
            return (string) $name[$lang];
        }
        if (is_string($name)) {
            return $name;
        }
        if (is_array($name)) {
            return (string) ($name['fr'] ?? reset($name) ?? 'Sans nom');
        }

        return 'Sans nom';
    }

    private function buildSlug(string $name, int $spellId): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'spell';
        }

        return $spellId > 0 ? $base.'-'.$spellId : $base;
    }
}
