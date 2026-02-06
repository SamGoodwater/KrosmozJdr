<?php

declare(strict_types=1);

namespace App\Services\Characteristic;

use App\Models\Characteristic;
use Illuminate\Support\Facades\File;

/**
 * Génère le fichier CSS des couleurs de caractéristiques (hex en BDD → classes .color-{key} avec --color).
 * Permet de garder le système de variable --color utilisé partout dans le SCSS.
 */
class CharacteristicColorCssGenerator
{
    /** Chemin du fichier généré (public, servi en statique). */
    public const OUTPUT_PATH = 'public/css/characteristic-colors.css';

    /**
     * Génère le fichier CSS à partir des caractéristiques ayant une couleur (hex) en base.
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
     * Construit le contenu CSS (classes .color-{key} et .bg-color-{key} avec --color / --bg-color en hex).
     */
    public function buildCss(): string
    {
        $lines = [
            '/* Généré depuis les caractéristiques (couleur hex en BDD). Ne pas éditer à la main. */',
            '/* Régénéré lors de la sauvegarde d’une caractéristique ou via php artisan characteristics:generate-color-css */',
            '',
        ];

        $characteristics = Characteristic::whereNotNull('color')
            ->where('color', '!=', '')
            ->get(['key', 'color']);

        foreach ($characteristics as $c) {
            $hex = $this->normalizeHex($c->color);
            if ($hex === null) {
                continue;
            }
            $class = $this->sanitizeClassKey($c->key);
            if ($class === '') {
                continue;
            }
            $lines[] = ".color-{$class} { --color: {$hex}; }";
            $lines[] = ".bg-color-{$class} { --bg-color: {$hex}; }";
            $lines[] = ".color-{$class}-500 { --color: {$hex}; }";
            $lines[] = ".bg-color-{$class}-950 { --bg-color: {$hex}; }";
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    private function normalizeHex(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $value = trim($value);
        if (preg_match('/^#([0-9A-Fa-f]{3}){1,2}$/', $value)) {
            return $value;
        }

        return null;
    }

    /** Clé safe pour une classe CSS (lettres, chiffres, tirets, underscores). */
    private function sanitizeClassKey(string $key): string
    {
        return (string) preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
    }
}
