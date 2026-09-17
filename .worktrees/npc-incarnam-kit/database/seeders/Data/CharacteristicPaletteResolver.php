<?php

declare(strict_types=1);

namespace Database\Seeders\Data;

/**
 * Associe une couleur hex aux noms de palettes Tailwind utilisés en base (sans nuance).
 * Ancres = teinte ~500 de chaque palette (Tailwind + brown projet).
 */
final class CharacteristicPaletteResolver
{
    /** @var array<string, string> nom_palette => #rrggbb (référence pour distance RGB) */
    private const ANCHORS = [
        'slate' => '#64748b',
        'gray' => '#6b7280',
        'zinc' => '#71717a',
        'neutral' => '#737373',
        'stone' => '#78716c',
        'red' => '#ef4444',
        'orange' => '#f97316',
        'amber' => '#f59e0b',
        'yellow' => '#eab308',
        'lime' => '#84cc16',
        'green' => '#22c55e',
        'emerald' => '#10b981',
        'teal' => '#14b8a6',
        'cyan' => '#06b6d4',
        'sky' => '#0ea5e9',
        'blue' => '#3b82f6',
        'indigo' => '#6366f1',
        'violet' => '#8b5cf6',
        'purple' => '#a855f7',
        'fuchsia' => '#d946ef',
        'pink' => '#ec4899',
        'rose' => '#f43f5e',
        'brown' => '#795548',
    ];

    /** Palettes autorisées en base / admin (ordre alphabétique pour selects). */
    public const ALLOWED_PALETTES = [
        'amber', 'blue', 'brown', 'cyan', 'emerald', 'fuchsia', 'gray', 'green',
        'indigo', 'lime', 'neutral', 'orange', 'pink', 'purple', 'red', 'rose',
        'sky', 'slate', 'stone', 'teal', 'violet', 'yellow', 'zinc',
    ];

    public static function hexToPalette(string $hex): string
    {
        $hex = strtolower(trim($hex));
        if ($hex === '' || $hex[0] !== '#') {
            return 'slate';
        }
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return 'slate';
        }
        $rgb = self::hexToRgb('#'.$hex);
        $best = 'slate';
        $bestDist = PHP_FLOAT_MAX;
        foreach (self::ANCHORS as $name => $anchorHex) {
            $d = self::rgbDistance($rgb, self::hexToRgb($anchorHex));
            if ($d < $bestDist) {
                $bestDist = $d;
                $best = $name;
            }
        }

        return $best;
    }

    /**
     * @return array{r:int,g:int,b:int}
     */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim(strtolower($hex), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * @param  array{r:int,g:int,b:int}  $a
     * @param  array{r:int,g:int,b:int}  $b
     */
    private static function rgbDistance(array $a, array $b): float
    {
        return sqrt(
            ($a['r'] - $b['r']) ** 2 +
            ($a['g'] - $b['g']) ** 2 +
            ($a['b'] - $b['b']) ** 2
        );
    }

    public static function isAllowedPalette(string $value): bool
    {
        return in_array(strtolower(trim($value)), self::ALLOWED_PALETTES, true);
    }
}
