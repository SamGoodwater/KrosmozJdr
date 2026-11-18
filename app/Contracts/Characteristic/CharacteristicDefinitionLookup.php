<?php

declare(strict_types=1);

namespace App\Contracts\Characteristic;

/**
 * Lecture minimale des définitions de caractéristiques (pour injection dans le runtime créature / tests).
 */
interface CharacteristicDefinitionLookup
{
    /**
     * @return array<string, mixed>|null
     */
    public function getDefinition(string $key, string $entity): ?array;

    /**
     * Bornes min/max déjà résolues, ou null si la caractéristique n’en a pas.
     *
     * @param  array<string, int|float>  $variables
     * @return array{min: int, max: int}|null
     */
    public function getLimits(string $characteristicKey, string $entity, array $variables = []): ?array;
}
