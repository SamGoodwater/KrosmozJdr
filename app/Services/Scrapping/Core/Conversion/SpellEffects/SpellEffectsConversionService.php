<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Jdr\DiceNotationService;
use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use Illuminate\Support\Str;

/**
 * Sous-service de conversion dédié aux effets de sorts DofusDB vers KrosmozJDR.
 *
 * Prend en entrée les données brutes du sort et les spell-levels (déjà récupérés),
 * résout chaque effectId via DofusDbEffectCatalog, applique le mapping effectId → SubEffect
 * et produit une structure prête pour l'intégration (EffectGroup + Effects + sous-effets).
 * Phase 3 : conversion des valeurs via characteristic_spell (value_converted).
 *
 * @see docs/50-Fonctionnalités/Scrapping/DOFUSDB_EFFECTS_CONVERSION.md
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md
 */
final class SpellEffectsConversionService
{
    public function __construct(
        private DofusDbEffectCatalog $effectCatalog,
        private DofusdbEffectMappingService $mappingService,
        private SpellEffectConversionFormulaResolver $formulaResolver,
        private DofusConversionService $dofusConversion,
        private CharacteristicGetterService $characteristicGetter,
        private DiceNotationService $diceNotationService,
    ) {
    }

    /**
     * Convertit les effets d'un sort DofusDB (sort brut + spell-levels) en structure KrosmozJDR.
     *
     * @param array<string, mixed> $spellRaw Réponse GET /spells/{id} (doit contenir id, name, spellLevels)
     * @param list<array<string, mixed>> $spellLevelsData Liste des réponses GET /spell-levels/{levelId} (grade, effects[], criticalEffect[])
     * @param array{lang?: string} $options lang pour le catalogue d'effets (défaut fr)
     */
    public function convert(
        array $spellRaw,
        array $spellLevelsData,
        array $options = []
    ): SpellEffectsConversionResult {
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

        return new SpellEffectsConversionResult($effectGroup, $effects);
    }

