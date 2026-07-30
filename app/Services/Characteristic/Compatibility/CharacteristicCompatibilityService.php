<?php

namespace App\Services\Characteristic\Compatibility;

use App\Services\Characteristic\Getter\CharacteristicGetterService;

/**
 * Vérifie si une caractéristique peut être portée par un type d'équipement.
 *
 * @example
 * $service->isObjectBonusAllowed('action_points', 1);
 */
final class CharacteristicCompatibilityService
{
    public function __construct(
        private readonly CharacteristicGetterService $getter,
    ) {}

    public function isObjectBonusAllowed(string $shortKey, ?int $itemTypeId): bool
    {
        $definition = $this->objectDefinition($shortKey);
        if ($definition === null) {
            return true;
        }

        $restricted = (bool) ($definition['allowed_item_type_restricted'] ?? false);
        if (! $restricted) {
            return true;
        }

        $allowed = $definition['allowed_item_type_ids'] ?? [];
        if (! is_array($allowed) || $allowed === [] || $itemTypeId === null) {
            return false;
        }

        return in_array($itemTypeId, array_map('intval', $allowed), true);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function objectDefinition(string $shortKey): ?array
    {
        $key = str_ends_with($shortKey, '_object') ? $shortKey : "{$shortKey}_object";
        $definition = $this->getter->getDefinition($key, 'item');

        return is_array($definition) ? $definition : null;
    }
}
