<?php

declare(strict_types=1);

/**
 * Plan de réalignement des spécialisations legacy sur les paliers de 2.4.2.
 *
 * Les exports HTML portent entre 0 et 8 bonus nommés répartis sur l'ancienne
 * grille. Les règles n'en autorisent plus que 3, acquis sans choix aux paliers
 * 3, 9 et 15 : ce fichier dit lesquels sont retenus comme aptitudes et à quel
 * palier. Tous les autres restent des capacités proposées à leur palier.
 *
 * Clé = nom du bonus tel qu'il apparaît dans le HTML legacy (avant inversion du
 * vocabulaire). Valeur = palier d'aptitude cible.
 *
 * @return array<string, array{aptitudes?: array<string, int>, authored?: array<int, array{name: string, html: string}>, replacements?: array<string, string>}>
 */

return [
    // Spectacle ambulant et Touche-à-tout sont déjà au bon palier ; Manipulateur
    // subtil couvre le palier 15. Voyageur, Rituel, Chant reposant et Orateur
    // exceptionnel redeviennent des capacités.
    'artiste' => [
        'aptitudes' => [
            'Spectacle ambulant' => 3,
            'Touche-à-tout' => 9,
            'Manipulateur subtil' => 15,
        ],
        'replacements' => [
            // Manipulateur subtil était rédigé « Au niveau 14 » alors qu'il est
            // acquis au palier 15.
            'Au niveau 14, lorsqu' => 'Lorsqu',
        ],
    ],

    // Refuge du pèlerin remonte du niveau 1 au palier 3, Médecine de terrain
    // descend de 10 à 9 et Haranguer les foules de 13 à 15. Aspect de l'avatar
    // reste la capacité forte du palier 20.
    'devot' => [
        'aptitudes' => [
            'Refuge du pèlerin' => 3,
            'Médecine de terrain' => 9,
            'Haranguer les foules' => 15,
        ],
    ],

    'erudit' => [
        'aptitudes' => [
            'Politicien' => 3,
            'Mémoire des formules' => 9,
            'Expertise en wakfu' => 15,
        ],
    ],

    // L'export legacy de l'Explorateur·rice n'a aucun bonus nommé en dehors de
    // Rituel : les deux autres aptitudes sont écrites ici.
    'explorateur_rice' => [
        'aptitudes' => [
            'Rituel' => 9,
        ],
        'authored' => [
            3 => [
                'name' => 'Jamais vraiment perdu',
                'html' => '<p>Tant que vous voyagez à l’air libre, vous savez toujours dans quelle direction vous allez et depuis combien de temps vous marchez. Vous ne pouvez pas être égaré·e par un terrain difficile, une nuit sans étoiles ou un itinéraire volontairement brouillé, sauf magie explicite.</p>',
            ],
            15 => [
                'name' => 'Œil de l’éclaireur·euse',
                'html' => '<p>En terrain naturel, vous repérez sans jet les traces de passage récentes, les campements abandonnés et les pièges non dissimulés par magie. Le groupe ne peut pas être surpris tant que vous êtes éveillé·e et en mesure de voir.</p>',
            ],
        ],
    ],

    // Frères d'armes remonte du niveau 1 au palier 3 pour laisser Défense en
    // phalange comme capacité. Position d'autorité et Général tombent déjà sur
    // les paliers 9 et 15.
    'milicien_ne' => [
        'aptitudes' => [
            'Frères d\'armes' => 3,
            'Position d\'autorité' => 9,
            'Général' => 15,
        ],
    ],

    // Argot des voleurs remonte du niveau 1 au palier 3 et Insaisissable
    // descend de 18 à 15.
    'voleur_euse' => [
        'aptitudes' => [
            'Argot des voleurs' => 3,
            'Furtivité suprême' => 9,
            'Insaisissable' => 15,
        ],
    ],
];
