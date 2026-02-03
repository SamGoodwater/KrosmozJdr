<?php

declare(strict_types=1);

namespace App\Services\Characteristic\DofusConversion;

use App\Services\Characteristic\CharacteristicService;
use App\Services\Characteristic\FormulaEvaluator;

/**
 * Formules de conversion DofusDB → KrosmozJDR.
 *
 * Implémente les formules / fonctions qui transforment les données brutes
 * DofusDB en données exploitables KrosmozJDR. Lit les formules depuis
 * DofusDbConversionFormulaService (BDD) et la config depuis DofusdbConversionConfigService (BDD).
 *
 * @see DofusdbConversionConfigService
 * @see docs/50-Fonctionnalités/Characteristics-DB/PLAN_FORMULES_DOFUSDB_BDD.md
 */
final class DofusDbConversionFormulas
{
    /** Caractéristique "ancre" pour le handler batch des résistances (res_neutre). */
    private const RESISTANCE_BATCH_ANCHOR = 'res_neutre';

    public function __construct(
        private readonly CharacteristicService $characteristicService,
        private readonly DofusDbConversionFormulaService $formulaService,
        private readonly DofusdbConversionConfigService $configService,
        private readonly FormulaEvaluator $formulaEvaluator,
        private readonly ConversionHandlerRegistry $handlerRegistry
    ) {
    }

    /**
     * Retourne la config de conversion DofusDB (mapping, limites) depuis la BDD.
     *
     * @return array<string, mixed>
     */
    public function getConversionConfig(): array
    {
        return $this->configService->getConfig();
    }

    /**
     * Convertit le niveau DofusDB en niveau Krosmoz : k = d / 10 (ou formule personnalisée).
     *
     * @param int|float|null $dofusdbValue Niveau DofusDB (d)
     * @param string $entityType monster, class ou item (pour clamp)
     */
    public function convertLevel(int|float|null $dofusdbValue, string $entityType = 'monster'): int
    {
        $d = $dofusdbValue !== null && is_numeric($dofusdbValue) ? (float) $dofusdbValue : 0.0;
        $conversionFormula = $this->formulaService->getConversionFormula('level', $entityType);
        if ($conversionFormula !== null) {
            $k = $this->formulaEvaluator->evaluateFormulaOrTable($conversionFormula, ['d' => $d]);
            $k = $k !== null ? (int) round($k) : 0;
        } else {
            $formula = $this->formulaService->getFormula('level', $entityType);
            if ($formula !== null && $formula['formula_type'] === 'linear') {
                $divisor = (int) ($formula['parameters']['divisor'] ?? 10);
            } else {
                $config = $this->configService->getConfig()['formulas']['level'] ?? [];
                $divisor = (int) ($config['divisor'] ?? 10);
            }
            $k = (int) round($d / $divisor);
        }
        $k = $this->applyValueHandler('level', $entityType, $k);

        return $this->clampToCharacteristicLimits('level', $k, $entityType);
    }

    /**
     * Convertit les points de vie DofusDB en vie Krosmoz : k = d / 200 + level * 5 (ou formule personnalisée).
     *
     * @param int|float|null $dofusdbValue Vie DofusDB (d)
     * @param int $krosmozLevel Niveau Krosmoz déjà converti (level)
     * @param string $entityType monster, class ou item (pour clamp)
     */
    public function convertLife(int|float|null $dofusdbValue, int $krosmozLevel, string $entityType = 'monster'): int
    {
        $d = $dofusdbValue !== null && is_numeric($dofusdbValue) ? (float) $dofusdbValue : 0.0;
        $conversionFormula = $this->formulaService->getConversionFormula('life', $entityType);
        if ($conversionFormula !== null) {
            $k = $this->formulaEvaluator->evaluateFormulaOrTable($conversionFormula, ['d' => $d, 'level' => $krosmozLevel]);
            $k = $k !== null ? (int) round($k) : 0;
        } else {
            $formula = $this->formulaService->getFormula('life', $entityType);
            if ($formula !== null && $formula['formula_type'] === 'linear_with_level') {
                $divisor = (int) ($formula['parameters']['divisor'] ?? 200);
                $levelFactor = (int) ($formula['parameters']['level_factor'] ?? 5);
            } else {
                $config = $this->configService->getConfig()['formulas']['life'] ?? [];
                $divisor = (int) ($config['divisor'] ?? 200);
                $levelFactor = (int) ($config['level_factor'] ?? 5);
            }
            $k = (int) round($d / $divisor + $krosmozLevel * $levelFactor);
        }
        $k = $this->applyValueHandler('life', $entityType, $k);

        return $this->clampToCharacteristicLimits('life', $k, $entityType);
    }

