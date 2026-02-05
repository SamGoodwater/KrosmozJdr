<?php

declare(strict_types=1);

namespace App\Services\Characteristic\Formula;

/**
 * Enrichit une map de variables (clé BDD => valeur) avec les noms courts pour les formules.
 *
 * En contexte entité, on peut écrire [level] au lieu de [level_creature]. Ce helper ajoute
 * les alias nom_court => valeur pour chaque clé qui se termine par _creature, _object ou _spell,
 * afin que l'évaluation de formule accepte les deux syntaxes.
 *
 * @see docs/10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md
 */
final class FormulaVariableResolver
{
    private const GROUP_SUFFIXES = ['_creature', '_object', '_spell'];

    /**
     * Enrichit la map avec des alias nom court (sans suffixe) pour le groupe donné.
     * Les clés existantes sont conservées pour que [level_creature] et [level] fonctionnent.
     *
     * @param array<string, int|float> $fullKeyToValue Map clé BDD complète => valeur
     * @return array<string, int|float> Map incluant les alias (nom court => valeur)
     */
    public static function withShortNames(string $group, array $fullKeyToValue): array
    {
        $suffix = '_' . $group;
        $out = $fullKeyToValue;

        foreach ($fullKeyToValue as $key => $value) {
            if ($key !== '' && str_ends_with($key, $suffix)) {
                $shortName = substr($key, 0, -strlen($suffix));
                if ($shortName !== '' && !array_key_exists($shortName, $out)) {
                    $out[$shortName] = $value;
                }
            }
        }

        return $out;
    }

    /**
     * Retourne le nom court (pour usage dans les formules) à partir de la clé BDD.
     * Si la clé se termine par _creature, _object ou _spell, retourne la partie sans suffixe.
     *
     * @return string Clé telle quelle si pas de suffixe reconnu, sinon nom court
     */
    public static function keyToShortName(string $fullKey): string
    {
        foreach (self::GROUP_SUFFIXES as $suffix) {
            if (str_ends_with($fullKey, $suffix)) {
                return substr($fullKey, 0, -strlen($suffix));
            }
        }
        return $fullKey;
    }
}
