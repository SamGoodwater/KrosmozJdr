<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Constantes partagées pour les zones d'impact (Effect, Spell).
 *
 * Notation : forme[-paramètres] (point, line-1x9, cross-0-2, circle-0-2, rect-3x4).
 * Icônes dans storage/app/public/images/icons/areas/ (point.svg, line.svg, etc.).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 */
final class AreaConstants
{
    /** Formes de base supportées (aligné avec les icônes disponibles). */
    public const SHAPES = ['point', 'line', 'cross', 'circle', 'rect'];

    /** Chemin relatif vers les icônes (depuis storage/images/). */
    public const ICON_BASE = 'icons/areas';

    /**
     * Mapping ID shape (legacy/select) → nom de forme.
     * Utilisé pour formulaires, rétrocompat.
     */
    public const SHAPE_ID_MAP = [
        1 => 'point',
        2 => 'line',
        3 => 'cross',
        4 => 'circle',
        5 => 'rect',
    ];

    /** Libellés pour affichage (formes de base). */
    public const SHAPE_LABELS = [
        'point' => 'Point',
        'line' => 'Ligne',
        'cross' => 'Croix',
        'circle' => 'Cercle',
        'rect' => 'Rectangle',
    ];

    /**
     * Extrait le nom de forme depuis une notation (point, line-1x9, circle-0-2, etc.).
     *
     * @return string|null Forme (point, line, cross, circle, rect) ou null si inconnu
     */
    public static function extractShapeFromNotation(?string $area): ?string
    {
        if ($area === null || $area === '') {
            return null;
        }
        $area = trim($area);
        if ($area === '') {
            return null;
        }
        // Partie avant le premier "-" (sauf pour "shape-99" → fallback point)
        $pos = strpos($area, '-');
        $shape = $pos !== false ? substr($area, 0, $pos) : $area;

        if (in_array($shape, self::SHAPES, true)) {
            return $shape;
        }
        if ($shape === 'shape') {
            return 'point'; // Fallback pour shape-{id}
        }

        return null;
    }

    /**
     * Retourne le chemin de l'icône pour une notation.
     *
     * @return string Chemin (ex: icons/areas/point.svg) ou point par défaut
     */
    public static function getIconPath(?string $area): string
    {
        $shape = self::extractShapeFromNotation($area);

        return $shape !== null
            ? self::ICON_BASE . '/' . $shape . '.svg'
            : self::ICON_BASE . '/point.svg';
    }

    /**
     * Libellé affiché pour une forme.
     */
    public static function getShapeLabel(?string $shape): string
    {
        return self::SHAPE_LABELS[$shape ?? ''] ?? (string) $shape;
    }
}
