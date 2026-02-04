<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Service de calcul de formules : évalue formules et tables de façon sécurisée (sans eval).
 * Utilisé par le service Conversion et pour les valeurs par défaut / dérivées.
 *
 * @see FormulaResolutionService
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 */
final class CharacteristicFormulaService
{
    public function __construct(
        private readonly FormulaResolutionService $resolutionService
    ) {
    }

    /**
     * Évalue une formule ou une table JSON avec les variables fournies.
     *
     * @param string|null $formula Formule (ex. floor([d]/10)) ou table (ex. {"characteristic":"level","0":0,"3":1})
     * @param array<string, int|float> $variables Map id => valeur (ex. d, level)
     * @return float|null Résultat ou null si invalide
     */
    public function evaluate(?string $formula, array $variables): ?float
    {
        return $this->resolutionService->evaluate($formula, $variables);
    }

    /** @return list<string> */
    public function validateFormula(?string $formula): array
    {
        return $this->resolutionService->validateFormula($formula);
    }

    /**
     * Évalue une formule pour chaque valeur d'une variable sur une plage [min, max].
     * Utilisé par l’API formula-preview (courbe niveau → valeur).
     *
     * @param string|null $formula Formule ou table JSON
     * @param string $variableName Nom de la variable à faire varier (ex. "level")
     * @param int $min Valeur minimale (incluse)
     * @param int $max Valeur maximale (incluse)
     * @param array<string, int|float> $baseVariables Variables fixes
     * @return array<int, float> Map valeur_variable => résultat
     */
    public function evaluateForVariableRange(
        ?string $formula,
        string $variableName,
        int $min,
        int $max,
        array $baseVariables = []
    ): array {
        return $this->resolutionService->evaluateForVariableRange($formula, $variableName, $min, $max, $baseVariables);
    }
}