    /**
     * Convertit un attribut DofusDB (Force, Intelligence, chance, agilité) en Krosmoz.
     *
     * class  : k = 6 + 24 * sqrt((d-50)/1150)
     * monster : k = 0 + 26 * sqrt((d-50)/1150)
     * Ou formule personnalisée via conversion_formula (variable [d]).
     *
     * @param string $characteristicId strength, intelligence, chance ou agility
     * @param int|float|string|null $dofusdbValue Valeur DofusDB (d), peut être string (API)
     * @param string $entityType class ou monster (formule différente)
     */
    public function convertAttribute(string $characteristicId, int|float|string|null $dofusdbValue, string $entityType = 'monster'): int
    {
        $d = $dofusdbValue !== null && $dofusdbValue !== '' && is_numeric($dofusdbValue) ? (float) $dofusdbValue : 0.0;
        $conversionFormula = $this->formulaService->getConversionFormula($characteristicId, $entityType);
        if ($conversionFormula !== null) {
            $k = $this->formulaEvaluator->evaluateFormulaOrTable($conversionFormula, ['d' => $d]);
            return $this->clampToCharacteristicLimits($characteristicId, $k !== null ? (int) round($k) : 0, $entityType);
        }
        $formula = $this->formulaService->getFormula($characteristicId, $entityType);
        if ($formula !== null && $formula['formula_type'] === 'sqrt_attribute') {
            $p = $formula['parameters'];
            $base = (float) ($p['base'] ?? 0);
            $coeff = (float) ($p['coeff'] ?? 26);
            $offset = (float) ($p['offset'] ?? 50);
            $denom = (float) ($p['denom'] ?? 1150);
        } else {
            $config = $this->configService->getConfig()['formulas']['attributes'] ?? [];
            $denom = (float) ($config['denom'] ?? 1150);
            $offset = (float) ($config['offset'] ?? 50);
            $entityParams = $config[$entityType] ?? $config['monster'] ?? ['base' => 0, 'coeff' => 26];
            $base = (float) ($entityParams['base'] ?? 0);
            $coeff = (float) ($entityParams['coeff'] ?? 26);
        }

        $ratio = ($d - $offset) / $denom;
        $ratio = max(0.0, $ratio);
        $k = (int) round($base + $coeff * sqrt($ratio));
        $k = $this->applyValueHandler($characteristicId, $entityType, $k);

        return $this->clampToCharacteristicLimits($characteristicId, $k, $entityType);
    }

    /**
     * Indique si une caractéristique est un attribut (Force, Intelligence, chance, agilité) avec formule sqrt.
     */
    public function isAttributeWithFormula(string $characteristicId): bool
    {
        $ids = $this->configService->getConfig()['formulas']['attribute_ids'] ?? ['strength', 'intelligence', 'chance', 'agility'];

        return in_array($characteristicId, $ids, true);
    }

