<?php

declare(strict_types=1);

namespace App\Services\Scrapping;

use App\Models\DofusdbConversionFormula;
use Illuminate\Support\Facades\Cache;

/**
 * Service de lecture des formules de conversion DofusDB → KrosmozJDR depuis la base.
 *
 * Expose les formules par (characteristic_id, entity) pour que DofusDbConversionFormulas
 * puisse appliquer la bonne formule selon l'entité (monster, class, item).
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/PLAN_FORMULES_DOFUSDB_BDD.md
 */
final class DofusDbConversionFormulaService
{
    private const CACHE_KEY = 'dofusdb_conversion_formulas.all';

    private const CACHE_TTL_SECONDS = 3600;

    /**
     * Retourne la formule de conversion (chaîne ou JSON table) pour une caractéristique et une entité.
     * Utilisée par DofusDbConversionFormulas avec FormulaEvaluator lorsque non vide.
     *
     * @return string|null conversion_formula ou null si vide
     */
    public function getConversionFormula(string $characteristicId, string $entity): ?string
    {
        $all = $this->getAllFormulas();
        $byChar = $all[$characteristicId] ?? null;
        if ($byChar === null) {
            return null;
        }
        $def = $byChar[$entity] ?? null;
        if ($def === null) {
            return null;
        }
        $raw = $def['conversion_formula'] ?? null;

        return is_string($raw) && trim($raw) !== '' ? $raw : null;
    }

    /**
     * Retourne la formule (type + paramètres) pour une caractéristique et une entité, ou null.
     * Utilisé en fallback lorsque conversion_formula est vide (rétrocompatibilité).
     *
     * @return array{formula_type: string, parameters: array<string, mixed>, handler_name: string|null}|null
     */
    public function getFormula(string $characteristicId, string $entity): ?array
    {
        $all = $this->getAllFormulas();
        $byChar = $all[$characteristicId] ?? null;
        if ($byChar === null) {
            return null;
        }
        $def = $byChar[$entity] ?? null;
        if ($def === null) {
            return null;
        }

        return [
            'formula_type' => $def['formula_type'],
            'parameters' => $def['parameters'] ?? [],
            'handler_name' => $def['handler_name'] ?? null,
        ];
    }

    /**
     * Retourne le nom du handler pour une caractéristique et une entité, ou null.
     *
     * @return string|null
     */
    public function getHandlerName(string $characteristicId, string $entity): ?string
    {
        $def = $this->getFormula($characteristicId, $entity);
        $name = $def['handler_name'] ?? null;

        return is_string($name) && trim($name) !== '' ? $name : null;
    }

    /**
     * Retourne toutes les formules (characteristic_id => entity => definition).
     * Mis en cache.
     *
     * @return array<string, array<string, array{formula_type: string, parameters: array, conversion_formula: string|null}>>
     */
    public function getAllFormulas(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            return $this->buildAllFormulas();
        });
    }

    /**
     * Invalide le cache (à appeler après création/update/suppression en base).
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Construit le tableau depuis la base (sans cache).
     *
     * @return array<string, array<string, array{formula_type: string, parameters: array, conversion_formula: string|null, handler_name: string|null}>>
     */
    private function buildAllFormulas(): array
    {
        $rows = DofusdbConversionFormula::query()->get();
        $out = [];
        foreach ($rows as $row) {
            $out[$row->characteristic_id][$row->entity] = [
                'formula_type' => $row->formula_type,
                'parameters' => $row->parameters ?? [],
                'conversion_formula' => $row->conversion_formula,
                'handler_name' => $row->handler_name,
            ];
        }

        return $out;
    }
}
