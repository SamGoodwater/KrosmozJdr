<?php

declare(strict_types=1);

namespace App\Services\Scrapping;

use App\Services\Scrapping\Conversion\Handlers\ResistanceDofusToKrosmozHandler;

/**
 * Registry des handlers de conversion nommés (fonctions DofusDB → KrosmozJDR).
 *
 * Deux types :
 * - Value handler : (entity, value) → value. Appliqué après formule ou array (post-traitement).
 * - Batch handler : (entity, dofusData, parameters) → array. Pour cas qui renvoient plusieurs champs (ex. résistances).
 *
 * En BDD, dofusdb_conversion_formulas.handler_name contient le nom du handler (null par défaut).
 * L'admin propose un select avec la liste des handlers (value + batch).
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/CONVERSION_100_BDD_ET_HANDLERS.md
 */
final class ConversionHandlerRegistry
{
    private const TYPE_VALUE = 'value';
    private const TYPE_BATCH = 'batch';

    /** @var array<string, callable> (entity, value) → value */
    private array $valueHandlers = [];

    /** @var array<string, callable> (entity, dofusData, parameters) → array */
    private array $batchHandlers = [];

    /** @var array<string, string> name => label pour l'admin */
    private array $valueLabels = [];

    /** @var array<string, string> name => label pour l'admin */
    private array $batchLabels = [];

    public function __construct()
    {
        $this->registerDefaultHandlers();
    }

    /**
     * Enregistre un handler value : (entity, value) → value.
     * Appliqué après la formule ou l'array pour post-traiter la valeur.
     *
     * @param string $name Clé stockée en BDD
     * @param callable $handler (string $entity, mixed $value): mixed
     * @param string|null $label Libellé pour le select admin
     */
    public function registerValueHandler(string $name, callable $handler, ?string $label = null): void
    {
        $this->valueHandlers[$name] = $handler;
        $this->valueLabels[$name] = $label ?? $name;
    }

    /**
     * Enregistre un handler batch : (entity, dofusData, parameters) → array.
     * Pour conversions qui renvoient plusieurs champs (ex. résistances).
     *
     * @param string $name Clé stockée en BDD
     * @param callable $handler (string $entity, array $dofusData, array $parameters): array<string, string>
     * @param string|null $label Libellé pour le select admin
     */
    public function registerBatchHandler(string $name, callable $handler, ?string $label = null): void
    {
        $this->batchHandlers[$name] = $handler;
        $this->batchLabels[$name] = $label ?? $name;
    }

    /**
     * Retourne le callable value pour un nom, ou null.
     *
     * @return callable|null (string $entity, mixed $value): mixed
     */
    public function getValueHandler(string $name): ?callable
    {
        return $this->valueHandlers[$name] ?? null;
    }

    /**
     * Retourne le callable batch pour un nom, ou null.
     *
     * @return callable|null (string $entity, array $dofusData, array $parameters): array
     */
    public function getBatchHandler(string $name): ?callable
    {
        return $this->batchHandlers[$name] ?? null;
    }

    /**
     * Retourne le handler (value ou batch) pour un nom.
     *
     * @return callable|null
     */
    public function get(string $name): ?callable
    {
        return $this->valueHandlers[$name] ?? $this->batchHandlers[$name] ?? null;
    }

    public function hasValueHandler(string $name): bool
    {
        return isset($this->valueHandlers[$name]);
    }

    public function hasBatchHandler(string $name): bool
    {
        return isset($this->batchHandlers[$name]);
    }

    public function has(string $name): bool
    {
        return $this->hasValueHandler($name) || $this->hasBatchHandler($name);
    }

    /**
     * Liste pour le select admin : value puis batch, avec name, label, type.
     *
     * @return list<array{name: string, label: string, type: string}>
     */
    public function allHandlersForSelect(): array
    {
        $out = [];
        foreach ($this->valueHandlers as $name => $_) {
            $out[] = ['name' => $name, 'label' => $this->valueLabels[$name] ?? $name, 'type' => self::TYPE_VALUE];
        }
        foreach ($this->batchHandlers as $name => $_) {
            $out[] = ['name' => $name, 'label' => $this->batchLabels[$name] ?? $name, 'type' => self::TYPE_BATCH];
        }

        return $out;
    }

    private function registerDefaultHandlers(): void
    {
        $this->registerBatchHandler(
            'resistance_dofus_to_krosmoz',
            [ResistanceDofusToKrosmozHandler::class, 'convert'],
            'Résistances Dofus → JDR (tiers 50/100/-50/-100 + plafond par créature)'
        );
    }
}
