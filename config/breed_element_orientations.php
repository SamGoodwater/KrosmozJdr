<?php

/**
 * Voix élémentaires et orientations de classe (fichiers dans storage/app/public/images/icons/).
 *
 * Les clés d’orientation correspondent au nom de fichier sans extension dans breed_orientations/.
 */
return [

    'orientation_extension' => 'png',

    'voice_icons' => [
        'air' => 'icons/caracteristics/air.webp',
        'earth' => 'icons/caracteristics/earth.webp',
        'fire' => 'icons/caracteristics/fire.webp',
        'water' => 'icons/caracteristics/water.webp',
    ],

    /**
     * Clés d’orientation autorisées (stems des fichiers dans breed_orientations/).
     */
    'allowed_orientation_keys' => [
        'amelioration',
        'degats',
        'entrave',
        'invocation',
        'placement',
        'protection',
        'soin',
        'tank',
    ],
];
