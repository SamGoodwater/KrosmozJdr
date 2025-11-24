<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du service DataIntegration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration des données dans la structure KrosmozJDR
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Mapping DofusDB vers KrosmozJDR
    |--------------------------------------------------------------------------
    |
    | Définit comment les entités DofusDB sont mappées vers KrosmozJDR
    |
    */

    'dofusdb_mapping' => [
        'krosmoz_to_dofusdb' => [
            'classes' => 'breeds',
            'monsters' => 'monsters',
            'items' => 'items',
            'spells' => 'spells',
            'effects' => 'effects',
            'panoplies' => 'item-sets',
        ],
        'dofusdb_to_krosmoz' => [
            'breeds' => 'classes',
            'monsters' => 'monsters',
            'items' => 'items',
            'spells' => 'spells',
            'effects' => 'effects',
            'item-sets' => 'panoplies',
        ],
        'field_mapping' => [
            'classes' => [
                'id' => 'id',
                'name' => 'name.fr',
                'description' => 'description.fr',
                'spells' => 'spells',
                'img' => 'img',
            ],
            'monsters' => [
                'id' => 'id',
                'name' => 'name.fr',
                'level' => 'grades.0.level',
                'life' => 'grades.0.lifePoints',
                'strength' => 'grades.0.strength',
                'intelligence' => 'grades.0.intelligence',
                'agility' => 'grades.0.agility',
                'luck' => 'grades.0.chance',
                'wisdom' => 'grades.0.wisdom',
                'chance' => 'grades.0.chance',
                'size' => 'size',
                'race' => 'race',
                'spells' => 'spells',
                'drops' => 'drops',
                'img' => 'img',
            ],
            'items' => [
                'id' => 'id',
                'name' => 'name.fr',
                'description' => 'description.fr',
                'type' => 'typeId',
                'level' => 'level',
                'effects' => 'effects',
                'img' => 'img',
            ],
            'spells' => [
                'id' => 'id',
                'name' => 'name.fr',
                'description' => 'description.fr',
                'class' => 'breedId',
                'levels' => 'spell-levels',
                'img' => 'img',
            ],
            'effects' => [
                'id' => 'id',
                'characteristic' => 'characteristic',
                'description' => 'description.fr',
            ],
        ],
        'items_type_mapping' => [
            'weapon' => [
                'dofusdb_type_id' => 1,
                'target_table' => 'items',
                'category' => 'weapon',
                'additional_fields' => ['damage', 'critical_chance', 'range'],
            ],
            'armor' => [
                'dofusdb_type_id' => 2,
                'target_table' => 'items',
                'category' => 'armor',
                'additional_fields' => ['defense', 'resistance', 'slot'],
            ],
            'shield' => [
                'dofusdb_type_id' => 3,
                'target_table' => 'items',
                'category' => 'shield',
                'additional_fields' => ['defense', 'resistance'],
            ],
            'ring' => [
                'dofusdb_type_id' => 9,
                'target_table' => 'items',
                'category' => 'accessory',
                'additional_fields' => ['slot', 'bonus'],
            ],
            'amulet' => [
                'dofusdb_type_id' => 10,
                'target_table' => 'items',
                'category' => 'accessory',
                'additional_fields' => ['slot', 'bonus'],
            ],
            'belt' => [
                'dofusdb_type_id' => 11,
                'target_table' => 'items',
                'category' => 'accessory',
                'additional_fields' => ['slot', 'bonus'],
            ],
            'potion' => [
                'dofusdb_type_id' => 12,
                'target_table' => 'consumables',
                'category' => 'potion',
                'additional_fields' => ['uses', 'effect_duration'],
            ],
            'boots' => [
                'dofusdb_type_id' => 13,
                'target_table' => 'items',
                'category' => 'accessory',
                'additional_fields' => ['slot', 'bonus'],
            ],
            'hat' => [
                'dofusdb_type_id' => 14,
                'target_table' => 'items',
                'category' => 'accessory',
                'additional_fields' => ['slot', 'bonus'],
            ],
            'resource' => [
                'dofusdb_type_id' => 15,
                'target_table' => 'resources',
                'category' => 'resource',
                'additional_fields' => ['harvest_level', 'harvest_tool'],
            ],
            'equipment' => [
                'dofusdb_type_id' => 16,
                'target_table' => 'items',
                'category' => 'equipment',
                'additional_fields' => ['slot', 'bonus', 'requirements'],
            ],
            'flower' => [
                'dofusdb_type_id' => 35,
                'target_table' => 'resources',
                'category' => 'flower',
                'additional_fields' => ['harvest_level', 'season'],
            ],
        ],
        'exclude_item_types' => [
            'cosmetic', 'pet', 'mount', 'emote', 'companion', 'trophy'
        ],
        'include_item_types' => [
            'consumable', 'resource', 'equipment', 'weapon', 'armor', 'accessory'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Entités KrosmozJDR
    |--------------------------------------------------------------------------
    |
    | Configuration des entités cibles dans KrosmozJDR
    |
    */

    'krosmoz_entities' => [
        'classes' => [
            'table' => 'classes',
            'model' => \App\Models\Entity\Classe::class,
            'fillable' => ['name', 'description', 'life', 'life_dice', 'specificity'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'creatures' => [
            'table' => 'creatures',
            'model' => \App\Models\Entity\Creature::class,
            'fillable' => ['name', 'level', 'life', 'strength', 'intelligence', 'agility', 'luck', 'wisdom', 'chance'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'monsters' => [
            'table' => 'monsters',
            'model' => \App\Models\Entity\Monster::class,
            'fillable' => ['creature_id', 'size', 'monster_race_id'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'items' => [
            'table' => 'items',
            'model' => \App\Models\Entity\Item::class,
            'fillable' => ['name', 'level', 'description', 'type', 'category', 'rarity', 'price'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'consumables' => [
            'table' => 'consumables',
            'model' => \App\Models\Entity\Consumable::class,
            'fillable' => ['name', 'level', 'description', 'type', 'category', 'rarity', 'price', 'uses', 'effect_duration'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'resources' => [
            'table' => 'resources',
            'model' => \App\Models\Entity\Resource::class,
            'fillable' => ['name', 'level', 'description', 'type', 'category', 'rarity', 'price', 'harvest_level', 'harvest_tool'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'spells' => [
            'table' => 'spells',
            'model' => \App\Models\Entity\Spell::class,
            'fillable' => ['name', 'description', 'class', 'cost', 'range', 'area', 'critical_hit', 'failure'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'effects' => [
            'table' => 'effects',
            'model' => \App\Models\Entity\Effect::class,
            'fillable' => ['description', 'type', 'value', 'condition'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
        'panoplies' => [
            'table' => 'panoplies',
            'model' => \App\Models\Entity\Panoply::class,
            'fillable' => ['name', 'description', 'bonus', 'requirements'],
            'timestamps' => true,
            'soft_deletes' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stratégies d'intégration
    |--------------------------------------------------------------------------
    |
    | Définit comment gérer les conflits et doublons
    |
    */

    'integration_strategies' => [
        'classe' => [
            'conflict_strategy' => 'update',
            'duplicate_handling' => 'update',
            'validation_rules' => ['name', 'description', 'life', 'life_dice'],
            'unique_fields' => ['name'],
            'update_fields' => ['description', 'life', 'life_dice', 'specificity'],
        ],
        'monster' => [
            'conflict_strategy' => 'update',
            'duplicate_handling' => 'update',
            'validation_rules' => ['name', 'level', 'life'],
            'unique_fields' => ['name'],
            'update_fields' => ['level', 'life', 'strength', 'intelligence', 'agility', 'luck', 'wisdom', 'chance', 'size'],
        ],
        'item' => [
            'conflict_strategy' => 'update',
            'duplicate_handling' => 'update',
            'validation_rules' => ['name', 'level', 'type'],
            'unique_fields' => ['name'],
            'update_fields' => ['level', 'description', 'type', 'category', 'rarity', 'price'],
        ],
        'spell' => [
            'conflict_strategy' => 'update',
            'duplicate_handling' => 'update',
            'validation_rules' => ['name', 'class'],
            'unique_fields' => ['name'],
            'update_fields' => ['description', 'class', 'cost', 'range', 'area', 'critical_hit', 'failure'],
        ],
        'effect' => [
            'conflict_strategy' => 'update',
            'duplicate_handling' => 'update',
            'validation_rules' => ['description', 'type'],
            'unique_fields' => ['description'],
            'update_fields' => ['type', 'value', 'condition'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestion des relations
    |--------------------------------------------------------------------------
    |
    | Configuration des relations entre entités
    |
    */

    'relations' => [
        'enabled' => env('SCRAPPING_INTEGRATION_CREATE_RELATIONS', true),
        'max_depth' => env('SCRAPPING_RELATIONS_MAX_DEPTH', 2),
        'relation_types' => [
            'many_to_many' => [
                'class_spells' => [
                    'pivot_table' => 'class_spell',
                    'foreign_key' => 'class_id',
                    'related_key' => 'spell_id',
                ],
                'monster_drops' => [
                    'pivot_table' => 'monster_drop',
                    'foreign_key' => 'monster_id',
                    'related_key' => 'item_id',
                ],
                'item_effects' => [
                    'pivot_table' => 'item_effect',
                    'foreign_key' => 'item_id',
                    'related_key' => 'effect_id',
                ],
                'spell_effects' => [
                    'pivot_table' => 'spell_effect',
                    'foreign_key' => 'spell_id',
                    'related_key' => 'effect_id',
                ],
            ],
            'one_to_many' => [
                'creature_monster' => [
                    'foreign_key' => 'creature_id',
                    'local_key' => 'id',
                ],
                'class_spells' => [
                    'foreign_key' => 'breedId',
                    'local_key' => 'id',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des transactions
    |--------------------------------------------------------------------------
    |
    | Paramètres de gestion des transactions de base de données
    |
    */

    'transactions' => [
        'enabled' => env('SCRAPPING_INTEGRATION_TRANSACTIONS', true),
        'auto_commit' => env('SCRAPPING_INTEGRATION_AUTO_COMMIT', false),
        'rollback_on_error' => env('SCRAPPING_INTEGRATION_ROLLBACK_ON_ERROR', true),
        'isolation_level' => env('SCRAPPING_INTEGRATION_ISOLATION_LEVEL', 'READ_COMMITTED'),
        'timeout' => env('SCRAPPING_INTEGRATION_TRANSACTION_TIMEOUT', 300), // secondes
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestion des lots
    |--------------------------------------------------------------------------
    |
    | Configuration du traitement par lots
    |
    */

    'batch_processing' => [
        'enabled' => env('SCRAPPING_INTEGRATION_BATCH_PROCESSING', true),
        'batch_size' => env('SCRAPPING_INTEGRATION_BATCH_SIZE', 100),
        'max_batch_size' => env('SCRAPPING_INTEGRATION_MAX_BATCH_SIZE', 1000),
        'batch_timeout' => env('SCRAPPING_INTEGRATION_BATCH_TIMEOUT', 1800), // 30 minutes
        'progress_reporting' => env('SCRAPPING_INTEGRATION_PROGRESS_REPORTING', true),
        'progress_interval' => env('SCRAPPING_INTEGRATION_PROGRESS_INTERVAL', 10), // secondes
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation des données
    |--------------------------------------------------------------------------
    |
    | Configuration de la validation avant sauvegarde
    |
    */

    'validation' => [
        'enabled' => env('SCRAPPING_INTEGRATION_VALIDATION_BEFORE_SAVE', true),
        'strict_mode' => env('SCRAPPING_INTEGRATION_STRICT_VALIDATION', false),
        'skip_invalid_entities' => env('SCRAPPING_INTEGRATION_SKIP_INVALID', false),
        'log_validation_errors' => env('SCRAPPING_INTEGRATION_LOG_VALIDATION', true),
        'max_validation_errors' => env('SCRAPPING_INTEGRATION_MAX_VALIDATION_ERRORS', 100),
        'custom_rules' => [
            'name_length' => ['min' => 1, 'max' => 255],
            'description_length' => ['min' => 0, 'max' => 1000],
            'level_range' => ['min' => 1, 'max' => 200],
            'life_range' => ['min' => 1, 'max' => 10000],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestion des erreurs
    |--------------------------------------------------------------------------
    |
    | Configuration de la gestion des erreurs d'intégration
    |
    */

    'error_handling' => [
        'log_errors' => env('SCRAPPING_INTEGRATION_LOG_ERRORS', true),
        'continue_on_error' => env('SCRAPPING_INTEGRATION_CONTINUE_ON_ERROR', true),
        'max_consecutive_errors' => env('SCRAPPING_INTEGRATION_MAX_CONSECUTIVE_ERRORS', 10),
        'error_cooldown' => env('SCRAPPING_INTEGRATION_ERROR_COOLDOWN', 300), // 5 minutes
        'retry_failed_integrations' => env('SCRAPPING_INTEGRATION_RETRY_FAILED', true),
        'max_retry_attempts' => env('SCRAPPING_INTEGRATION_MAX_RETRY_ATTEMPTS', 3),
        'error_categories' => [
            'validation' => 'Erreurs de validation',
            'database' => 'Erreurs de base de données',
            'relation' => 'Erreurs de relations',
            'mapping' => 'Erreurs de mapping',
            'unknown' => 'Erreurs inconnues',
        ],
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
        'bulk_insert' => env('SCRAPPING_INTEGRATION_BULK_INSERT', true),
        'bulk_update' => env('SCRAPPING_INTEGRATION_BULK_UPDATE', true),
        'chunk_size' => env('SCRAPPING_INTEGRATION_CHUNK_SIZE', 1000),
        'disable_foreign_key_checks' => env('SCRAPPING_INTEGRATION_DISABLE_FK_CHECKS', false),
        'disable_unique_checks' => env('SCRAPPING_INTEGRATION_DISABLE_UNIQUE_CHECKS', false),
        'optimize_tables_after_import' => env('SCRAPPING_INTEGRATION_OPTIMIZE_TABLES', false),
        'analyze_tables_after_import' => env('SCRAPPING_INTEGRATION_ANALYZE_TABLES', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nettoyage et maintenance
    |--------------------------------------------------------------------------
    |
    | Configuration du nettoyage après intégration
    |
    */

    'cleanup' => [
        'enabled' => env('SCRAPPING_INTEGRATION_CLEANUP_AFTER_INTEGRATION', true),
        'cleanup_temporary_data' => env('SCRAPPING_INTEGRATION_CLEANUP_TEMP_DATA', true),
        'cleanup_old_logs' => env('SCRAPPING_INTEGRATION_CLEANUP_LOGS', true),
        'cleanup_old_metrics' => env('SCRAPPING_INTEGRATION_CLEANUP_METRICS', true),
        'cleanup_old_cache' => env('SCRAPPING_INTEGRATION_CLEANUP_CACHE', true),
        'cleanup_retention_days' => [
            'logs' => env('SCRAPPING_INTEGRATION_LOGS_RETENTION_DAYS', 30),
            'metrics' => env('SCRAPPING_INTEGRATION_METRICS_RETENTION_DAYS', 90),
            'cache' => env('SCRAPPING_INTEGRATION_CACHE_RETENTION_DAYS', 7),
        ],
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
        'enabled' => env('SCRAPPING_INTEGRATION_LOGGING_ENABLED', true),
        'level' => env('SCRAPPING_INTEGRATION_LOG_LEVEL', 'info'),
        'channel' => env('SCRAPPING_INTEGRATION_LOG_CHANNEL', 'daily'),
        'log_integrations' => env('SCRAPPING_INTEGRATION_LOG_INTEGRATIONS', true),
        'log_validations' => env('SCRAPPING_INTEGRATION_LOG_VALIDATIONS', true),
        'log_relations' => env('SCRAPPING_INTEGRATION_LOG_RELATIONS', true),
        'log_performance' => env('SCRAPPING_INTEGRATION_LOG_PERFORMANCE', true),
    ],

    'monitoring' => [
        'enabled' => env('SCRAPPING_INTEGRATION_MONITORING_ENABLED', true),
        'collect_metrics' => env('SCRAPPING_INTEGRATION_COLLECT_METRICS', true),
        'metrics_interval' => env('SCRAPPING_INTEGRATION_METRICS_INTERVAL', 60), // secondes
        'alert_thresholds' => [
            'integration_error_rate' => env('SCRAPPING_INTEGRATION_ALERT_ERROR_RATE', 0.1), // 10%
            'validation_error_rate' => env('SCRAPPING_INTEGRATION_ALERT_VALIDATION_RATE', 0.05), // 5%
            'relation_error_rate' => env('SCRAPPING_INTEGRATION_ALERT_RELATION_RATE', 0.1), // 10%
            'processing_time' => env('SCRAPPING_INTEGRATION_ALERT_PROCESSING_TIME', 300), // 5 minutes
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
        'enabled' => env('SCRAPPING_INTEGRATION_TESTING_ENABLED', false),
        'use_test_database' => env('SCRAPPING_INTEGRATION_USE_TEST_DB', true),
        'test_data_limit' => env('SCRAPPING_INTEGRATION_TEST_DATA_LIMIT', 10),
        'validate_test_results' => env('SCRAPPING_INTEGRATION_VALIDATE_TEST_RESULTS', true),
        'log_test_operations' => env('SCRAPPING_INTEGRATION_LOG_TEST_OPERATIONS', false),
        'cleanup_test_data' => env('SCRAPPING_INTEGRATION_CLEANUP_TEST_DATA', true),
    ],
];
