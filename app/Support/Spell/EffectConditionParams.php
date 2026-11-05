<?php

declare(strict_types=1);

namespace App\Support\Spell;

/**
 * Constantes et normalisations pour les conditions / règles de valeur d’un effet de sort.
 *
 * Stockées dans {@code params} du pivot {@code spell_degree_effects} :
 * {@code applies_to}, {@code conditions}, {@code value_rules}.
 *
 * @example
 * EffectConditionParams::normalizeConditions([
 *     ['left' => '[caster:agi]', 'op' => '>=', 'right' => '3'],
 * ]);
 */
final class EffectConditionParams
{
    public const APPLIES_TO_CASTER = 'caster';

    public const APPLIES_TO_TARGETS = 'targets';

    public const APPLIES_TO_BOTH = 'caster_and_targets';

    /** @var list<string> */
    public const APPLIES_TO = [
        self::APPLIES_TO_CASTER,
        self::APPLIES_TO_TARGETS,
        self::APPLIES_TO_BOTH,
    ];

    /** @var list<string> */
    public const OPERATORS = ['<', '<=', '>', '>=', '=', '><'];

    /** @var list<string> */
    public const JOINS = ['AND', 'OR'];

    /** @var list<string> */
    public const ACTIONS = ['replace', 'add', 'subtract'];

    public const MAX_CONDITIONS = 10;

    public const MAX_VALUE_RULES = 10;

    public const SIDE_MAX_LENGTH = 255;

    /**
     * @return list<array{left: string, op: string, right: string, join?: string}>
     */
    public static function normalizeConditions(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $i => $row) {
            if (! is_array($row)) {
                continue;
            }
            $left = trim((string) ($row['left'] ?? ''));
            $right = trim((string) ($row['right'] ?? ''));
            $op = (string) ($row['op'] ?? '');
            if ($left === '' && $right === '') {
                continue;
            }
            if (! in_array($op, self::OPERATORS, true)) {
                continue;
            }
            $item = [
                'left' => $left,
                'op' => $op,
                'right' => $right,
            ];
            if ($i > 0 || isset($row['join'])) {
                $join = strtoupper((string) ($row['join'] ?? 'AND'));
                if (in_array($join, self::JOINS, true)) {
                    $item['join'] = $join;
                }
            }
            $out[] = $item;
            if (count($out) >= self::MAX_CONDITIONS) {
                break;
            }
        }

        return $out;
    }

    /**
     * @return list<array{conditions: list<array{left: string, op: string, right: string, join?: string}>, action: string, formula: string}>
     */
    public static function normalizeValueRules(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $action = (string) ($row['action'] ?? '');
            $formula = trim((string) ($row['formula'] ?? ''));
            if (! in_array($action, self::ACTIONS, true) || $formula === '') {
                continue;
            }
            $out[] = [
                'conditions' => self::normalizeConditions($row['conditions'] ?? []),
                'action' => $action,
                'formula' => $formula,
            ];
            if (count($out) >= self::MAX_VALUE_RULES) {
                break;
            }
        }

        return $out;
    }

    public static function normalizeAppliesTo(mixed $raw): string
    {
        $value = is_string($raw) ? $raw : '';
        if (in_array($value, self::APPLIES_TO, true)) {
            return $value;
        }

        return self::APPLIES_TO_TARGETS;
    }

    /**
     * Nettoie les clés liées aux conditions dans {@code params} avant persistance.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public static function sanitizeParams(array $params): array
    {
        if (array_key_exists('applies_to', $params)) {
            $params['applies_to'] = self::normalizeAppliesTo($params['applies_to']);
        }

        if (array_key_exists('conditions', $params)) {
            $conditions = self::normalizeConditions($params['conditions']);
            if ($conditions === []) {
                unset($params['conditions']);
            } else {
                $params['conditions'] = $conditions;
            }
        }

        if (array_key_exists('value_rules', $params)) {
            $rules = self::normalizeValueRules($params['value_rules']);
            if ($rules === []) {
                unset($params['value_rules']);
            } else {
                $params['value_rules'] = $rules;
            }
        }

        return $params;
    }
}
