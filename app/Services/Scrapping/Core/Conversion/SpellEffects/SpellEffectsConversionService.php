<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Conversion\SpellEffects;

use App\Models\Entity\Spell;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Jdr\DiceNotationService;
use App\Services\Scrapping\Config\DofusDbEffectCatalog;
use App\Services\Scrapping\Config\DofusDbSpellStateCatalog;
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
    private const SAVE_DC_DEFAULT_FORMULA = '10 + modificateur de caractéristique';
    private const SUB_EFFECT_SLUG_APPLY_STATE = 'appliquer-etat';
    private const SUB_EFFECT_SLUG_SELF_APPLY_STATE = 's-appliquer-etat';

    public function __construct(
        private DofusDbEffectCatalog $effectCatalog,
        private DofusDbSpellStateCatalog $spellStateCatalog,
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

        $resolution = $this->inferSpellResolution($effects, $spellRaw);

        return new SpellEffectsConversionResult($effectGroup, $effects, $resolution);
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

            $definition = $this->effectCatalog->get($effectId, $lang);
            $stateData = $this->resolveSpellStateData($instance, $definition, $lang);

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
            }

            $order = isset($instance['order']) ? (int) $instance['order'] : $index;
            $params = $subEffectSlug === DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER
                ? $this->buildParamsForOther($instance, $definition, $lang)
                : $this->buildParams($instance, $definition, $charSource ?? 'none', $subEffectSlug, $mappedCharacteristicKey);
            $params['dofus_effect_id'] = $effectId;
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
     * @param array<string, mixed> $instance
     * @param array<string, mixed> $definition
     * @param array<string, mixed> $stateData
     * @return array<string, mixed>
     */
    private function buildParamsForState(array $instance, array $stateData, int $effectId): array
    {
        $params = [
            'state_dofusdb_id' => (int) ($stateData['id'] ?? 0),
            'state_name' => $this->extractLocalizedValue($stateData['name'] ?? null, 'fr'),
            'state_icon' => isset($stateData['icon']) ? (string) $stateData['icon'] : null,
            'state_image' => isset($stateData['img']) ? (string) $stateData['img'] : null,
            'dispellable' => isset($instance['dispellable']) ? (bool) $instance['dispellable'] : null,
            'target_mask' => isset($instance['targetMask']) ? (string) $instance['targetMask'] : null,
            'target_id' => isset($instance['targetId']) && is_numeric($instance['targetId']) ? (int) $instance['targetId'] : null,
            'dofus_effect_id' => $effectId,
            'state_flags' => [
                'cant_be_moved' => (bool) ($stateData['cantBeMoved'] ?? false),
                'cant_be_pushed' => (bool) ($stateData['cantBePushed'] ?? false),
                'prevents_spell_cast' => (bool) ($stateData['preventsSpellCast'] ?? false),
                'invulnerable' => (bool) ($stateData['invulnerable'] ?? false),
                'incurable' => (bool) ($stateData['incurable'] ?? false),
            ],
        ];
        $this->addDurationToParams($instance, $params);

        return $params;
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
     * @param array<string, mixed> $instance
     * @param array<string, mixed> $definition
     * @return array<string, mixed>|null
     */
    private function resolveSpellStateData(array $instance, array $definition, string $lang): ?array
    {
        if (!$this->isStateEffectDefinition($definition)) {
            return null;
        }

        $stateId = $this->extractStateIdFromInstance($instance);
        if ($stateId <= 0) {
            return null;
        }

        $state = $this->spellStateCatalog->get($stateId, $lang);
        if ($state === []) {
            return ['id' => $stateId, 'name' => null];
        }

        return $state;
    }

    /**
     * @param array<string, mixed> $definition
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
     * @param array<string, mixed> $instance
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
     * @param array<string, mixed> $instance
     */
    private function resolveStateSubEffectSlug(array $instance): string
    {
        $targetMask = strtoupper((string) ($instance['targetMask'] ?? ''));
        if ($targetMask !== '' && str_contains($targetMask, 'C')) {
            return self::SUB_EFFECT_SLUG_SELF_APPLY_STATE;
        }

        return self::SUB_EFFECT_SLUG_APPLY_STATE;
    }

    /**
     * @param mixed $value
     */
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
    private function buildParams(
        array $instance,
        array $definition,
        string $charSource,
        string $subEffectSlug,
        ?string $mappedCharacteristicKey = null
    ): array
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

        $this->applyValueConversion($instance, $subEffectSlug, $params);

        return $params;
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

    /**
     * Déduit la résolution du sort (jet d'attaque / sauvegarde / réussite auto) à partir des sous-effets.
     * Règles métier:
     * - Retraits caractéristiques => sauvegarde (prioritaire, même avec dommages).
     * - Dommages (même avec déplacement) => jet d'attaque (contre CA).
     * - Boosts/soins/invocation/soutien => réussite auto.
     * - Placement sans dommage explicite => sauvegarde (cas défensif par défaut).
     *
     * @param list<array<string, mixed>> $effects
     * @param array<string, mixed> $spellRaw
     * @return array<string, string|null>
     */
    private function inferSpellResolution(array $effects, array $spellRaw): array
    {
        $hasDamage = false;
        $hasPlacement = false;
        $hasRemoval = false;
        $hasSupport = false;
        $saveAbilityHint = null;

        foreach ($effects as $effect) {
            $subEffects = $effect['sub_effects'] ?? [];
            if (!is_array($subEffects)) {
                continue;
            }

            foreach ($subEffects as $subEffect) {
                if (!is_array($subEffect)) {
                    continue;
                }

                $slug = (string) ($subEffect['sub_effect_slug'] ?? '');
                $params = is_array($subEffect['params'] ?? null) ? $subEffect['params'] : [];
                $characteristic = isset($params['characteristic']) ? strtolower((string) $params['characteristic']) : '';
                $valueFormula = isset($params['value_formula']) ? trim((string) $params['value_formula']) : '';

                if (in_array($slug, ['frapper', 'voler-vie'], true)) {
                    $hasDamage = true;
                    continue;
                }

                if ($slug === 'déplacer') {
                    $hasPlacement = true;
                    continue;
                }

                if ($slug === 'retirer' || $slug === 'voler-caracteristiques') {
                    $hasRemoval = true;
                    if ($saveAbilityHint === null) {
                        $saveAbilityHint = $this->inferSaveAbilityFromCharacteristicKey($characteristic);
                    }
                    continue;
                }

                if (in_array($slug, ['booster', 'soigner', 'protéger', 'invoquer'], true)) {
                    $hasSupport = true;
                }

                if ($slug === 'booster') {
                    if ($this->isCharacteristicRemovalKey($characteristic) || str_starts_with($valueFormula, '-')) {
                        $hasRemoval = true;
                        if ($saveAbilityHint === null) {
                            $saveAbilityHint = $this->inferSaveAbilityFromCharacteristicKey($characteristic);
                        }
                    }
                }

                if ($slug === DofusDbEffectMapping::SUB_EFFECT_SLUG_OTHER) {
                    $otherText = strtolower((string) ($params['value'] ?? ''));
                    if ($otherText !== '') {
                        if ($this->isRemovalText($otherText)) {
                            $hasRemoval = true;
                            if ($saveAbilityHint === null) {
                                $saveAbilityHint = $this->inferSaveAbilityFromOtherText($otherText);
                            }
                        }
                        if ($this->isPlacementText($otherText)) {
                            $hasPlacement = true;
                        }
                        if ($this->isDamageText($otherText)) {
                            $hasDamage = true;
                        }
                        if ($this->isSupportText($otherText)) {
                            $hasSupport = true;
                        }
                    }
                }
            }
        }

        if ($hasRemoval || ($hasPlacement && !$hasDamage && !$hasSupport)) {
            return [
                'resolution_mode' => Spell::RESOLUTION_SAVING_THROW,
                'attack_characteristic_key' => null,
                'save_characteristic_key' => $saveAbilityHint ?? 'sagesse',
                'save_dc_formula' => self::SAVE_DC_DEFAULT_FORMULA,
                'save_success_note' => $hasDamage
                    ? "En cas de sauvegarde réussie, réduire l'effet (ex: demi-dégâts) et annuler les retraits."
                    : "En cas de sauvegarde réussie, annuler l'effet du sort.",
            ];
        }

        if ($hasDamage) {
            return [
                'resolution_mode' => Spell::RESOLUTION_ATTACK_ROLL,
                'attack_characteristic_key' => $this->inferAttackCharacteristicFromSpellRaw($spellRaw),
                'save_characteristic_key' => null,
                'save_dc_formula' => null,
                'save_success_note' => null,
            ];
        }

        if (!$hasDamage && !$hasRemoval) {
            return [
                'resolution_mode' => Spell::RESOLUTION_AUTO_SUCCESS,
                'attack_characteristic_key' => null,
                'save_characteristic_key' => null,
                'save_dc_formula' => null,
                'save_success_note' => null,
            ];
        }

        return [
            'resolution_mode' => Spell::RESOLUTION_ATTACK_ROLL,
            'attack_characteristic_key' => $this->inferAttackCharacteristicFromSpellRaw($spellRaw),
            'save_characteristic_key' => null,
            'save_dc_formula' => null,
            'save_success_note' => null,
        ];
    }

    private function inferAttackCharacteristicFromSpellRaw(array $spellRaw): string
    {
        $elementId = isset($spellRaw['elementId']) && is_numeric($spellRaw['elementId'])
            ? (int) $spellRaw['elementId']
            : (isset($spellRaw['spell_global']['elementId']) && is_numeric($spellRaw['spell_global']['elementId'])
                ? (int) $spellRaw['spell_global']['elementId']
                : null);

        return match ($elementId) {
            1 => 'intel',
            2 => 'chance',
            3 => 'strong',
            4 => 'agi',
            default => 'strong',
        };
    }

    private function isCharacteristicRemovalKey(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        $needles = [
            'retrait_pa',
            'retrait_pm',
            'ap_reduction',
            'mp_reduction',
            'dodge_action_points',
            'dodge_movement_points',
            'dodge_spell',
            'fuite',
            'tacle',
        ];
        foreach ($needles as $needle) {
            if (str_contains($key, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function inferSaveAbilityFromCharacteristicKey(string $key): ?string
    {
        if ($key === '') {
            return null;
        }

        if (str_contains($key, 'retrait_pa') || str_contains($key, 'retrait_pm') || str_contains($key, 'ap_reduction') || str_contains($key, 'mp_reduction')) {
            return 'sagesse';
        }
        if (str_contains($key, 'fuite') || str_contains($key, 'tacle') || str_contains($key, 'agi')) {
            return 'agi';
        }

        return null;
    }

    private function inferSaveAbilityFromOtherText(string $otherText): ?string
    {
        $text = $this->normalizeDecisionText($otherText);

        if (preg_match('/\b(pa|pm|retrait pa|retrait pm)\b/u', $text) === 1) {
            return 'sagesse';
        }
        if (preg_match('/\b(fuite|tacle|agilite)\b/u', $text) === 1) {
            return 'agi';
        }
        if (preg_match('/\b(force)\b/u', $text) === 1) {
            return 'strong';
        }
        if (preg_match('/\b(intelligence)\b/u', $text) === 1) {
            return 'intel';
        }
        if (preg_match('/\b(chance)\b/u', $text) === 1) {
            return 'chance';
        }
        if (preg_match('/\b(vitalite)\b/u', $text) === 1) {
            return 'vitality';
        }

        return null;
    }

    private function isRemovalText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        if ($normalized === '' || str_contains($normalized, 'kamas')) {
            return false;
        }

        // "X dommages ... PA utilise" = scaling de dégâts, pas un retrait de PA.
        if (str_contains($normalized, 'dommage') && str_contains($normalized, 'pa utilise')) {
            return false;
        }

        $mentionsNegativePattern = preg_match('/-\s*#|\bretire\b|\bretrait\b/u', $normalized) === 1;
        $mentionsSteal = preg_match('/\b(vole|vol de)\b/u', $normalized) === 1;
        $mentionsStat = preg_match('/\b(pa|pm|fuite|tacle|portee|sagesse|intelligence|agilite|chance|force|vitalite)\b/u', $normalized) === 1;

        return $mentionsNegativePattern || ($mentionsSteal && $mentionsStat);
    }

    private function isPlacementText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(repousse|attire|teleporte|pousse|avance|recule|deplace|echange de position)\b/u', $normalized) === 1;
    }

    private function isDamageText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(dommage|dommages|degat|degats|vol de vie|frappe)\b/u', $normalized) === 1;
    }

    private function isSupportText(string $text): bool
    {
        $normalized = $this->normalizeDecisionText($text);

        return preg_match('/\b(invoque|soin|protege|bouclier|boost|augmente|rend)\b/u', $normalized) === 1;
    }

    private function normalizeDecisionText(string $text): string
    {
        $value = trim(mb_strtolower($text));
        if ($value === '') {
            return '';
        }

        // Supprime tags/sprites et harmonise les accents pour des regex stables.
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
        return $spellId > 0 ? $base . '-' . $spellId : $base;
    }
}
