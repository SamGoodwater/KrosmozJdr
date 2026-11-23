<?php

use App\Http\Controllers\PageController;
use Database\Seeders\NavMenuSeeder;

/**
 * Configuration du menu de navigation (groupes, entrées).
 *
 * Seedée par NavMenuSeeder. Structure des Bibliothèques pour le menu principal.
 * Les groupes sans enfants visibles (filtrage `read_level`) n’apparaissent pas.
 *
 * @see NavMenuSeeder
 * @see PageController::menu()
 */
return [
    'groups' => [
        [
            'id' => 'referentiels',
            'title' => "L'Essentiel",
            'menu_group' => "L'Essentiel",
            'order' => 0,
            'icon' => 'fa-book-bookmark',
        ],
        [
            'id' => 'regles',
            'title' => 'Règles',
            'menu_group' => 'Règles',
            'order' => 1,
            'icon' => 'fa-book',
        ],
        [
            'id' => 'bibliotheques',
            'title' => 'Bibliothèques',
            'menu_group' => 'Bibliothèques',
            'order' => 2,
            'icon' => 'fa-book-open-reader',
        ],
        [
            'id' => 'pour-les-mj',
            'title' => 'Pour les MJ',
            'menu_group' => 'Pour les MJ',
            'order' => 3,
            'icon' => 'fa-hat-wizard',
        ],
        [
            'id' => 'informations',
            'title' => 'Informations',
            'menu_group' => 'Informations',
            'order' => 4,
            'icon' => 'fa-circle-info',
        ],
    ],
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
            'label' => 'Aptitudes',
            'route' => 'entities.aptitudes.index',
            'entity_key' => 'aptitude',
            'order' => 4,
        ],
        [
            'label' => 'Monstres',
            'route' => 'entities.monsters.index',
            'entity_key' => 'monster',
            'order' => 5,
        ],
        [
            'label' => 'PNJ',
            'route' => 'entities.npcs.index',
            'entity_key' => 'npc',
            'order' => 6,
        ],
        [
            'label' => 'Équipements',
            'route' => 'entities.items.index',
            'entity_key' => 'item',
            'order' => 7,
        ],
        [
            'label' => 'Panoplies',
            'route' => 'entities.panoplies.index',
            'entity_key' => 'panoply',
            'order' => 8,
        ],
        [
            'label' => 'Consommables',
            'route' => 'entities.consumables.index',
            'entity_key' => 'consumable',
            'order' => 9,
        ],
        [
            'label' => 'Ressources',
            'route' => 'entities.resources.index',
            'entity_key' => 'resource',
            'order' => 10,
        ],
        [
            'label' => 'États',
            'route' => 'entities.conditions.index',
            'entity_key' => 'condition',
            'order' => 11,
        ],
        [
            'label' => 'Traits',
            'route' => 'entities.creature-traits.index',
            'entity_key' => 'creature-trait',
            'order' => 12,
        ],
        [
            'label' => 'Langues',
            'route' => 'admin.languages.index',
            'entity_key' => 'language',
            'order' => 13,
        ],
    ],
];
