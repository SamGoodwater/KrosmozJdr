<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Vocabulaire joueur Krosmoz (aligné sur le livre de règles).
 */
final class KrosmozGameTerms
{
    /**
     * Remplace l’adjectif Dofus « désenvoûtable » par le terme JDR « dissipable ».
     *
     * Ne touche pas « désenvoûtement » ni le verbe « désenvoûter ».
     *
     * @example KrosmozGameTerms::replaceDesenvoutableWithDissipable('PAS DÉSENVOÛTABLE.')
     * // 'PAS DISSIPABLE.'
     */
    public static function replaceDesenvoutableWithDissipable(string $text): string
    {
        $replaced = preg_replace_callback(
            '/d[eéèëEÉÈË]senvo[uûUÛ]tables?/iu',
            static function (array $matches): string {
                $src = $matches[0];
                $plural = str_ends_with(mb_strtolower($src, 'UTF-8'), 's');
                if (mb_strtoupper($src, 'UTF-8') === $src) {
                    return $plural ? 'DISSIPABLES' : 'DISSIPABLE';
                }
                $first = mb_substr($src, 0, 1, 'UTF-8');
                if ($first === mb_strtoupper($first, 'UTF-8') && $first !== mb_strtolower($first, 'UTF-8')) {
                    return $plural ? 'Dissipables' : 'Dissipable';
                }

                return $plural ? 'dissipables' : 'dissipable';
            },
            $text
        );

        return is_string($replaced) ? $replaced : $text;
    }

    /**
     * Applique le remplacement sur une chaîne ou, récursivement, sur un tableau (JSON décodé).
     *
     * @example KrosmozGameTerms::replaceInMixed(['value' => 'Pas désenvoutable'])
     * // ['value' => 'Pas dissipable']
     */
    public static function replaceInMixed(mixed $value): mixed
    {
        if (is_string($value)) {
            return self::replaceDesenvoutableWithDissipable($value);
        }
        if (! is_array($value)) {
            return $value;
        }

        $out = [];
        foreach ($value as $key => $item) {
            $out[$key] = self::replaceInMixed($item);
        }

        return $out;
    }
}
