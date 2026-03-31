<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Répertoire des sauvegardes
    |--------------------------------------------------------------------------
    |
    | Par défaut : storage/app/backups (hors archive « storage » pour éviter récursion).
    |
    */
    'path' => env('PROJECT_BACKUP_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Conservation (jours)
    |--------------------------------------------------------------------------
    |
    | Fichiers de sauvegarde plus anciens sont supprimés lors du passage de la commande
    | (sauf si --no-prune). Défaut : 30 jours (~1 mois).
    |
    */
    'retention_days' => (int) env('PROJECT_BACKUP_RETENTION_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Binaire mysqldump
    |--------------------------------------------------------------------------
    |
    | Laisser vide pour utiliser « mysqldump » du PATH.
    |
    */
    'mysqldump_path' => env('PROJECT_BACKUP_MYSQLDUMP_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Préfixe des fichiers générés
    |--------------------------------------------------------------------------
    */
    'filename_prefix' => env('PROJECT_BACKUP_PREFIX', 'project-backup'),

];
