<?php

declare(strict_types=1);

namespace App\Services\Characteristics;

use App\Support\Characteristics\CharacteristicDefinitionNaming;

/**
 * Règles qualité éditoriales pour les JSON characteristic-definitions (release 1.3.2).
 */
final class CharacteristicDefinitionQualityService
{
    /** @var list<string> */
    private const NORMS_EXEMPT_STEMS = [
        'name', 'description', 'price', 'weight', 'level', 'rarity', 'category', 'casting_time',
        'hostility', 'life_dice',
        'duration', 'resolution_mode', 'save_dc_formula', 'save_success_note', 'spell_type',
        'attack_characteristic_key', 'save_characteristic_key', 'element', 'is_magic', 'is_passive',
        'allows_reaction', 'range_editable', 'ritual_available', 'sight_line', 'auto_success_if_willing_target',
        'life_dice', 'hit_dice', 'hostility', 'mastery_bonus', 'wakfu_reserve', 'wakfu_recharge',
    ];

    /**
     * @param  array<string, mixed>  $definition
     * @return list<string>
     */
    public function qualityIssues(string $path, array $definition): array
    {
        $issues = [];
        $key = (string) ($definition['characteristic']['key'] ?? '');
        $parsed = CharacteristicDefinitionNaming::parseCharacteristicKey($key);
        if ($parsed === null) {
            return ['clé non parsable'];
        }

        $type = (string) ($definition['characteristic']['type'] ?? 'int');
        $helper = (string) ($definition['characteristic']['helper'] ?? '');
        $entities = $definition['entities'] ?? [];
        if (! is_array($entities)) {
            return ['entities invalide'];
        }

        $star = $entities['*'] ?? null;
        $hasEntityRows = $star !== null || $this->hasNonWildcardEntityRows($entities);
        if (! $hasEntityRows && empty($definition['characteristic']['linked_to_key'])) {
            $issues[] = 'entities[*] manquant';

            return $issues;
        }

        if (is_array($star)) {
            $issues = array_merge($issues, $this->issuesForEntityRow($parsed, $type, $helper, $star));
        }

        return $issues;
    }

    /**
     * @param  array{stem: string, group: string}  $parsed
     * @param  array<string, mixed>  $row
     * @return list<string>
     */
    private function issuesForEntityRow(array $parsed, string $type, string $helper, array $row): array
    {
        $issues = [];
        $stem = $parsed['stem'];
        $group = $parsed['group'];

        if ($this->requiresNormsGrid($group, $type, $stem)) {
            $grid = $row['norms_grid'] ?? null;
            if ($grid === null || $grid === [] || $grid === '') {
                $issues[] = 'norms_grid manquant';
            }
        }

        if ($group === 'object' && $this->helperImpliesItemTypeRestriction($helper)) {
            $dofus = $row['item_type_dofus_ids'] ?? null;
            $ids = $row['item_type_ids'] ?? null;
            $hasRestriction = is_array($dofus) && $dofus !== [] || is_array($ids) && $ids !== [];
            if (! $hasRestriction) {
                $issues[] = 'item_type_dofus_ids manquant (helper équipement ciblé)';
            }
        }

        if ($this->expectsConversion($group, $type, $stem)) {
            $formula = $row['conversion_formula'] ?? null;
            if ($formula === null || $formula === '') {
                $issues[] = 'conversion_formula vide';
            }
        }

        return $issues;
    }

    /**
     * @param  array<string, mixed>  $entities
     */
    private function hasNonWildcardEntityRows(array $entities): bool
    {
        foreach ($entities as $key => $row) {
            if ($key !== '*' && is_array($row)) {
                return true;
            }
        }

        return false;
    }

    private function requiresNormsGrid(string $group, string $type, string $stem): bool
    {
        if (in_array($type, ['string', 'bool'], true)) {
            return false;
        }
        if (in_array($stem, self::NORMS_EXEMPT_STEMS, true)) {
            return false;
        }
        if (str_ends_with($stem, '_passive') && $group === 'object') {
            return true;
        }
        if ($group === 'creature' && $this->isCreatureCompetenceStem($stem)) {
            return false;
        }

        return in_array($group, ['creature', 'object', 'spell'], true);
    }

    private function expectsConversion(string $group, string $type, string $stem): bool
    {
        if (in_array($type, ['string', 'bool'], true)) {
            return false;
        }
        if (in_array($stem, self::NORMS_EXEMPT_STEMS, true)) {
            return false;
        }

        if ($group === 'object' && $this->isObjectCompetenceBonusStem($stem)) {
            return true;
        }

        return $group === 'spell' || ($group === 'object' && ! str_contains($stem, 'passive'));
    }

    private function isCreatureCompetenceStem(string $stem): bool
    {
        if (str_ends_with($stem, '_passive') || str_ends_with($stem, '_mastery')) {
            return true;
        }

        return in_array($stem, [
            'acrobatics', 'animal_handling', 'arcana', 'athletics', 'deception', 'history',
            'insight', 'intimidation', 'investigation', 'medicine', 'nature', 'perception',
            'performance', 'persuasion', 'religion', 'sleight_of_hand', 'stealth', 'survival',
        ], true);
    }

    public function isObjectCompetenceBonusStem(string $stem): bool
    {
        if (str_ends_with($stem, '_passive')) {
            return false;
        }

        return in_array($stem, [
            'acrobatics', 'animal_handling', 'arcana', 'athletics', 'deception', 'history',
            'insight', 'intimidation', 'investigation', 'medicine', 'nature', 'perception',
            'performance', 'persuasion', 'religion', 'sleight_of_hand', 'stealth', 'survival',
        ], true);
    }

    public function helperImpliesItemTypeRestriction(string $helper): bool
    {
        $h = mb_strtolower($helper);

        return str_contains($h, 'amulette')
            || str_contains($h, 'chapeau')
            || str_contains($h, 'cape')
            || str_contains($h, 'arme')
            || str_contains($h, 'arc')
            || str_contains($h, 'baguette')
            || str_contains($h, 'épée')
            || str_contains($h, 'epee')
            || str_contains($h, 'dague')
            || str_contains($h, 'marteau')
            || str_contains($h, 'pelle')
            || str_contains($h, 'bâton')
            || str_contains($h, 'baton');
    }

    /**
     * @return list<int>
     */
    public function suggestedDofusTypeIdsForHelper(string $helper): array
    {
        $h = mb_strtolower($helper);
        if (str_contains($h, 'amulette')) {
            return [1];
        }
        if (str_contains($h, 'chapeau') || str_contains($h, 'cape')
            || (str_contains($h, 'équipement') && (str_contains($h, 'chapeau') || str_contains($h, 'cape')))) {
            return [9, 10];
        }
        if (str_contains($h, 'arme') || str_contains($h, 'arc') || str_contains($h, 'baguette')
            || str_contains($h, 'bâton') || str_contains($h, 'baton') || str_contains($h, 'dague')
            || str_contains($h, 'épée') || str_contains($h, 'epee') || str_contains($h, 'marteau')
            || str_contains($h, 'pelle')) {
            return [2, 3, 4, 5, 6, 7, 8];
        }

        return [];
    }
}
