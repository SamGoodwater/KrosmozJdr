<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration générale du scrapping
    |--------------------------------------------------------------------------
    |
    | Configuration globale des services de scrapping KrosmozJDR
    |
    */

    'enabled' => env('SCRAPPING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Configuration de l'orchestrateur
    |--------------------------------------------------------------------------
    |
    | Paramètres de coordination et de gestion des processus
    |
    */

    'orchestrator' => [
        'max_concurrent_processes' => env('SCRAPPING_MAX_CONCURRENT', 3),
        'process_timeout' => env('SCRAPPING_PROCESS_TIMEOUT', 3600),
        'retry_attempts' => env('SCRAPPING_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('SCRAPPING_RETRY_DELAY', 60),
        'enable_parallel_processing' => env('SCRAPPING_PARALLEL', true),
        'max_memory_usage' => env('SCRAPPING_MAX_MEMORY', 1024),
        'default_priority' => env('SCRAPPING_DEFAULT_PRIORITY', 'normal'),
        'enable_notifications' => env('SCRAPPING_NOTIFICATIONS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration de la collecte de données
    |--------------------------------------------------------------------------
    |
    | Paramètres pour la récupération des données depuis des sites externes
    |
    */

    'data_collect' => [
        'dofusdb_base_url' => env('DOFUSDB_BASE_URL', 'https://api.dofusdb.fr'),
        'timeout' => env('SCRAPPING_COLLECT_TIMEOUT', 30),
        'retry_attempts' => env('SCRAPPING_COLLECT_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('SCRAPPING_COLLECT_RETRY_DELAY', 1000),
        'cache_ttl' => env('SCRAPPING_COLLECT_CACHE_TTL', 3600),
        'user_agent' => env('SCRAPPING_USER_AGENT', 'KrosmozJDR-Scrapping/1.0'),
        'rate_limit' => [
            'requests_per_minute' => env('SCRAPPING_RATE_LIMIT_REQUESTS', 60),
            'delay_between_requests' => env('SCRAPPING_RATE_LIMIT_DELAY', 1000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration de la conversion de données
    |--------------------------------------------------------------------------
    |
    | Paramètres pour la conversion des valeurs selon les caractéristiques KrosmozJDR
    |
    */

    'data_conversion' => [
        'use_generic_mappings' => env('DATA_CONVERSION_USE_GENERIC_MAPPINGS', true),
        'use_generic_formulas' => env('DATA_CONVERSION_USE_GENERIC_FORMULAS', true),
        'use_generic_validation_rules' => env('DATA_CONVERSION_USE_GENERIC_VALIDATION_RULES', true),
        'strict_mode' => env('DATA_CONVERSION_STRICT_MODE', false),
        'auto_validation' => env('DATA_CONVERSION_AUTO_VALIDATION', true),
        'auto_correction' => env('DATA_CONVERSION_AUTO_CORRECTION', true),
        'default_language' => env('DATA_CONVERSION_DEFAULT_LANGUAGE', 'fr'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration de l'intégration de données
    |--------------------------------------------------------------------------
    |
    | Paramètres pour l'intégration des données dans la structure KrosmozJDR
    |
    */

    'data_integration' => [
        'batch_size' => env('SCRAPPING_INTEGRATION_BATCH_SIZE', 100),
        'enable_transactions' => env('SCRAPPING_INTEGRATION_TRANSACTIONS', true),
        'conflict_strategy' => env('SCRAPPING_INTEGRATION_CONFLICT_STRATEGY', 'update'),
        'duplicate_handling' => env('SCRAPPING_INTEGRATION_DUPLICATE_HANDLING', 'update'),
        'validation_before_save' => env('SCRAPPING_INTEGRATION_VALIDATION_BEFORE_SAVE', true),
        'create_relations' => env('SCRAPPING_INTEGRATION_CREATE_RELATIONS', true),
        'cleanup_after_integration' => env('SCRAPPING_INTEGRATION_CLEANUP', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des timeouts par type de processus
    |--------------------------------------------------------------------------
    |
    | Timeouts spécifiques selon le type d'import
    |
    */

    'process_timeouts' => [
        'individual_import' => [
            'total_timeout' => env('SCRAPPING_INDIVIDUAL_TIMEOUT', 1800),
            'step_timeout' => 300,
            'collection_timeout' => 600,
            'conversion_timeout' => 300,
            'integration_timeout' => 600
        ],
        'batch_import' => [
            'total_timeout' => env('SCRAPPING_BATCH_TIMEOUT', 7200),
            'entity_timeout' => 600,
            'batch_timeout' => 1800
        ],
        'category_import' => [
            'total_timeout' => env('SCRAPPING_CATEGORY_TIMEOUT', 14400),
            'batch_timeout' => 3600,
            'entity_timeout' => 300
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration de la concurrence
    |--------------------------------------------------------------------------
    |
    | Paramètres de gestion de la concurrence et des ressources
    |
    */

    'concurrency_settings' => [
        'max_concurrent_processes' => env('SCRAPPING_MAX_CONCURRENT', 3),
        'max_concurrent_entities' => 5,
        'max_concurrent_batches' => 2,
        'resource_limits' => [
            'memory_per_process' => env('SCRAPPING_MEMORY_PER_PROCESS', 512),
            'cpu_per_process' => env('SCRAPPING_CPU_PER_PROCESS', 50),
            'network_connections' => env('SCRAPPING_MAX_NETWORK_CONNECTIONS', 10)
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des alertes et seuils
    |--------------------------------------------------------------------------
    |
    | Seuils d'alerte pour le monitoring
    |
    */

    'alert_thresholds' => [
        'process_timeout' => env('SCRAPPING_ALERT_TIMEOUT', 3600),
        'memory_limit' => env('SCRAPPING_ALERT_MEMORY', 1024),
        'cpu_limit' => env('SCRAPPING_ALERT_CPU', 90),
        'error_rate_threshold' => env('SCRAPPING_ALERT_ERROR_RATE', 0.05),
        'success_rate_minimum' => env('SCRAPPING_ALERT_SUCCESS_RATE', 0.95),
        'step_duration_max' => env('SCRAPPING_ALERT_STEP_DURATION', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des stratégies de retry
    |--------------------------------------------------------------------------
    |
    | Stratégies de récupération automatique en cas d'erreur
    |
    */

    'retry_strategies' => [
        'collection_errors' => [
            'max_attempts' => 3,
            'backoff_multiplier' => 2,
            'initial_delay' => 5,
            'max_delay' => 60
        ],
        'conversion_errors' => [
            'max_attempts' => 2,
            'backoff_multiplier' => 1.5,
            'initial_delay' => 1,
            'max_delay' => 10
        ],
        'integration_errors' => [
            'max_attempts' => 3,
            'backoff_multiplier' => 2,
            'initial_delay' => 10,
            'max_delay' => 120
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des stratégies de fallback
    |--------------------------------------------------------------------------
    |
    | Stratégies de récupération en cas d'échec
    |
    */

    'fallback_strategies' => [
        'value_out_of_range' => 'use_limit',
        'missing_required_field' => 'use_default',
        'invalid_format' => 'skip_entity',
        'service_unavailable' => 'retry_later',
        'database_error' => 'rollback_and_retry'
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des logs
    |--------------------------------------------------------------------------
    |
    | Paramètres de logging et de traçabilité
    |
    */

    'logging' => [
        'enabled' => env('SCRAPPING_LOGGING_ENABLED', true),
        'level' => env('SCRAPPING_LOG_LEVEL', 'info'),
        'channel' => env('SCRAPPING_LOG_CHANNEL', 'daily'),
        'max_files' => env('SCRAPPING_LOG_MAX_FILES', 30),
        'correlation_enabled' => env('SCRAPPING_LOG_CORRELATION', true),
        'performance_logging' => env('SCRAPPING_LOG_PERFORMANCE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des notifications
    |--------------------------------------------------------------------------
    |
    | Paramètres de notification des événements de scrapping
    |
    */

    'notifications' => [
        'enabled' => env('SCRAPPING_NOTIFICATIONS_ENABLED', true),
        'channels' => [
            'database' => env('SCRAPPING_NOTIFY_DATABASE', true),
            'mail' => env('SCRAPPING_NOTIFY_MAIL', false),
            'slack' => env('SCRAPPING_NOTIFY_SLACK', false),
        ],
        'events' => [
            'process_started' => true,
            'process_completed' => true,
            'process_failed' => true,
            'process_cancelled' => true,
            'high_error_rate' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des webhooks
    |--------------------------------------------------------------------------
    |
    | Paramètres pour les webhooks externes
    |
    */

    'webhooks' => [
        'enabled' => env('SCRAPPING_WEBHOOKS_ENABLED', false),
        'endpoints' => [
            'process_completed' => env('SCRAPPING_WEBHOOK_PROCESS_COMPLETED', ''),
            'process_failed' => env('SCRAPPING_WEBHOOK_PROCESS_FAILED', ''),
            'high_error_rate' => env('SCRAPPING_WEBHOOK_HIGH_ERROR_RATE', ''),
        ],
        'timeout' => env('SCRAPPING_WEBHOOK_TIMEOUT', 10),
        'retry_attempts' => env('SCRAPPING_WEBHOOK_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des métriques
    |--------------------------------------------------------------------------
    |
    | Paramètres de collecte et d'affichage des métriques
    |
    */

    'metrics' => [
        'enabled' => env('SCRAPPING_METRICS_ENABLED', true),
        'collection_interval' => env('SCRAPPING_METRICS_INTERVAL', 60),
        'retention_period' => env('SCRAPPING_METRICS_RETENTION', 86400 * 30), // 30 jours
        'export_formats' => ['json', 'csv'],
        'dashboard_enabled' => env('SCRAPPING_METRICS_DASHBOARD', true),
    ],
];
