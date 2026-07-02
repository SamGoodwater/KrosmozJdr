<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Validation stricte de la notation de zone Krosmoz (Effect.area, etc.).
 *
 * @see docs/features/effects/README.md
 */
final class AreaNotation
{
    /**
     * Chaîne vide ou null = valide (champ optionnel).
     */
    public static function isValid(?string $area): bool
    {
        if ($area === null) {
            return true;
        }
        $s = trim($area);
        if ($s === '') {
            return true;
        }
        if (strlen($s) > 64) {
            return false;
        }

        if ($s === 'point') {
            return true;
        }

        if (preg_match('/^line-1x(\d+)$/', $s, $m)) {
            return (int) $m[1] >= 1;
        }

        if (preg_match('/^cross-(\d+)-(\d+)$/', $s, $m)) {
            $a = (int) $m[1];
            $b = (int) $m[2];

            return $a <= $b;
        }

        if (preg_match('/^circle-(\d+)-(\d+)$/', $s, $m)) {
            $a = (int) $m[1];
            $b = (int) $m[2];

            return $a <= $b;
        }

        if (preg_match('/^rect-(\d+)x(\d+)$/', $s, $m)) {
            return (int) $m[1] >= 1 && (int) $m[2] >= 1;
        }

        if (preg_match('/^shape-(\d+)$/', $s, $m)) {
            return (int) $m[1] >= 1;
        }

        if (preg_match('/^shape-(\d+)-(\d+)-(\d+)$/', $s, $m)) {
            return (int) $m[1] >= 1;
        }

        return false;
    }

    /**
     * Libellé français pour tooltips et résumés UI (ex. chips sous-effets de sort).
     *
     * S’aligne sur la notation Krosmoz (voir `docs/features/effects/README.md`).
     * avec paramètres issus de la notation Krosmoz.
     *
     * @return string Chaîne vide si null/vide ; sinon phrase lisible ; notation brute en dernier recours
     */
    public static function describeInFrench(?string $area): string
    {
        if ($area === null) {
            return '';
        }
        $s = trim($area);
        if ($s === '') {
            return '';
        }

        if ($s === 'point') {
            return 'cible unique';
        }

        if (preg_match('/^line-1x(\d+)$/', $s, $m)) {
            $n = (int) $m[1];

            return $n === 1 ? 'ligne de 1 case' : "ligne de {$n} cases";
        }

        if (preg_match('/^rect-(\d+)x(\d+)$/', $s, $m)) {
            $w = (int) $m[1];
            $h = (int) $m[2];
            if ($w === $h) {
                return $w === 1
                    ? 'carré de 1 case'
                    : "carré de {$w} × {$w} cases";
            }

            return "rectangle de {$w} par {$h} cases";
        }

        if (preg_match('/^cross-(\d+)-(\d+)$/', $s, $m)) {
            $a = (int) $m[1];
            $b = (int) $m[2];
            if ($a === $b) {
                return $b === 0
                    ? 'cible unique'
                    : "croix : bras jusqu'à {$b} case".($b > 1 ? 's' : '');
            }

            return "croix de {$a} à {$b} cases";
        }

        if (preg_match('/^circle-(\d+)-(\d+)$/', $s, $m)) {
            $a = (int) $m[1];
            $b = (int) $m[2];
            if ($a === 0 && $b === 0) {
                return 'cible unique';
            }
            if ($a === $b && $a > 0) {
                return $b === 1
                    ? 'cercle (contour) à 1 case du centre'
                    : "cercle (contour) à {$b} cases du centre";
            }
            if ($a === 0) {
                return $b === 1
                    ? "disque : jusqu'à 1 case du centre"
                    : "disque : jusqu'à {$b} cases du centre";
            }

            return "anneau circulaire : de {$a} à {$b} cases du centre";
        }

        if (preg_match('/^shape-(\d+)$/', $s, $m)) {
            return 'zone spéciale (forme '.$m[1].')';
        }

        if (preg_match('/^shape-(\d+)-(\d+)-(\d+)$/', $s, $m)) {
            return 'zone spéciale (forme '.$m[1].', '.$m[2].'–'.$m[3].')';
        }

        return 'zone '.$s;
    }
}
