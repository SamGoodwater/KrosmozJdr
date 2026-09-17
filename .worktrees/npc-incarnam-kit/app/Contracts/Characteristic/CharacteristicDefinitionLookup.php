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
}
