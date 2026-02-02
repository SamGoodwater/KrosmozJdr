<?php

declare(strict_types=1);

/**
 * Configuration équipements et caractéristiques par slot.
 *
 * Définit quels types d'équipement peuvent donner quelles caractéristiques (ids de config/characteristics.php)
 * et le bonus max par palier de niveau. Paliers : 1-2, 3-4, 5-6, 7-8, 9-10, 11-12, 13-14, 15-16, 17-18, 19-20.
 * Pour un niveau N, le max = bracket_max[floor((N-1)/2)] (index 0 = niveaux 1-2, index 1 = 3-4, etc.).
 *
 * forgemagie_max : bonus max ajouté par forgemagie sur un objet de ce slot pour cette caractéristique (null = non forgemageable).
 *
 * Référence : docs/110- To Do/Equipements et forgemagie.pdf
 */

return [
    'slots' => [
        'weapon' => [
            'name' => 'Arme',
            'characteristics' => [
                'touch' => [
                    'bracket_max' => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5],
                    'forgemagie_max' => null,
                ],
                'do_fixe_neutre' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 5,
                ],
                'do_fixe_terre' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 5,
                ],
                'do_fixe_feu' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 5,
                ],
                'do_fixe_air' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 5,
                ],
                'do_fixe_eau' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 5,
                ],
                'do_fixe_multiple' => [
                    'bracket_max' => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3],
                    'forgemagie_max' => 2,
                ],
            ],
        ],
        'hat' => [
            'name' => 'Chapeau',
            'characteristics' => [
                'life' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 20,
                ],
                'vitality' => [
                    'bracket_max' => [0, 1, 2, 2, 3, 4, 5, 6, 7, 8],
                    'forgemagie_max' => 2,
                ],
                'wisdom' => [
                    'bracket_max' => [0, 1, 2, 2, 3, 4, 5, 6, 7, 8],
                    'forgemagie_max' => 2,
                ],
                'athletisme' => [
                    'bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                    'forgemagie_max' => 3,
                ],
                'intimidation' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'acrobaties' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'discretion' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'escamotage' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'arcanes' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'histoire' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'investigation' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'nature' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'religion' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'dressage' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'medecine' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'perception' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'perspicacite' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'survie' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'persuasion' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'representation' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'supercherie' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'artisanat' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'herbaliste' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'connaissance_creatures' => ['bracket_max' => [0, 1, 1, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
            ],
        ],
        'cape' => [
            'name' => 'Cape',
            'characteristics' => [
                'ini' => [
                    'bracket_max' => [0, 1, 1, 1, 2, 2, 2, 3, 3, 3],
                    'forgemagie_max' => 3,
                ],
                'life' => ['bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5], 'forgemagie_max' => 20],
                'strength' => ['bracket_max' => [0, 1, 2, 2, 3, 4, 5, 6, 7, 8], 'forgemagie_max' => 2],
                'intelligence' => ['bracket_max' => [0, 1, 2, 2, 3, 4, 5, 6, 7, 8], 'forgemagie_max' => 2],
                'chance' => ['bracket_max' => [0, 1, 2, 2, 3, 4, 5, 6, 7, 8], 'forgemagie_max' => 2],
                'agility' => ['bracket_max' => [0, 1, 2, 2, 3, 4, 5, 6, 7, 8], 'forgemagie_max' => 2],
                'athletisme' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'intimidation' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'acrobaties' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'discretion' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'escamotage' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'arcanes' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'histoire' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'investigation' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'nature' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'religion' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'dressage' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'medecine' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'perception' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'perspicacite' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'survie' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'persuasion' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'representation' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'supercherie' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'artisanat' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'herbaliste' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
                'connaissance_creatures' => ['bracket_max' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 1], 'forgemagie_max' => 3],
            ],
        ],
        'amulet' => [
            'name' => 'Amulette',
            'characteristics' => [
                'life' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 20,
                ],
                'pa' => [
                    'bracket_max' => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5],
                    'forgemagie_max' => 1,
                ],
                'dodge_pa' => [
                    'bracket_max' => [0, 1, 1, 1, 2, 2, 2, 3, 3, 3],
                    'forgemagie_max' => 2,
                ],
            ],
        ],
        'boots' => [
            'name' => 'Bottes',
            'characteristics' => [
                'life' => [
                    'bracket_max' => [1, 1, 2, 2, 3, 3, 4, 4, 5, 5],
                    'forgemagie_max' => 20,
                ],
                'pm' => [
                    'bracket_max' => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5],
                    'forgemagie_max' => 1,
                ],
                'dodge_pm' => [
                    'bracket_max' => [0, 1, 1, 1, 2, 2, 2, 3, 3, 3],
                    'forgemagie_max' => 2,
                ],
            ],
        ],
        'ring' => [
            'name' => 'Anneau',
            'characteristics' => [
                'invocation' => [
                    'bracket_max' => [0, 1, 1, 1, 1, 2, 2, 2, 3, 3],
                    'forgemagie_max' => 1,
                ],
                'po' => [
                    'bracket_max' => [0, 1, 1, 1, 1, 2, 2, 2, 3, 3],
                    'forgemagie_max' => 1,
                ],
                'life' => [
                    'bracket_max' => [1, 1, 1, 1, 2, 2, 2, 3, 3, 3],
                    'forgemagie_max' => 20,
                ],
            ],
        ],
        'belt' => [
            'name' => 'Ceinture',
            'characteristics' => [
                'tacle' => [
                    'bracket_max' => [1, 1, 2, 3, 3, 4, 5, 6, 7, 8],
                    'forgemagie_max' => 2,
                ],
                'fuite' => [
                    'bracket_max' => [1, 1, 2, 3, 3, 4, 5, 6, 7, 8],
                    'forgemagie_max' => 2,
                ],
                'master_bonus' => [
                    'bracket_max' => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3],
                    'forgemagie_max' => null,
                ],
            ],
        ],
        'shield' => [
            'name' => 'Bouclier',
            'characteristics' => [
                'ca' => [
                    'bracket_max' => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5],
                    'forgemagie_max' => null,
                ],
                'res_fixe_neutre' => [
                    'bracket_max' => [1, 2, 2, 3, 4, 4, 5, 6, 6, 7],
                    'forgemagie_max' => 3,
                ],
                'res_fixe_terre' => [
                    'bracket_max' => [1, 2, 2, 3, 4, 4, 5, 6, 6, 7],
                    'forgemagie_max' => 3,
                ],
                'res_fixe_feu' => [
                    'bracket_max' => [1, 2, 2, 3, 4, 4, 5, 6, 6, 7],
                    'forgemagie_max' => 3,
                ],
                'res_fixe_air' => [
                    'bracket_max' => [1, 2, 2, 3, 4, 4, 5, 6, 6, 7],
                    'forgemagie_max' => 3,
                ],
                'res_fixe_eau' => [
                    'bracket_max' => [1, 2, 2, 3, 4, 4, 5, 6, 6, 7],
                    'forgemagie_max' => 3,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper : max bonus équipement pour un niveau donné
    |--------------------------------------------------------------------------
    | bracket_max = 10 valeurs pour paliers 1-2, 3-4, ..., 19-20.
    | max_for_level(level) = bracket_max[floor((level - 1) / 2)] pour level 1..20.
    */
];
