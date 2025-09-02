<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuration des caractéristiques KrosmozJDR
    |--------------------------------------------------------------------------
    |
    | Ce fichier charge les définitions des caractéristiques depuis les fichiers JSON
    | dans le dossier config/characteristics/
    |
    | Ces définitions sont utilisées par l'ensemble du projet pour :
    | - Validation des données
    | - Conversion entre systèmes
    | - Génération de formulaires
    | - Documentation automatique
    | - Tests de cohérence
    |
    */

    // Chargement des définitions des caractéristiques
    'definitions' => json_decode(
        file_get_contents(__DIR__ . '/characteristics/characteristics.json'),
        true
    ),

    // Chargement des formules de calcul
    'formulas' => json_decode(
        file_get_contents(__DIR__ . '/characteristics/formulas.json'),
        true
    ),

    // Chargement des règles de validation
    'validation_rules' => json_decode(
        file_get_contents(__DIR__ . '/characteristics/validation_rules.json'),
        true
    ),

    // Chargement des mappings d'entités
    'entity_mappings' => json_decode(
        file_get_contents(__DIR__ . '/characteristics/entity_mappings.json'),
        true
    ),

    /*
    |--------------------------------------------------------------------------
    | Paramètres généraux
    |--------------------------------------------------------------------------
    */

    // Langue par défaut pour les textes multilingues
    'default_language' => env('CHARACTERISTICS_DEFAULT_LANGUAGE', 'fr'),

    // Cache des configurations (en secondes)
    'cache_ttl' => env('CHARACTERISTICS_CACHE_TTL', 3600),

    // Validation automatique des caractéristiques
    'auto_validation' => env('CHARACTERISTICS_AUTO_VALIDATION', true),

    // Mode strict pour la validation
    'strict_mode' => env('CHARACTERISTICS_STRICT_MODE', false),
];
