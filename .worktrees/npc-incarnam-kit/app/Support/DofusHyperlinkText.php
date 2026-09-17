<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Extrait le libellé affichable des hyperliens Ankama/DofusDB.
 *
 * Format courant : `{{spell,32891,1::Évadé}}` → `Évadé`.
 * Plusieurs occurrences et imbrications simples sont gérées.
 */
final class DofusHyperlinkText
{
    /**
     * Remplace chaque `{{…::libellé}}` par le libellé, sans toucher au texte hors hyperlien.
     *
     * @example DofusHyperlinkText::toDisplayLabel('{{spell,1,1::Évadé}}') // 'Évadé'
     * @example DofusHyperlinkText::toDisplayLabel('État {{spell,1,1::X}}.') // 'État X.'
     */
    public static function toDisplayLabel(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        $out = trim($text);
        if ($out === '' || ! str_contains($out, '{{')) {
            return $out;
        }

        $previous = null;
        $guard = 0;
        while ($previous !== $out && $guard < 16) {
            $previous = $out;
            $out = (string) preg_replace_callback(
                '/\{\{[^{}]*?::((?:(?!\}\}).)*?)\}\}/u',
                static fn (array $m): string => $m[1],
                $out
            );
            $guard++;
        }

        return trim($out);
    }
}
