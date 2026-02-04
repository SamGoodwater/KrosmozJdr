<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Limit;

/**
 * Résultat d’une validation des données contre les limites des caractéristiques.
 */
final class ValidationResult
{
    /** @param list<array{path: string, message: string}> $errors */
    public function __construct(
        private bool $valid,
        private array $errors = []
    ) {
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    /** @return list<array{path: string, message: string}> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function ok(): self
    {
        return new self(true, []);
    }

    /** @param list<array{path: string, message: string}> $errors */
    public static function fail(array $errors): self
    {
        return new self(false, $errors);
    }
}
