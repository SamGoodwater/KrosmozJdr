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
}