    /**
     * @param array<string, mixed> $levelData Un spell-level (effects[], criticalEffect[])
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
        $slug = $baseSlug . '-' . $degree;

        $subEffects = [];
        $effectsList = $levelData['effects'] ?? [];
        $criticalList = $this->indexCriticalEffectsByOrder($levelData['criticalEffect'] ?? []);

        foreach ($effectsList as $index => $instance) {
            if (!is_array($instance)) {
                continue;
            }
            $effectId = isset($instance['effectId']) ? (int) $instance['effectId'] : 0;
            if ($effectId === 0) {
                continue;
            }

            $mapping = $this->mappingService->getSubEffectForEffectId($effectId);
            $subEffectSlug = null;
            $charSource = null;
            $definition = [];

            if ($mapping !== null) {
                [$subEffectSlug, $charSource] = $mapping;
                if ($charSource === 'element') {
                    $definition = $this->effectCatalog->get($effectId, $lang);
                }
            } else {
                $subEffectSlug = DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER;
                $definition = $this->effectCatalog->get($effectId, $lang);
            }

            $order = isset($instance['order']) ? (int) $instance['order'] : $index;
            $params = $subEffectSlug === DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER
                ? $this->buildParamsForOther($instance, $definition, $lang)
                : $this->buildParams($instance, $definition, $charSource ?? 'none', $subEffectSlug);
            $critOnly = false;

            $criticalInstance = $criticalList[$order] ?? null;
            if ($criticalInstance !== null && is_array($criticalInstance)) {
                $critFormula = $this->buildValueFormula($criticalInstance);
                if ($critFormula !== null && $critFormula !== '') {
                    $params['value_formula_crit'] = $critFormula;
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
     * Extrait la notation zone (point, line-WxL, cross-N, circle-N, rect-WxH) depuis le premier zoneDescr du niveau.
     *
     * @param array<string, mixed> $levelData spell-level (effects[].zoneDescr)
     * @return string|null
     */
    private function extractAreaNotationFromLevel(array $levelData): ?string
    {
        $effectsList = $levelData['effects'] ?? [];
        foreach ($effectsList as $inst) {
            if (!is_array($inst)) {
                continue;
            }
            $zone = $inst['zoneDescr'] ?? null;
            if (!is_array($zone)) {
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
     * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
     * @param array{shape?: int, param1?: int, param2?: int} $zoneDescr
     */
    public static function zoneDescrToNotation(array $zoneDescr): ?string
    {
        $shape = isset($zoneDescr['shape']) ? (int) $zoneDescr['shape'] : 0;
        $p1 = isset($zoneDescr['param1']) ? (int) $zoneDescr['param1'] : 0;
        $p2 = isset($zoneDescr['param2']) ? (int) $zoneDescr['param2'] : 0;

        return match (true) {
            $shape === 0, $shape === 80 => 'point',  // case unique (CAC)
            $shape === 67, $shape === 79 => self::circleNotation($p1, $p2),  // 67 cercle, 79 anneau sans centre
            $shape === 76 => 'line-1x' . max(1, $p1 ?: 1),  // ligne
            $shape === 88 => self::crossNotation(0, $p1 ?: 1),  // croix pleine (min=0)
            $shape === 81 => self::crossNotation(1, $p1 ?: 1),  // croix sans centre (min=1)
            $shape === 71 => self::rectNotation($p1, $p2),  // carré (ou rect si p2)
            // Anciens IDs (rétrocompat)
            $shape === 1 => 'line-1x' . max(1, $p1 ?: 1),
            $shape === 2, $shape === 4 => self::crossNotation(0, $p1 ?: 1),
            $shape === 3 => self::circleNotation($p1, $p2),
            default => $shape > 0 ? 'shape-' . $shape . ($p1 !== 0 || $p2 !== 0 ? '-' . $p1 . '-' . $p2 : '') : null,
        };
    }

    /**
     * Notation cercle : circle-{min}-{max} (rayon intérieur, rayon extérieur).
     */
    private static function circleNotation(int $p1, int $p2): string
    {
        if ($p2 <= 0) {
            $radius = max(1, $p1);
            return 'circle-0-' . $radius;
        }
        $min = max(0, $p1);
        $max = max($min, $p2);
        return 'circle-' . $min . '-' . $max;
    }

    /**
     * Notation croix : cross-{min}-{max}. 0-N = pleine, 1-N = sans centre.
     */
    private static function crossNotation(int $min, int $max): string
    {
        $max = max(1, $max);
        $min = max(0, min($min, $max));
        return 'cross-' . $min . '-' . $max;
    }

    /**
     * Notation rectangle / carré : rect-{W}x{H}. Si param2 = 0, carré NxN.
     */
    private static function rectNotation(int $p1, int $p2): string
    {
        $w = max(1, $p1 ?: 1);
        $h = $p2 > 0 ? max(1, $p2) : $w;
        return 'rect-' . $w . 'x' . $h;
    }

    /**
     * Déduit target_type (direct / trap / glyph) depuis le niveau (Dofus : triggers, etc.).
     *
     * @param array<string, mixed> $levelData
     * @return string
     */
    private function extractTargetTypeFromLevel(array $levelData): string
    {
        $effectsList = $levelData['effects'] ?? [];
        foreach ($effectsList as $inst) {
            if (!is_array($inst)) {
                continue;
            }
            $triggers = $inst['triggers'] ?? null;
            if (is_string($triggers) && str_contains(strtoupper($triggers), 'P')) {
                return \App\Models\Effect::TARGET_TRAP;
            }
            if (is_string($triggers) && str_contains(strtoupper($triggers), 'G')) {
                return \App\Models\Effect::TARGET_GLYPH;
            }
        }
        return \App\Models\Effect::TARGET_DIRECT;
    }

    /**
     * @param list<array<string, mixed>> $criticalEffect
     * @return array<int, array<string, mixed>> order => instance
     */
    private function indexCriticalEffectsByOrder(array $criticalEffect): array
    {
        $indexed = [];
        foreach ($criticalEffect as $inst) {
            if (!is_array($inst)) {
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
     * @param array<string, mixed> $instance Instance d'effet (diceNum, diceSide, value)
     * @param array<string, mixed> $definition Définition GET /effects/{id} (description multilingue)
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

        return $params;
    }

    /**
     * Ajoute la durée (tours) aux params si présente dans l'instance (ex. "2 durée" sur la carte sort).
     *
     * @param array<string, mixed> $instance Instance d'effet (duration)
     * @param array<string, mixed> $params Params à enrichir — modifié par référence
     */
    private function addDurationToParams(array $instance, array &$params): void
    {
        if (!array_key_exists('duration', $instance)) {
            return;
        }
        $duration = $instance['duration'];
        if (is_numeric($duration)) {
            $params['duration'] = (int) $duration;
        }
    }

    /**
     * Extrait la description d'une définition d'effet DofusDB (champ description multilingue).
     */
    private function extractEffectDescription(array $definition, string $lang): string
    {
        $desc = $definition['description'] ?? null;
        if (is_string($desc)) {
            return $desc;
        }
        if (is_array($desc) && isset($desc[$lang])) {
            return (string) $desc[$lang];
        }
        if (is_array($desc) && isset($desc['fr'])) {
            return (string) $desc['fr'];
        }
        if (is_array($desc)) {
            $first = reset($desc);
            return $first !== false ? (string) $first : '';
        }
        return '';
    }

    /**
     * @param array<string, mixed> $instance Instance d'effet (diceNum, diceSide, value, effectElement)
     * @param array<string, mixed> $definition Définition /effects/{id} (elementId, characteristic)
     * @return array<string, mixed> params pour le pivot (value_formula, characteristic, value_converted, value_formula_crit si fourni ailleurs)
     */
    private function buildParams(array $instance, array $definition, string $charSource, string $subEffectSlug): array
    {
        $params = [
            'value_formula' => $this->buildValueFormula($instance),
            'value_formula_crit' => null,
        ];
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

        $this->applyValueConversion($instance, $subEffectSlug, $params);

        return $params;
    }

    /**
     * Calcule la valeur Dofus « d » (moyenne des dés ou valeur fixe) pour la conversion.
     * Quand diceSide est 0, diceNum porte souvent la valeur (ex. 10 = 10%, 50 = 50).
     *
     * @param array<string, mixed> $instance Instance d'effet (diceNum, diceSide, value)
     * @return float|null Moyenne diceNum*(diceSide+1)/2, ou diceNum si diceSide=0, ou value, ou null
     */
    private function computeDofusValueForConversion(array $instance): ?float
    {
        $diceNum = isset($instance['diceNum']) && is_numeric($instance['diceNum']) ? (int) $instance['diceNum'] : null;
        $diceSide = isset($instance['diceSide']) && is_numeric($instance['diceSide']) ? (int) $instance['diceSide'] : null;
        if ($diceNum !== null && $diceSide !== null && $diceNum > 0 && $diceSide > 0) {
            return $diceNum * ($diceSide + 1) / 2.0;
        }
        if ($diceNum !== null && $diceNum > 0 && ($diceSide === null || $diceSide === 0)) {
            return (float) $diceNum;
        }
        $value = isset($instance['value']) && is_numeric($instance['value']) ? (float) $instance['value'] : null;
        if ($value !== null) {
            return $value;
        }
        return null;
    }

    /**
     * Applique la conversion BDD (characteristic_spell) et remplit params.value_converted si possible.
     *
     * @param array<string, mixed> $instance Instance d'effet DofusDB
     * @param array<string, mixed> $params Params déjà remplis (value_formula, characteristic) — modifié par référence
     */
    private function applyValueConversion(array $instance, string $subEffectSlug, array &$params): void
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
            ['d' => $d],
            SpellEffectConversionFormulaResolver::ENTITY_SPELL,
            $fallback,
            $context
        );
        $params['value_converted'] = $converted;

        $conversionFunctionId = $this->characteristicGetter->getConversionFunctionId(
            $characteristicKey,
            SpellEffectConversionFormulaResolver::ENTITY_SPELL
        );
        if ($conversionFunctionId === 'convertToDice') {
            $params['dice_formula'] = $this->diceNotationService->toDiceNotation((float) $converted);
        }
    }

    /**
     * Construit la formule de valeur : XdY (dés), ou valeur fixe (diceNum si diceSide=0, sinon value).
     * Quand diceSide est 0, Dofus utilise souvent diceNum pour la valeur (ex. 10 = 10%, 50 = 50).
     *
     * @param array<string, mixed> $instance
     */
    private function buildValueFormula(array $instance): ?string
    {
        $diceNum = isset($instance['diceNum']) && is_numeric($instance['diceNum']) ? (int) $instance['diceNum'] : null;
        $diceSide = isset($instance['diceSide']) && is_numeric($instance['diceSide']) ? (int) $instance['diceSide'] : null;
        if ($diceNum !== null && $diceSide !== null && $diceNum > 0 && $diceSide > 0) {
            return $diceNum . 'd' . $diceSide;
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
        return $spellId > 0 ? $base . '-' . $spellId : $base;
    }
}
