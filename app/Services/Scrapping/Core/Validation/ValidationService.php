<?php

namespace App\Services\Scrapping\Core\Validation;

use App\Services\Characteristic\CharacteristicService;

/**
 * Service de validation : vérifie les données converties contre la table des caractéristiques (BDD).
 *
 * Utilise CharacteristicService qui lit la table characteristics + characteristic_entities.
 * Les limites (min, max, required, validation_message) sont définies par entité et par caractéristique en BDD.
 *
 * Dérive :
 * - champs requis (entities[entity].required)
 * - limites min/max (entities[entity].min/max pour type int)
 * - valeurs autorisées (value_available pour type array, selon applies_to)
 *
 * Entités supportées : monster, class (breed → class), spell, item.
 * Alias : player, npc, breed → class pour la config.
 */
final class ValidationService
{
    /**
     * Alias d'entité pour la config : player, npc et breed utilisent les règles de class.
     */
    private const ENTITY_ALIASES = [
        'player' => 'class',
        'npc' => 'class',
        'breed' => 'class',
    ];

    public function __construct(
        private readonly CharacteristicService $characteristicService
    ) {
    }

    /**
     * Valide les données converties pour un type d'entité donné.
     *
     * @param array<string, array<string, mixed>> $convertedData Structure par modèle (ex. ['creatures' => [...], 'monsters' => [...]])
     * @param string $entityType Type d'entité KrosmozJDR (ex. monster, npc, player, class, item) — player/npc résolus en class
     * @return ValidationResult
     */
    public function validate(array $convertedData, string $entityType): ValidationResult
    {
        $definitions = $this->characteristicService->getCharacteristics();
        $configEntity = $this->resolveConfigEntity($entityType);
        $errors = [];

        $merged = $this->mergeModels($convertedData);

        $requiredIds = $this->getRequiredFieldIds($definitions, $configEntity);
        foreach ($requiredIds as $id) {
            if ($this->hasCharacteristicValue($definitions, $id, $merged)) {
                continue;
            }
            if ($id === 'chance' && array_key_exists('luck', $merged)) {
                continue;
            }
            $errors[] = [
                'path' => $id,
                'message' => "Champ requis manquant : {$id}",
            ];
        }

        foreach ($merged as $field => $value) {
            if ($value === null) {
                continue;
            }

            $charId = $this->getCharacteristicIdByField($definitions, $field);
            if ($charId === null) {
                continue;
            }

            $def = $definitions[$charId] ?? null;
            if ($def === null || ($def['type'] ?? '') !== 'int') {
                continue;
            }

            $entityDef = $def['entities'][$configEntity] ?? null;
            if ($entityDef === null || !isset($entityDef['min'], $entityDef['max'])) {
                continue;
            }

            $min = (int) $entityDef['min'];
            $max = (int) $entityDef['max'];
            $v = is_numeric($value) ? (int) $value : 0;
            if ($v < $min || $v > $max) {
                $message = $entityDef['validation_message']
                    ?? "{$charId}={$v} hors limites [{$min}, {$max}] pour {$entityType}";
                $message = str_replace([':min', ':max'], [(string) $min, (string) $max], $message);
                $errors[] = [
                    'path' => $field,
                    'message' => $message,
                ];
            }
        }

        foreach ($definitions as $id => $def) {
            if (!in_array($configEntity, $def['applies_to'] ?? [], true)) {
                continue;
            }
            if (($def['type'] ?? '') !== 'array') {
                continue;
            }

            $valueAvailable = $def['value_available'] ?? null;
            if (!is_array($valueAvailable)) {
                continue;
            }

            foreach ($convertedData as $model => $fields) {
                if (!is_array($fields)) {
                    continue;
                }
                $rawValue = $fields[$id] ?? $fields[$def['db_column'] ?? $id] ?? null;
                if ($rawValue === null) {
                    continue;
                }
                if (!in_array($rawValue, $valueAvailable, true)) {
                    $errors[] = [
                        'path' => "{$model}.{$id}",
                        'message' => "{$id}=\"{$rawValue}\" non autorisée. Valeurs : " . implode(', ', $valueAvailable),
                    ];
                }
            }
        }

        return $errors === []
            ? ValidationResult::ok()
            : ValidationResult::fail($errors);
    }

    /**
     * Retourne l'entité config à utiliser (npc → player).
     */
    private function resolveConfigEntity(string $entityType): string
    {
        return self::ENTITY_ALIASES[$entityType] ?? $entityType;
    }

    /**
     * Liste des ids de caractéristiques requis pour une entité (dérivé de characteristics).
     *
     * @param array<string, mixed> $definitions
     * @return array<int, string>
     */
    private function getRequiredFieldIds(array $definitions, string $entityType): array
    {
        $ids = [];
        foreach ($definitions as $id => $def) {
            $entities = $def['entities'] ?? [];
            if (isset($entities[$entityType]['required']) && $entities[$entityType]['required'] === true) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Vérifie si une valeur est présente dans merged (via id ou db_column).
     *
     * @param array<string, mixed> $definitions
     * @param array<string, mixed> $merged
     */
    private function hasCharacteristicValue(array $definitions, string $id, array $merged): bool
    {
        if (array_key_exists($id, $merged)) {
            return true;
        }
        $def = $definitions[$id] ?? null;
        if ($def !== null && isset($def['db_column']) && $def['db_column'] !== $id) {
            return array_key_exists($def['db_column'], $merged);
        }

        return false;
    }

    /**
     * Retourne l'id de caractéristique pour une clé de données (id ou db_column).
     *
     * @param array<string, mixed> $definitions
     */
    private function getCharacteristicIdByField(array $definitions, string $field): ?string
    {
        if (isset($definitions[$field])) {
            return $field;
        }
        foreach ($definitions as $id => $def) {
            if (($def['db_column'] ?? $id) === $field) {
                return $id;
            }
        }

        return null;
    }

    /**
     * Fusionne tous les modèles en un seul tableau (clé => valeur) pour les champs requis et limites.
     *
     * @param array<string, array<string, mixed>> $convertedData
     * @return array<string, mixed>
     */
    private function mergeModels(array $convertedData): array
    {
        $merged = [];
        foreach ($convertedData as $model => $fields) {
            if (!is_array($fields)) {
                continue;
            }
            foreach ($fields as $field => $value) {
                $merged[$field] = $value;
            }
        }

        return $merged;
    }
}
