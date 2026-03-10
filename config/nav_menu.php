<?php

/**
 * Configuration du menu de navigation (groupes, entrées).
 *
 * Seedée par NavMenuSeeder. Structure des Bibliothèques pour le menu principal.
 *
 * @see \Database\Seeders\NavMenuSeeder
 */
return [
    'bibliotheques' => [
        [
            'label' => 'Classes',
            'route' => 'entities.breeds.index',
            'entity_key' => 'breed',
            'order' => 0,
        ],
        [
            'label' => 'Spécialisations',
            'route' => 'entities.specializations.index',
            'entity_key' => 'specialization',
            'order' => 1,
        ],
        [
            'label' => 'Sorts',
            'route' => 'entities.spells.index',
            'entity_key' => 'spell',
            'order' => 2,
        ],
        [
            'label' => 'Capacités',
            'route' => 'entities.capabilities.index',
            'entity_key' => 'capability',
            'order' => 3,
        ],
        [
            'label' => 'Monstres',
            'route' => 'entities.monsters.index',
            'entity_key' => 'monster',
            'order' => 4,
        ],
        [
            'label' => 'Équipements',
            'route' => 'entities.items.index',
            'entity_key' => 'item',
            'order' => 5,
        ],
        [
            'label' => 'Panoplies',
            'route' => 'entities.panoplies.index',
            'entity_key' => 'panoply',
            'order' => 6,
        ],
        [
            'label' => 'Consommables',
            'route' => 'entities.consumables.index',
            'entity_key' => 'consumable',
            'order' => 7,
        ],
        [
            'label' => 'Ressources',
            'route' => 'entities.resources.index',
            'entity_key' => 'resource',
            'order' => 8,
        ],
        [
            'label' => 'États',
            'url' => '/pages/etats',
            'entity_key' => 'condition',
            'order' => 9,
        ],
    ],
];
