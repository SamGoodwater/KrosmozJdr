<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du service DataCollect
    |--------------------------------------------------------------------------
    |
    | Ce fichier contient la configuration pour la collecte de données
    | depuis l'API DofusDB et autres sources externes.
    |
    */

    // Configuration de l'API DofusDB
    'dofusdb' => [
        'base_url' => env('DOFUSDB_BASE_URL', 'https://api.dofusdb.fr'),
        'default_language' => env('DOFUSDB_DEFAULT_LANGUAGE', 'fr'),
        'supported_languages' => ['fr', 'en', 'de', 'es', 'pt'],
        
        // Limites de l'API
        'rate_limit' => [
            'requests_per_second' => env('DOFUSDB_RATE_LIMIT', 1),
            'max_concurrent_requests' => env('DOFUSDB_MAX_CONCURRENT', 3),
            'retry_after_seconds' => env('DOFUSDB_RETRY_AFTER', 60),
        ],
        
        // Timeouts et retry
        'timeout' => [
            'request' => env('DOFUSDB_REQUEST_TIMEOUT', 15),
            'connection' => env('DOFUSDB_CONNECTION_TIMEOUT', 10),
            'read' => env('DOFUSDB_READ_TIMEOUT', 30),
        ],
        
        'retry' => [
            'max_attempts' => env('DOFUSDB_MAX_RETRIES', 3),
            'backoff_multiplier' => env('DOFUSDB_BACKOFF_MULTIPLIER', 2),
            'initial_delay' => env('DOFUSDB_INITIAL_DELAY', 1),
        ],
    ],

    // Configuration du cache
    'cache' => [
        'enabled' => env('DOFUSDB_CACHE_ENABLED', true),
        'driver' => env('DOFUSDB_CACHE_DRIVER', 'redis'),
        'ttl' => env('DOFUSDB_CACHE_TTL', 3600), // 1 heure
        'tags' => ['dofusdb', 'data-collect'],
        'prefix' => 'dofusdb:',
    ],

    // Configuration des entités à collecter
    'entities' => [
        'breeds' => [
            'endpoint' => '/breeds',
            'enabled' => true,
            'batch_size' => 19, // Total connu
            'fields' => [
                'id', 'guideItemId', 'maleLook', 'femaleLook', 'creatureBonesId',
                'maleArtwork', 'femaleArtwork', 'statsPointsForStrength',
                'statsPointsForIntelligence', 'statsPointsForChance', 'description'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
        ],

        'monsters' => [
            'endpoint' => '/monsters',
            'enabled' => true,
            'batch_size' => 100, // Pagination recommandée
            'fields' => [
                'id', 'name', 'level', 'lifePoints', 'actionPoints', 'movementPoints',
                'experience', 'kamas', 'img'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
        ],

        'items' => [
            'endpoint' => '/items',
            'enabled' => true,
            'batch_size' => 50, // Pagination recommandée
            'fields' => [
                'id', 'typeId', 'iconId', 'level', 'realWeight', 'price',
                'name', 'description', 'type', 'effects', 'img', 'itemSetId',
                'possibleEffects', 'dropMonsterIds', 'hasRecipe', 'isLegendary'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
            
            // Mapping des types d'objets basé sur l'analyse
            'type_mapping' => [
                // Armes (SuperType 2)
                'weapons' => [
                    'type_ids' => [1, 2, 3, 4, 5, 6, 7, 8, 19, 20],
                    'super_type_id' => 2,
                    'enabled' => true,
                ],
                
                // Accessoires (SuperTypes 3, 4, 5)
                'accessories' => [
                    'type_ids' => [9, 10, 11],
                    'super_type_ids' => [3, 4, 5],
                    'enabled' => true,
                ],
                
                // Consommables (SuperType 6)
                'consumables' => [
                    'type_ids' => [12, 13, 14],
                    'super_type_id' => 6,
                    'enabled' => true,
                ],
                
                // Ressources (SuperType 9)
                'resources' => [
                    'type_ids' => [15, 35],
                    'super_type_id' => 9,
                    'enabled' => true,
                ],
                
                // Équipements (SuperTypes 10, 11, 12)
                'equipment' => [
                    'type_ids' => [16, 17, 18],
                    'super_type_ids' => [10, 11, 12],
                    'enabled' => true,
                ],
                
                // Spéciaux
                'special' => [
                    'type_ids' => [203, 205],
                    'super_type_ids' => [14, 26],
                    'enabled' => true,
                ],
            ],
            
            // Types d'objets à exclure
            'exclude_types' => [
                204, // Animal de compagnie (aucun objet trouvé)
            ],
        ],

        'spells' => [
            'endpoint' => '/spells',
            'enabled' => true,
            'batch_size' => 100, // Pagination recommandée
            'fields' => [
                'id', 'typeId', 'iconId', 'spellLevels', 'name', 'description',
                'img', 'boundScriptUsageData', 'criticalHitBoundScriptUsageData'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
        ],

        'spell_levels' => [
            'endpoint' => '/spell-levels',
            'enabled' => true,
            'batch_size' => 100, // Pagination recommandée
            'fields' => [
                'id', 'spellId', 'grade', 'spellBreed', 'apCost', 'minRange',
                'range', 'criticalHitProbability', 'effects', 'zoneDescr'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
        ],

        'effects' => [
            'endpoint' => '/effects',
            'enabled' => true,
            'batch_size' => 50, // Pagination recommandée
            'fields' => [
                'id', 'iconId', 'characteristic', 'category', 'description',
                'showInTooltip', 'useDice', 'boost', 'active', 'elementId',
                'isInPercent', 'hideValueInTooltip'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
        ],

        'item_sets' => [
            'endpoint' => '/item-sets',
            'enabled' => true,
            'batch_size' => 50, // Pagination recommandée
            'fields' => [
                'id', 'items', 'name', 'description', 'bonus'
            ],
            'filters' => [
                'lang' => 'fr',
                'sort' => 'id',
            ],
        ],
    ],

    // Configuration de la pagination
    'pagination' => [
        'default_limit' => 50,
        'max_limit' => 100,
        'max_skip' => 10000,
        'auto_paginate' => true,
        'progress_tracking' => true,
    ],

    // Configuration des timeouts
    'timeouts' => [
        'entity_collection' => [
            'breeds' => 30,      // 19 entités
            'monsters' => 300,   // 4,900 entités
            'items' => 600,      // 20,853 objets
            'spells' => 900,     // 16,187 entités
            'spell_levels' => 1200, // 33,019 entités
            'effects' => 60,     // 823 entités
            'item_sets' => 120,  // 856 entités
        ],
        'batch_processing' => [
            'small' => 30,       // < 100 entités
            'medium' => 120,     // 100-1000 entités
            'large' => 300,      // 1000-10000 entités
            'huge' => 600,       // > 10000 entités
        ],
    ],

    // Configuration des retry
    'retry' => [
        'strategies' => [
            'exponential_backoff' => [
                'enabled' => true,
                'base_delay' => 1,
                'max_delay' => 60,
                'multiplier' => 2,
            ],
            'linear_backoff' => [
                'enabled' => false,
                'delay' => 5,
                'max_delay' => 30,
            ],
        ],
        'conditions' => [
            'http_errors' => [500, 502, 503, 504],
            'timeout_errors' => true,
            'connection_errors' => true,
            'rate_limit_errors' => [429],
        ],
    ],

    // Configuration du logging
    'logging' => [
        'enabled' => env('DOFUSDB_LOGGING_ENABLED', true),
        'level' => env('DOFUSDB_LOG_LEVEL', 'info'),
        'channels' => ['daily', 'slack'],
        'include_request_data' => true,
        'include_response_data' => false, // Pour éviter les logs trop volumineux
        'log_slow_requests' => true,
        'slow_request_threshold' => 5, // secondes
    ],

    // Configuration du monitoring
    'monitoring' => [
        'enabled' => env('DOFUSDB_MONITORING_ENABLED', true),
        'metrics' => [
            'request_count' => true,
            'response_time' => true,
            'error_rate' => true,
            'cache_hit_rate' => true,
            'rate_limit_hits' => true,
        ],
        'alerts' => [
            'error_rate_threshold' => 0.1, // 10%
            'response_time_threshold' => 10, // secondes
            'rate_limit_threshold' => 5, // hits par minute
        ],
    ],

    // Configuration des métadonnées
    'metadata' => [
        'include_timestamps' => true,
        'include_source_info' => true,
        'include_collection_stats' => true,
        'include_validation_results' => true,
    ],

    // Configuration des transformations
    'transformations' => [
        'normalize_field_names' => true,
        'convert_types' => true,
        'flatten_nested_objects' => false,
        'remove_empty_fields' => false,
    ],

    // Configuration de la validation
    'validation' => [
        'enabled' => true,
        'rules' => [
            'required_fields' => ['id', '_id'],
            'field_types' => [
                'id' => 'integer',
                '_id' => 'string',
                'name' => 'array',
                'description' => 'array',
            ],
            'data_quality' => [
                'check_missing_names' => true,
                'check_missing_descriptions' => true,
                'check_invalid_ids' => true,
            ],
        ],
    ],

    // Configuration des fallbacks
    'fallbacks' => [
        'enabled' => true,
        'strategies' => [
            'cache_only' => [
                'enabled' => true,
                'priority' => 1,
            ],
            'alternative_endpoints' => [
                'enabled' => false,
                'priority' => 2,
            ],
            'default_data' => [
                'enabled' => false,
                'priority' => 3,
            ],
        ],
    ],

    // Configuration des tests
    'testing' => [
        'enabled' => env('DOFUSDB_TESTING_ENABLED', false),
        'mock_responses' => true,
        'test_endpoints' => [
            'breeds' => '/breeds?lang=fr&$limit=2',
            'monsters' => '/monsters?lang=fr&$limit=2',
            'items' => '/items?typeId=15&lang=fr&$limit=2',
            'spells' => '/spells?lang=fr&$limit=2',
        ],
        'timeout_multiplier' => 0.1, // 10% du timeout normal en test
    ],
];
