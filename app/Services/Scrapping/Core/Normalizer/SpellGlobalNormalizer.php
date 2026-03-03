<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Normalizer;

/**
 * Construit une vue "paramètres globaux" d'un sort pour simplifier le mapping.
 *
 * Les données DofusDB pour un sort sont à plusieurs niveaux (spell → spell-levels → effects).
 * Ce normaliseur fusionne les infos du sort et du premier niveau (grade 1) dans un seul
 * objet `spell_global`, afin que la conversion n'ait qu'une source à lire (spell_global.*).
 *
 * Utilisé par l'Orchestrator avant ConversionService::convert() pour entity=spell.
 *
 * @see docs/50-Fonctionnalités/Scrapping/DOFUSDB_API_SPELLS_REFERENCE.md
 * @see docs/50-Fonctionnalités/Scrapping/DOFUSDB_EFFECTS_CONVERSION.md
 */
final class SpellGlobalNormalizer
{
    /** Clés copiées depuis la racine du sort (GET /spells/{id}). */
    private const FROM_SPELL_ROOT = [
        'id',
        'name',
        'description',
        'img',
        'elementId',
        'categoryId',
    ];

    /** Clés copiées depuis le premier spell-level (levels.0). */
    private const FROM_LEVEL_0 = [
        'apCost',
        'minRange',
        'range',
        'grade',
        'maxCastPerTurn',
        'maxCastPerTarget',
        'castTestLos',
        'rangeCanBeBoosted',
        'minCastInterval',
        'minCastIntervalEditable',
        'isMagic',
        'powerful',
    ];

    /**
     * Construit l'objet spell_global à partir de raw (spell + levels).
     *
     * @param array<string, mixed> $raw Données brutes : réponse GET /spells/{id} + raw['levels'] = liste des spell-levels
     * @return array<string, mixed> Vue plate des paramètres globaux (grade 1) pour le mapping
     */
    public function build(array $raw): array
    {
        $global = [];

        foreach (self::FROM_SPELL_ROOT as $key) {
            if (array_key_exists($key, $raw)) {
                $global[$key] = $raw[$key];
            }
        }

        $level0 = $raw['levels'][0] ?? null;
        if (is_array($level0)) {
            foreach (self::FROM_LEVEL_0 as $key) {
                if (array_key_exists($key, $level0)) {
                    $global[$key] = $level0[$key];
                }
            }
            // Zone : premier effet du premier niveau (convention Krosmoz)
            $effects = $level0['effects'] ?? [];
            $firstEffect = is_array($effects[0] ?? null) ? $effects[0] : null;
            if ($firstEffect !== null && array_key_exists('zoneDescr', $firstEffect)) {
                $global['area'] = $firstEffect['zoneDescr'];
            }
        }

        return $global;
    }
}
