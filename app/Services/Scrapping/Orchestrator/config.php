<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration de l'Orchestrateur de Scrapping
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique au service d'orchestration
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Paramètres de processus
    |--------------------------------------------------------------------------
    |
    | Configuration des processus d'import et de gestion
    |
    */

    'process_management' => [
        'max_concurrent_processes' => env('SCRAPPING_MAX_CONCURRENT', 3),
        'process_timeout' => env('SCRAPPING_PROCESS_TIMEOUT', 3600),
        'enable_parallel_processing' => env('SCRAPPING_PARALLEL', true),
        'max_memory_usage' => env('SCRAPPING_MAX_MEMORY', 1024),
        'default_priority' => env('SCRAPPING_DEFAULT_PRIORITY', 'normal'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stratégies de retry
    |--------------------------------------------------------------------------
    |
    | Configuration des tentatives de récupération automatique
    |
    */

    'retry_strategies' => [
        'max_attempts' => env('SCRAPPING_RETRY_ATTEMPTS', 3),
        'initial_delay' => env('SCRAPPING_RETRY_DELAY', 60),
        'backoff_multiplier' => 2,
        'max_delay' => 300,
        'jitter' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gestion des erreurs
    |--------------------------------------------------------------------------
    |
    | Configuration de la gestion des erreurs et des fallbacks
    |
    */

    'error_handling' => [
        'enable_fallbacks' => true,
        'log_all_errors' => true,
        'notify_on_critical_errors' => true,
        'auto_rollback_on_failure' => true,
        'max_consecutive_failures' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring et métriques
    |--------------------------------------------------------------------------
    |
    | Configuration du suivi et des métriques de performance
    |
    */

    'monitoring' => [
        'enable_progress_tracking' => true,
        'progress_update_interval' => 5, // secondes
        'enable_performance_metrics' => true,
        'metrics_collection_interval' => 60, // secondes
        'enable_health_checks' => true,
        'health_check_interval' => 300, // secondes
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Configuration des notifications et alertes
    |
    */

    'notifications' => [
        'enabled' => env('SCRAPPING_NOTIFICATIONS_ENABLED', true),
        'channels' => [
            'database' => true,
            'mail' => env('SCRAPPING_NOTIFY_MAIL', false),
            'slack' => env('SCRAPPING_NOTIFY_SLACK', false),
        ],
        'events' => [
            'process_started' => true,
            'process_completed' => true,
            'process_failed' => true,
            'process_cancelled' => true,
            'high_error_rate' => true,
            'resource_usage_high' => true,
        ],
        'thresholds' => [
            'error_rate_warning' => 0.1, // 10%
            'error_rate_critical' => 0.25, // 25%
            'memory_usage_warning' => 0.8, // 80%
            'memory_usage_critical' => 0.95, // 95%
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging et traçabilité
    |--------------------------------------------------------------------------
    |
    | Configuration des logs et de la traçabilité
    |
    */

    'logging' => [
        'enabled' => env('SCRAPPING_LOGGING_ENABLED', true),
        'level' => env('SCRAPPING_LOG_LEVEL', 'info'),
        'channel' => env('SCRAPPING_LOG_CHANNEL', 'daily'),
        'correlation_enabled' => true,
        'performance_logging' => true,
        'audit_logging' => true,
        'max_log_files' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des services
    |--------------------------------------------------------------------------
    |
    | Configuration des services utilisés par l'orchestrateur
    |
    */

    'services' => [
        'data_collect' => [
            'class' => \App\Services\Scrapping\DataCollect\DataCollectService::class,
            'timeout' => env('SCRAPPING_COLLECT_TIMEOUT', 300),
            'retry_attempts' => env('SCRAPPING_COLLECT_RETRY_ATTEMPTS', 3),
        ],
        'data_conversion' => [
            'class' => \App\Services\Scrapping\DataConversion\DataConversionService::class,
            'timeout' => env('SCRAPPING_CONVERSION_TIMEOUT', 300),
            'retry_attempts' => env('SCRAPPING_CONVERSION_RETRY_ATTEMPTS', 2),
        ],
        'data_integration' => [
            'class' => \App\Services\Scrapping\DataIntegration\DataIntegrationService::class,
            'timeout' => env('SCRAPPING_INTEGRATION_TIMEOUT', 600),
            'retry_attempts' => env('SCRAPPING_INTEGRATION_RETRY_ATTEMPTS', 3),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des entités
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique par type d'entité
    |
    */

    'entities' => [
        'class' => [
            'priority' => 'high',
            'timeout' => 1800,
            'retry_attempts' => 3,
            'batch_size' => 10,
        ],
        'monster' => [
            'priority' => 'normal',
            'timeout' => 3600,
            'retry_attempts' => 3,
            'batch_size' => 5,
        ],
        'item' => [
            'priority' => 'normal',
            'timeout' => 1800,
            'retry_attempts' => 3,
            'batch_size' => 20,
        ],
        'spell' => [
            'priority' => 'normal',
            'timeout' => 1800,
            'retry_attempts' => 3,
            'batch_size' => 15,
        ],
        'effect' => [
            'priority' => 'low',
            'timeout' => 900,
            'retry_attempts' => 2,
            'batch_size' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des webhooks
    |--------------------------------------------------------------------------
    |
    | Configuration des webhooks externes
    |
    */

    'webhooks' => [
        'enabled' => env('SCRAPPING_WEBHOOKS_ENABLED', false),
        'endpoints' => [
            'process_started' => env('SCRAPPING_WEBHOOK_PROCESS_STARTED', ''),
            'process_completed' => env('SCRAPPING_WEBHOOK_PROCESS_COMPLETED', ''),
            'process_failed' => env('SCRAPPING_WEBHOOK_PROCESS_FAILED', ''),
            'process_cancelled' => env('SCRAPPING_WEBHOOK_PROCESS_CANCELLED', ''),
            'high_error_rate' => env('SCRAPPING_WEBHOOK_HIGH_ERROR_RATE', ''),
        ],
        'timeout' => env('SCRAPPING_WEBHOOK_TIMEOUT', 10),
        'retry_attempts' => env('SCRAPPING_WEBHOOK_RETRY_ATTEMPTS', 3),
        'secret' => env('SCRAPPING_WEBHOOK_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration des métriques
    |--------------------------------------------------------------------------
    |
    | Configuration de la collecte et de l'export des métriques
    |
    */

    'metrics' => [
        'enabled' => env('SCRAPPING_METRICS_ENABLED', true),
        'collection_interval' => env('SCRAPPING_METRICS_INTERVAL', 60),
        'retention_period' => env('SCRAPPING_METRICS_RETENTION', 2592000), // 30 jours
        'export_formats' => ['json', 'csv'],
        'dashboard_enabled' => env('SCRAPPING_METRICS_DASHBOARD', true),
        'real_time_updates' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration de sécurité
    |--------------------------------------------------------------------------
    |
    | Paramètres de sécurité et de validation
    |
    */

    'security' => [
        'validate_inputs' => true,
        'sanitize_data' => true,
        'rate_limiting' => [
            'enabled' => true,
            'max_requests_per_minute' => 60,
            'max_requests_per_hour' => 1000,
        ],
        'audit_trail' => true,
        'encrypt_sensitive_data' => false,
    ],
];
