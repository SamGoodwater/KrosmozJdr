<?php

namespace App\Services\Scrapping\Core\Norms;

use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Norms\NormsResolver;
use App\Services\Characteristic\Norms\PowerCoefficientAssigner;
use App\Services\Characteristic\Pricing\EquipmentPriceCalculator;
use App\Services\Entity\Equipment\DuplicateEquipmentSignatureChecker;

/**
 * Enrichit une entité convertie avec des informations de normes en preview.
 *
 * @example
 * $converted = $processor->enrichPreview('item', $converted, $raw);
 */
final class NormAwareEntityProcessor
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly NormsResolver $normsResolver,
        private readonly PowerCoefficientAssigner $powerAssigner,
        private readonly EquipmentPriceCalculator $priceCalculator,
        private readonly DuplicateEquipmentSignatureChecker $signatureChecker,
    ) {}

    /**
     * @param  array<string, array<string, mixed>>  $converted
     * @param  array<string, mixed>  $raw
     * @return array<string, array<string, mixed>>
     */
    public function enrichPreview(string $entity, array $converted, array $raw): array
    {
        if ($entity !== 'item' || ! is_array($converted['items'] ?? null)) {
            return $converted;
        }

        $item = $converted['items'];
        $bonus = $this->decodeBonus($item['effect'] ?? null);
        if ($bonus === []) {
            return $converted;
        }

        $level = max(1, min(NormsResolver::MAX_LEVEL, (int) ($item['level'] ?? 1)));
        $itemTypeId = isset($item['item_type_id']) && is_numeric($item['item_type_id']) ? (int) $item['item_type_id'] : null;
        $powerIndex = $this->powerAssigner->assign('item:'.($item['dofusdb_id'] ?? $raw['id'] ?? spl_object_id((object) $item)));

        $priceUnits = [];
        $report = [];
        foreach ($bonus as $shortKey => $value) {
            if (! is_numeric($value)) {
                continue;
            }
            $definition = $this->getter->getDefinition("{$shortKey}_object", 'item');
            $grid = is_array($definition['norms_grid'] ?? null) ? $definition['norms_grid'] : null;
            $conditions = is_array($definition['norms_conditions'] ?? null) ? $definition['norms_conditions'] : [];
            $comparison = $this->normsResolver->compare((float) $value, $grid, $level, $powerIndex, $conditions);
            $priceUnits[$shortKey] = $definition['base_price_per_unit'] ?? null;
            $report[$shortKey] = [
                'value' => (float) $value,
                'norm_value' => $comparison['value'],
                'delta' => $comparison['delta'],
                'in_band' => $comparison['in_band'],
            ];
        }

        $converted['items']['price_calculated'] = $this->priceCalculator->calculate($bonus, $priceUnits, $level, $powerIndex);
        $converted['items']['_smart_creation'] = [
            'power_index' => $powerIndex,
            'power_label' => NormsResolver::POWER_LEVELS[$powerIndex] ?? 'neutral',
            'bonus_signature' => $this->signatureChecker->signature($bonus, $level, $itemTypeId),
            'norms_report' => $report,
        ];

        return $converted;
    }

    /**
     * @return array<string, int|float>
     */
    private function decodeBonus(mixed $value): array
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;
        if (! is_array($decoded)) {
            return [];
        }

        $bonus = [];
        foreach ($decoded as $key => $rawValue) {
            if (is_numeric($rawValue)) {
                $bonus[(string) $key] = (float) $rawValue;
            }
        }

        return $bonus;
    }
}
