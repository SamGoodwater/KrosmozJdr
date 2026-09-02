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
        'settings' => [
            'align' => 'left',
            'size' => 'md',
            'enableRichReferences' => false,
        ],
        'data' => [
            'content' => null, // null au lieu de '' pour éviter les problèmes de validation
        ],
    ],
    'image' => [
        'settings' => [
            'align' => 'center',
            'size' => 'md',
            'zoom' => 100,
            'lazyLoad' => false,
            'documentDisplayMode' => 'preview',
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
            'directVideoDisplayMode' => 'preview',
        ],
        'data' => [
            'src' => null,
            'type' => 'youtube',
        ],
    ],
    'entity_table' => [
        'settings' => [
            'entity' => 'spells',
            'filters' => [],
            // Legacy : la lecture utilise la pagination serveur (ignore ce plafond).
            'limit' => 50,
        ],
        'data' => [
            'entity' => null,
            'filters' => [],
            'columns' => [],
        ],
    ],
    'legal_markdown' => [
        'settings' => [],
        'data' => [
            'sourceUrl' => null,
            'title' => null,
        ],
    ],
    'characteristic_norms' => [
        'settings' => [
            'characteristic_key' => '',
            'group' => 'creature',
            'entity' => '*',
        ],
        'data' => [],
    ],
    'characteristic_norms_catalog' => [
        'settings' => [
            'group' => 'spell',
            'entity' => '*',
            'characteristic_keys' => [],
        ],
        'data' => [],
    ],
    'characteristic_reference_table' => [
        'settings' => [
            'group' => 'all',
            'entity' => '*',
            'search' => '',
            'sort_by' => 'group',
            'sort_dir' => 'asc',
            'status_filter' => 'all',
            'show_prices' => true,
            'show_only_with_equipment' => false,
        ],
        'data' => [],
    ],
    'equipment_bonus_table' => [
        'settings' => [],
        'data' => [],
    ],
    'forgemagie_rune_table' => [
        'settings' => [
            'sort_by' => 'name',
            'sort_dir' => 'asc',
            'show_base_price' => false,
        ],
        'data' => [],
    ],
];
