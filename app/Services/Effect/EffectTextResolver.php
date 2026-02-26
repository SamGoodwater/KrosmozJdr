<?php

declare(strict_types=1);

namespace App\Services\Effect;

/**
 * Résout les variables [var] dans un template et formate la notation ndX.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/SYNTAXE_EFFETS.md
 */
final class EffectTextResolver
{
    /** Pattern [nom_var] : lettres, chiffres, underscore. */
    private const VARIABLE_PATTERN = '/\[([a-zA-Z_][a-zA-Z0-9_]*)\]/';

    /** Pattern ndX (ex. 1d6, 2d10). */
    private const DICE_PATTERN = '/\b(\d+)d(\d+)\b/i';

    /**
     * Remplace les variables [key] par les valeurs du contexte.
     *
     * @param array<string, int|float|string> $context Map nom => valeur (ex. agi => 15, value => 10)
     * @return string Template avec [key] remplacés (les non fournis restent [key])
     */
    public function resolveEffectText(string $template, array $context): string
    {
        $result = $template;
        foreach ($context as $key => $val) {
            if (is_scalar($val)) {
                $result = str_replace('[' . $key . ']', (string) $val, $result);
            }
        }

        return $result;
    }

    /**
     * Formate la notation ndX dans un texte.
     *
     * @param bool $human_readable Si true : "2d6" → "2 dés à 6 faces"
     */
    public function formatDiceInText(string $text, bool $human_readable = false): string
    {
        if (!$human_readable) {
            return $text;
        }

        return (string) preg_replace_callback(
            self::DICE_PATTERN,
            fn (array $m) => $this->formatDice($m[0], true),
            $text
        );
    }

    /**
     * Formate une notation ndX isolée.
     *
     * @param string $notation Ex. "2d6"
     * @param bool $human_readable Si true : "2 dés à 6 faces"
     */
    public function formatDice(string $notation, bool $human_readable = false): string
    {
        if (!$human_readable || !preg_match(self::DICE_PATTERN, $notation, $m)) {
            return $notation;
        }

        $n = (int) $m[1];
        $faces = (int) $m[2];
        $de = $n === 1 ? 'dé' : 'dés';

        return $n . ' ' . $de . ' à ' . $faces . ' face' . ($faces > 1 ? 's' : '');
    }
}
