<?php

namespace App\Services\Scrapping\V2\Orchestrator;

use App\Services\Scrapping\V2\Integration\IntegrationResult;

/**
 * Résultat d’un run orchestrateur (un objet ou une liste).
 */
final class OrchestratorResult
{
    /**
     * @param array<string, mixed>|null $raw Données brutes (fetchOne)
     * @param array<string, array<string, mixed>>|array<int, array<string, array<string, mixed>>>|null $converted Données converties (une structure ou liste)
     * @param list<array{path: string, message: string}> $validationErrors
     * @param array<string, mixed>|null $meta Meta fetchMany (total, pages, etc.)
     * @param list<IntegrationResult>|null $integrationResults Résultats d’intégration (liste en runMany)
     */
    public function __construct(
        private bool $success,
        private string $message = '',
        private ?array $raw = null,
        private ?array $converted = null,
        private array $validationErrors = [],
        private ?IntegrationResult $integrationResult = null,
        private ?array $integrationResults = null,
        private ?array $meta = null
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /** @return array<string, mixed>|null */
    public function getRaw(): ?array
    {
        return $this->raw;
    }

    /** @return array<string, array<string, mixed>>|array<int, array<string, array<string, mixed>>>|null */
    public function getConverted(): ?array
    {
        return $this->converted;
    }

    /** @return list<array{path: string, message: string}> */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function getIntegrationResult(): ?IntegrationResult
    {
        return $this->integrationResult;
    }

    /** @return list<IntegrationResult>|null */
    public function getIntegrationResults(): ?array
    {
        return $this->integrationResults;
    }

    /** @return array<string, mixed>|null */
    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public static function fail(string $message, array $validationErrors = []): self
    {
        return new self(false, $message, null, null, $validationErrors, null, null, null);
    }

    public static function ok(
        string $message,
        ?array $raw = null,
        ?array $converted = null,
        ?IntegrationResult $integrationResult = null,
        ?array $integrationResults = null,
        ?array $meta = null
    ): self {
        return new self(true, $message, $raw, $converted, [], $integrationResult, $integrationResults, $meta);
    }
}
