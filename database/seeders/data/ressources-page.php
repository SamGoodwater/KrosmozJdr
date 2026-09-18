<?php

declare(strict_types=1);

/**
 * Page CMS « Ressources » : fichiers téléchargeables, racine du menu Règles.
 *
 * Le chapitre 5 (équilibrage) est réservé aux MJ ; les téléchargements restent publics.
 *
 * @return array{
 *   title: string,
 *   slug: string,
 *   parent_slug: string,
 *   menu_order: int,
 *   fallback_menu_order: int,
 *   icon: string|null,
 *   sections: list<array{slug: string, title: string, template: string, html?: string, settings?: array<string, mixed>}>
 * }
 */
return [
    'title' => 'Ressources',
    'slug' => 'ressources-de-jeu',
    'parent_slug' => '',
    'menu_order' => 90,
    'fallback_menu_order' => 90,
    'icon' => 'fa-download',
    'sections' => [
        [
            'slug' => 'intro',
            'title' => 'Fichiers pour jouer',
            'template' => 'text',
            'html' => '<p>Ici tu récupères le <strong>livre de règles</strong> (PDF ou OpenDocument), la <strong>fiche de personnage</strong> et le <strong>logo</strong> du projet. D’autres fichiers s’ajouteront au fur et à mesure.</p>'
                .'<p>Le livre compilé reprend les chapitres joueur du menu Règles. L’équilibrage des entités (classes, sorts, objets…) est dans Pour les MJ, pas dans ce PDF. Si le fichier n’est pas encore là, un administrateur doit le générer depuis la gestion du contenu — ça ne se fait pas tout seul à chaque visite.</p>'
                .'<p>Le détail des règles reste à lire en ligne, à commencer par [[kref:page:regles-1-1-presentation-du-jeu|la présentation du jeu]].</p>',
        ],
        [
            'slug' => 'fichiers',
            'title' => 'Téléchargements',
            'template' => 'download_catalog',
            'settings' => [
                'groups' => [],
            ],
        ],
    ],
];
