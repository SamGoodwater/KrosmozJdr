<?php

declare(strict_types=1);

/**
 * Normes (chartes) pour les caractéristiques du groupe sort.
 *
 * Deux catégories :
 * - Type 1 : descripteurs du sort (PA, portée, zone, durée, etc.)
 * - Type 2 : effets du sort (dégâts, soins, bouclier, buffs stats, etc.)
 *
 * Calibrées selon les règles 5.2.3 (Sorts et aptitudes) :
 *   - Dégâts : 1d6→5d6+mod selon le niveau (avg 3.5→17.5+mod)
 *   - Soins  : 1d4→5d4+mod (avg 2.5→12.5+mod)
 *   - Coûts PA : 2-5 PA, sorts forts 5+ PA
 */

return [

    // =====================================================================
    // EFFETS PRINCIPAUX (Type 2 "action")
    // =====================================================================

    // Dégâts bruts — courbe exponentielle douce (5.2.3.2)
    // lvl1 neutre : ~3 (1d6 avg), lvl20 neutre : ~28 (5d6+mod)
    'dommages_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 10, 11],
            'weak' => [2, 2, 3, 3, 4, 4, 5, 6, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 20],
            'neutral' => [3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 17, 19, 21, 23, 25, 28],
            'strong' => [4, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 18, 20, 22, 25, 27, 30, 33, 36],
            'very_strong' => [5, 5, 6, 8, 9, 10, 12, 13, 15, 16, 18, 21, 23, 25, 28, 31, 34, 37, 40, 45],
        ],
        'norms_description' => 'Dégâts bruts de sort. Courbe exponentielle douce alignée 5.2.3 : ~1d6→5d6+mod.',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort coûteux (5+ PA) → dégâts ligne supérieure'],
            ['characteristic_key' => 'action_points_spell', 'operator' => '<=', 'value' => 2, 'target' => 'power', 'modifier' => -1, 'comment' => 'Sort peu cher (≤2 PA) → dégâts ligne inférieure'],
            ['characteristic_key' => 'area_spell', 'operator' => '>=', 'value' => 2, 'target' => 'power', 'modifier' => -1, 'comment' => 'Sort en zone (≥2) → dégâts réduits (multi-cible)'],
        ],
    ],

    // Soins — légèrement inférieurs aux dégâts (5.2.3.2)
    'soin_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 7, 7, 8],
            'weak' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 11, 12, 14],
            'neutral' => [2, 3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 14, 15, 16, 18, 20],
            'strong' => [3, 4, 4, 5, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 18, 19, 21, 23, 26],
            'very_strong' => [4, 5, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 17, 19, 20, 22, 24, 26, 29, 32],
        ],
        'norms_description' => 'Soins bruts de sort. Légèrement inférieur aux dégâts (5.2.3).',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort coûteux → soins supérieurs'],
        ],
    ],

    // Bouclier — entre soins et dégâts
    'bouclier_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 7, 8, 10],
            'weak' => [2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 12, 13, 17],
            'neutral' => [3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 11, 12, 13, 15, 16, 18, 19, 24],
            'strong' => [4, 4, 5, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 17, 19, 21, 23, 25, 31],
            'very_strong' => [5, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 18, 19, 21, 24, 26, 28, 31, 38],
        ],
        'norms_description' => 'Bouclier (absorption temporaire). Entre soins et dégâts.',
    ],

    // Vol de vie — inférieur aux dégâts purs (double effet)
    'vol_vie_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 7],
            'weak' => [1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 13],
            'neutral' => [2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 18],
            'strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18, 23],
            'very_strong' => [3, 4, 4, 5, 6, 7, 8, 8, 9, 10, 11, 13, 14, 15, 16, 18, 19, 21, 23, 29],
        ],
        'norms_description' => 'Vol de vie. Inférieur aux dégâts purs (double effet : dégâts + soin).',
    ],

    // =====================================================================
    // DESCRIPTEURS DU SORT (Type 1)
    // =====================================================================

    // Coût PA (3-5 PA neutre, règle 5.2.3.1)
    'action_points_spell' => [
        'norms_grid' => [
            'very_weak' => [2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2],
            'weak' => [2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
            'neutral' => [3, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4],
            'strong' => [3, 3, 3, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5],
            'very_strong' => [4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6, 6],
        ],
        'norms_description' => 'Coût PA du sort. Actions simples 3-4 PA, fortes 5+ PA (5.2.3.1).',
    ],

    // Portée max
    'spell_range_max_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 4],
            'weak' => [2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
            'neutral' => [3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 6, 7, 7, 7, 8, 8, 8, 9, 10],
            'strong' => [4, 4, 5, 5, 5, 6, 6, 7, 7, 8, 8, 8, 9, 9, 10, 10, 11, 11, 12, 13],
            'very_strong' => [5, 5, 6, 6, 7, 7, 8, 8, 9, 10, 10, 11, 11, 12, 12, 13, 13, 14, 15, 16],
        ],
        'norms_description' => 'Portée maximale du sort. Progression par paliers.',
    ],

    // Portée min
    'spell_range_min_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3],
        ],
        'norms_description' => 'Portée minimale du sort.',
    ],

    // Zone
    'area_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'strong' => [0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 5, 5],
        ],
        'norms_description' => 'Taille de zone du sort. Progression par paliers.',
    ],

    // Puissance du sort (indice global)
    'power_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 8],
            'weak' => [0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 14],
            'neutral' => [1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 7, 8, 9, 10, 11, 12, 13, 15, 20],
            'strong' => [1, 2, 2, 3, 4, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 19, 26],
            'very_strong' => [2, 2, 3, 4, 5, 5, 6, 8, 9, 10, 11, 12, 14, 15, 16, 18, 20, 22, 24, 30],
        ],
        'norms_description' => 'Indice de puissance du sort. Progression linéaire.',
    ],

    // =====================================================================
    // BUFFS/DEBUFFS STATS via sort (Type 2 stats)
    // =====================================================================
    'agi_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 10],
            'strong' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 7, 8, 9, 13],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 16],
        ],
        'norms_description' => 'Bonus agilité via sort (buff temporaire).',
    ],
    'chance_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 10],
            'strong' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 7, 8, 9, 13],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 16],
        ],
        'norms_description' => 'Bonus chance via sort (buff temporaire).',
    ],
    'intel_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 10],
            'strong' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 7, 8, 9, 13],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 16],
        ],
        'norms_description' => 'Bonus intelligence via sort (buff temporaire).',
    ],
    'strong_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 10],
            'strong' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 7, 8, 9, 13],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 16],
        ],
        'norms_description' => 'Bonus force via sort (buff temporaire).',
    ],
    'vitality_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 10],
            'strong' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 7, 8, 9, 13],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 16],
        ],
        'norms_description' => 'Bonus vitalité via sort (buff temporaire).',
    ],
    'sagesse_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 10],
            'strong' => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 7, 8, 9, 13],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 8, 9, 10, 11, 16],
        ],
        'norms_description' => 'Bonus sagesse via sort (buff temporaire).',
    ],

    // CA via sort
    'armor_class_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'neutral' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3],
            'strong' => [1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4],
            'very_strong' => [1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5],
        ],
        'norms_description' => 'Bonus CA via sort. Faible pour éviter le stacking.',
    ],

    // Résistances fixes via sort
    'fixed_resistance_air_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 10],
            'strong' => [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 8, 8, 9, 13],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 16],
        ],
        'norms_description' => 'Résistance fixe air via sort.',
    ],
    'fixed_resistance_eau_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 10],
            'strong' => [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 8, 8, 9, 13],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 16],
        ],
        'norms_description' => 'Résistance fixe eau via sort.',
    ],
    'fixed_resistance_feu_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 10],
            'strong' => [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 8, 8, 9, 13],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 16],
        ],
        'norms_description' => 'Résistance fixe feu via sort.',
    ],
    'fixed_resistance_neutre_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 10],
            'strong' => [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 8, 8, 9, 13],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 16],
        ],
        'norms_description' => 'Résistance fixe neutre via sort.',
    ],
    'fixed_resistance_terre_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 4],
            'weak' => [0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 7],
            'neutral' => [1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 10],
            'strong' => [1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 6, 7, 8, 8, 9, 13],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 16],
        ],
        'norms_description' => 'Résistance fixe terre via sort.',
    ],

    // Résistances % via sort
    'res_air_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % air via sort.',
    ],
    'res_eau_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % eau via sort.',
    ],
    'res_feu_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % feu via sort.',
    ],
    'res_neutre_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % neutre via sort.',
    ],
    'res_terre_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % terre via sort.',
    ],
    'res_sagesse_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % sagesse via sort.',
    ],
    'res_vitalite_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 8],
            'weak' => [1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 14],
            'neutral' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10, 10, 12, 20],
            'strong' => [3, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 10, 10, 12, 13, 13, 15, 26],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 12, 13, 14, 16, 17, 19, 32],
        ],
        'norms_description' => 'Résistance % vitalité via sort.',
    ],

    // Dommages fixes via sort
    'fixed_damage_air_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes air via sort.',
    ],
    'fixed_damage_earth_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes terre via sort.',
    ],
    'fixed_damage_fire_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes feu via sort.',
    ],
    'fixed_damage_neutral_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes neutre via sort.',
    ],
    'fixed_damage_water_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes eau via sort.',
    ],
    'fixed_damage_sagesse_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes sagesse via sort.',
    ],
    'fixed_damage_vitalite_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes vitalité via sort.',
    ],

    // Critique sort
    'critical_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'very_strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Bonus critique de sort.',
    ],

    // Soin bonus sort
    'heal_bonus_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 3],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 4],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 7],
        ],
        'norms_description' => 'Bonus soin de sort.',
    ],

    // Toucher sort
    'hit_bonus_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Bonus toucher de sort.',
    ],

    // PM sort
    'movement_points_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2],
            'strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3],
            'very_strong' => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 6],
        ],
        'norms_description' => 'Bonus PM via sort.',
    ],

    // Invocations sort
    'summoning_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'neutral' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3],
        ],
        'norms_description' => 'Invocations bonus via sort.',
    ],

    // =====================================================================
    // DESCRIPTEURS DU SORT — contraintes de lancer
    // =====================================================================

    // Lancers par cible — combien de fois le sort peut viser la même cible par tour
    'cast_per_target_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'weak' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'neutral' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2],
            'strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3],
            'very_strong' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3],
        ],
        'norms_description' => 'Nombre de fois où le sort peut cibler la même cible par tour. Majorité des sorts = 1.',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '<=', 'value' => 2, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort peu cher → plus de lancers par cible autorisés'],
        ],
    ],

    // Lancers par tour — nombre total de lancers par tour
    'cast_per_turn_spell' => [
        'norms_grid' => [
            'very_weak' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'weak' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2],
            'neutral' => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3],
            'strong' => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 3, 4, 4],
            'very_strong' => [2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 5, 5],
        ],
        'norms_description' => 'Nombre total de lancers par tour. Les sorts faibles en PA peuvent être lancés plus souvent.',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '<=', 'value' => 2, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort peu cher → plus de lancers par tour'],
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => -1, 'comment' => 'Sort coûteux → moins de lancers par tour'],
        ],
    ],

    // Temps d'incantation — 0 = instantané (action), 1 = action bonus, 2+ = rounds
    'casting_time_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'strong' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
            'very_strong' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
        ],
        'norms_description' => 'Temps d\'incantation en actions/tours. 0 = instantané (1 action). La plupart des sorts sont instantanés.',
    ],

    // Durée — en tours (0 = instantané, 1+ = effet persistant)
    'duration_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'neutral' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3],
            'strong' => [1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3, 4, 4, 5],
            'very_strong' => [1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 5, 5, 5, 5, 5],
        ],
        'norms_description' => 'Durée de l\'effet en tours. 0 = instantané, 1-5 tours. Les buffs durent généralement 1-3 tours.',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort coûteux → durée plus longue justifiée'],
        ],
    ],

    // Délai entre deux lancers — cooldown en tours
    'number_between_two_cast_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1],
            'strong' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2],
            'very_strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3],
        ],
        'norms_description' => 'Cooldown en tours entre deux lancers. Les sorts puissants imposent un délai.',
    ],

    // Temps avant réutilisation — cooldown global en tours
    'time_before_use_again_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Cooldown global en tours. 0 = aucun cooldown. Les sorts très puissants (ultimates) ont un cooldown élevé.',
    ],

    // =====================================================================
    // BUFFS/DEBUFFS SECONDAIRES via sort
    // =====================================================================

    // Esquive PA via sort
    'dodge_action_points_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'very_strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 4],
        ],
        'norms_description' => 'Bonus esquive PA via sort (buff temporaire).',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort coûteux → esquive supérieure'],
        ],
    ],

    // Esquive PM via sort
    'dodge_movement_points_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'very_strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 4],
        ],
        'norms_description' => 'Bonus esquive PM via sort (buff temporaire).',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort coûteux → esquive supérieure'],
        ],
    ],

    // Fuite via sort
    'dodge_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 3],
            'neutral' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 5],
            'strong' => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 7],
            'very_strong' => [0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 7, 8, 10],
        ],
        'norms_description' => 'Bonus fuite via sort (buff temporaire).',
    ],

    // Tacle via sort
    'tackle_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 3],
            'neutral' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 5],
            'strong' => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 7],
            'very_strong' => [0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 7, 8, 10],
        ],
        'norms_description' => 'Bonus tacle via sort (buff temporaire).',
    ],

    // Initiative via sort
    'initiative_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 2],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 5],
            'neutral' => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 8],
            'strong' => [0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 12],
            'very_strong' => [0, 1, 1, 2, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 15],
        ],
        'norms_description' => 'Bonus initiative via sort (buff temporaire).',
    ],

    // Bonus maîtrise via sort
    'mastery_bonus_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 4],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 7],
        ],
        'norms_description' => 'Bonus maîtrise de compétence via sort.',
    ],

    // Résistance critiques via sort
    'critical_damage_reduction_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'very_strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4],
        ],
        'norms_description' => 'Réduction des dommages critiques via sort.',
    ],

    // Dommages fixes multi-élément via sort
    'do_fixe_multiple_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 5],
        ],
        'norms_description' => 'Dommages fixes multi-élément via sort.',
        'norms_conditions' => [
            ['characteristic_key' => 'action_points_spell', 'operator' => '>=', 'value' => 5, 'target' => 'power', 'modifier' => 1, 'comment' => 'Sort coûteux → dommages fixes supérieurs'],
        ],
    ],

    // Résistance poussée via sort
    'push_damage_reduction_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'very_strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4],
        ],
        'norms_description' => 'Réduction des dommages de poussée via sort.',
    ],

    // Portée bonus (buff sort qui augmente la portée d'un allié)
    'range_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong' => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3],
            'very_strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4, 4],
        ],
        'norms_description' => 'Bonus de portée via sort (buff la portée des sorts de la cible).',
    ],

    // Réserve Wakfu via sort
    'wakfu_reserve_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 3],
            'neutral' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 5],
            'strong' => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 8],
            'very_strong' => [0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 10],
        ],
        'norms_description' => 'Points de réserve Wakfu regagnés via sort.',
    ],

    // =====================================================================
    // SAUVEGARDES via sort (buff temporaire)
    // =====================================================================

    'save_agility_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Bonus jet de sauvegarde Agilité via sort.',
    ],
    'save_chance_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Bonus jet de sauvegarde Chance via sort.',
    ],
    'save_intelligence_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Bonus jet de sauvegarde Intelligence via sort.',
    ],
    'save_strength_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Bonus jet de sauvegarde Force via sort.',
    ],
    'save_vitality_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Bonus jet de sauvegarde Vitalité via sort.',
    ],
    'save_wisdom_spell' => [
        'norms_grid' => [
            'very_weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'weak' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral' => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'strong' => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'very_strong' => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 5],
        ],
        'norms_description' => 'Bonus jet de sauvegarde Sagesse via sort.',
    ],

];
