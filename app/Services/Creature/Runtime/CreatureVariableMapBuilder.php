<?php

declare(strict_types=1);

namespace App\Services\Creature\Runtime;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Models\Entity\Creature;

/**
 * Construit la carte de variables [id] => valeur à partir des colonnes créature (+ métadonnées characteristic_creature).
 */
final class CreatureVariableMapBuilder
{
    /** Attributs créature exclus (non numériques / non stats). */
    private const EXCLUDED_ATTRIBUTES = [
        'id',
        'name',
        'description',
        'location',
        'other_info',
        'state',
        'image',
        'kamas',
        'drop_',
        'other_item',
        'other_consumable',
        'other_resource',
        'other_spell',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
    ];

    /**
     * Carte initiale : lignes characteristic_creature (db_column) + colonnes scalaires non couvertes.
     *
     * @return array<string, int|float>
     */
    public function buildBaseMap(Creature $creature, string $entity): array
    {
        $variables = [];

        $rows = CharacteristicCreature::query()
            ->whereIn('entity', [CharacteristicCreature::ENTITY_ALL, $entity])
            ->whereNotNull('db_column')
            ->with('characteristic')
            ->get()
            ->groupBy('characteristic_id');

        foreach ($rows as $group) {
            /** @var \Illuminate\Support\Collection<int, CharacteristicCreature> $group */
            $base = $group->firstWhere('entity', CharacteristicCreature::ENTITY_ALL);
            $overlay = $group->firstWhere('entity', $entity);
            $row = $overlay ?? $base;
            if ($row === null || $row->characteristic === null) {
                continue;
            }
            /** @var Characteristic $characteristic */
            $characteristic = $row->characteristic;
            $col = $row->db_column;
            if ($col === null || $col === '') {
                continue;
            }
            $key = $characteristic->key;
            $variables[$key] = $this->parseNumeric($creature->getAttribute($col));
        }

        foreach ($creature->getAttributes() as $attr => $value) {
            if (in_array($attr, self::EXCLUDED_ATTRIBUTES, true)) {
                continue;
            }
            if (isset($variables[$attr])) {
                continue;
            }
            if (! $this->isLikelyScalarStat($value)) {
                continue;
            }
            $variables[$attr] = $this->parseNumeric($value);
        }

        return $variables;
    }

    private function isLikelyScalarStat(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }
        if (is_int($value) || is_float($value)) {
            return true;
        }
        if (is_string($value)) {
            return is_numeric($value);
        }

        return false;
    }

    private function parseNumeric(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        return 0.0;
    }
}