    /**
     * Convertit l'initiative DofusDB en initiative KrosmozJDR.
     *
     * Class  : ratio = (d - 300) / 4700 ; ratio = max(0, min(ratio, 1)) ; k = 10 * ratio ; k >= 0
     * Monster : ratio = (d - 500) / 5000 ; ratio = min(ratio, 1) (ratio peut être < 0) ; k = 10 * ratio (peut être < 0)
     * Ou formule personnalisée via conversion_formula (variable [d]).
     *
     * @param int|float|null $dofusdbValue Initiative DofusDB (d)
     * @param string $entityType class ou monster
     */
    public function convertInitiative(int|float|null $dofusdbValue, string $entityType = 'monster'): int
    {
        $d = $dofusdbValue !== null && is_numeric($dofusdbValue) ? (float) $dofusdbValue : 0.0;
        $conversionFormula = $this->formulaService->getConversionFormula('ini', $entityType);
        if ($conversionFormula !== null) {
            $k = $this->formulaEvaluator->evaluateFormulaOrTable($conversionFormula, ['d' => $d]);
            return $this->clampToCharacteristicLimits('ini', $k !== null ? (int) round($k) : 0, $entityType);
        }
        $formula = $this->formulaService->getFormula('ini', $entityType);
        if ($formula !== null && $formula['formula_type'] === 'ratio_initiative') {
            $params = $formula['parameters'];
            $offset = (float) ($params['offset'] ?? 500);
            $denom = (float) ($params['denom'] ?? 5000);
            $factor = (float) ($params['factor'] ?? 10);
            $clampRatioMinZero = (bool) ($params['clamp_ratio_min_zero'] ?? false);
            $minZero = (bool) ($params['min_zero'] ?? false);
        } else {
            $config = $this->configService->getConfig()['formulas']['initiative'] ?? [];
            $params = $config[$entityType] ?? $config['monster'] ?? [];
            $offset = (float) ($params['offset'] ?? 500);
            $denom = (float) ($params['denom'] ?? 5000);
            $factor = (float) ($params['factor'] ?? 10);
            $clampRatioMinZero = (bool) ($params['clamp_ratio_min_zero'] ?? false);
            $minZero = (bool) ($params['min_zero'] ?? false);
        }

        $ratio = $denom !== 0.0 ? ($d - $offset) / $denom : 0.0;
        $ratio = $clampRatioMinZero ? max(0.0, min(1.0, $ratio)) : min(1.0, $ratio);
        $k = (int) round($factor * $ratio);
        if ($minZero) {
            $k = max(0, $k);
        }
        $k = $this->applyValueHandler('ini', $entityType, $k);

        return $this->clampToCharacteristicLimits('ini', $k, $entityType);
    }

    /**
     * Applique le handler value (entity, value) → value si enregistré en BDD pour (characteristicId, entity).
     */
    private function applyValueHandler(string $characteristicId, string $entityType, int $value): int
    {
        $handlerName = $this->formulaService->getHandlerName($characteristicId, $entityType);
        if ($handlerName === null || !$this->handlerRegistry->hasValueHandler($handlerName)) {
            return $value;
        }
        $handler = $this->handlerRegistry->getValueHandler($handlerName);
        if ($handler === null) {
            return $value;
        }
        $result = $handler($entityType, $value);

        return is_numeric($result) ? (int) round((float) $result) : $value;
    }

    /**
     * Convertit toutes les résistances Dofus (grades ou champs directs) en res_* et res_fixe_* Krosmoz.
     * Si un handler est enregistré en BDD (res_neutre + handler_name), il est utilisé ; sinon fallback
     * par élément via convertResistance (config element_id_to_resistance).
     *
     * @param array<string, mixed> $dofusData Données brutes (ex. monster avec grades.0.neutralResistance, …)
     * @param string $entityType monster, class ou item
     * @return array<string, string> Map champ Krosmoz => valeur (res_* et res_fixe_*)
     */
    public function convertResistancesBatch(array $dofusData, string $entityType = 'monster'): array
    {
        $handlerName = $this->formulaService->getHandlerName(self::RESISTANCE_BATCH_ANCHOR, $entityType);
        if ($handlerName !== null && $this->handlerRegistry->hasBatchHandler($handlerName)) {
            $formula = $this->formulaService->getFormula(self::RESISTANCE_BATCH_ANCHOR, $entityType);
            $parameters = $formula['parameters'] ?? [];
            $handler = $this->handlerRegistry->getBatchHandler($handlerName);
            $result = $handler($entityType, $dofusData, $parameters);
            if (is_array($result)) {
                return $result;
            }
        }

        $grade = $dofusData['grades'][0] ?? $dofusData;
        $dofusKeys = [
            'neutralResistance' => 'res_neutre',
            'earthResistance' => 'res_terre',
            'fireResistance' => 'res_feu',
            'airResistance' => 'res_air',
            'waterResistance' => 'res_eau',
        ];
        $out = [];
        foreach ($dofusKeys as $dofusKey => $resField) {
            $value = $grade[$dofusKey] ?? 0;
            $elementId = match ($dofusKey) {
                'neutralResistance' => 0,
                'earthResistance' => 1,
                'fireResistance' => 2,
                'airResistance' => 3,
                'waterResistance' => 4,
                default => -1,
            };
            $converted = $this->convertResistance($elementId, $value, $entityType);
            if ($converted !== null) {
                $out[$converted['field']] = (string) $converted['value'];
            }
        }
        foreach (['res_fixe_neutre', 'res_fixe_terre', 'res_fixe_feu', 'res_fixe_air', 'res_fixe_eau'] as $f) {
            if (!isset($out[$f])) {
                $out[$f] = '0';
            }
        }

        return $out;
    }

