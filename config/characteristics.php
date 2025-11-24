<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des caractéristiques du jeu KrosmozJDR
    |--------------------------------------------------------------------------
    |
    | Définit les caractéristiques, seuils et formules de calcul utilisés
    | par le système de jeu et le service de conversion de données
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Caractéristiques de base
    |--------------------------------------------------------------------------
    |
    | Définition des caractéristiques principales du jeu
    |
    */

    'base_characteristics' => [
        'strength' => [
            'name' => 'Force',
            'description' => 'Capacité physique et musculaire',
            'min_value' => 0,
            'max_value' => 100,
            'default_value' => 10,
            'unit' => 'points',
        ],
        'intelligence' => [
            'name' => 'Intelligence',
            'description' => 'Capacité mentale et magique',
            'min_value' => 0,
            'max_value' => 100,
            'default_value' => 10,
            'unit' => 'points',
        ],
        'agility' => [
            'name' => 'Agilité',
            'description' => 'Souplesse et rapidité',
            'min_value' => 0,
            'max_value' => 100,
            'default_value' => 10,
            'unit' => 'points',
        ],
        'luck' => [
            'name' => 'Chance',
            'description' => 'Facteur chance et hasard',
            'min_value' => 0,
            'max_value' => 100,
            'default_value' => 10,
            'unit' => 'points',
        ],
        'wisdom' => [
            'name' => 'Sagesse',
            'description' => 'Connaissance et expérience',
            'min_value' => 0,
            'max_value' => 100,
            'default_value' => 10,
            'unit' => 'points',
        ],
        'chance' => [
            'name' => 'Chance',
            'description' => 'Facteur chance et hasard (synonyme de luck)',
            'min_value' => 0,
            'max_value' => 100,
            'default_value' => 10,
            'unit' => 'points',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites par type d'entité
    |--------------------------------------------------------------------------
    |
    | Définit les limites des caractéristiques selon le type d'entité
    |
    */

    'limits' => [
        'life' => [
            'class' => [1, 1000],
            'monster' => [1, 10000],
            'npc' => [1, 5000],
            'player' => [1, 2000],
        ],
        'level' => [
            'class' => [1, 200],
            'monster' => [1, 200],
            'npc' => [1, 200],
            'player' => [1, 200],
            'item' => [1, 200],
            'spell' => [1, 200],
        ],
        'attributes' => [
            'strength' => [
                'class' => [0, 100],
                'monster' => [0, 1000],
                'npc' => [0, 500],
                'player' => [0, 100],
            ],
            'intelligence' => [
                'class' => [0, 100],
                'monster' => [0, 1000],
                'npc' => [0, 500],
                'player' => [0, 100],
            ],
            'agility' => [
                'class' => [0, 100],
                'monster' => [0, 1000],
                'npc' => [0, 500],
                'player' => [0, 100],
            ],
            'luck' => [
                'class' => [0, 100],
                'monster' => [0, 1000],
                'npc' => [0, 500],
                'player' => [0, 100],
            ],
            'wisdom' => [
                'class' => [0, 100],
                'monster' => [0, 1000],
                'npc' => [0, 500],
                'player' => [0, 100],
            ],
            'chance' => [
                'class' => [0, 100],
                'monster' => [0, 1000],
                'npc' => [0, 500],
                'player' => [0, 100],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formules de calcul
    |--------------------------------------------------------------------------
    |
    | Définit les formules de calcul pour les caractéristiques dérivées
    |
    */

    'formulas' => [
        'life_calculation' => [
            'base_formula' => 'base_life + (level * life_per_level) + (vitality * vitality_multiplier)',
            'variables' => [
                'base_life' => 100,
                'life_per_level' => 5,
                'vitality_multiplier' => 10,
            ],
            'modifiers' => [
                'class_bonus' => 1.2,
                'monster_bonus' => 1.5,
                'npc_bonus' => 1.1,
            ],
        ],
        'damage_calculation' => [
            'physical_formula' => 'strength * strength_multiplier + weapon_damage',
            'magical_formula' => 'intelligence * intelligence_multiplier + spell_power',
            'variables' => [
                'strength_multiplier' => 1.5,
                'intelligence_multiplier' => 2.0,
            ],
        ],
        'defense_calculation' => [
            'formula' => 'base_defense + (armor_defense * armor_multiplier) + (agility * agility_bonus)',
            'variables' => [
                'base_defense' => 10,
                'armor_multiplier' => 1.0,
                'agility_bonus' => 0.5,
            ],
        ],
        'critical_hit_calculation' => [
            'formula' => 'base_critical + (luck * luck_multiplier) + weapon_critical_bonus',
            'variables' => [
                'base_critical' => 5,
                'luck_multiplier' => 0.3,
            ],
        ],
        'spell_cost_calculation' => [
            'formula' => 'base_cost + (level * level_cost_multiplier) + (intelligence * intelligence_cost_reduction)',
            'variables' => [
                'base_cost' => 3,
                'level_cost_multiplier' => 0.5,
                'intelligence_cost_reduction' => -0.1,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Valeurs par défaut
    |--------------------------------------------------------------------------
    |
    | Définit les valeurs par défaut pour les caractéristiques
    |
    */

    'defaults' => [
        'size' => [
            'valid_values' => ['tiny', 'small', 'medium', 'large', 'huge'],
            'default' => 'medium',
            'descriptions' => [
                'tiny' => 'Très petit',
                'small' => 'Petit',
                'medium' => 'Moyen',
                'large' => 'Grand',
                'huge' => 'Énorme',
            ],
        ],
        'rarity' => [
            'valid_values' => ['common', 'uncommon', 'rare', 'epic', 'legendary'],
            'default' => 'common',
            'descriptions' => [
                'common' => 'Commun',
                'uncommon' => 'Peu commun',
                'rare' => 'Rare',
                'epic' => 'Épique',
                'legendary' => 'Légendaire',
            ],
        ],
        'effect_types' => [
            'valid_values' => ['buff', 'debuff', 'neutral'],
            'default' => 'neutral',
            'descriptions' => [
                'buff' => 'Amélioration',
                'debuff' => 'Affaiblissement',
                'neutral' => 'Neutre',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Seuils et validations
    |--------------------------------------------------------------------------
    |
    | Définit les seuils de validation et d'alerte
    |
    */

    'thresholds' => [
        'price' => [
            'limits' => [0, 1000000],
            'warning_threshold' => 100000,
            'critical_threshold' => 500000,
        ],
        'cost' => [
            'limits' => [0, 100],
            'warning_threshold' => 50,
            'critical_threshold' => 80,
        ],
        'range' => [
            'limits' => [1, 20],
            'warning_threshold' => 15,
            'critical_threshold' => 18,
        ],
        'area' => [
            'limits' => [1, 10],
            'warning_threshold' => 8,
            'critical_threshold' => 9,
        ],
        'critical_hit' => [
            'limits' => [0, 100],
            'warning_threshold' => 80,
            'critical_threshold' => 95,
        ],
        'failure' => [
            'limits' => [0, 100],
            'warning_threshold' => 80,
            'critical_threshold' => 95,
        ],
        'effect_value' => [
            'limits' => [-100, 100],
            'warning_threshold' => [-50, 50],
            'critical_threshold' => [-80, 80],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Champs requis par type d'entité
    |--------------------------------------------------------------------------
    |
    | Définit les champs obligatoires pour chaque type d'entité
    |
    */

    'required_fields' => [
        'class' => ['name', 'description', 'life', 'life_dice'],
        'monster' => ['name', 'level', 'life', 'strength', 'intelligence', 'agility', 'luck', 'wisdom', 'chance'],
        'npc' => ['name', 'level', 'life', 'strength', 'intelligence', 'agility', 'luck', 'wisdom', 'chance'],
        'item' => ['name', 'level', 'description', 'type', 'category'],
        'spell' => ['name', 'description', 'class', 'cost', 'range', 'area'],
        'effect' => ['description', 'type', 'value'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Règles de validation avancées
    |--------------------------------------------------------------------------
    |
    | Définit les règles de validation complexes
    |
    */

    'validation_rules' => [
        'level_consistency' => [
            'rule' => 'Le niveau doit être cohérent avec les caractéristiques',
            'formula' => 'level <= max_level_for_characteristics',
            'error_message' => 'Le niveau est trop élevé pour les caractéristiques actuelles',
        ],
        'life_consistency' => [
            'rule' => 'La vie doit être cohérente avec le niveau et la classe',
            'formula' => 'life >= min_life_for_level AND life <= max_life_for_level',
            'error_message' => 'La vie n\'est pas cohérente avec le niveau',
        ],
        'attribute_balance' => [
            'rule' => 'Les attributs doivent être équilibrés',
            'formula' => 'SUM(attributes) <= max_total_attributes_for_level',
            'error_message' => 'La somme des attributs dépasse la limite pour ce niveau',
        ],
        'spell_requirements' => [
            'rule' => 'Les prérequis du sort doivent être respectés',
            'formula' => 'player_level >= spell_level AND player_class == spell_class',
            'error_message' => 'Les prérequis du sort ne sont pas respectés',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Corrections automatiques
    |--------------------------------------------------------------------------
    |
    | Définit les corrections automatiques à appliquer
    |
    */

    'auto_corrections' => [
        'enabled' => true,
        'rules' => [
            'out_of_range_values' => [
                'action' => 'clamp_to_limits',
                'description' => 'Clamper les valeurs hors limites aux valeurs min/max',
            ],
            'missing_required_fields' => [
                'action' => 'use_default_values',
                'description' => 'Utiliser les valeurs par défaut pour les champs manquants',
            ],
            'invalid_formats' => [
                'action' => 'format_correction',
                'description' => 'Corriger les formats invalides',
            ],
            'inconsistent_data' => [
                'action' => 'recalculate_from_formulas',
                'description' => 'Recalculer les données incohérentes à partir des formules',
            ],
        ],
        'limits' => [
            'max_corrections_per_entity' => 5,
            'max_corrections_per_batch' => 100,
            'correction_timeout' => 30, // secondes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Métriques et monitoring
    |--------------------------------------------------------------------------
    |
    | Définit les métriques à collecter pour le monitoring
    |
    */

    'metrics' => [
        'enabled' => true,
        'collection_interval' => 60, // secondes
        'metrics' => [
            'conversion_success_rate' => [
                'type' => 'percentage',
                'description' => 'Taux de succès des conversions',
                'warning_threshold' => 0.95,
                'critical_threshold' => 0.90,
            ],
            'validation_error_rate' => [
                'type' => 'percentage',
                'description' => 'Taux d\'erreurs de validation',
                'warning_threshold' => 0.05,
                'critical_threshold' => 0.10,
            ],
            'correction_rate' => [
                'type' => 'percentage',
                'description' => 'Taux de corrections automatiques',
                'warning_threshold' => 0.20,
                'critical_threshold' => 0.30,
            ],
            'conversion_duration' => [
                'type' => 'duration',
                'description' => 'Durée moyenne des conversions',
                'warning_threshold' => 5, // secondes
                'critical_threshold' => 10, // secondes
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des langues
    |--------------------------------------------------------------------------
    |
    | Définit la configuration multilingue
    |
    */

    'languages' => [
        'default' => 'fr',
        'supported' => ['fr', 'en', 'de', 'es', 'pt'],
        'fallback' => 'en',
        'translations' => [
            'fr' => [
                'strength' => 'Force',
                'intelligence' => 'Intelligence',
                'agility' => 'Agilité',
                'luck' => 'Chance',
                'wisdom' => 'Sagesse',
                'chance' => 'Chance',
            ],
            'en' => [
                'strength' => 'Strength',
                'intelligence' => 'Intelligence',
                'agility' => 'Agility',
                'luck' => 'Luck',
                'wisdom' => 'Wisdom',
                'chance' => 'Chance',
            ],
        ],
    ],
];
