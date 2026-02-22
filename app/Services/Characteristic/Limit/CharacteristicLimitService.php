<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Limit;

use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Service limite : valide une valeur ou un ensemble de champs selon le type de caractéristique
 * (boolean, list, string/int) et les limites définies en base. S'appuie sur le Getter.
 */
final class CharacteristicLimitService
{
    /** Alias d'entité (player, npc, breed → class pour les règles) */
    private const ENTITY_ALIASES = [
        'player' => 'class',
        'npc' => 'class',
        'breed' => 'class',
    ];

    /** Types pour lesquels on applique min/max (numérique). */
    private const NUMERIC_TYPES = ['string', 'int', 'integer'];

    public function __construct(
        private readonly CharacteristicGetterService $getter
    ) {
    }

    /**
     * Valide une valeur pour une caractéristique et une entité (validation unitaire).
     * Règles selon le type : boolean → vrai/faux ; list → valeur dans value_available ; string/int → min/max.
     */
    public function validateSingle(string $characteristicKey, mixed $value, string $entity): ValidationResult
    {
        $def = $this->getter->getDefinition($characteristicKey, $entity);
        if ($def === null) {
            return ValidationResult::ok();
        }

        $type = isset($def['type']) ? strtolower((string) $def['type']) : 'string';

        if ($type === 'boolean' || $type === 'bool') {
            if (! $this->isValidBoolean($value)) {
                return ValidationResult::fail([[
                    'path' => $characteristicKey,
                    'message' => "{$characteristicKey} doit être vrai ou faux (true/false, 1/0).",
                ]]);
            }
            return ValidationResult::ok();
        }

        if ($type === 'list' || $type === 'array') {
            $allowed = $def['value_available'] ?? null;
            if (is_array($allowed) && $allowed !== []) {
                $normalized = $this->normalizeValueForList($value);
                if (! $this->valueInList($normalized, $allowed)) {
                    return ValidationResult::fail([[
                        'path' => $characteristicKey,
                        'message' => "{$characteristicKey} doit être une des valeurs autorisées : " . implode(', ', array_map(strval(...), $allowed)) . ".",
                    ]]);
                }
            }
            return ValidationResult::ok();
        }

        // string, int, integer ou défaut : min/max
        $limits = $this->getter->getLimits($characteristicKey, $entity);
        if ($limits === null) {
            return ValidationResult::ok();
        }
        $v = is_numeric($value) ? (int) (float) $value : 0;
        if ($v < $limits['min'] || $v > $limits['max']) {
            return ValidationResult::fail([[
                'path' => $characteristicKey,
                'message' => "{$characteristicKey}={$v} hors limites [{$limits['min']}, {$limits['max']}] pour {$entity}",
            ]]);
        }

        return ValidationResult::ok();
    }

    /**
     * Valide les données converties pour un type d'entité (tous les champs ayant une définition).
     * Applique la règle selon le type de chaque caractéristique (boolean, list, min/max).
     *
     * @param array<string, array<string, mixed>> $convertedData Données par modèle (ex. ['creatures' => [...], 'monsters' => [...]])
     * @param string $entityType Type d'entité (monster, class, item, resource, spell, etc.)
     */
    public function validate(array $convertedData, string $entityType): ValidationResult
    {
        $entity = self::ENTITY_ALIASES[$entityType] ?? $entityType;
        $merged = $this->mergeModels($convertedData);
        $errors = [];

        foreach ($merged as $field => $value) {
            if ($value === null) {
                continue;
            }
            $def = $this->getter->getDefinitionByField($field, $entity);
            if ($def === null) {
                continue;
            }
            $key = $def['key'] ?? $field;
            $result = $this->validateSingle($key, $value, $entity);
            if (! $result->isValid()) {
                foreach ($result->getErrors() as $err) {
                    $errors[] = ['path' => $field, 'message' => $err['message']];
                }
            }
        }

        return $errors === [] ? ValidationResult::ok() : ValidationResult::fail($errors);
    }

    /**
     * Clampe les valeurs numériques des données converties dans les limites min/max avant validation.
     * Convention : éviter les échecs de validation pour des valeurs hors bornes en les ramenant dans l'intervalle.
     *
     * @param array<string, array<string, mixed>> $convertedData Données par modèle (ex. ['creatures' => [...], 'monsters' => [...]])
     * @return array<string, array<string, mixed>> Données avec champs numériques clamés
     */
    public function clampConvertedData(array $convertedData, string $entityType): array
    {
        $entity = self::ENTITY_ALIASES[$entityType] ?? $entityType;
        foreach ($convertedData as $model => $fields) {
            if (! is_array($fields)) {
                continue;
            }
            foreach ($fields as $field => $value) {
                if ($value === null || ! is_numeric($value)) {
                    continue;
                }
                $def = $this->getter->getDefinitionByField($field, $entity);
                if ($def === null) {
                    continue;
                }
                $key = $def['key'] ?? $field;
                $limits = $this->getter->getLimits($key, $entity);
                if ($limits !== null) {
                    $convertedData[$model][$field] = $this->clamp($key, (int) (float) $value, $entity);
                }
            }
        }
        return $convertedData;
    }

    /**
     * Clampe une valeur dans les limites min/max pour l'entité.
     * Ne s'applique qu'aux types numériques (string/int) ; pour boolean/list, retourne la valeur inchangée.
     */
    public function clamp(string $characteristicKey, int $value, string $entity): int
    {
        $def = $this->getter->getDefinition($characteristicKey, $entity);
        if ($def !== null) {
            $type = isset($def['type']) ? strtolower((string) $def['type']) : 'string';
            if (! in_array($type, self::NUMERIC_TYPES, true)) {
                return $value;
            }
        }
        $limits = $this->getter->getLimits($characteristicKey, $entity);
        if ($limits === null) {
            return $value;
        }

        return max($limits['min'], min($limits['max'], $value));
    }

    private function isValidBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return true;
        }
        if ($value === 0 || $value === 1) {
            return true;
        }
        if (is_string($value)) {
            $v = strtolower(trim($value));
            return in_array($v, ['true', 'false', '1', '0', 'oui', 'non', 'yes', 'no', 'y', 'n', 'o', 'vrai', 'faux'], true);
        }

        return false;
    }

    /** @param array<int|string, mixed> $allowed */
    private function valueInList(mixed $value, array $allowed): bool
    {
        foreach ($allowed as $a) {
            if ($value === $a || (string) $value === (string) $a) {
                return true;
            }
            if (is_numeric($value) && is_numeric($a) && (float) $value === (float) $a) {
                return true;
            }
        }

        return false;
    }

    private function normalizeValueForList(mixed $value): mixed
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return str_contains((string) $value, '.') ? (float) $value : (int) $value;
        }

        return $value;
    }

    /**
     * @param array<string, array<string, mixed>> $convertedData
     * @return array<string, mixed>
     */
    private function mergeModels(array $convertedData): array
    {
        $merged = [];
        foreach ($convertedData as $model => $fields) {
            if (! is_array($fields)) {
                continue;
            }
            foreach ($fields as $field => $value) {
                $merged[$field] = $value;
            }
        }
        return $merged;
    }
}
