<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Entity\Specialization;

/**
 * Visuel public d'une spécialisation : un objet, fond transparent.
 *
 * Fichiers : `storage/app/public/images/specializations/{slug}.png`.
 *
 * @example SpecializationImagePaths::urlForName('Artiste');
 */
final class SpecializationImagePaths
{
    public const PUBLIC_PREFIX = '/storage/images/specializations';

    /**
     * Slug ASCII du nom (avec ou sans marque de genre) → fichier.
     *
     * @var array<string, string>
     */
    private const NAME_SLUGS = [
        'erudit' => 'erudit',
        'milicien' => 'milicien',
        'milicienne' => 'milicien',
        'voleur' => 'voleur',
        'voleuse' => 'voleur',
        'voleureuse' => 'voleur',
        'devot' => 'devot',
        'artiste' => 'artiste',
        'explorateur' => 'explorateur',
        'exploratrice' => 'explorateur',
        'explorateurice' => 'explorateur',
        'explorateurrice' => 'explorateur',
        'artisan' => 'artisan',
        'artisane' => 'artisan',
        'negociant' => 'negociant',
        'negociante' => 'negociant',
        'sylvain' => 'sylvain',
        'sylvaine' => 'sylvain',
        'marin' => 'marin',
        'marine' => 'marin',
        'courtisan' => 'courtisan',
        'courtisane' => 'courtisan',
    ];

    public static function urlForName(string $name): ?string
    {
        $slug = self::slugFromName($name);
        if ($slug === null || ! is_file(self::absolutePath($slug))) {
            return null;
        }

        return self::PUBLIC_PREFIX.'/'.$slug.'.png';
    }

    /**
     * Pose l'icône locale si la fiche n'a pas déjà une image choisie à la main.
     */
    public static function assignIfMissing(Specialization $specialization): bool
    {
        $url = self::urlForName((string) $specialization->name);
        if ($url === null) {
            return false;
        }

        $current = trim((string) ($specialization->image ?? ''));
        $ownsCurrent = $current === '' || str_starts_with($current, self::PUBLIC_PREFIX.'/');
        if (! $ownsCurrent || $current === $url) {
            return false;
        }

        $specialization->image = $url;

        return true;
    }

    public static function slugFromName(string $name): ?string
    {
        $key = self::asciiKey($name);
        if ($key === '') {
            return null;
        }

        return self::NAME_SLUGS[$key] ?? null;
    }

    public static function absolutePath(string $slug): string
    {
        return storage_path('app/public/images/specializations/'.$slug.'.png');
    }

    /**
     * « Milicien·ne » et « Négociant·e » deviennent `milicienne` / `negociante`.
     */
    public static function asciiKey(string $name): string
    {
        $trimmed = trim(str_replace(['·', '•'], '', $name));
        if ($trimmed === '') {
            return '';
        }

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $trimmed);
        if (! is_string($ascii) || $ascii === '') {
            $ascii = $trimmed;
        }

        $ascii = strtolower($ascii);
        $ascii = preg_replace('/[^a-z0-9]+/', '', $ascii) ?? '';

        return $ascii;
    }
}
