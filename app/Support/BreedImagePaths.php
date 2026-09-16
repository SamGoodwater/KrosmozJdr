<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Entity\Breed;

/**
 * Chemins publics des visuels de classe sous `storage/app/public/images/breeds/{slug}/`.
 *
 * @example BreedImagePaths::columnValuesForSlug('iop')['image_full_male'];
 */
final class BreedImagePaths
{
    public const PUBLIC_PREFIX = '/storage/images/breeds';

    /** @var array<int, string> id Dofus → slug dossier ASCII */
    public const DOFUS_ID_SLUGS = [
        1 => 'feca',
        2 => 'osamodas',
        3 => 'enutrof',
        4 => 'sram',
        5 => 'xelor',
        6 => 'ecaflip',
        7 => 'eniripsa',
        8 => 'iop',
        9 => 'cra',
        10 => 'sadida',
        11 => 'sacrieur',
        12 => 'pandawa',
        13 => 'roublard',
        14 => 'zobal',
        15 => 'steamer',
        16 => 'eliotrope',
        17 => 'huppermage',
        18 => 'ouginak',
        20 => 'forgelance',
    ];

    /** @var array<string, string> colonne BDD → fichier */
    public const FILES = [
        'symbol_full' => 'symbol-full.png',
        'symbol_bw' => 'symbol-bw.png',
        'logo_male' => 'logo_m.png',
        'logo_female' => 'logo_f.png',
        'image_full_male' => 'full_m.png',
        'image_full_female' => 'full_f.png',
    ];

    /**
     * @return array{
     *     symbol_full: string|null,
     *     symbol_bw: string|null,
     *     logo_male: string|null,
     *     logo_female: string|null,
     *     image_full_male: string|null,
     *     image_full_female: string|null,
     *     image: string|null,
     *     icon: string|null
     * }
     */
    public static function columnValuesForSlug(string $slug): array
    {
        $slug = self::asciiSlug($slug);
        $out = [];
        foreach (self::FILES as $column => $file) {
            $out[$column] = self::exists($slug, $file) ? self::publicUrl($slug, $file) : null;
        }
        $out['image'] = $out['image_full_male'] ?? $out['image_full_female'];
        $out['icon'] = $out['symbol_bw'] ?? $out['symbol_full'];

        return $out;
    }

    public static function applyTo(Breed $breed, ?string $slug = null): bool
    {
        $resolved = $slug ?? self::slugFor($breed);
        if ($resolved === null || $resolved === '') {
            return false;
        }

        $breed->fill(self::columnValuesForSlug($resolved));

        return true;
    }

    public static function slugFor(Breed $breed): ?string
    {
        $dofusId = $breed->dofusdb_id;
        if (is_numeric($dofusId)) {
            $fromId = self::DOFUS_ID_SLUGS[(int) $dofusId] ?? null;
            if ($fromId !== null) {
                return $fromId;
            }
        }

        return self::slugFromName((string) $breed->name);
    }

    public static function slugFromName(string $name): ?string
    {
        $ascii = self::asciiSlug($name);
        if ($ascii === 'foggernaut') {
            return 'steamer';
        }
        if ($ascii === '') {
            return null;
        }

        return is_dir(self::absoluteDir($ascii)) ? $ascii : null;
    }

    public static function publicUrl(string $slug, string $file): string
    {
        return self::PUBLIC_PREFIX.'/'.$slug.'/'.$file;
    }

    public static function exists(string $slug, string $file): bool
    {
        return is_file(self::absoluteDir($slug).DIRECTORY_SEPARATOR.$file);
    }

    public static function menuIcon(Breed $breed): ?string
    {
        return self::firstNonEmpty([
            $breed->symbol_bw,
            $breed->icon,
            $breed->symbol_full,
            $breed->image,
        ]);
    }

    public static function menuIconHover(Breed $breed): ?string
    {
        $hover = self::firstNonEmpty([
            $breed->symbol_full,
            $breed->symbol_bw,
            $breed->icon,
        ]);
        $base = self::menuIcon($breed);
        if ($hover === null || $hover === $base) {
            return null;
        }

        return $hover;
    }

    public static function asciiSlug(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '';
        }
        $ascii = strtr(mb_strtolower($trimmed), [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n', 'ÿ' => 'y',
        ]);
        $ascii = preg_replace('/[^a-z0-9]+/', '', $ascii) ?? $ascii;

        return $ascii;
    }

    private static function absoluteDir(string $slug): string
    {
        return storage_path('app/public/images/breeds/'.$slug);
    }

    /**
     * @param  list<string|null>  $candidates
     */
    private static function firstNonEmpty(array $candidates): ?string
    {
        foreach ($candidates as $value) {
            if (! is_string($value)) {
                continue;
            }
            $trimmed = trim($value);
            if ($trimmed !== '') {
                return $trimmed;
            }
        }

        return null;
    }
}
