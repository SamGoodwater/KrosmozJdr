<?php

namespace App\Services\Scrapping\Core\Orchestrator;

use App\Services\Scrapping\Core\Integration\IntegrationResult;

/**
 * Résultat d’un run orchestrateur (un objet ou une liste).
 */
final class OrchestratorResult
{
    /**
     * @param  array<string, mixed>|null  $raw  Données brutes (fetchOne)
     * @param  array<string, array<string, mixed>>|array<int, array<string, array<string, mixed>>>|null  $converted  Données converties (une structure ou liste)
     * @param  list<array{path: string, message: string}>  $validationErrors
     * @param  array<string, mixed>|null  $meta  Meta fetchMany (total, pages, etc.)
     * @param  list<IntegrationResult>|null  $integrationResults  Résultats d’intégration (liste en runMany)
     * @param  list<array{level:string,code:string,message:string,context:array<string,mixed>}>  $diagnostics
     * @param  list<array<string,mixed>>|null  $itemResults
     */
    public function __construct(
        private bool $success,
        private string $message = '',
        private ?array $raw = null,
        private ?array $converted = null,
        private array $validationErrors = [],
        private ?IntegrationResult $integrationResult = null,
        private ?array $integrationResults = null,
        private ?array $meta = null,
        private ?array $relations = null,
        private array $diagnostics = [],
        private ?array $itemResults = null,
    ) {}

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

    /** @return list<array{type: string, id: int}>|null */
    public function getRelations(): ?array
    {
        return $this->relations;
    }

    /** @return list<array{level:string,code:string,message:string,context:array<string,mixed>}> */
    public function getDiagnostics(): array
    {
        return $this->diagnostics;
    }

    /** @return list<array<string,mixed>>|null */
    public function getItemResults(): ?array
    {
        return $this->itemResults;
    }

    public static function fail(string $message, array $validationErrors = []): self
    {
        return new self(false, $message, null, null, $validationErrors, null, null, null, null, [], null);
    }

    /**
     * Échec de validation en conservant raw (et optionnellement converted) pour l’affichage (ex. prévisualisation).
     */
    public static function validationFailed(
        string $message,
        array $validationErrors,
        ?array $raw = null,
        ?array $converted = null,
        array $diagnostics = [],
    ): self {
        return new self(false, $message, $raw, $converted, $validationErrors, null, null, null, null, $diagnostics, null);
    }

    public static function ok(
        string $message,
        ?array $raw = null,
        ?array $converted = null,
        ?IntegrationResult $integrationResult = null,
        ?array $integrationResults = null,
        ?array $meta = null,
        ?array $relations = null,
        array $diagnostics = [],
        ?array $itemResults = null,
    ): self {
        return new self(true, $message, $raw, $converted, [], $integrationResult, $integrationResults, $meta, $relations, $diagnostics, $itemResults);
    }
}
