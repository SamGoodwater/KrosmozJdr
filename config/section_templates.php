<?php

/**
 * Configuration des templates de sections
 * 
 * Ce fichier contient les valeurs par défaut pour chaque template de section.
 * Il doit être synchronisé avec les fichiers config.js des templates frontend.
 * 
 * Structure : chaque template a 'settings' et 'data' par défaut.
 * 
 * @note Ce fichier doit être mis à jour manuellement lorsque les configs JS changent.
 * Un script de synchronisation automatique pourrait être créé à l'avenir.
 * 
 * @see resources/js/Pages/Organismes/section/templates/ pour les fichiers config.js de chaque template
 */

return [
    'text' => [
        'settings' => [],
        'data' => [
            'content' => null, // null au lieu de '' pour éviter les problèmes de validation
        ],
    ],
    'image' => [
        'settings' => [
            'align' => 'center',
            'size' => 'md',
        ],
        'data' => [
            'src' => null,
            'alt' => null,
            'caption' => null,
        ],
    ],
    'gallery' => [
        'settings' => [
            'columns' => 3,
            'gap' => 'md',
        ],
        'data' => [
            'images' => [],
        ],
    ],
    'video' => [
        'settings' => [
            'autoplay' => false,
            'controls' => true,
        ],
        'data' => [
            'src' => null,
            'type' => 'youtube',
        ],
    ],
    'entity_table' => [
        'settings' => [],
        'data' => [
            'entity' => null,
            'filters' => [],
            'columns' => [],
        ],
    ],
];

