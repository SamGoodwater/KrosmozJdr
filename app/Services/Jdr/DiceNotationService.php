<?php

declare(strict_types=1);

namespace App\Services\Jdr;

/**
 * Convertit des valeurs numériques (une valeur ou une plage min–max) en notation de dés JDR : ndX ou ndX+y.
 *
 * Utilisable partout où l'on doit afficher ou stocker un tirage de dés à partir de valeurs brutes
 * (ex. conversion Dofus → Krosmoz, affichage barèmes, génération de fiches).
 *
 * Règles de choix :
 * - Écart relatif &lt; 5 % ou valeur unique → forme ndX+y pour coller au plus près de la valeur (ex. 2d4+4).
 * - Écart &gt; 30 % → forme ndX uniquement, petit n et grand X pour l'aléatoire (ex. 2d6, 2d12).
 * - Écart entre 5 % et 30 % → forme ndX uniquement, grand n et petit X (ex. 3d4, 2d6).
 *
 * Les valeurs d'entrée sont considérées comme la cible Krosmoz (échelle déjà réduite si besoin).
 * Les dés classiques utilisés : d4, d6, d8, d10, d12, d20.
 *
 * @see docs/50-Fonctionnalités/Scrapping/DICE_NOTATION_SERVICE.md
 */
final class DiceNotationService
{
    /** Faces de dés autorisées (ordre croissant). */
    private const SIDES = [4, 6, 8, 10, 12, 20];

    /** Seuil en dessous duquel on privilégie ndX+y (valeur quasi fixe). */
    private const SPREAD_THRESHOLD_FIXED = 0.05;

    /** Seuil au-dessus duquel on privilégie petit n / grand X (fort aléatoire). */
    private const SPREAD_THRESHOLD_RANDOM = 0.30;

    /**
     * Convertit une valeur unique ou une plage min–max en notation ndX ou ndX+y.
     *
     * @param float $min Valeur minimale (ou valeur unique si max = null).
     * @param float|null $max Valeur maximale ; si null, une seule valeur cible (min = max).
     * @return string Notation "ndX" ou "ndX+y" (ex. "2d6", "2d4+4").
     */
    public function toDiceNotation(float $min, ?float $max = null): string
    {
        if ($max === null || $max <= $min) {
            $max = $min;
        }
        $min = max(0, $min);
        $max = max($min, $max);
        $avg = ($min + $max) / 2.0;
        $spreadRatio = $max > 0 ? (($max - $min) / $max) : 0.0;

        if ($spreadRatio < self::SPREAD_THRESHOLD_FIXED) {
            return $this->formatFixedModifier((int) round($avg));
        }
        if ($spreadRatio > self::SPREAD_THRESHOLD_RANDOM) {
            return $this->formatWideRandom((int) round($min), (int) round($max), $avg);
        }
        return $this->formatMediumSpread((int) round($min), (int) round($max), $avg);
    }

    /**
     * Cas valeur quasi fixe ou unique : ndX+y pour se rapprocher au plus près.
     */
    private function formatFixedModifier(int $target): string
    {
        if ($target <= 0) {
            return '1d4';
        }
        $best = ['n' => 1, 'X' => 4, 'y' => 0, 'diff' => (float) $target];
        foreach (self::SIDES as $X) {
            for ($n = 1; $n <= 8; $n++) {
                $diceAvg = $n * ($X + 1) / 2.0;
                $y = (int) round($target - $diceAvg);
                if ($y < 0) {
                    continue;
                }
                $actual = $diceAvg + $y;
                $diff = abs($actual - $target);
                if ($diff < $best['diff']) {
                    $best = ['n' => $n, 'X' => $X, 'y' => $y, 'diff' => $diff];
                }
            }
        }
        return $best['y'] > 0
            ? $best['n'] . 'd' . $best['X'] . '+' . $best['y']
            : $best['n'] . 'd' . $best['X'];
    }

    /**
     * Cas fort écart : petit n, grand X (aléatoire, pas de modificateur).
     */
    private function formatWideRandom(int $min, int $max, float $avg): string
    {
        $best = null;
        foreach (array_reverse(self::SIDES) as $X) {
            for ($n = 1; $n <= 3; $n++) {
                $rangeMin = $n;
                $rangeMax = $n * $X;
                $diceAvg = $n * ($X + 1) / 2.0;
                if ($rangeMax < $max) {
                    continue;
                }
                $score = abs($diceAvg - $avg) + ($rangeMin > $min ? 10 : 0) + $n * 0.1;
                if ($best === null || $score < $best['score']) {
                    $best = ['n' => $n, 'X' => $X, 'score' => $score];
                }
            }
        }
        if ($best === null) {
            $best = ['n' => 2, 'X' => 12, 'score' => 0];
        }
        return $best['n'] . 'd' . $best['X'];
    }

    /**
     * Cas écart moyen : grand n, petit X (courbe en cloche, pas de modificateur).
     */
    private function formatMediumSpread(int $min, int $max, float $avg): string
    {
        $best = null;
        foreach (self::SIDES as $X) {
            for ($n = 2; $n <= 6; $n++) {
                $rangeMin = $n;
                $rangeMax = $n * $X;
                $diceAvg = $n * ($X + 1) / 2.0;
                if ($rangeMax < $max || $rangeMin > $min) {
                    continue;
                }
                $score = abs($diceAvg - $avg);
                if ($best === null || $score < $best['score']) {
                    $best = ['n' => $n, 'X' => $X, 'score' => $score];
                }
            }
        }
        if ($best === null) {
            $best = ['n' => 2, 'X' => 6, 'score' => 0];
        }
        return $best['n'] . 'd' . $best['X'];
    }
}
