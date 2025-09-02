<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration du service de dataconversion
    |--------------------------------------------------------------------------
    |
    | Configuration spécifique au service de dataconversion
    | Utilise les définitions génériques de config/characteristics.php
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Paramètres de dataconversion
    |--------------------------------------------------------------------------
    */

    // Mode strict (rejeter les données invalides vs. utiliser des valeurs par défaut)
    'strict_mode' => env('DATA_CONVERSION_STRICT_MODE', false),

    // Niveau de détail des logs
    'log_level' => env('DATA_CONVERSION_LOG_LEVEL', 'info'),

    // Validation automatique
    'auto_validation' => env('DATA_CONVERSION_AUTO_VALIDATION', true),

    // Correction automatique des valeurs hors limites
    'auto_correction' => env('DATA_CONVERSION_AUTO_CORRECTION', true),

    // Langue par défaut pour les textes multilingues
    'default_language' => env('DATA_CONVERSION_DEFAULT_LANGUAGE', 'fr'),

    /*
    |--------------------------------------------------------------------------
    | Paramètres de performance
    |--------------------------------------------------------------------------
    */

    // Taille des lots pour le traitement en masse
    'batch_size' => env('DATA_CONVERSION_BATCH_SIZE', 100),

    // Limite de mémoire pour le traitement (en MB)
    'memory_limit' => env('DATA_CONVERSION_MEMORY_LIMIT', 512),

    // Timeout pour les opérations de conversion (en secondes)
    'timeout' => env('DATA_CONVERSION_TIMEOUT', 300),

    // Cache des configurations (en secondes)
    'cache_ttl' => env('DATA_CONVERSION_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Paramètres de débogage
    |--------------------------------------------------------------------------
    */

    // Mode simulation (ne pas sauvegarder en base)
    'simulation_mode' => env('DATA_CONVERSION_SIMULATION_MODE', false),

    // Sauvegarder les données brutes
    'save_raw_data' => env('DATA_CONVERSION_SAVE_RAW_DATA', false),

    // Sauvegarder les données converties
    'save_converted_data' => env('DATA_CONVERSION_SAVE_CONVERTED_DATA', true),

    // Générer des rapports détaillés
    'generate_reports' => env('DATA_CONVERSION_GENERATE_REPORTS', true),

    /*
    |--------------------------------------------------------------------------
    | Paramètres de sécurité
    |--------------------------------------------------------------------------
    */

    // Validation stricte des entrées
    'strict_input_validation' => env('DATA_CONVERSION_STRICT_INPUT_VALIDATION', true),

    // Sanitisation des données
    'sanitize_data' => env('DATA_CONVERSION_SANITIZE_DATA', true),

    // Protection contre les injections
    'prevent_injection' => env('DATA_CONVERSION_PREVENT_INJECTION', true),

    /*
    |--------------------------------------------------------------------------
    | Paramètres spécifiques à Dofus
    |--------------------------------------------------------------------------
    */

    // URL de l'API DofusDB
    'dofusdb_api_url' => env('DATA_CONVERSION_API_URL', 'https://api.dofusdb.fr'),

    // Rate limiting pour l'API DofusDB (requêtes par minute)
    'rate_limit' => env('DATA_CONVERSION_RATE_LIMIT', 60),

    // Timeout pour les requêtes API (en secondes)
    'api_timeout' => env('DATA_CONVERSION_API_TIMEOUT', 30),

    // Retry attempts pour les requêtes API
    'retry_attempts' => env('DATA_CONVERSION_RETRY_ATTEMPTS', 3),

    // Delay entre les retries (en secondes)
    'retry_delay' => env('DATA_CONVERSION_RETRY_DELAY', 5),

    /*
    |--------------------------------------------------------------------------
    | Paramètres de mapping
    |--------------------------------------------------------------------------
    */

    // Utiliser les mappings génériques de config/characteristics.php
    'use_generic_mappings' => env('DATA_CONVERSION_USE_GENERIC_MAPPINGS', true),

    // Mappings spécifiques au service (si nécessaire)
    'specific_mappings' => [
        // Mappings spécifiques à DofusDB si différents des mappings génériques
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de formules
    |--------------------------------------------------------------------------
    */

    // Utiliser les formules génériques de config/characteristics.php
    'use_generic_formulas' => env('DATA_CONVERSION_USE_GENERIC_FORMULAS', true),

    // Formules spécifiques au service (si nécessaire)
    'specific_formulas' => [
        // Formules spécifiques à DofusDB si différentes des formules génériques
    ],

    /*
    |--------------------------------------------------------------------------
    | Paramètres de validation
    |--------------------------------------------------------------------------
    */

    // Utiliser les règles de validation génériques de config/characteristics.php
    'use_generic_validation_rules' => env('DATA_CONVERSION_USE_GENERIC_VALIDATION_RULES', true),

    // Règles de validation spécifiques au service (si nécessaire)
    'specific_validation_rules' => [
        // Règles spécifiques à DofusDB si différentes des règles génériques
    ],
];
