<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Limit;

use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Service limite : valide un objet entité ou une caractéristique contre les limites (min, max).
 * S’appuie uniquement sur le Getter.
 */
final class CharacteristicLimitService
{
    /** Alias d’entité (player, npc, breed → class pour les règles) */
    private const ENTITY_ALIASES = [
        'player' => 'class',
        'npc' => 'class',
        'breed' => 'class',
    ];

    public function __construct(
        private readonly CharacteristicGetterService $getter
    ) {
    }

    /**
     * Valide les données converties pour un type d’entité.
     *
     * @param array<string, array<string, mixed>> $convertedData Données par modèle (ex. ['creatures' => [...], 'monsters' => [...]])
     * @param string $entityType Type d’entité (monster, class, item, resource, spell, etc.)
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
            // Résolution field → characteristic_key : pour l’instant on suppose que le champ = une clé connue
            // et qu’on a une définition getter par (key, entity). On pourrait lister les définitions disponibles.
            $limits = $this->getter->getLimitsByField($field, $entity);
            if ($limits === null) {
                continue;
            }
            $v = is_numeric($value) ? (int) $value : 0;
            if ($v < $limits['min'] || $v > $limits['max']) {
                $errors[] = [
                    'path' => $field,
                    'message' => "{$field}={$v} hors limites [{$limits['min']}, {$limits['max']}] pour {$entityType}",
                ];
            }
        }

        return $errors === [] ? ValidationResult::ok() : ValidationResult::fail($errors);
    }

    /**
     * Clampe une valeur dans les limites de la caractéristique pour l’entité.
     */
    public function clamp(string $characteristicKey, int $value, string $entity): int
    {
        $limits = $this->getter->getLimits($characteristicKey, $entity);
        if ($limits === null) {
            return $value;
        }
        return max($limits['min'], min($limits['max'], $value));
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
