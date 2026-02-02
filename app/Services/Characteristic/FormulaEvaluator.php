<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

/**
 * Évalue une formule de caractéristique (syntaxe [id], floor, ceil, + - * /)
 * ou un tableau par caractéristique (JSON : characteristic + seuils → valeur fixe ou formule).
 * Utilisé pour l'aperçu graphique des formules en admin.
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 * @see FormulaConfigDecoder
 */
final class FormulaEvaluator
{
    /**
     * Évalue le champ formula (formule simple ou table JSON par caractéristique).
     *
     * @param string|null $formula Formule ou JSON table (ex. {"characteristic":"level","1":0,"7":2,"14":4})
     * @param array<string, int|float> $variables Map id => valeur (ex. level, vitality)
     * @return float|null Résultat ou null si invalide
     */
    public function evaluateFormulaOrTable(?string $formula, array $variables): ?float
    {
        if ($formula === null || trim($formula) === '') {
            return null;
        }

        $decoded = FormulaConfigDecoder::decode($formula);
        if ($decoded['type'] === 'table') {
            return $this->evaluateTable($decoded, $variables);
        }

        return $this->evaluate($decoded['expression'], $variables);
    }

    /**
     * Évalue une table par caractéristique : prend la valeur de la caractéristique,
     * trouve la tranche (plus grand seuil ≤ valeur), retourne la valeur fixe ou évalue la sous-formule.
     *
     * @param array{type: 'table', characteristic: string, entries: list<array{from: int, value: int|float|string}>} $decoded
     * @param array<string, int|float> $variables
     */
    private function evaluateTable(array $decoded, array $variables): ?float
    {
        $char = $decoded['characteristic'];
        $entries = $decoded['entries'];
        if ($entries === []) {
            return null;
        }

        $refValue = isset($variables[$char]) ? (float) $variables[$char] : 0.0;
        $chosen = null;
        $bestFrom = -1;
        foreach ($entries as $entry) {
            $from = (int) ($entry['from'] ?? 0);
            if ($from <= $refValue && $from >= $bestFrom) {
                $bestFrom = $from;
                $chosen = $entry;
            }
        }
        if ($chosen === null) {
            $chosen = $entries[0];
        }

        $value = $chosen['value'];
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        return $this->evaluate((string) $value, $variables);
    }

    /**
     * Évalue une formule en remplaçant les variables [id] par les valeurs fournies.
     *
     * @param string $formula Formule (ex. [vitality]*10+[level]*2)
     * @param array<string, int|float> $variables Map id => valeur
     * @return float|null Résultat ou null si formule invalide
     */
    public function evaluate(string $formula, array $variables): ?float
    {
        if ($formula === '') {
            return null;
        }

        $expr = $formula;
        foreach ($variables as $id => $value) {
            $expr = str_replace('[' . $id . ']', (string) (float) $value, $expr);
        }

        // Remplacer les [restants] par 0 pour éviter des erreurs
        $expr = preg_replace('/\[\w+\]/', '0', $expr);
        if ($expr === null) {
            return null;
        }

        // Autoriser floor( et ceil( : remplacer par une expression safe
        $expr = preg_replace('/\bfloor\s*\(/i', 'floor(', $expr);
        $expr = preg_replace('/\bceil\s*\(/i', 'ceil(', $expr);

        // Vérifier que l'expression ne contient que des caractères autorisés (chiffres, opérateurs, floor, ceil)
        if (!preg_match('/^[\d\s+\-*\/().floorceil]+$/i', $expr)) {
            return null;
        }

        try {
            $result = eval('return ' . $expr . ';');
        } catch (\Throwable) {
            return null;
        }

        return is_numeric($result) ? (float) $result : null;
    }
}
