<?php

declare(strict_types=1);

/**
 * Remplace les couleurs hex par des noms de palettes Tailwind dans les fichiers de seed.
 * Usage : php database/seeders/scripts/apply_tailwind_palettes_to_seeders.php
 */

require dirname(__DIR__, 3).'/vendor/autoload.php';

use Database\Seeders\Data\CharacteristicPaletteResolver;

$files = [
    dirname(__DIR__).'/data/characteristics.php',
    dirname(__DIR__).'/data/characteristic_icons_colors.php',
];

function replaceHexInFile(string $path): int
{
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException("Cannot read {$path}");
    }
    $original = $content;

    $content = preg_replace_callback(
        "/'(?:#[0-9a-fA-F]{6}|#[0-9a-fA-F]{3})'/",
        static function (array $m): string {
            $hex = trim($m[0], "'");

            return "'".CharacteristicPaletteResolver::hexToPalette($hex)."'";
        },
        $content
    ) ?? $content;

    $content = preg_replace_callback(
        '/"(?:#[0-9a-fA-F]{6}|#[0-9a-fA-F]{3})"/',
        static function (array $m): string {
            $hex = trim($m[0], '"');

            return '"'.CharacteristicPaletteResolver::hexToPalette($hex).'"';
        },
        $content
    ) ?? $content;

    if ($content !== $original) {
        file_put_contents($path, $content);

        return substr_count($original, '#') - substr_count($content, '#');
    }

    return 0;
}

$total = 0;
foreach ($files as $f) {
    if (! is_file($f)) {
        fwrite(STDERR, "Missing {$f}\n");
        exit(1);
    }
    $n = replaceHexInFile($f);
    echo basename($f).': approx hex fragments removed diff '.($n)."\n";
    $total += $n;
}
echo "Done. Total # count reduction indicator: {$total}\n";
