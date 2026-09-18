<?php

declare(strict_types=1);

/**
 * Page CMS « Ressources MJ » : PDF d’atelier, menu Pour les MJ.
 *
 * @return array{
 *   title: string,
 *   slug: string,
 *   menu_order: int,
 *   icon: string|null,
 *   sections: list<array{slug: string, title: string, template: string, html?: string, settings?: array<string, mixed>}>
 * }
 */
return [
    'title' => 'Ressources MJ',
    'slug' => 'ressources-mj',
    'menu_order' => 860,
    'icon' => 'fa-download',
    'sections' => [
        [
            'slug' => 'intro',
            'title' => 'Fichiers pour la table',
            'template' => 'text',
            'html' => '<p>Ici tu récupères l’<strong>atelier MJ</strong> (PDF ou OpenDocument) : calibrage des classes, sorts, objets, rencontres et économie.</p>'
                .'<p>Les fiches à jour sont dans les [[kref:page:bibliotheque-breed|Bibliothèques]]. Pour concevoir une fiche, passe par [[kref:page:creation|Création]]. Le [[kref:page:ressources-de-jeu|livre joueur]] ne reprend pas cet atelier.</p>',
        ],
        [
            'slug' => 'fichiers',
            'title' => 'Téléchargements MJ',
            'template' => 'download_catalog',
            'settings' => [
                'groups' => ['mj'],
            ],
        ],
    ],
];
