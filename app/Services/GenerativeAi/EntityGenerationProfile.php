<?php

declare(strict_types=1);

namespace App\Services\GenerativeAi;

/**
 * Profil de génération pour un type d'entité (champs / caracs figés, étalons).
 *
 * @example
 * $frozen = $profile->isCharacteristicFrozen('intelligence_object');
 */
final readonly class EntityGenerationProfile
{
    /**
     * @param  '*'|list<string>  $frozenFields
     * @param  list<string>  $writableFields
     * @param  '*'|list<string>  $frozenCharacteristics
     * @param  list<string>  $writableCharacteristics
     * @param  list<int>  $exampleIds
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public string $entity,
        public bool $hasDofusSource,
        public string|array $frozenFields,
        public array $writableFields,
        public string|array $frozenCharacteristics,
        public array $writableCharacteristics,
        public array $exampleIds,
        public array $extra = [],
    ) {}

    public function isFieldFrozen(string $field): bool
    {
        return $this->isFrozen($field, $this->frozenFields, $this->writableFields);
    }

    /**
     * @param  string  $characteristicKey  Clé `characteristics.key` (ex. intelligence_object).
     */
    public function isCharacteristicFrozen(string $characteristicKey): bool
    {
        return $this->isFrozen($characteristicKey, $this->frozenCharacteristics, $this->writableCharacteristics);
    }

    /**
     * @param  '*'|list<string>  $frozen
     * @param  list<string>  $writable
     */
    private function isFrozen(string $name, string|array $frozen, array $writable): bool
    {
        if (in_array($name, $writable, true)) {
            return false;
        }

        if ($frozen === '*') {
            return true;
        }

        return in_array($name, $frozen, true);
    }
}
