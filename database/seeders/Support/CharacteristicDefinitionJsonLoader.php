<?php

declare(strict_types=1);

namespace Database\Seeders\Support;

use JsonException;

/**
 * Charge des définitions de caractéristiques depuis `database/seeders/data/caracterictis-definition/*.json`.
 *
 * @description
 * - Un fichier par caractéristique : nom de fichier = clé BDD (`snake_case`, séparateur `_`) + `.json`.
 * - Les clés racine commençant par `_` sont ignorées (méta / notes), elles ne sont pas persistées.
 * - Fichiers exclus du seed : préfixe `_`, suffixe `.demo.json`, tout nom ne matchant pas `^[a-z0-9_]+$`.
 * - Si aucun fichier « seedable » n’est trouvé, retourne `null` pour laisser le seeder utiliser le fallback PHP.
 */
final class CharacteristicDefinitionJsonLoader
{
    /**
     * @return list<array<string, mixed>>|null null = répertoire absent ou aucun fichier utilisable → fallback PHP
     */
    public static function loadFromDirectory(string $relativeToBasePath): ?array
    {
        $dir = base_path($relativeToBasePath);
        if (! is_dir($dir)) {
            return null;
        }

        $paths = glob($dir.'/*.json') ?: [];
        $paths = array_values(array_filter($paths, static fn (string $p): bool => self::isSeedableFilename($p)));
        if ($paths === []) {
            return null;
        }

        sort($paths, SORT_STRING);

        $rows = [];
        foreach ($paths as $path) {
            $rows[] = self::decodeAndNormalizeFile($path);
        }

        return $rows;
    }

    private static function isSeedableFilename(string $path): bool
    {
        $base = basename($path);
        if (! str_ends_with($base, '.json')) {
            return false;
        }

        $stem = basename($path, '.json');
        if (str_starts_with($stem, '_')) {
            return false;
        }

        if (str_ends_with($stem, '.demo')) {
            return false;
        }

        return (bool) preg_match('/^[a-z0-9_]+$/', $stem);
    }

    /**
     * @return array<string, mixed>
     */
    private static function decodeAndNormalizeFile(string $path): array
    {
        $stem = basename($path, '.json');
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException('CharacteristicDefinitionJsonLoader : lecture impossible : '.$path);
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException(
                'CharacteristicDefinitionJsonLoader : JSON invalide : '.$path.' — '.$e->getMessage(),
                0,
                $e
            );
        }

        if (! is_array($data)) {
            throw new \RuntimeException('CharacteristicDefinitionJsonLoader : racine attendue : objet JSON : '.$path);
        }

        $data = self::stripMetaKeys($data);

        $key = $data['key'] ?? null;
        if (! is_string($key) || $key === '') {
            throw new \RuntimeException('CharacteristicDefinitionJsonLoader : clé « key » manquante ou vide : '.$path);
        }

        if ($key !== $stem) {
            throw new \RuntimeException(sprintf(
                'CharacteristicDefinitionJsonLoader : incohérence nom de fichier / key — fichier « %s », key « %s ».',
                $stem,
                $key
            ));
        }

        return $data;
    }

    /**
     * Supprime les clés racine préfixées par `_` (commentaires / méta).
     *
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private static function stripMetaKeys(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            if (is_string($k) && str_starts_with($k, '_')) {
                continue;
            }
            $out[$k] = $v;
        }

        return $out;
    }
}
