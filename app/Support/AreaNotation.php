<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Validation stricte de la notation de zone Krosmoz (Effect.area, etc.).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
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
}
