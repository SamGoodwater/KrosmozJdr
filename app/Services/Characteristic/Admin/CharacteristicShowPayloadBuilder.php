<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Admin;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\CharacteristicObject;
use App\Models\CharacteristicSpell;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Config\ScrappingMappingService;

/**
 * Construit le payload Inertia pour la page « show » d'une caractéristique (selected, mappings, etc.).
 * Une seule responsabilité : assembler les données d'affichage à partir du modèle et des services.
 *
 * @see docs/50-Fonctionnalités/AUDIT_OPTIMISATION_SCRAPPING_MAPPING_CARACTERISTIQUES.md
 */
final class CharacteristicShowPayloadBuilder
{
    private const ENTITIES_BY_GROUP = [
        'creature' => ['*', 'monster', 'class', 'npc'],
        'object' => ['*', 'item', 'consumable', 'resource', 'panoply'],
        'spell' => ['*', 'spell'],
    ];

    public function __construct(
        private readonly CharacteristicGetterService $getter,
        private readonly ScrappingMappingService $mappingService
    ) {
    }

    /**
     * Payload pour la vue show : selected, scrappingMappingsUsingThis, characteristicsForConvertToLinked.
     *
     * @return array{selected: array<string, mixed>, scrappingMappingsUsingThis: list<array>, characteristicsForConvertToLinked: list<array>}
     */
    public function build(Characteristic $characteristic): array
    {
        $characteristic->loadMissing('masterCharacteristic');
        $effective = $characteristic->effectiveCharacteristic();
        $group = $this->inferPrimaryGroup($characteristic);
        $entitiesForGroup = self::ENTITIES_BY_GROUP[$group] ?? ['*'];

        $entities = $this->buildEntities($characteristic, $characteristic->key, $group, $entitiesForGroup);
        $conversionFormulas = $this->buildConversionFormulas($characteristic->key, $entitiesForGroup);
        $entityOverrideKeys = array_values(array_filter(
            array_unique(array_column($entities, 'entity')),
            fn (string $e): bool => $e !== '*'
        ));

        $selected = [
            'id' => $characteristic->key,
            'name' => $effective->name,
            'short_name' => $effective->short_name,
            'description' => $effective->descriptions,
            'helper' => $effective->helper,
            'icon' => $effective->icon,
            'color' => $effective->color,
            'type' => $effective->type,
            'unit' => $effective->unit,
            'sort_order' => $characteristic->sort_order,
            'entities' => $entities,
            'entity_override_keys' => $entityOverrideKeys,
            'conversion_formulas' => $conversionFormulas,
            'group' => $group,
            'entitiesByGroup' => self::ENTITIES_BY_GROUP,
            'is_linked' => $characteristic->isLinked(),
            'master_key' => $characteristic->isLinked() ? $effective->key : null,
        ];

        $scrappingMappingsUsingThis = $this->mappingService->listMappingsForCharacteristic($characteristic->id);
        $characteristicsForConvertToLinked = [];
        if (! $characteristic->isLinked()) {
            $characteristicsForConvertToLinked = Characteristic::whereNull('linked_to_characteristic_id')
                ->where('id', '!=', $characteristic->id)
                ->orderBy('sort_order')
                ->orderBy('key')
                ->get()
                ->map(fn (Characteristic $c): array => [
                    'id' => $c->id,
                    'key' => $c->key,
                    'name' => $c->name,
                    'group' => $c->group ?? $this->inferPrimaryGroup($c),
                ])
                ->all();
        }

        return [
            'selected' => $selected,
            'scrappingMappingsUsingThis' => $scrappingMappingsUsingThis,
            'characteristicsForConvertToLinked' => $characteristicsForConvertToLinked,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildEntities(Characteristic $characteristic, string $characteristicKey, string $group, array $entitiesForGroup): array
    {
        $entities = [];
        if ($characteristic->isLinked()) {
            foreach ($entitiesForGroup as $entity) {
                $def = $this->getter->getDefinition($characteristicKey, $entity);
                if ($def !== null) {
                    $entities[] = $this->definitionToEntityRow($def);
                }
            }
        } else {
            foreach (CharacteristicCreature::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $entities[] = $this->groupRowToEntity($row, $characteristic);
            }
            foreach (CharacteristicObject::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $entities[] = $this->groupRowToEntity($row, $characteristic);
            }
            foreach (CharacteristicSpell::where('characteristic_id', $characteristic->id)->orderBy('entity')->get() as $row) {
                $entities[] = $this->groupRowToEntity($row, $characteristic);
            }
            $entities = array_values(array_filter($entities, fn (array $ent): bool => in_array($ent['entity'] ?? '', $entitiesForGroup, true)));
        }

        return $entities;
    }

    /**
     * @return list<array{entity: string, conversion_formula: string, formula_display: string, handler_name: string}>
     */
    private function buildConversionFormulas(string $characteristicKey, array $entitiesForGroup): array
    {
        $out = [];
        foreach ($entitiesForGroup as $entity) {
            if ($entity === '*') {
                continue;
            }
            $def = $this->getter->getDefinition($characteristicKey, $entity);
            $out[] = [
                'entity' => $entity,
                'conversion_formula' => $def['conversion_formula'] ?? '',
                'formula_display' => $def['formula_display'] ?? '',
                'handler_name' => '',
            ];
        }
        return $out;
    }

    private function inferPrimaryGroup(Characteristic $characteristic): string
    {
        if (CharacteristicCreature::where('characteristic_id', $characteristic->id)->exists()) {
            return 'creature';
        }
        if (CharacteristicObject::where('characteristic_id', $characteristic->id)->exists()) {
            return 'object';
        }
        if (CharacteristicSpell::where('characteristic_id', $characteristic->id)->exists()) {
            return 'spell';
        }
        return 'creature';
    }

    /**
     * @param array<string, mixed> $def
     * @return array<string, mixed>
     */
    private function definitionToEntityRow(array $def): array
    {
        $out = [
            'entity' => $def['entity'] ?? '*',
            'db_column' => $def['db_column'] ?? null,
            'min' => $def['min'] ?? null,
            'max' => $def['max'] ?? null,
            'formula' => $def['formula'] ?? null,
            'formula_display' => $def['formula_display'] ?? null,
            'default_value' => $def['default_value'] ?? null,
            'conversion_formula' => $def['conversion_formula'] ?? null,
            'conversion_dofus_sample' => $def['conversion_dofus_sample'] ?? null,
            'conversion_krosmoz_sample' => $def['conversion_krosmoz_sample'] ?? null,
            'conversion_sample_rows' => null,
            'forgemagie_allowed' => false,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
        ];
        if (isset($def['forgemagie_allowed'])) {
            $out['forgemagie_allowed'] = (bool) $def['forgemagie_allowed'];
        }
        if (isset($def['forgemagie_max'])) {
            $out['forgemagie_max'] = (int) $def['forgemagie_max'];
        }
        if (array_key_exists('base_price_per_unit', $def)) {
            $out['base_price_per_unit'] = $def['base_price_per_unit'];
        }
        if (array_key_exists('rune_price_per_unit', $def)) {
            $out['rune_price_per_unit'] = $def['rune_price_per_unit'];
        }
        return $out;
    }

    /**
     * @param CharacteristicCreature|CharacteristicObject|CharacteristicSpell $row
     * @return array<string, mixed>
     */
    private function groupRowToEntity(CharacteristicCreature|CharacteristicObject|CharacteristicSpell $row, Characteristic $characteristic): array
    {
        $out = [
            'entity' => $row->entity,
            'db_column' => $row->db_column,
            'min' => $row->min,
            'max' => $row->max,
            'formula' => $row->formula,
            'formula_display' => $row->formula_display,
            'default_value' => $row->default_value,
            'conversion_formula' => $row->conversion_formula,
            'conversion_dofus_sample' => $row->conversion_dofus_sample,
            'conversion_krosmoz_sample' => $row->conversion_krosmoz_sample,
            'conversion_sample_rows' => $row->conversion_sample_rows,
            'forgemagie_allowed' => false,
            'forgemagie_max' => 0,
            'base_price_per_unit' => null,
            'rune_price_per_unit' => null,
        ];
        if ($row instanceof CharacteristicObject) {
            $out['forgemagie_allowed'] = $row->forgemagie_allowed;
            $out['forgemagie_max'] = $row->forgemagie_max;
            $out['base_price_per_unit'] = $row->base_price_per_unit;
            $out['rune_price_per_unit'] = $row->rune_price_per_unit;
        }
        return $out;
    }
}
