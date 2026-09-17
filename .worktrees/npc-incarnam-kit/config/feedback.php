<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email de fallback si aucun admin en base
    |--------------------------------------------------------------------------
    |
    | Si aucun utilisateur avec rôle admin n'existe, le mail est envoyé à cette
    | adresse (ex. config('mail.from.address')).
    |
    */
    'fallback_email' => env('FEEDBACK_FALLBACK_EMAIL', null),

    /*
    |--------------------------------------------------------------------------
    | Throttle (requêtes par minute)
    |--------------------------------------------------------------------------
    |
    | Limite le nombre de retours par IP pour éviter le spam.
    |
    */
    'throttle_per_minute' => (int) env('FEEDBACK_THROTTLE_PER_MINUTE', 6),
];