    /**
     * Convertit une valeur de résistance DofusDB (elementId + valeur) en champ KrosmozJDR.
     *
     * Utilise DofusdbConversionConfigService (element_id_to_resistance en BDD).
     * Pour une conversion batch avec handlers (tiers 50/100/-50/-100 + plafonds), utiliser convertResistancesBatch.
     *
     * @param int $elementId Identifiant élément DofusDB (-1, 0 = neutre, 1 = terre, 2 = feu, 3 = air, 4 = eau)
     * @param int|float|null $value Valeur brute DofusDB
     * @param string $entityType Entité KrosmozJDR (monster, class, item) pour les limites
     * @return array{field: string, value: int}|null Champ KrosmozJDR (res_*) et valeur clampée, ou null si elementId inconnu
     */
    public function convertResistance(int $elementId, int|float|null $value, string $entityType = 'monster'): ?array
    {
        $config = $this->getConversionConfig();
        $map = $config['element_id_to_resistance'] ?? [];
        $field = $map[$elementId] ?? null;
        if ($field === null) {
            return null;
        }

        $v = is_numeric($value) ? (int) round((float) $value) : 0;
        $v = $this->clampToCharacteristicLimits($field, $v, $entityType);

        return ['field' => $field, 'value' => $v];
    }

    /** Plages Dofus (d min, d max) par caractéristique pour la prévisualisation. */
    private const PREVIEW_RANGES = [
        'level' => [0, 200],
        'life' => [0, 5000],
        'strength' => [50, 1200],
        'intelligence' => [50, 1200],
        'chance' => [50, 1200],
        'agility' => [50, 1200],
        'initiative' => [0, 6000],
        'ini' => [0, 6000],
    ];

    /**
     * Retourne des points (d Dofus, k JDR) pour prévisualiser une formule de conversion.
     * Utilisé pour les graphiques d'illustration (admin).
     *
     * @param string $characteristicId level, life, strength, initiative, etc.
     * @param string $entityType monster, class ou item
     * @param int|null $dMin Valeur Dofus min (null = défaut selon caractéristique)
     * @param int|null $dMax Valeur Dofus max (null = défaut selon caractéristique)
     * @param int $steps Nombre de points (répartis entre dMin et dMax)
     * @param string|null $formulaOverride Formule ou table JSON à utiliser (aperçu en édition, non sauvegardée)
     * @return list<array{x: int|float, y: int|float>}
     */
    public function getPreviewPoints(string $characteristicId, string $entityType, ?int $dMin = null, ?int $dMax = null, int $steps = 50, ?string $formulaOverride = null): array
    {
        $range = self::PREVIEW_RANGES[$characteristicId] ?? [0, 200];
        $dMin = $dMin ?? $range[0];
        $dMax = $dMax ?? $range[1];

        $points = [];
        if ($dMax <= $dMin || $steps < 2) {
            return $points;
        }

        $step = ($dMax - $dMin) / ($steps - 1);
        $levelKrosmoz = 10;

        if ($formulaOverride !== null && trim($formulaOverride) !== '') {
            for ($i = 0; $i < $steps; $i++) {
                $d = (float) ($dMin + $i * $step);
                $vars = ['d' => $d, 'level' => $levelKrosmoz];
                $y = $this->formulaEvaluator->evaluateFormulaOrTable($formulaOverride, $vars);
                $points[] = ['x' => (int) round($d), 'y' => $y !== null ? round($y, 2) : 0];
            }

            return $points;
        }

        for ($i = 0; $i < $steps; $i++) {
            $d = (int) round($dMin + $i * $step);
            $k = $this->convertValueForPreview($characteristicId, $entityType, $d, $levelKrosmoz);
            $points[] = ['x' => $d, 'y' => $k];
        }

        return $points;
    }

