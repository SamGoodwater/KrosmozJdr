<?php

declare(strict_types=1);

/**
 * Catalogue des fichiers téléchargeables (livre de règles, fiches, logo).
 *
 * Les clés `generated` sont produites par `rules:compile-downloads` dans
 * `storage/app/public/{generated_directory}/`. Les autres pointent vers des
 * fichiers déjà versionnés sur le disque public.
 *
 * Pour ajouter un fichier plus tard : une entrée ici suffit ; la page
 * Ressources et l’API les affichent automatiquement.
 *
 * @return array{
 *   disk: string,
 *   generated_directory: string,
 *   items: list<array{
 *     key: string,
 *     label: string,
 *     description: string,
 *     group: string,
 *     group_label: string,
 *     icon: string,
 *     mime: string,
 *     generated?: bool,
 *     filename?: string,
 *     path?: string
 *   }>
 * }
 */
return [
    'disk' => 'public',
    'generated_directory' => 'downloads/generated',
    'items' => [
        [
            'key' => 'rules-pdf',
            'label' => 'Livre de règles (PDF)',
            'description' => 'Le livre complet, compilé depuis les chapitres Markdown. Prêt à imprimer.',
            'group' => 'regles',
            'group_label' => 'Livre de règles',
            'icon' => 'fa-file-pdf',
            'mime' => 'application/pdf',
            'generated' => true,
            'filename' => 'krosmoz-jdr-regles.pdf',
        ],
        [
            'key' => 'rules-odt',
            'label' => 'Livre de règles (OpenDocument)',
            'description' => 'Le même livre au format ODT, ouvrable dans LibreOffice ou Word.',
            'group' => 'regles',
            'group_label' => 'Livre de règles',
            'icon' => 'fa-file-lines',
            'mime' => 'application/vnd.oasis.opendocument.text',
            'generated' => true,
            'filename' => 'krosmoz-jdr-regles.odt',
        ],
        [
            'key' => 'character-sheet-pdf',
            'label' => 'Fiche de personnage (PDF)',
            'description' => 'Fiche imprimable pour jouer en présentiel.',
            'group' => 'fiches',
            'group_label' => 'Fiches de personnage',
            'icon' => 'fa-file-pdf',
            'mime' => 'application/pdf',
            'generated' => false,
            'path' => 'sheet/fiche_perso 0.1.3.9.pdf',
        ],
        [
            'key' => 'character-sheet-pptx',
            'label' => 'Fiche de personnage (PowerPoint)',
            'description' => 'Fiche modifiable pour personnaliser la mise en page.',
            'group' => 'fiches',
            'group_label' => 'Fiches de personnage',
            'icon' => 'fa-file-powerpoint',
            'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'generated' => false,
            'path' => 'sheet/fiche_perso 0.1.3.9.pptx',
        ],
        [
            'key' => 'logo-png',
            'label' => 'Logo Krosmoz JDR (PNG)',
            'description' => 'Logo du projet, fond transparent, pour affiches, fiches et réseaux.',
            'group' => 'identite',
            'group_label' => 'Identité visuelle',
            'icon' => 'fa-image',
            'mime' => 'image/png',
            'generated' => false,
            'path' => 'images/logos/logo.png',
        ],
        [
            'key' => 'logo-full-png',
            'label' => 'Logo complet (PNG)',
            'description' => 'Variante large du logo, avec le nom du jeu.',
            'group' => 'identite',
            'group_label' => 'Identité visuelle',
            'icon' => 'fa-image',
            'mime' => 'image/png',
            'generated' => false,
            'path' => 'images/logos/logo_full.png',
        ],
    ],
];
