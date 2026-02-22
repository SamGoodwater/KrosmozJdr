<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Conversion;

use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;

/**
 * Service de conversion Dofus → Krosmoz.
 * Une seule entrée : convert(). Source de vérité : conversion_formula en BDD par caractéristique/entité.
 * Chaîne : formule BDD → éventuelle conversion_function (Registry, choisie en UI) → clamp (Limite).
 */
final class DofusConversionService
{
    /** Entités ayant une rareté dérivée du niveau (object) */
    public const RARITY_ENTITIES = ['item', 'consumable', 'resource', 'panoply'];

    /** Contexte pour la fonction personnalisée : convertedOutput, raw. */
    public const CONTEXT_CONVERTED_OUTPUT = 'convertedOutput';
    public const CONTEXT_RAW = 'raw';

    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly CharacteristicFormulaService $formulaService,
        private readonly CharacteristicLimitService $limitService,
        private readonly ConversionFunctionRegistry $functionRegistry
    ) {
    }

    /**
     * Conversion : formule BDD puis éventuelle fonction personnalisée (conversion_function en BDD) puis clamp.
     *
     * @param array<string, float|int> $variables Ex. ['d' => 100] ou ['d' => 50, 'level' => 5]
     * @param float|null $fallbackWhenFormulaNull Si formule absente en BDD, valeur utilisée avant clamp (sinon 0)
     * @param array<string, mixed> $context Optionnel : convertedOutput, raw (pour la fonction personnalisée)
     */
    public function convert(string $characteristicKey, array $variables, string $entityType, ?float $fallbackWhenFormulaNull = null, array $context = []): int
    {
        $formula = $this->getter->getConversionFormula($characteristicKey, $entityType);
        if ($formula !== null) {
            $k = $this->formulaService->evaluate($formula, $variables);
            $k = $k !== null ? (float) $k : 0.0;
        } else {
            $k = $fallbackWhenFormulaNull ?? 0.0;
        }
        $k = $this->applyConversionFunction($k, $characteristicKey, $entityType, $context);

        return $this->limitService->clamp($characteristicKey, (int) round($k), $entityType);
    }

    /**
     * Clé de caractéristique pour le niveau selon l'entité (level_creature ou level_object).
     */
    public function getLevelCharacteristicKey(string $entityType): string
    {
        return $this->levelCharacteristicKeyForEntity($entityType);
    }

    /**
     * Fallback rareté par niveau (bandes) quand aucune formule BDD pour rarity_object.
     */
    public function getRarityFallbackForLevel(int $levelKrosmoz): int
    {
        return $this->rarityFallbackFromBands($levelKrosmoz);
    }

    /**
     * Convertit un attribut Dofus (Force, Intelligence, etc.) en Krosmoz.
     * Construit la clé comme characteristicId + '_creature'. Transmet $context à convert() pour la fonction personnalisée éventuelle.
     *
     * @param array<string, mixed> $context Transmis à convert() (convertedOutput, raw pour conversion_function)
     */
    public function convertAttribute(string $characteristicId, int|float|string|null $dofusValue, string $entityType, array $context = []): int
    {
        $key = $characteristicId . '_creature';

        return $this->convertByCharacteristicKey($key, $dofusValue, $entityType, $context);
    }

    /**
     * Convertit une valeur Dofus en Krosmoz via la formule et les limites de la caractéristique (clé BDD).
     * Utilisé par le pipeline lorsque la règle de mapping a un characteristic_id. Transmet $context à convert().
     *
     * @param array<string, mixed> $context Transmis à convert() (convertedOutput, raw pour conversion_function)
     */
    public function convertByCharacteristicKey(string $characteristicKey, int|float|string|null $dofusValue, string $entityType, array $context = []): int
    {
        $d = $dofusValue !== null && $dofusValue !== '' && is_numeric($dofusValue) ? (float) $dofusValue : 0.0;
        $fallback = (float) round(6 + 24 * sqrt(max(0, ($d - 50) / 1150)));

        return $this->convert($characteristicKey, ['d' => $d], $entityType, $fallback, $context);
    }

    /**
     * Convertit une valeur Dofus d’effet d’équipement (item/resource/consumable/panoply) en valeur Krosmoz.
     * Formule et limites depuis la BDD (clé du groupe object).
     *
     * @param string $characteristicKey Clé du groupe object (ex. intel_object, strong_object)
     * @param int|float $dofusValue Valeur Dofus (ex. 12 pour from=10, to=13)
     * @param string $entityType item, consumable, resource ou panoply
     * @param array<string, mixed> $context Transmis à convert() (pour conversion_function)
     */
    public function convertObjectAttribute(string $characteristicKey, int|float $dofusValue, string $entityType, array $context = []): int
    {
        $d = is_numeric($dofusValue) ? (float) $dofusValue : 0.0;

        return $this->convert($characteristicKey, ['d' => $d], $entityType, $d, $context);
    }

    /**
     * Applique la fonction personnalisée (conversion_function) si associée à la caractéristique en BDD depuis l'UI.
     */
    private function applyConversionFunction(float $value, string $characteristicKey, string $entityType, array $context): float
    {
        $functionId = $this->getter->getConversionFunctionId($characteristicKey, $entityType);
        if ($functionId === null) {
            return $value;
        }
        $callable = $this->functionRegistry->get($functionId);
        if ($callable === null) {
            return $value;
        }
        $convertedOutput = is_array($context[self::CONTEXT_CONVERTED_OUTPUT] ?? null) ? $context[self::CONTEXT_CONVERTED_OUTPUT] : [];
        $raw = is_array($context[self::CONTEXT_RAW] ?? null) ? $context[self::CONTEXT_RAW] : [];
        $result = $callable($value, $convertedOutput, $raw, $characteristicKey, $entityType);

        return is_numeric($result) ? (float) $result : $value;
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

    private function rarityFallbackFromBands(int $levelKrosmoz): int
    {
        $bands = [0 => 0, 3 => 1, 7 => 2, 10 => 3, 17 => 4];
        krsort($bands, SORT_NUMERIC);
        foreach ($bands as $minLevel => $rarity) {
            if ($levelKrosmoz >= $minLevel) {
                return $rarity;
            }
        }
        return 0;
    }
}
