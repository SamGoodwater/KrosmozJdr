<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Preview;

/**
 * Construit les sorties prévisualisation pour la commande scrapping :
 * - raw_useful : extraction des champs bruts utiles à Krosmoz (d'après le mapping).
 * - verbose : pour chaque propriété Krosmoz, raw_value, converted_value, valid, existing_value.
 */
final class ScrappingPreviewBuilder
{
    /**
     * Extrait du brut DofusDB les valeurs utiles à Krosmoz (chemins du mapping).
     * Clés = champ cible (field), valeur = valeur brute à ce chemin.
     *
     * @param array<string, mixed> $raw Données brutes DofusDB
     * @param array<string, mixed> $entityConfig Config entité (mapping avec from.path et to[].field)
     * @return array<string, mixed>
     */
    public static function buildRawUseful(array $raw, array $entityConfig): array
    {
        $out = [];
        $mapping = $entityConfig['mapping'] ?? [];
        if (!is_array($mapping)) {
            return $out;
        }
        foreach ($mapping as $map) {
            if (!is_array($map)) {
                continue;
            }
            $from = (array) ($map['from'] ?? []);
            $path = (string) ($from['path'] ?? '');
            if ($path === '') {
                continue;
            }
            $value = self::getByPath($raw, $path);
            $targets = $map['to'] ?? [];
            if (!is_array($targets)) {
                continue;
            }
            foreach ($targets as $target) {
                if (!is_array($target)) {
                    continue;
                }
                $field = $target['field'] ?? null;
                if (is_string($field) && $field !== '') {
                    $out[$field] = $value;
                }
            }
        }
        return $out;
    }

    /**
     * Fusionne les modèles convertis en un seul tableau (clé => valeur).
     *
     * @param array<string, array<string, mixed>> $converted Structure par modèle (creatures, monsters, …)
     * @return array<string, mixed>
     */
    public static function mergeConverted(array $converted): array
    {
        $merged = [];
        foreach ($converted as $model => $fields) {
            if (!is_array($fields)) {
                continue;
            }
            foreach ($fields as $field => $value) {
                $merged[$field] = $value;
            }
        }
        return $merged;
    }

    /**
     * Construit la structure verbose pour un item : par propriété, raw / converti / valide / existant.
     *
     * @param array<string, mixed> $rawUseful Valeurs brutes utiles (champ => valeur)
     * @param array<string, mixed> $convertedMerged Données converties fusionnées
     * @param list<array{path: string, message: string}> $validationErrors Erreurs de validation
     * @param array<string, mixed>|null $existing Attributs de l'entité existante en BDD (mêmes clés)
     * @return array<string, array{raw_value: mixed, converted_value: mixed, valid: bool, existing_value: mixed}> Propriété => détail
     */
    public static function buildVerboseProperties(
        array $rawUseful,
        array $convertedMerged,
        array $validationErrors,
        ?array $existing
    ): array {
        $errorPaths = [];
        foreach ($validationErrors as $err) {
            $path = $err['path'] ?? '';
            if ($path !== '') {
                $errorPaths[$path] = true;
                $base = explode('.', $path)[0] ?? $path;
                $errorPaths[$base] = true;
            }
        }
        $allKeys = array_keys($rawUseful + $convertedMerged);
        $allKeys = array_values(array_unique($allKeys));
        $properties = [];
        foreach ($allKeys as $key) {
            $valid = !isset($errorPaths[$key]);
            $properties[$key] = [
                'raw_value' => $rawUseful[$key] ?? null,
                'converted_value' => $convertedMerged[$key] ?? null,
                'valid' => $valid,
                'existing_value' => $existing[$key] ?? null,
            ];
        }
        return $properties;
    }

    /**
     * @param array<string, mixed> $data
     * @return mixed
     */
    private static function getByPath(array $data, string $path): mixed
    {
        $parts = explode('.', $path);
        $cur = $data;
        foreach ($parts as $part) {
            if (!is_array($cur)) {
                return null;
            }
            if (ctype_digit($part)) {
                $cur = $cur[(int) $part] ?? null;
                continue;
            }
            $cur = $cur[$part] ?? null;
        }
        return $cur;
    }
}
