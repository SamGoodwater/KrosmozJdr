<?php

declare(strict_types=1);

/**
 * Config de conversion DofusDB (pass_through, mappings, limits_source, etc.).
 * Réexporter depuis la BDD si modifié via l'interface (quand export sera en place).
 */
return [
    'pass_through_characteristics' => [
        'name',
        'description',
        'pa',
        'pm',
        'invocation',
        'po',
    ],
    'characteristic_transformations' => [
        'monster' => [],
        'class' => [],
        'item' => [],
        'spell' => [],
    ],
    'limits_source' => ['characteristics'],
    'effect_id_to_characteristic' => [],
    'element_id_to_resistance' => [
        -1 => 'res_neutre',
        0 => 'res_neutre',
        1 => 'res_terre',
        2 => 'res_feu',
        3 => 'res_air',
        4 => 'res_eau',
    ],
    'limits' => [
        'monster' => [],
        'class' => [],
        'item' => [],
    ],
];
