<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Routes exclues du payload Ziggy (Inertia shareOnce + artisan ziggy:generate)
    |--------------------------------------------------------------------------
    |
    | Réduit la taille JSON (~50–80 %) en prod/dev sans Debugbar dans le bundle.
    |
    */

    'except' => [
        'debugbar.*',
        'horizon.*',
        'telescope.*',
    ],

];
