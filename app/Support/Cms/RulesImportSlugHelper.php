<?php

namespace App\Support\Cms;

use Illuminate\Support\Str;

/**
 * Slugs de pages « règles » alignés sur l’import TOC (préfixe {@code regles-}).
 */
final class RulesImportSlugHelper
{
    public static function buildPageSlug(string $number, string $title): string
    {
        $normalizedNumber = str_replace('.', '-', trim($number));

        return Str::slug("regles-{$normalizedNumber}-{$title}");
    }

    /**
     * Slug de section aligné sur l’import TOC (préfixe {@code regle-}).
     */
    public static function buildSectionSlug(string $number, string $title): string
    {
        $normalizedNumber = str_replace('.', '-', trim($number));

        return Str::slug("regle-{$normalizedNumber}-{$title}");
    }

    /**
     * L’identité d’une section règles est le numéro TOC, pas le titre du libellé.
     *
     * `regle-1-2-3` et `regle-1-2-3-ancien-titre` correspondent à 1.2.3 ;
     * `regle-1-2-30-autre` ne correspond pas (le tiret final évite le préfixe ambigu).
     */
    public static function sectionSlugMatchesNumber(string $slug, string $number): bool
    {
        $normalizedNumber = str_replace('.', '-', trim($number));
        if ($normalizedNumber === '') {
            return false;
        }

        $base = 'regle-'.$normalizedNumber;

        return $slug === $base || str_starts_with($slug, $base.'-');
    }
}
