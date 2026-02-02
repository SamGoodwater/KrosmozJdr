<?php

namespace App\Services\Scrapping\Core\Validation;

/**
 * Résultat d’une validation des données converties contre les caractéristiques KrosmozJDR.
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
