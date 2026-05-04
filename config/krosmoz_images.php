<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload images (entités, ressources, avatars)
    |--------------------------------------------------------------------------
    |
    | Taille max par défaut : 5 Mo (aligné avec les contrôleurs existants).
    | Les conversions WebP / miniatures sont gérées par Spatie Media Library.
    |
    */
    'max_upload_kb' => (int) env('KROSMOZ_IMAGE_MAX_UPLOAD_KB', 5120),

];
