<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du service DataConversion
    |--------------------------------------------------------------------------
    |
    | Configuration pour la conversion des données selon les caractéristiques KrosmozJDR
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Configuration des caractéristiques
    |--------------------------------------------------------------------------
    |
    | Paramètres pour l'utilisation des caractéristiques du jeu
    |
    */

    'characteristics' => [
        'use_generic_mappings' => env('DATA_CONVERSION_USE_GENERIC_MAPPINGS', true),
        'use_generic_formulas' => env('DATA_CONVERSION_USE_GENERIC_FORMULAS', true),
        'use_generic_validation_rules' => env('DATA_CONVERSION_USE_GENERIC_VALIDATION_RULES', true),
        'config_file' => 'characteristics',
        'cache_characteristics' => env('DATA_CONVERSION_CACHE_CHARACTERISTICS', true),
        'cache_ttl' => env('DATA_CONVERSION_CHARACTERISTICS_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Règles de conversion
    |--------------------------------------------------------------------------
    |
    | Paramètres pour le comportement de conversion
    |
    */

    'conversion_rules' => [
        'strict_mode' => env('DATA_CONVERSION_STRICT_MODE', false),
        'auto_validation' => env('DATA_CONVERSION_AUTO_VALIDATION', true),
        'auto_correction' => env('DATA_CONVERSION_AUTO_CORRECTION', true),
        'default_language' => env('DATA_CONVERSION_DEFAULT_LANGUAGE', 'fr'),
        'fallback_language' => env('DATA_CONVERSION_FALLBACK_LANGUAGE', 'en'),
        'preserve_original_values' => env('DATA_CONVERSION_PRESERVE_ORIGINAL', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des entités
    |--------------------------------------------------------------------------
    |
    | Paramètres spécifiques par type d'entité
    |
    */

    'entities' => [
        'class' => [
            'priority' => 'high',
            'validation_strictness' => 'strict',
            'auto_correction' => true,
            'required_fields' => ['name', 'description', 'life', 'life_dice'],
            'optional_fields' => ['specificity', 'img'],
            'conversion_rules' => [
                'life' => [
                    'min_value' => 1,
                    'max_value' => 1000,
                    'default_value' => 100,
                    'clamp_out_of_range' => true,
                ],
                'life_dice' => [
                    'required' => true,
                    'pattern' => '/^\d+d\d+(\+\d+)?$/',
                    'default_value' => '1d6',
                ],
            ],
        ],
        'monster' => [
            'priority' => 'normal',
            'validation_strictness' => 'normal',
            'auto_correction' => true,
            'required_fields' => ['name', 'level', 'life', 'strength', 'intelligence', 'agility', 'luck', 'wisdom', 'chance'],
            'optional_fields' => ['size', 'monster_race_id', 'img'],
            'conversion_rules' => [
                'level' => [
                    'min_value' => 1,
                    'max_value' => 200,
                    'default_value' => 1,
                    'clamp_out_of_range' => true,
                ],
                'life' => [
                    'min_value' => 1,
                    'max_value' => 10000,
                    'default_value' => 100,
                    'clamp_out_of_range' => true,
                ],
                'attributes' => [
                    'min_value' => 0,
                    'max_value' => 1000,
                    'default_value' => 10,
                    'clamp_out_of_range' => true,
                ],
                'size' => [
                    'valid_values' => ['tiny', 'small', 'medium', 'large', 'huge'],
                    'default_value' => 'medium',
                    'case_sensitive' => false,
                ],
            ],
        ],
        'item' => [
            'priority' => 'normal',
            'validation_strictness' => 'normal',
            'auto_correction' => true,
            'required_fields' => ['name', 'level', 'description', 'type', 'category'],
            'optional_fields' => ['rarity', 'price', 'img'],
            'conversion_rules' => [
                'level' => [
                    'min_value' => 1,
                    'max_value' => 200,
                    'default_value' => 1,
                    'clamp_out_of_range' => true,
                ],
                'price' => [
                    'min_value' => 0,
                    'max_value' => 1000000,
                    'default_value' => 0,
                    'clamp_out_of_range' => true,
                ],
                'rarity' => [
                    'valid_values' => ['common', 'uncommon', 'rare', 'epic', 'legendary'],
                    'default_value' => 'common',
                    'case_sensitive' => false,
                ],
                'type' => [
                    'mapping' => [
                        'weapon' => 'weapon',
                        'armor' => 'armor',
                        'shield' => 'shield',
                        'ring' => 'accessory',
                        'amulet' => 'accessory',
                        'belt' => 'accessory',
                        'boots' => 'accessory',
                        'hat' => 'accessory',
                        'potion' => 'consumable',
                        'flower' => 'resource',
                        'resource' => 'resource',
                        'equipment' => 'equipment',
                    ],
                    'default_value' => 'equipment',
                ],
            ],
        ],
        'spell' => [
            'priority' => 'normal',
            'validation_strictness' => 'normal',
            'auto_correction' => true,
            'required_fields' => ['name', 'description', 'class', 'cost', 'range', 'area'],
            'optional_fields' => ['critical_hit', 'failure', 'img', 'levels'],
            'conversion_rules' => [
                'cost' => [
                    'min_value' => 0,
                    'max_value' => 100,
                    'default_value' => 3,
                    'clamp_out_of_range' => true,
                ],
                'range' => [
                    'min_value' => 1,
                    'max_value' => 20,
                    'default_value' => 1,
                    'clamp_out_of_range' => true,
                ],
                'area' => [
                    'min_value' => 1,
                    'max_value' => 10,
                    'default_value' => 1,
                    'clamp_out_of_range' => true,
                ],
                'critical_hit' => [
                    'min_value' => 0,
                    'max_value' => 100,
                    'default_value' => 5,
                    'clamp_out_of_range' => true,
                ],
                'failure' => [
                    'min_value' => 0,
                    'max_value' => 100,
                    'default_value' => 0,
                    'clamp_out_of_range' => true,
                ],
            ],
        ],
        'effect' => [
            'priority' => 'low',
            'validation_strictness' => 'normal',
            'auto_correction' => true,
            'required_fields' => ['description', 'type', 'value'],
            'optional_fields' => ['condition', 'duration'],
            'conversion_rules' => [
                'value' => [
                    'min_value' => -100,
                    'max_value' => 100,
                    'default_value' => 0,
                    'clamp_out_of_range' => true,
                ],
                'type' => [
                    'valid_values' => ['buff', 'debuff', 'neutral'],
                    'default_value' => 'neutral',
                    'case_sensitive' => false,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formules de conversion
    |--------------------------------------------------------------------------
    |
    | Formules personnalisées pour la conversion des valeurs
    |
    */

    'custom_formulas' => [
        'enabled' => env('DATA_CONVERSION_CUSTOM_FORMULAS', true),
        'formulas' => [
            'monster_life_by_level' => [
                'formula' => 'base_life + (level * life_per_level_multiplier)',
                'variables' => [
                    'base_life' => 50,
                    'life_per_level_multiplier' => 25,
                ],
                'description' => 'Calcul de la vie d\'un monstre selon son niveau',
            ],
            'item_price_by_level' => [
                'formula' => 'base_price + (level * price_per_level_multiplier)',
                'variables' => [
                    'base_price' => 10,
                    'price_per_level_multiplier' => 5,
                ],
                'description' => 'Calcul du prix d\'un objet selon son niveau',
            ],
            'spell_cost_by_level' => [
                'formula' => 'base_cost + (level * cost_per_level_multiplier)',
                'variables' => [
                    'base_cost' => 2,
                    'cost_per_level_multiplier' => 0.5,
                ],
                'description' => 'Calcul du coût d\'un sort selon son niveau',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation et correction
    |--------------------------------------------------------------------------
    |
    | Paramètres de validation et correction automatique
    |
    */

    'validation' => [
        'enabled' => env('DATA_CONVERSION_VALIDATION_ENABLED', true),
        'strict_mode' => env('DATA_CONVERSION_STRICT_MODE', false),
        'skip_invalid_entities' => env('DATA_CONVERSION_SKIP_INVALID', false),
        'log_validation_errors' => env('DATA_CONVERSION_LOG_VALIDATION', true),
        'max_validation_errors' => env('DATA_CONVERSION_MAX_VALIDATION_ERRORS', 100),
    ],

    'correction' => [
        'enabled' => env('DATA_CONVERSION_CORRECTION_ENABLED', true),
        'auto_correct_values' => env('DATA_CONVERSION_AUTO_CORRECT_VALUES', true),
        'auto_correct_formats' => env('DATA_CONVERSION_AUTO_CORRECT_FORMATS', true),
        'use_default_values' => env('DATA_CONVERSION_USE_DEFAULTS', true),
        'log_corrections' => env('DATA_CONVERSION_LOG_CORRECTIONS', true),
        'max_corrections_per_entity' => env('DATA_CONVERSION_MAX_CORRECTIONS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestion des erreurs
    |--------------------------------------------------------------------------
    |
    | Paramètres de gestion des erreurs de conversion
    |
    */

    'error_handling' => [
        'log_errors' => env('DATA_CONVERSION_LOG_ERRORS', true),
        'continue_on_error' => env('DATA_CONVERSION_CONTINUE_ON_ERROR', true),
        'max_consecutive_errors' => env('DATA_CONVERSION_MAX_CONSECUTIVE_ERRORS', 10),
        'error_cooldown' => env('DATA_CONVERSION_ERROR_COOLDOWN', 300), // 5 minutes
        'retry_failed_conversions' => env('DATA_CONVERSION_RETRY_FAILED', true),
        'max_retry_attempts' => env('DATA_CONVERSION_MAX_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance et optimisation
    |--------------------------------------------------------------------------
    |
    | Paramètres d'optimisation des performances
    |
    */

    'performance' => [
        'batch_processing' => env('DATA_CONVERSION_BATCH_PROCESSING', true),
        'batch_size' => env('DATA_CONVERSION_BATCH_SIZE', 100),
        'parallel_processing' => env('DATA_CONVERSION_PARALLEL', false),
        'max_parallel_jobs' => env('DATA_CONVERSION_MAX_PARALLEL', 4),
        'memory_limit' => env('DATA_CONVERSION_MEMORY_LIMIT', 512), // MB
        'timeout' => env('DATA_CONVERSION_TIMEOUT', 300), // secondes
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging et monitoring
    |--------------------------------------------------------------------------
    |
    | Paramètres de logging et monitoring
    |
    */

    'logging' => [
        'enabled' => env('DATA_CONVERSION_LOGGING_ENABLED', true),
        'level' => env('DATA_CONVERSION_LOG_LEVEL', 'info'),
        'channel' => env('DATA_CONVERSION_LOG_CHANNEL', 'daily'),
        'log_conversions' => env('DATA_CONVERSION_LOG_CONVERSIONS', true),
        'log_validations' => env('DATA_CONVERSION_LOG_VALIDATIONS', true),
        'log_corrections' => env('DATA_CONVERSION_LOG_CORRECTIONS', true),
        'log_performance' => env('DATA_CONVERSION_LOG_PERFORMANCE', true),
    ],

    'monitoring' => [
        'enabled' => env('DATA_CONVERSION_MONITORING_ENABLED', true),
        'collect_metrics' => env('DATA_CONVERSION_COLLECT_METRICS', true),
        'metrics_interval' => env('DATA_CONVERSION_METRICS_INTERVAL', 60), // secondes
        'alert_thresholds' => [
            'conversion_error_rate' => env('DATA_CONVERSION_ALERT_ERROR_RATE', 0.1), // 10%
            'validation_error_rate' => env('DATA_CONVERSION_ALERT_VALIDATION_RATE', 0.05), // 5%
            'correction_rate' => env('DATA_CONVERSION_ALERT_CORRECTION_RATE', 0.3), // 30%
            'processing_time' => env('DATA_CONVERSION_ALERT_PROCESSING_TIME', 60), // secondes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des tests
    |--------------------------------------------------------------------------
    |
    | Paramètres pour les tests et le développement
    |
    */

    'testing' => [
        'enabled' => env('DATA_CONVERSION_TESTING_ENABLED', false),
        'mock_characteristics' => env('DATA_CONVERSION_MOCK_CHARACTERISTICS', false),
        'test_data_limit' => env('DATA_CONVERSION_TEST_DATA_LIMIT', 10),
        'validate_test_results' => env('DATA_CONVERSION_VALIDATE_TEST_RESULTS', true),
        'log_test_operations' => env('DATA_CONVERSION_LOG_TEST_OPERATIONS', false),
    ],
];