    /**
     * Convertit une valeur Dofus (d) en JDR (k) pour une caractéristique/entité (pour prévisualisation).
     */
    private function convertValueForPreview(string $characteristicId, string $entityType, float $d, int $levelKrosmoz): int
    {
        if ($characteristicId === 'level') {
            return $this->convertLevel((int) $d, $entityType);
        }
        if ($characteristicId === 'life') {
            return $this->convertLife((int) $d, $levelKrosmoz, $entityType);
        }
        if ($this->isAttributeWithFormula($characteristicId)) {
            return $this->convertAttribute($characteristicId, $d, $entityType);
        }
        if ($characteristicId === 'ini' || $characteristicId === 'initiative') {
            return $this->convertInitiative((int) $d, $entityType);
        }

        return (int) round($d);
    }

    /**
     * Clampe une valeur selon les limites de config/characteristics.php pour une caractéristique donnée.
     *
     * @param string $characteristicId Id de la caractéristique (ex. res_terre, strength)
     * @param int $value Valeur à clamper
     * @param string $entityType monster, class ou item
     */
    public function clampToCharacteristicLimits(string $characteristicId, int $value, string $entityType): int
    {
        $config = $this->configService->getConfig();
        $limitsSource = $config['limits_source'] ?? 'characteristics';
        if ($limitsSource !== 'characteristics') {
            $limitsByEntity = $config['limits'] ?? [];
            $local = $limitsByEntity[$entityType] ?? [];
            $min = $local[$characteristicId]['min'] ?? -999;
            $max = $local[$characteristicId]['max'] ?? 999;

            return max($min, min($max, $value));
        }

        $limits = $this->characteristicService->getLimits($characteristicId, $entityType);
        if ($limits === null) {
            return $value;
        }

        return max($limits['min'], min($limits['max'], $value));
    }

    /**
     * Convertit un tableau d'effets DofusDB en structure bonus KrosmozJDR.
     *
     * À implémenter selon la structure des effets (effectId → characteristic, valeur, etc.).
     * Utilise DofusdbConversionConfigService (effect_id_to_characteristic en BDD).
     *
     * @param array<int, mixed> $effects Effets bruts DofusDB (ex. levels[].effects ou item possibleEffects)
     * @param string $entityType monster, class ou item
     * @return array<string, int> Map champ KrosmozJDR => valeur (bonus)
     */
    public function effectsToBonus(array $effects, string $entityType = 'item'): array
    {
        $config = $this->getConversionConfig();
        $effectToChar = $config['effect_id_to_characteristic'] ?? [];
        $out = [];

        foreach ($effects as $effect) {
            if (!is_array($effect)) {
                continue;
            }
            $effectId = (int) ($effect['effectId'] ?? $effect['effect_id'] ?? 0);
            $char = $effectToChar[$effectId] ?? null;
            if ($char === null) {
                continue;
            }
            $val = $this->extractEffectValue($effect);
            $out[$char] = $this->clampToCharacteristicLimits($char, $val, $entityType);
        }

        return $out;
    }

    /**
     * Extrait une valeur numérique d'un effet DofusDB (value, diceNum, diceSide, etc.).
     *
     * @param array<string, mixed> $effect
     */
    private function extractEffectValue(array $effect): int
    {
        $value = (int) ($effect['value'] ?? 0);
        $diceNum = (int) ($effect['diceNum'] ?? 0);
        $diceSide = (int) ($effect['diceSide'] ?? 0);
        if ($diceNum > 0 && $diceSide > 0) {
            $value += (int) round(($diceNum * ($diceSide + 1)) / 2.0);
        }

        return $value;
    }
}
