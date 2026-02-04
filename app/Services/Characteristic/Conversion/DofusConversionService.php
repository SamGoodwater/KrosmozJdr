<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Conversion;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;

/**
 * Service de conversion Dofus → Krosmoz.
 * S’appuie sur le Getter (formules de conversion + limites), le service Formules (calcul) et le service Limite (clamp).
 */
final class DofusConversionService
{
    /** Entités ayant une rareté dérivée du niveau (object) */
    private const RARITY_ENTITIES = ['item', 'consumable', 'resource', 'panoply'];

    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly CharacteristicFormulaService $formulaService,
        private readonly CharacteristicLimitService $limitService
    ) {
    }

    /**
     * Convertit le niveau Dofus (d) en niveau Krosmoz pour une entité.
     */
    public function convertLevel(int|float|null $dofusValue, string $entityType): int
    {
        $d = $dofusValue !== null && is_numeric($dofusValue) ? (float) $dofusValue : 0.0;
        $key = $this->levelCharacteristicKeyForEntity($entityType);
        $formula = $this->getter->getConversionFormula($key, $entityType);
        if ($formula !== null) {
            $k = $this->formulaService->evaluate($formula, ['d' => $d]);
            $k = $k !== null ? (int) round($k) : 0;
        } else {
            $k = (int) round($d / 10);
        }
        return $this->limitService->clamp($key, $k, $entityType);
    }

    /**
     * Rareté par niveau Krosmoz (pour object). Utilisé lors du scrapping ; une fois enregistrée, la rareté est éditable à la main.
     */
    public function getRarityByLevel(int $levelKrosmoz, string $entity): int
    {
        if (! in_array($entity, self::RARITY_ENTITIES, true)) {
            return 0;
        }
        $formula = $this->getter->getConversionFormula('rarity_object', $entity);
        if ($formula !== null) {
            $result = $this->formulaService->evaluate($formula, ['level' => $levelKrosmoz]);
            if ($result !== null) {
                return (int) round($result);
            }
        }
        $bands = [0 => 0, 3 => 1, 7 => 2, 10 => 3, 17 => 4];
        krsort($bands, SORT_NUMERIC);
        foreach ($bands as $minLevel => $rarity) {
            if ($levelKrosmoz >= $minLevel) {
                return (int) $rarity;
            }
        }
        return 0;
    }

    /**
     * Convertit la vie Dofus en vie Krosmoz (créature). Variables : d, level (niveau Krosmoz).
     */
    public function convertLife(int|float|null $dofusValue, int $krosmozLevel, string $entityType): int
    {
        $d = $dofusValue !== null && is_numeric($dofusValue) ? (float) $dofusValue : 0.0;
        $key = 'life_creature';
        $formula = $this->getter->getConversionFormula($key, $entityType);
        if ($formula !== null) {
            $k = $this->formulaService->evaluate($formula, ['d' => $d, 'level' => $krosmozLevel]);
            $k = $k !== null ? (int) round($k) : 0;
        } else {
            $k = (int) round($d / 200 + $krosmozLevel * 5);
        }
        return $this->limitService->clamp($key, $k, $entityType);
    }

    /**
     * Convertit un attribut Dofus (Force, Intelligence, etc.) en Krosmoz.
     */
    public function convertAttribute(string $characteristicId, int|float|string|null $dofusValue, string $entityType): int
    {
        $d = $dofusValue !== null && $dofusValue !== '' && is_numeric($dofusValue) ? (float) $dofusValue : 0.0;
        $key = $characteristicId . '_creature';
        $formula = $this->getter->getConversionFormula($key, $entityType);
        if ($formula !== null) {
            $k = $this->formulaService->evaluate($formula, ['d' => $d]);
            $k = $k !== null ? (int) round($k) : 0;
        } else {
            $k = (int) round(6 + 24 * sqrt(max(0, ($d - 50) / 1150)));
        }
        return $this->limitService->clamp($key, $k, $entityType);
    }

    /**
     * Convertit l’initiative Dofus en Krosmoz (créature).
     */
    public function convertInitiative(int|float|null $dofusValue, string $entityType): int
    {
        $d = $dofusValue !== null && is_numeric($dofusValue) ? (float) $dofusValue : 0.0;
        $key = 'ini_creature';
        $formula = $this->getter->getConversionFormula($key, $entityType);
        if ($formula !== null) {
            $k = $this->formulaService->evaluate($formula, ['d' => $d]);
            $k = $k !== null ? (int) round($k) : 0;
        } else {
            $k = (int) round($d);
        }
        return $this->limitService->clamp($key, $k, $entityType);
    }

    /**
     * Clampe une valeur convertie dans les limites de la caractéristique.
     */
    public function clampToLimits(string $characteristicKey, int $value, string $entityType): int
    {
        return $this->limitService->clamp($characteristicKey, $value, $entityType);
    }

    /**
     * Convertit en batch les résistances DofusDB (grades.0.*Resistance) vers les champs res_* Krosmoz.
     * Utilisé par le scrapping quand resistanceBatch est activé dans la config d’entité (ex. monster).
     *
     * @param array<string, mixed> $raw Données brutes (ex. grades.0.neutralResistance, earthResistance, …)
     * @return array<string, int|string> Map res_neutre, res_terre, res_feu, res_air, res_eau => valeur
     */
    public function convertResistancesBatch(array $raw, string $entityType): array
    {
        $grades = $raw['grades'][0] ?? [];
        if (! is_array($grades)) {
            return [];
        }
        $map = [
            'neutralResistance' => 'res_neutre',
            'earthResistance' => 'res_terre',
            'fireResistance' => 'res_feu',
            'airResistance' => 'res_air',
            'waterResistance' => 'res_eau',
        ];
        $out = [];
        foreach ($map as $apiKey => $dbKey) {
            $v = $grades[$apiKey] ?? null;
            if ($v === null && ! array_key_exists($apiKey, $grades)) {
                continue;
            }
            $out[$dbKey] = is_numeric($v) ? (int) $v : (string) ($v ?? '0');
        }

        return $out;
    }

    private function levelCharacteristicKeyForEntity(string $entityType): string
    {
        if (in_array($entityType, ['item', 'consumable', 'resource', 'panoply'], true)) {
            return 'level_object';
        }
        if (in_array($entityType, ['monster', 'class', 'npc'], true)) {
            return 'level_creature';
        }
        return 'level_object';
    }
}
