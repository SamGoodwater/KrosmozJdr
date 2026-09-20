<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : l’Artiste.
 *
 * @return array<string, mixed>
 */
$guaranteed = 'garantie';
$freeSlot = 'choix';

$choices = [
    1 => '3 compétences',
    3 => '1 compétence',
    6 => '1 compétence',
    9 => '1 compétence (expertise possible)',
    12 => '1 compétence',
    15 => '1 compétence (expertise possible)',
    20 => '1 compétence (expertise possible)',
];

return [
    'playable' => true,
    'name' => 'Artiste',
    'importPageSlug' => 'import-specialization-artiste',
    'importPageTitle' => 'Spécialisation Artiste',
    'sectionSlugPrefix' => 'import-artiste',
    'shortDescription' => 'Spécialisation axée sur la performance, le spectacle et la créativité.',
    'description' => 'Tu vis de la scène : une voix, un geste, une toile, un mensonge élégant. Taverne d’Astrub, cour de Bonta, rue de Brâkmar : on te paie pour regarder, et parfois pour oublier.',
    'identity' => 'En tant qu’Artiste, tu n’es pas un Ecaflip de plus. Tes capacités servent à tenir une salle, à inspirer, à faire croire. On vient te chercher pour le spectacle, pas pour un grimoire d’illusions recopié.',
    'focus' => 'Performance, inspiration, création, salles et rues',
    'characteristics' => 'Chance (principale). Sagesse pour lire la salle.',
    'idealFor' => 'Ecaflip, Eniripsa, Pandawa, Roublard ; tout personnage qui veut un métier de scène hors des sorts de classe.',
    'difference' => 'L’Artiste n’est pas un ensorceleur de masse, ni un Courtisan. Tes capacités tiennent sur une scène, une chanson, un déguisement. Tu ne remplaces pas le Voleur pour crocheter, ni le Négociant pour signer.',
    'synergies' => [
        'Ecaflip' => 'Le dé et la scène : tu joues, et la salle joue avec toi.',
        'Eniripsa' => 'La voix qui soigne et celle qui fait rire : deux publics.',
        'Pandawa' => 'La taverne est déjà une scène. Tu n’as qu’à lever le verre.',
        'Roublard' => 'Poudre, détour, entrée : le spectacle peut aussi faire mal.',
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses un instrument, un masque, un carnet. Le premier geste, c’est de faire taire la salle une seconde.',
            'masteries' => [
                'Outils' => 'Un instrument, un masque, des pigments ou un carnet. Selon ta forme : marionnettes, cartes, voix seule.',
                'Jets de sauvegarde' => 'Chance, Sagesse.',
                'Compétences' => 'Choisis-en trois parmi : Représentation, Persuasion, Supercherie, Escamotage.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus.',
            ],
            'capacities' => [
                [
                    'name' => 'Inspiration artistique',
                    'type' => $guaranteed,
                    'effect' => 'Tu inspires une créature qui t’entend : +1d4 au prochain jet d’attaque, de sauvegarde ou de compétence. Jusqu’à la fin de son prochain tour. 1 PA, 1×/tour. Pas de cumul.',
                ],
                [
                    'name' => 'Performance',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : 10 minutes de scène (chanson, conte, geste). La salle t’écoute. Avantage au prochain test de Chance pour obtenir un toit, un repas, un silence ou une information légère. 1×/scène.',
                ],
                [
                    'name' => 'Illusion de scène',
                    'type' => $freeSlot,
                    'effect' => 'Un son ou une image petite (un objet, une voix, une flamme) dans 6 cases, une minute. Ça ne blesse pas. Un examen attentif (Perspicacité) la perce. 1 PA en combat.',
                ],
                [
                    'name' => 'Affiche magique',
                    'type' => $freeSlot,
                    'effect' => 'Tu écris une phrase sur une surface. Elle reste une scène, lisible seulement pour qui tu nommes, ou pour tout le monde. Une page. Les codes restent des codes.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On te reconnaît d’une ville à l’autre. Le tapis roule tout seul.',
            'capacities' => [
                [
                    'name' => 'Déguisement de scène',
                    'type' => $guaranteed,
                    'effect' => 'En 10 minutes tu changes visage, voix, habits. Ça tient une scène, ou 1 heure si tu ne parles pas trop. Un test de Supercherie contre Perspicacité si on te colle. 1 réserve de Wakfu pour le tenir en combat (1 PA).',
                ],
                [
                    'name' => 'Éclat de rire',
                    'type' => $freeSlot,
                    'effect' => 'Une créature qui t’entend et te voit : jet de Sagesse ou elle perd son action à rire. 2 PA, 1×/scène. Les bêtes et les morts-vivants s’en fichent.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Spectacle ambulant',
                    'type' => 'Contextuelle (ville, taverne)',
                    'effect' => 'En ville ou dans une auberge, tu trouves toujours une scène, un repas et un lit contre une représentation. Avantage aux tests de Chance pour qu’on t’invite, pas pour qu’on te paie en kamas rares.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Tu ne forces plus. Tu accroches, et les gens viennent.',
            'capacities' => [
                [
                    'name' => 'Accroche',
                    'type' => $guaranteed,
                    'effect' => 'Une créature qui t’entend et te comprend : tu lui souffles une action simple et raisonnable (approche, attends, regarde ailleurs). Jet de Sagesse pour résister. 2 PA, 1×/scène. Pas de suicide, pas de trahison évidente.',
                ],
                [
                    'name' => 'Image de salle',
                    'type' => $freeSlot,
                    'effect' => 'Une image ou un décor dans un cube de 4 cases, une scène. Ça ne blesse pas. Un examen attentif la perce. 1 réserve de Wakfu.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tu sais faire un peu de tout ce qui brille. Assez pour passer.',
            'capacities' => [
                [
                    'name' => 'Visage d’emprunt',
                    'type' => $guaranteed,
                    'effect' => 'Tu prends l’apparence d’une personne vue. Une heure, ou une scène en combat (2 PA). Les proches ont avantage pour te percer. 1 réserve de Wakfu.',
                ],
                [
                    'name' => 'Rêvevin',
                    'type' => $freeSlot,
                    'effect' => 'Tu imprègnes une bouteille. Jusqu’à huit créatures qui boivent partagent un souvenir, un rêve ou un message d’une minute. Hors combat. 1×/jour.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Touche-à-tout',
                    'type' => 'Passive',
                    'effect' => 'Quand tu tentes un test d’outil ou de compétence que tu ne maîtrises pas, tu n’as pas le désavantage du « jamais vu ». Tu n’as pas l’expertise non plus : tu improvises juste assez.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'On raconte déjà des choses sur toi. Tu décides lesquelles restent.',
            'capacities' => [
                [
                    'name' => 'Légende',
                    'type' => $guaranteed,
                    'effect' => 'Tu nommes une personne, un lieu ou un objet connus. En 10 minutes tu extrais ce que la rumeur tient pour vrai — assez pour jouer, pas assez pour un dossier. 1×/scène.',
                ],
                [
                    'name' => 'Double de scène',
                    'type' => $freeSlot,
                    'effect' => 'Tu laisses une image de toi sur place et tu deviens discret (pas invisible magique : on te rate si on ne te cherche pas). 1 minute, 2 PA, 1×/scène.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Un geste, et la salle se lève. Un autre, et elle s’assoit.',
            'capacities' => [
                [
                    'name' => 'Festin des héros',
                    'type' => $guaranteed,
                    'effect' => 'Une heure de table : mets, boissons, chaleur. Jusqu’à 8 créatures. Chacune ignore un niveau de fatigue et gagne 1d4 PV temporaires jusqu’au prochain repos. 1 réserve de Wakfu, 1×/jour.',
                ],
                [
                    'name' => 'Salle entière',
                    'type' => $freeSlot,
                    'effect' => 'Jusqu’à six créatures qui t’entendent : même effet qu’Accroche, un seul jet pour le groupe (le plus haut). 3 PA, 1×/scène.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Manipulateur subtil',
                    'type' => 'Contextuelle (scène sociale)',
                    'effect' => 'Quand tu parles à une salle, une cour ou une table, tu as l’avantage aux tests de Chance (Persuasion, Supercherie) pour faire croire une émotion, pas un contrat. On peut encore te démasquer.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, tu l’as déjà chanté.',
            'capacities' => [
                [
                    'name' => 'Apothéose',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par jour, ta scène devient vraie pour une minute : les allié·e·s qui te voient ont avantage à tous leurs jets, et la première attaque contre toi rate. Ensuite, la salle se souvient — trop bien.',
                ],
                [
                    'name' => 'Théâtre de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies une salle pour 8 heures : planches, lumières, silence extérieur. Rien de créé ne survit. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
