<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Conversion;

/**
 * Registry des fonctions de conversion optionnelles (Dofus → Krosmoz).
 *
 * Utilisé par l'UI admin (select conversion_function) et par DofusConversionService::convert() :
 * si une caractéristique a conversion_function renseigné en BDD (associé depuis l'UI), la fonction
 * enregistrée ici est appliquée après la formule.
 *
 * Signature d'une fonction : (valeur après formule, données converties en cours, données brutes,
 * clé caractéristique, entité) → valeur finale avant clamp.
 *
 * @see DofusConversionService
 */
final class ConversionFunctionRegistry
{
    /**
     * Signature d'une fonction de conversion :
     * (valeur après formule ou brute, données converties en cours, données brutes, clé caractéristique, entité) → valeur finale.
     *
     * @var array<string, callable(float, array, array, string, string): int|float>
     */
    private array $functions = [];

    /** Libellés pour l'UI (id => label). */
    private array $labels = [];

    /**
     * Enregistre une fonction de conversion.
     *
     * @param callable(float, array, array, string, string): int|float $callable
     */
    public function register(string $id, callable $callable, string $label = ''): void
    {
        $this->functions[$id] = $callable;
        $this->labels[$id] = $label !== '' ? $label : $id;
    }

    public function get(string $id): ?callable
    {
        return $this->functions[$id] ?? null;
    }

    /** @return list<string> */
    public function ids(): array
    {
        return array_keys($this->functions);
    }

    /** Retourne les options pour un select UI : [ { id, label }, ... ]. */
    public function options(): array
    {
        $out = [];
        foreach ($this->ids() as $id) {
            $out[] = ['id' => $id, 'label' => $this->labels[$id] ?? $id];
        }
        return $out;
    }

    public function has(string $id): bool
    {
        return isset($this->functions[$id]);
    }
}
