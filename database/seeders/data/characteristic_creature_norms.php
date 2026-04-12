<?php

declare(strict_types=1);

/**
 * Normes (chartes) pour les caractéristiques du groupe créature.
 *
 * Chaque clé = characteristic_key, chaque valeur = [norms_grid, norms_conditions?, norms_description?].
 * norms_grid : 5 puissances (very_weak, weak, neutral, strong, very_strong) × 20 niveaux (indices 0–19 = niveaux 1–20).
 *
 * Les valeurs sont calibrées d'après les règles d'équilibrage (5.2.x) :
 *   - Caractéristiques primaires : base D&D (6–24, modificateurs -2 à +7)
 *   - Points de vie : exponentiel doux, lié à la vitalité
 *   - PA/PM : par paliers, rares
 *   - CA : linéaire 10–18, lié à l'agilité
 *   - Dommages/résistances : linéaire modéré
 *   - Compétences (mastery) : paliers 0/1/2
 */

return [

    // =====================================================================
    // Points de vie — progression exponentielle douce
    // Très faible = cible fragile (mage verre), très fort = tank pur
    // =====================================================================
    'life_points_creature' => [
        'norms_grid' => [
            'very_weak'   => [4, 5, 6, 7, 8, 10, 12, 14, 16, 18, 21, 24, 27, 30, 34, 38, 42, 44, 46, 48],
            'weak'        => [7, 9, 10, 12, 14, 17, 20, 24, 28, 32, 36, 42, 47, 53, 59, 66, 73, 77, 80, 84],
            'neutral'     => [10, 13, 15, 18, 21, 25, 30, 35, 40, 46, 52, 60, 68, 76, 85, 95, 105, 110, 115, 120],
            'strong'      => [13, 17, 20, 23, 27, 33, 39, 46, 52, 60, 68, 78, 88, 99, 111, 124, 137, 143, 150, 156],
            'very_strong' => [16, 21, 24, 28, 34, 40, 48, 56, 64, 74, 83, 96, 109, 122, 136, 152, 168, 176, 184, 192],
        ],
        'norms_description' => 'Points de vie. Progression exponentielle douce. Très faible = cible fragile (mage verre), très fort = tank pur.',
        'norms_conditions' => [
            ['characteristic_key' => 'vitality_creature', 'operator' => '>=', 'value' => 18, 'target' => 'power', 'modifier' => 1, 'comment' => 'Vitalité élevée → +1 ligne puissance PV'],
            ['characteristic_key' => 'vitality_creature', 'operator' => '<=', 'value' => 10, 'target' => 'power', 'modifier' => -1, 'comment' => 'Vitalité basse → -1 ligne puissance PV'],
        ],
    ],

    // =====================================================================
    // Points d'action — par paliers de 5 niveaux
    // Base 6, augmentation lente (6→7→8→9 neutre)
    // =====================================================================
    'action_points_creature' => [
        'norms_grid' => [
            'very_weak'   => [4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5],
            'weak'        => [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6],
            'neutral'     => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9],
            'strong'      => [7, 7, 7, 7, 7, 8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10],
            'very_strong' => [8, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 11, 11, 11, 11, 12],
        ],
        'norms_description' => 'Points d\'action par tour. Progression par paliers de 5 niveaux. Base 6, max ~12.',
    ],

    // =====================================================================
    // Points de mouvement — très stable
    // =====================================================================
    'movement_points_creature' => [
        'norms_grid' => [
            'very_weak'   => [2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2],
            'weak'        => [3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3],
            'neutral'     => [3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4],
            'strong'      => [4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5],
            'very_strong' => [4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6],
        ],
        'norms_description' => 'Points de mouvement par tour. Stable, +1 aux niveaux intermédiaires et hauts.',
    ],

    // =====================================================================
    // Initiative — progression linéaire modérée
    // =====================================================================
    'initiative_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 4],
            'weak'        => [0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 7],
            'neutral'     => [0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 10],
            'strong'      => [0, 1, 1, 2, 2, 3, 3, 4, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 11, 13],
            'very_strong' => [0, 1, 2, 2, 3, 3, 4, 5, 5, 5, 6, 7, 8, 8, 10, 10, 11, 12, 14, 16],
        ],
        'norms_description' => 'Bonus d\'initiative. Progression linéaire modérée.',
    ],

    // =====================================================================
    // Portée naturelle — paliers
    // =====================================================================
    'range_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3],
            'strong'      => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 4, 4],
            'very_strong' => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 5, 5, 6],
        ],
        'norms_description' => 'Portée naturelle. Paliers de 5 niveaux.',
    ],

    // =====================================================================
    // Invocations simultanées — paliers
    // =====================================================================
    'summoning_creature' => [
        'norms_grid' => [
            'very_weak'   => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'weak'        => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'neutral'     => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2],
            'strong'      => [1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3],
            'very_strong' => [1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 4],
        ],
        'norms_description' => 'Nombre d\'invocations simultanées.',
    ],

    // =====================================================================
    // Classe d'armure — linéaire, conditionnée par agilité
    // Ref D&D 5e : 10 base + bonus, cap 22
    // =====================================================================
    'armor_class_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8],
            'weak'        => [8, 8, 8, 8, 9, 9, 9, 9, 10, 10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 13],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 13, 13, 14, 14, 15, 15, 16, 16, 18],
            'strong'      => [12, 12, 12, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 19, 20],
            'very_strong' => [13, 13, 14, 14, 15, 15, 16, 16, 17, 17, 17, 18, 18, 19, 19, 20, 20, 21, 21, 22],
        ],
        'norms_description' => 'Classe d\'armure. Progression linéaire. Base 10 neutre lvl 1.',
        'norms_conditions' => [
            ['characteristic_key' => 'agility_creature', 'operator' => '>=', 'value' => 16, 'target' => 'power', 'modifier' => 1, 'comment' => 'Agilité haute (≥16) → +1 ligne CA (esquive naturelle)'],
        ],
    ],

    // =====================================================================
    // Bonus toucher — linéaire
    // =====================================================================
    'hit_bonus_creature' => [
        'norms_grid' => [
            'very_weak'   => [1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4],
            'weak'        => [1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 6, 7],
            'neutral'     => [2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 6, 7, 7, 8, 8, 9, 10],
            'strong'      => [3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 12],
            'very_strong' => [3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 9, 9, 10, 10, 11, 11, 12, 12],
        ],
        'norms_description' => 'Bonus au toucher (attaque). Progression linéaire.',
    ],

    // =====================================================================
    // Caractéristiques primaires (Force, Agi, Intel, Chance, Vitalité, Sagesse)
    // D&D-like : 6–24, neutre commence à 10, finit à 16
    // =====================================================================
    'agility_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8],
            'weak'        => [7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 11, 11],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 16],
            'strong'      => [12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 20],
            'very_strong' => [14, 14, 14, 15, 15, 16, 16, 16, 17, 17, 18, 18, 19, 19, 20, 20, 21, 22, 23, 24],
        ],
        'norms_description' => 'Agilité. Progression linéaire 10→16 neutre (min 6, max 24).',
    ],
    'chance_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8],
            'weak'        => [7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 11, 11],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 16],
            'strong'      => [12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 20],
            'very_strong' => [14, 14, 14, 15, 15, 16, 16, 16, 17, 17, 18, 18, 19, 19, 20, 20, 21, 22, 23, 24],
        ],
        'norms_description' => 'Chance. Progression linéaire 10→16 neutre (min 6, max 24).',
    ],
    'intelligence_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8],
            'weak'        => [7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 11, 11],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 16],
            'strong'      => [12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 20],
            'very_strong' => [14, 14, 14, 15, 15, 16, 16, 16, 17, 17, 18, 18, 19, 19, 20, 20, 21, 22, 23, 24],
        ],
        'norms_description' => 'Intelligence. Progression linéaire 10→16 neutre (min 6, max 24).',
    ],
    'strength_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8],
            'weak'        => [7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 11, 11],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 16],
            'strong'      => [12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 20],
            'very_strong' => [14, 14, 14, 15, 15, 16, 16, 16, 17, 17, 18, 18, 19, 19, 20, 20, 21, 22, 23, 24],
        ],
        'norms_description' => 'Force. Progression linéaire 10→16 neutre (min 6, max 24).',
    ],
    'vitality_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8],
            'weak'        => [7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 11, 11],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 16],
            'strong'      => [12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 20],
            'very_strong' => [14, 14, 14, 15, 15, 16, 16, 16, 17, 17, 18, 18, 19, 19, 20, 20, 21, 22, 23, 24],
        ],
        'norms_description' => 'Vitalité. Progression linéaire 10→16 neutre (min 6, max 24).',
    ],
    'wisdom_creature' => [
        'norms_grid' => [
            'very_weak'   => [6, 6, 6, 6, 6, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 8, 8, 8, 8, 8],
            'weak'        => [7, 7, 8, 8, 8, 8, 9, 9, 9, 9, 9, 10, 10, 10, 10, 10, 10, 10, 11, 11],
            'neutral'     => [10, 10, 10, 10, 11, 11, 11, 11, 12, 12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 16],
            'strong'      => [12, 12, 12, 13, 13, 13, 14, 14, 14, 15, 15, 15, 16, 16, 17, 17, 18, 18, 19, 20],
            'very_strong' => [14, 14, 14, 15, 15, 16, 16, 16, 17, 17, 18, 18, 19, 19, 20, 20, 21, 22, 23, 24],
        ],
        'norms_description' => 'Sagesse. Progression linéaire 10→16 neutre (min 6, max 24).',
    ],

    // =====================================================================
    // Modificateurs des caractéristiques primaires
    // D&D : mod = floor((score - 10) / 2), donc -2 à +7
    // =====================================================================
    'modifier_agility_creature' => [
        'norms_grid' => [
            'very_weak'   => [-2, -2, -2, -2, -2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
            'weak'        => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3],
            'strong'      => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
        ],
        'norms_description' => 'Modificateur d\'agilité. D&D : floor((score-10)/2). Échelle -2 à +7.',
    ],
    'modifier_chance_creature' => [
        'norms_grid' => [
            'very_weak'   => [-2, -2, -2, -2, -2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
            'weak'        => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3],
            'strong'      => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
        ],
        'norms_description' => 'Modificateur de chance. D&D : floor((score-10)/2). Échelle -2 à +7.',
    ],
    'modifier_intelligence_creature' => [
        'norms_grid' => [
            'very_weak'   => [-2, -2, -2, -2, -2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
            'weak'        => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3],
            'strong'      => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
        ],
        'norms_description' => 'Modificateur d\'intelligence. D&D : floor((score-10)/2). Échelle -2 à +7.',
    ],
    'modifier_strength_creature' => [
        'norms_grid' => [
            'very_weak'   => [-2, -2, -2, -2, -2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
            'weak'        => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3],
            'strong'      => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
        ],
        'norms_description' => 'Modificateur de force. D&D : floor((score-10)/2). Échelle -2 à +7.',
    ],
    'modifier_vitality_creature' => [
        'norms_grid' => [
            'very_weak'   => [-2, -2, -2, -2, -2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
            'weak'        => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3],
            'strong'      => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
        ],
        'norms_description' => 'Modificateur de vitalité. D&D : floor((score-10)/2). Échelle -2 à +7.',
    ],
    'modifier_wisdom_creature' => [
        'norms_grid' => [
            'very_weak'   => [-2, -2, -2, -2, -2, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
            'weak'        => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3],
            'strong'      => [1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5],
            'very_strong' => [2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 7],
        ],
        'norms_description' => 'Modificateur de sagesse. D&D : floor((score-10)/2). Échelle -2 à +7.',
    ],

    // =====================================================================
    // Sauvegardes — linéaire 0→8 neutre
    // =====================================================================
    'save_agility_creature' => [
        'norms_grid' => [
            'very_weak'   => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 8],
            'strong'      => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 10, 11, 13],
        ],
        'norms_description' => 'Sauvegarde agilité. Progression linéaire neutre 0→8.',
    ],
    'save_chance_creature' => [
        'norms_grid' => [
            'very_weak'   => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 8],
            'strong'      => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 10, 11, 13],
        ],
        'norms_description' => 'Sauvegarde chance. Progression linéaire neutre 0→8.',
    ],
    'save_intelligence_creature' => [
        'norms_grid' => [
            'very_weak'   => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 8],
            'strong'      => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 10, 11, 13],
        ],
        'norms_description' => 'Sauvegarde intelligence. Progression linéaire neutre 0→8.',
    ],
    'save_strength_creature' => [
        'norms_grid' => [
            'very_weak'   => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 8],
            'strong'      => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 10, 11, 13],
        ],
        'norms_description' => 'Sauvegarde force. Progression linéaire neutre 0→8.',
    ],
    'save_vitality_creature' => [
        'norms_grid' => [
            'very_weak'   => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 8],
            'strong'      => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 10, 11, 13],
        ],
        'norms_description' => 'Sauvegarde vitalité. Progression linéaire neutre 0→8.',
    ],
    'save_wisdom_creature' => [
        'norms_grid' => [
            'very_weak'   => [-1, -1, -1, -1, -1, -1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'weak'        => [0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 8],
            'strong'      => [1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 10],
            'very_strong' => [2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 9, 9, 10, 11, 13],
        ],
        'norms_description' => 'Sauvegarde sagesse. Progression linéaire neutre 0→8.',
    ],

    // =====================================================================
    // Esquive PA / PM / Fuite / Tacle
    // =====================================================================
    'dodge_action_points_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 4],
            'weak'        => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 10],
            'strong'      => [0, 0, 1, 1, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 13],
            'very_strong' => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 10, 10, 12, 13, 16],
        ],
        'norms_description' => 'Esquive retrait PA. Progression linéaire.',
    ],
    'dodge_movement_points_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 4],
            'weak'        => [0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'neutral'     => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 8, 10],
            'strong'      => [0, 0, 1, 1, 2, 2, 3, 3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 9, 10, 13],
            'very_strong' => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 8, 8, 10, 10, 12, 13, 16],
        ],
        'norms_description' => 'Esquive retrait PM. Progression linéaire.',
    ],
    'dodge_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 4],
            'weak'        => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 7],
            'neutral'     => [0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 10],
            'strong'      => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 9, 10, 11, 13],
            'very_strong' => [0, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 7, 8, 9, 10, 11, 11, 12, 14, 17],
        ],
        'norms_description' => 'Fuite (désengagement). Progression linéaire.',
    ],
    'tackle_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 4],
            'weak'        => [0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 5, 6, 7],
            'neutral'     => [0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 6, 7, 7, 8, 8, 10],
            'strong'      => [0, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 6, 7, 7, 8, 9, 9, 10, 11, 13],
            'very_strong' => [0, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 7, 8, 9, 10, 11, 11, 12, 14, 17],
        ],
        'norms_description' => 'Tacle (empêcher fuite). Progression linéaire.',
    ],

    // =====================================================================
    // Dommages fixes élémentaires — linéaire modéré 0→5
    // =====================================================================
    'fixed_damage_earth_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes terre. Progression linéaire modérée.',
    ],
    'fixed_damage_fire_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes feu. Progression linéaire modérée.',
    ],
    'fixed_damage_air_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes air. Progression linéaire modérée.',
    ],
    'fixed_damage_water_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes eau. Progression linéaire modérée.',
    ],
    'fixed_damage_neutral_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes neutre. Progression linéaire modérée.',
    ],
    'fixed_damage_multiple_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes multiples. Progression linéaire modérée.',
    ],
    'fixed_damage_sagesse_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes sagesse. Progression linéaire modérée.',
    ],
    'fixed_damage_vitalite_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Dommages fixes vitalité. Progression linéaire modérée.',
    ],

    // =====================================================================
    // Résistances fixes élémentaires — linéaire 0→5
    // =====================================================================
    'fixed_resistance_air_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Résistance fixe air. Progression linéaire.',
    ],
    'fixed_resistance_earth_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Résistance fixe terre. Progression linéaire.',
    ],
    'fixed_resistance_fire_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Résistance fixe feu. Progression linéaire.',
    ],
    'fixed_resistance_neutral_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Résistance fixe neutre. Progression linéaire.',
    ],
    'fixed_resistance_water_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 4],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 5],
            'strong'      => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 7],
            'very_strong' => [0, 0, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6, 7, 8],
        ],
        'norms_description' => 'Résistance fixe eau. Progression linéaire.',
    ],

    // =====================================================================
    // Résistances % élémentaires — linéaire 0→30 neutre
    // =====================================================================
    'resistance_air_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % air. Progression linéaire. Max théorique 100% mais normes jusqu\'à ~50%.',
    ],
    'resistance_earth_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % terre. Progression linéaire.',
    ],
    'resistance_fire_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % feu. Progression linéaire.',
    ],
    'resistance_neutral_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % neutre. Progression linéaire.',
    ],
    'resistance_water_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % eau. Progression linéaire.',
    ],
    'resistance_sagesse_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % sagesse. Progression linéaire.',
    ],
    'resistance_vitalite_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 2, 2, 3, 3, 4, 4, 5, 5, 12],
            'weak'        => [0, 0, 0, 0, 1, 1, 2, 2, 3, 3, 4, 5, 5, 6, 7, 8, 9, 10, 11, 21],
            'neutral'     => [0, 0, 0, 2, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13, 14, 16, 18, 30],
            'strong'      => [0, 0, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 39],
            'very_strong' => [0, 2, 3, 4, 5, 5, 7, 8, 9, 10, 12, 13, 15, 17, 19, 21, 24, 27, 30, 48],
        ],
        'norms_description' => 'Résistance % vitalité. Progression linéaire.',
    ],

    // =====================================================================
    // Critique / Soin / Maîtrise
    // =====================================================================
    'critical_hit_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2],
            'strong'      => [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3],
            'very_strong' => [0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3],
        ],
        'norms_description' => 'Bonus de critique. Progression lente par paliers (max 3).',
    ],
    'heal_bonus_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 2],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3],
            'neutral'     => [0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 4],
            'strong'      => [0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5],
            'very_strong' => [0, 0, 0, 1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 5, 5, 6],
        ],
        'norms_description' => 'Bonus de soin. Progression linéaire modérée.',
    ],
    'mastery_bonus_creature' => [
        'norms_grid' => [
            'very_weak'   => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2],
            'weak'        => [1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 3],
            'neutral'     => [2, 2, 2, 2, 2, 3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 5, 5, 5, 6, 6],
            'strong'      => [3, 3, 3, 3, 3, 4, 4, 4, 4, 4, 5, 5, 5, 5, 5, 6, 6, 6, 6, 6],
            'very_strong' => [3, 3, 3, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6],
        ],
        'norms_description' => 'Bonus de compétence (mastery). Paliers +2/+3/+4/+6 neutre.',
    ],
    'wakfu_reserve_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'neutral'     => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 3, 3, 3, 3, 3],
            'strong'      => [0, 0, 0, 0, 1, 1, 1, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5],
            'very_strong' => [0, 0, 0, 1, 1, 2, 2, 2, 3, 3, 3, 4, 4, 4, 5, 5, 6, 6, 7, 9],
        ],
        'norms_description' => 'Réserve de Wakfu (PA bonus hors tour). Progression par paliers.',
    ],

    // =====================================================================
    // Compétences (mastery) — 0=non, 1=maîtrise, 2=expertise
    // =====================================================================
    'acrobatics_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise acrobatie (0=non, 1=maîtrise, 2=expertise).',
    ],
    'animal_handling_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise dressage (0=non, 1=maîtrise, 2=expertise).',
    ],
    'arcana_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise arcane (0=non, 1=maîtrise, 2=expertise).',
    ],
    'athletics_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise athlétisme (0=non, 1=maîtrise, 2=expertise).',
    ],
    'deception_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise supercherie (0=non, 1=maîtrise, 2=expertise).',
    ],
    'history_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise histoire (0=non, 1=maîtrise, 2=expertise).',
    ],
    'insight_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise perspicacité (0=non, 1=maîtrise, 2=expertise).',
    ],
    'intimidation_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise intimidation (0=non, 1=maîtrise, 2=expertise).',
    ],
    'investigation_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise investigation (0=non, 1=maîtrise, 2=expertise).',
    ],
    'medicine_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise médecine (0=non, 1=maîtrise, 2=expertise).',
    ],
    'nature_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise nature (0=non, 1=maîtrise, 2=expertise).',
    ],
    'perception_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise perception (0=non, 1=maîtrise, 2=expertise).',
    ],
    'performance_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise représentation (0=non, 1=maîtrise, 2=expertise).',
    ],
    'persuasion_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise persuasion (0=non, 1=maîtrise, 2=expertise).',
    ],
    'religion_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise religion (0=non, 1=maîtrise, 2=expertise).',
    ],
    'sleight_of_hand_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise escamotage (0=non, 1=maîtrise, 2=expertise).',
    ],
    'stealth_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise discrétion (0=non, 1=maîtrise, 2=expertise).',
    ],
    'survival_mastery_creature' => [
        'norms_grid' => [
            'very_weak'   => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'weak'        => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'neutral'     => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            'strong'      => [0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2],
            'very_strong' => [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2],
        ],
        'norms_description' => 'Maîtrise survie (0=non, 1=maîtrise, 2=expertise).',
    ],

];
