<?php

declare(strict_types=1);

namespace App\Services\Effect;

/**
 * Sanitization du texte des effets (template, description, formula).
 * Supprime HTML/JS ; préserve lettres, chiffres, ponctuation, [var] et ndX.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/EFFETS_TEMPLATES_ET_SURETE.md
 * @see docs/50-Fonctionnalités/Spell-Effects/SYNTAXE_EFFETS.md
 */
final class EffectTextSanitizer
{
    /**
     * Nettoie une chaîne destinée à un champ effet (template, description, formula).
     * - Supprime toutes les balises HTML et le contenu dangereux (script, event handlers).
     * - Préserve les variables [nom] et la notation ndX (ex. 2d6).
     *
     * @param string $text Texte brut (saisie ou import).
     * @return string Texte sûr pour stockage.
     *
     * @example
     * sanitize('Inflige [value] dégâts <script>x</script>'); // "Inflige [value] dégâts "
     * sanitize('Jet 2d6 + [agi]'); // "Jet 2d6 + [agi]"
     */
    public function sanitize(string $text): string
    {
        $text = (string) $text;

        // 1. Supprimer toutes les balises HTML (y compris script, style, etc.)
        $text = strip_tags($text);

        // 2. Supprimer les caractères de contrôle et null bytes
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? $text;

        // 3. Supprimer les chevrons restants (éviter ré-injection)
        $text = str_replace(['<', '>'], '', $text);

        // 4. Supprimer les protocoles dangereux (javascript:, data:, vbscript:)
        $text = preg_replace('#\s*javascript\s*:\s*#iu', ' ', $text) ?? $text;
        $text = preg_replace('#\s*data\s*:\s*#iu', ' ', $text) ?? $text;
        $text = preg_replace('#\s*vbscript\s*:\s*#iu', ' ', $text) ?? $text;

        // 5. Supprimer les event handlers (onclick=..., onerror=..., etc.)
        $text = preg_replace('/\s*on\w+\s*=\s*["\']?[^"\'\s]*["\']?/iu', ' ', $text) ?? $text;

        // 6. Normaliser les espaces multiples et trim
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
