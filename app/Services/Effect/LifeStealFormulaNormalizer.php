<?php

declare(strict_types=1);

namespace App\Services\Effect;

/**
 * Normalise la formule « PV volés » pour un sous-effet frapper : raccourcis pourcentages, puis évaluation avec [dgt].
 *
 * - `dgt` = dégâts primaires (sans résistances) issus de la formule de dégâts du même pivot.
 * - `50%`, `12.5 %` → `[dgt]*(50/100)` (chaque occurrence `N%` dans la chaîne).
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 */
final class LifeStealFormulaNormalizer
{
    /**
     * @return string|null Chaîne normalisée ou null si entrée vide / uniquement blancs
     */
    public static function normalize(?string $formula): ?string
    {
        if ($formula === null) {
            return null;
        }
        $t = trim($formula);
        if ($t === '') {
            return null;
        }

        $out = preg_replace_callback(
            '/(\d+(?:\.\d+)?)\s*%/u',
            static function (array $m): string {
                $n = (float) $m[1];

                return '[dgt]*('.$n.'/100)';
            },
            $t
        );

        return is_string($out) ? $out : $t;
    }
}
