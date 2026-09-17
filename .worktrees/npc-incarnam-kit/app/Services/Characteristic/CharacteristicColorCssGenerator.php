<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\Characteristic;
use Database\Seeders\Data\CharacteristicPaletteResolver;
use Illuminate\Support\Facades\File;

/**
 * Génère le fichier CSS des couleurs de caractéristiques (palette Tailwind ou hex legacy → .color-{key} avec --color).
 * Source unique : colonne {@see Characteristic::$color} ; pas de duplication dans les thèmes SCSS.
 */
class CharacteristicColorCssGenerator
{
    /** Chemin du fichier généré (public, servi en statique). */
    public const OUTPUT_PATH = 'public/css/characteristic-colors.css';

    /**
     * Génère le fichier CSS à partir des caractéristiques ayant une couleur renseignée en base.
     */
    public function generate(): bool
    {
        $path = base_path(self::OUTPUT_PATH);
        $dir = dirname($path);
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $css = $this->buildCss();

        return File::put($path, $css) !== false;
    }

    /**
     * Construit le contenu CSS (classes .color-{key} avec --color en var(--color-{palette}-nuance)).
     */
    public function buildCss(): string
    {
        $lines = [
            '/* Généré depuis les caractéristiques (palette Tailwind en BDD). Ne pas éditer à la main. */',
            '/* Régénéré lors de la sauvegarde d’une caractéristique ou via php artisan characteristics:generate-color-css */',
            '',
        ];

        $characteristics = Characteristic::whereNotNull('color')
            ->where('color', '!=', '')
            ->get(['key', 'color']);

        foreach ($characteristics as $c) {
            $resolved = $this->resolveColorCssValue($c->color);
            if ($resolved === null) {
                continue;
            }
            $class = $this->sanitizeClassKey($c->key);
            if ($class === '') {
                continue;
            }
            $lines[] = ".color-{$class} { --color: {$resolved['main']}; }";
            $lines[] = ".bg-color-{$class} { --bg-color: {$resolved['bg']}; }";
            $lines[] = ".color-{$class}-500 { --color: {$resolved['main']}; }";
            $lines[] = ".bg-color-{$class}-950 { --bg-color: {$resolved['bg']}; }";
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * @return array{main: string, bg: string}|null
     */
    private function resolveColorCssValue(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim($value);
        if (preg_match('/^#([0-9A-Fa-f]{3}){1,2}$/', $value)) {
            return [
                'main' => $value,
                'bg' => $value,
            ];
        }
        if (preg_match('/^([a-z]+)-(\d{2,3})$/', strtolower($value), $m)) {
            $token = $m[1].'-'.$m[2];

            return [
                'main' => "var(--color-{$token})",
                'bg' => "var(--color-{$token})",
            ];
        }
        if (CharacteristicPaletteResolver::isAllowedPalette($value)) {
            $p = strtolower($value);

            return [
                'main' => "var(--color-{$p}-500)",
                'bg' => "var(--color-{$p}-950)",
            ];
        }

        return null;
    }

    /** Clé safe pour une classe CSS (lettres, chiffres, tirets, underscores). */
    private function sanitizeClassKey(string $key): string
    {
        return (string) preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
    }
}
