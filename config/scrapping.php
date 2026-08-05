<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Collecte DofusDB
    |--------------------------------------------------------------------------
    |
    | Ces options sont consommées par DofusDbClient et les catalogues DofusDB.
    |
    */
    'data_collect' => [
        'dofusdb_base_url' => env('DOFUSDB_BASE_URL', 'https://api.dofusdb.fr'),
        'default_language' => env('DOFUSDB_DEFAULT_LANGUAGE', 'fr'),
        'timeout' => (int) env('SCRAPPING_COLLECT_TIMEOUT', 30),
        'retry_attempts' => (int) env('SCRAPPING_COLLECT_RETRY_ATTEMPTS', 3),
        'retry_delay' => (int) env('SCRAPPING_COLLECT_RETRY_DELAY', 1000),
        'cache_ttl' => (int) env('SCRAPPING_COLLECT_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Images importées
    |--------------------------------------------------------------------------
    |
    | L'URL est validée avant téléchargement par IntegrationService.
    |
    */
    'images' => [
        'enabled' => (bool) env('SCRAPPING_IMAGES_ENABLED', true),
        'allowed_hosts' => [
            'api.dofusdb.fr',
        ],
    ],
];
