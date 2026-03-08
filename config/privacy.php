<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Export RGPD — exécution synchrone
    |--------------------------------------------------------------------------
    |
    | Quand activé, le job GenerateUserDataExportJob s'exécute immédiatement
    | dans la requête HTTP (sans worker de queue). L'export est disponible
    | dès le retour de la page.
    |
    | Utile en dev / petits déploiements sans queue worker configuré.
    | En production avec beaucoup d'utilisateurs, garder false et
    | exécuter `php artisan queue:work` pour traiter les jobs.
    |
    */

    'export_sync' => env('PRIVACY_EXPORT_SYNC', false),

    /*
    |--------------------------------------------------------------------------
    | Suppression du compte — délai de rétractation (jours)
    |--------------------------------------------------------------------------
    |
    | Nombre de jours pendant lesquels l'utilisateur peut annuler sa demande
    | de suppression. Passé ce délai, la commande privacy:process-deletion-requests
    | enverra le job d'effacement. Défaut : 7 jours.
    |
    */

    'erasure_withdrawal_days' => (int) env('PRIVACY_ERASURE_WITHDRAWAL_DAYS', 7),

];
