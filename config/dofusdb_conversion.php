<?php

declare(strict_types=1);

/**
 * Données de conversion DofusDB → KrosmozJDR.
 *
 * Contient uniquement des tableaux et constantes (mapping effectId → champ,
 * elementId → res_*, etc.). Les formules / fonctions PHP qui utilisent ces
 * données vivent dans App\Services\Scrapping\V2\Conversion\ (FormatterApplicator
 * et DofusDbConversionFormulas).
 *
 * Limites min/max : une seule source = CharacteristicService (config ou BDD).
 * Aucune copie des limites ici ; ce fichier ne fait que référencer cette source
 * via 'limits_source' => 'characteristics'. La section 'limits' (vide par défaut)
 * ne sert que si limits_source === 'local'.
 *
 * @see App\Services\Characteristic\CharacteristicService
 * @see docs/50-Fonctionnalités/Scrapping/Refonte/CONVERSION_FORMULAS_PLACEMENT.md
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Caractéristiques pass-through (aucune transformation)
    |--------------------------------------------------------------------------
    |
    | Ces champs sont recopiés tels quels depuis DofusDB. Aucune formule ni
    | formatter spécifique (sauf pickLang si multilingue). Pour description :
    | pas de truncate, on garde tout le texte.
    |
    */
    'pass_through_characteristics' => [
        'name',
        'description', // pas de truncate, on garde tout
        'pa',
        'pm',
        'invocation',
        'po',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transformations par entité (à compléter)
    |--------------------------------------------------------------------------
    |
    | Pour chaque entité (monster, class, item, spell, etc.), liste des
    | caractéristiques avec la transformation à appliquer (formule ou formatter).
    | Les pass_through_characteristics ne figurent pas ici.
    |
    */
    'characteristic_transformations' => [
        'monster' => [],
        'class' => [],
        'item' => [],
        'spell' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formules de conversion (k = Krosmoz, d = DofusDB)
    |--------------------------------------------------------------------------
    |
    | Paramètres utilisés par DofusDbConversionFormulas. Les valeurs sont
    | clampées aux limites du CharacteristicService après conversion.
    |
    | level  : k = d / 10
    | life   : k = d / 200 + level * 5  (level = niveau Krosmoz déjà converti)
    | Force, Intelligence, chance, agilité (strength, intelligence, chance, agility) :
    |   class  : k = 6 + 24 * sqrt((d-50)/1150)
    |   monster : k = 0 + 26 * sqrt((d-50)/1150)
    |
    | initiative :
    |   class  : ratio = (d - 300) / 4700 ; ratio = max(0, min(ratio, 1)) ; k = 10 * ratio ; k >= 0
    |   monster : ratio = (d - 500) / 5000 ; ratio = max(0, min(ratio, 1)) ; k = 10 * ratio (peut être < 0)
    |
    */
    'formulas' => [
        'level' => [
            'formula' => 'k = d / 10',
            'divisor' => 10,
        ],
        'life' => [
            'formula' => 'k = d / 200 + level * 5',
            'divisor' => 200,
            'level_factor' => 5,
        ],
        'attributes' => [
            'formula_class' => 'k = 6 + 24 * sqrt((d-50)/1150)',
            'formula_monster' => 'k = 0 + 26 * sqrt((d-50)/1150)',
            'formula_item' => 'k = 6 + 24 * sqrt((d-50)/1150)',
            'denom' => 1150,
            'offset' => 50,
            'class' => ['base' => 6, 'coeff' => 24],
            'monster' => ['base' => 0, 'coeff' => 26],
            'item' => ['base' => 6, 'coeff' => 24],
        ],
        'attribute_ids' => ['strength', 'intelligence', 'chance', 'agility'],

        'initiative' => [
            'formula_class' => 'ratio = (d - 300) / 4700 ; ratio = max(0, min(ratio, 1)) ; k = 10 * ratio ; k >= 0',
            'formula_monster' => 'ratio = (d - 500) / 5000 ; ratio = min(ratio, 1) (ratio peut être < 0) ; k = 10 * ratio (peut être < 0)',
            'class' => ['offset' => 300, 'denom' => 4700, 'factor' => 10, 'clamp_ratio_min_zero' => true, 'min_zero' => true],
            'monster' => ['offset' => 500, 'denom' => 5000, 'factor' => 10, 'clamp_ratio_min_zero' => false, 'min_zero' => false],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Source des limites après conversion
    |--------------------------------------------------------------------------
    |
    | 'characteristics' = utiliser CharacteristicService pour min/max par entité.
    | 'local' = définir les bornes dans ce fichier.
    |
    */
    'limits_source' => 'characteristics',

    /*
    |--------------------------------------------------------------------------
    | Mapping effectId DofusDB → champ KrosmozJDR
    |--------------------------------------------------------------------------
    |
    | Exemple : 118 => 'strength', 126 => 'intelligence'.
    | À compléter selon la doc DofusDB / effets.
    |
    */
    'effect_id_to_characteristic' => [
        // 118 => 'strength',
        // 126 => 'intelligence',
        // ...
    ],

    /*
    |--------------------------------------------------------------------------
    | Mapping elementId DofusDB → résistance KrosmozJDR (res_*)
    |--------------------------------------------------------------------------
    |
    | -1 ou 0 => neutre, 1 => terre, 2 => feu, 3 => air, 4 => eau (à vérifier).
    |
    */
    'element_id_to_resistance' => [
        -1 => 'res_neutre',
        0 => 'res_neutre',
        1 => 'res_terre',
        2 => 'res_feu',
        3 => 'res_air',
        4 => 'res_eau',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bornes locales (optionnel)
    |--------------------------------------------------------------------------
    |
    | Utilisé si limits_source === 'local'. Sinon, les limites viennent de
    | config/characteristics.php (entities[entity].min/max).
    |
    */
    'limits' => [
        'monster' => [],
        'class' => [],
        'item' => [],
    ],
];
