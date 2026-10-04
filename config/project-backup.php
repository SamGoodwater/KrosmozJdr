<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Répertoire des sauvegardes
    |--------------------------------------------------------------------------
    |
    | Par défaut : storage/app/backups (hors archive « storage » pour éviter récursion).
    | Chemin absolu ou relatif à la racine du projet ; doit rester sous base_path().
    |
    */
    'path' => env('PROJECT_BACKUP_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Conservation (jours)
    |--------------------------------------------------------------------------
    |
    | Archives plus anciennes que cette durée sont purgées lors d’un run
    | (sauf si --no-prune). Défaut : 30 jours (~1 mois).
    |
    */
    'retention_days' => (int) env('PROJECT_BACKUP_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Binaire mysqldump / mysql
    |--------------------------------------------------------------------------
    |
    | Laisser vide pour utiliser le binaire du PATH.
    |
    */
    'mysqldump_path' => env('PROJECT_BACKUP_MYSQLDUMP_PATH', ''),
    'mysql_path' => env('PROJECT_BACKUP_MYSQL_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Préfixe des fichiers générés
    |--------------------------------------------------------------------------
    */
    'filename_prefix' => env('PROJECT_BACKUP_PREFIX', 'project-backup'),

    /*
    |--------------------------------------------------------------------------
    | Timeouts (secondes)
    |--------------------------------------------------------------------------
    */
    'dump_timeout' => (int) env('PROJECT_BACKUP_DUMP_TIMEOUT', 3600),
    'archive_timeout' => (int) env('PROJECT_BACKUP_ARCHIVE_TIMEOUT', 7200),
    'restore_timeout' => (int) env('PROJECT_BACKUP_RESTORE_TIMEOUT', 7200),
    'lock_ttl' => (int) env('PROJECT_BACKUP_LOCK_TTL', 7200),

    /*
    |--------------------------------------------------------------------------
    | Version du manifeste ZIP
    |--------------------------------------------------------------------------
    */
    'manifest_version' => 2,

];
