<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Marin·e.
 *
 * Signature : Lire la houle (niveau 3). Capacités catalogue liées à l’eau et au camp.
 *
 * @return array<string, mixed>
 */
$guaranteed = 'garantie';
$freeSlot = 'choix';

$link = static function (string $name, string $slot = 'choix'): array {
    return [
        'name' => $name,
        'type' => $slot,
        'link' => true,
    ];
};

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
    'name' => 'Marin·e',
    'importPageSlug' => 'import-specialization-marin-e',
    'importPageTitle' => 'Spécialisation Marin·e',
    'sectionSlugPrefix' => 'import-marin-e',
    'shortDescription' => 'Spécialisation tournée vers la mer, les navires, l’équipage et Sufokia.',
    'description' => 'L’Explorateur·rice lit la carte ; toi, tu lis la houle. Sufokia, l’île de Moon, un pont qui penche : tu vis du large, pas du sentier. Pirate, pêcheur·euse, corsaire : tu sais ce qu’un bateau peut encaisser.',
    'identity' => 'En tant que Marin·e, tu as le sel dans les coutures et un nœud plus fiable qu’un serment de Brâkmar. Tes capacités servent à tenir un pont, à nager, à lire le ciel. On te cherche pour traverser, pas pour pister une forêt.',
    'focus' => 'Mer, navires, équipage, météo',
    'characteristics' => 'Agilité (principale). Chance pour lire un équipage et une tempête.',
    'idealFor' => 'Pandawa, Roublard, Enutrof, Steamer ; personnages de Sufokia, pirates, escortes de convois maritimes.',
    'difference' => 'L’Explorateur·rice oriente, piste, désamorce. Le Marin·e manœuvre un navire, lit le ciel, tient un équipage et nage quand ça coule. Tu ne remplaces pas l’Artisan·e pour forger une épée, ni le Courtisan pour un salon.',
    'synergies' => [
        'Pandawa' => 'Rhum, mer, équilibre douteux sur un pont mouillé.',
        'Roublard' => 'Abordage, contrebande, criques sans douane.',
        'Enutrof' => 'Chasse au trésor, cartes mouillées, épaves.',
        'Steamer' => 'Navire comme machine, abordage comme formation.',
    ],
    'styles_at' => 3,
    'styles' => [
        'matelot' => [
            'name' => 'Matelot',
            'blurb' => 'La corde, la manœuvre, les six bras qui tirent ensemble.',
            'save' => 'la Force',
        ],
        'timonier' => [
            'name' => 'Timonier',
            'blurb' => 'La barre, la passe, le ciel. Tu sais si la mer laisse passer.',
            'save' => 'la Sagesse',
        ],
        'corsaire' => [
            'name' => 'Corsaire',
            'blurb' => 'L’approche, l’angle mort, le grappin au bon moment.',
            'save' => 'la Chance',
        ],
        'pecheur-euse' => [
            'name' => 'Pêcheur·euse',
            'blurb' => 'Le filet, le banc, l’heure où l’on sort. Tu restes sur l’eau toute la journée.',
            'save' => 'la Vitalité',
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu reconnais un gréement pourri, un nœud qui va lâcher, une odeur de tempête. La mer n’est pas un décor : c’est un patron.',
            'masteries' => [
                'Outils' => 'Matériel de navigation : boussole, cartes marines, cordages.',
                'Jets de sauvegarde' => 'Agilité, Chance.',
                'Compétences' => 'Choisis-en trois parmi : Athlétisme, Acrobaties, Perception, Survie, Supercherie.',
                'Métiers' => 'Pêcheur ou Poissonnier au niveau 1. Pas d’artisanat d’armes offert.',
                'Langues' => 'Aucune langue bonus. Le jargon des ports, tu l’as déjà dans la bouche.',
            ],
            'capacities' => [
                [
                    'name' => 'Lire la houle',
                    'type' => $guaranteed,
                    'effect' => '10 minutes d’observation : tu prévois météo côtière, courant, ou si un navire à l’horizon file ou chasse. Pas une divination magique. 1×/scène.',
                ],
                $link('Respiration aquatique'),
                $link('Marche sur l\'eau'),
                $link('Alarme'),
                $link('Lumière'),
                $link('Message'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Pied marin',
                    'type' => 'Passive',
                    'effect' => 'Sur un pont, une jetée, un sol qui bouge, tu ignores le malus d’équilibre du terrain agité. Pas un bonus en donjon stable.',
                ],
            ],
        ],
        3 => [
            'unlock_style' => true,
            'choice' => $choices[3],
            'flavor' => 'Tu nages. Tu lis la houle. L’estomac suit.',
            'capacities' => [
                [
                    'name' => 'Quart selon toi',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par scène, tu donnes un ordre de manœuvre : jusqu’à six personnes qui t’entendent ont +1 au prochain test de navire (hisser, tenir, larguer).',
                ],
                $link('Respiration aquatique'),
                $link('Marche sur l\'eau'),
                $link('Alarme'),
                $link('Localiser une créature'),
                $link('Petite hutte'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Estomac de mer',
                    'type' => 'Passive',
                    'effect' => 'Tu ignores mal de mer, roulis, et la fatigue des veilles en mer. En carrosse : optionnel. En donjon : rien.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Un cri, et six personnes bougent dans le même sens. Ça marche aussi pour un chariot.',
            'capacities' => [
                [
                    'name' => 'Cri de pont',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Une fois par scène, tu donnes un ordre clair à un petit groupe (hisser, abattre, larguer, reculer un chariot, tenir une porte). Jusqu’à 6 créatures qui t’entendent : +1 à leur prochain jet lié à cet ordre. 1 PA depuis la réserve de Wakfu.',
                ],
                $link('Respiration aquatique'),
                $link('Marche sur l\'eau'),
                $link('Localiser une créature'),
                $link('Alarme'),
                $link('Assistance'),
                $link('Aide'),
                $link('Bourrasque')
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tu vois la voile avant les autres. Tu prépares l’abordage comme un plan, pas comme une charge.',
            'capacities' => [
                [
                    'name' => 'Abordage calculé',
                    'type' => $guaranteed,
                    'effect' => 'Tu prépares une approche (angles morts, grappins, qui tient la barre). Le groupe a avantage au premier jet de la scène d’abordage ou d’intrusion par les toits. 10 minutes de préparation, 1×/scène.',
                ],
                $link('Localiser une créature'),
                $link('Voir l\'invisible'),
                $link('Alarme'),
                $link('Petite hutte'),
                $link('Lévitation'),
                $link('Vol'),
                $link('Bourrasque')
            ],
            'aptitudes' => [
                [
                    'name' => 'Yeux de vigie',
                    'type' => 'Contextuelle (horizon / mer)',
                    'effect' => 'Avantage à la Perception pour voiles, récifs, silhouettes sur l’eau. En forêt dense : annulé.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'La tempête n’est plus une fin. C’est une manœuvre. Le bateau encaisse.',
            'capacities' => [
                [
                    'name' => 'Tenir la barre',
                    'type' => $guaranteed,
                    'effect' => 'Pendant une tempête ou une poursuite navale, tu transformes un échec collectif en « on encaisse, on ne chavire pas ». Une fois par traversée. Le MJ pose le prix (dégâts au navire, retard).',
                ],
                $link('Respiration aquatique'),
                $link('Marche sur l\'eau'),
                $link('Liberté de mouvement'),
                $link('Petite hutte'),
                $link('Divination'),
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Tu connais une passe que les cartes n’ont pas. Tu prends la barre d’un équipage qui n’est pas le tien.',
            'capacities' => [
                [
                    'name' => 'Route secrète',
                    'type' => $guaranteed,
                    'effect' => 'Tu connais une passe, un brouillard, un horaire de douane qui raccourcit ou cache une traversée. Le MJ pose le prix (temps, bakchich, risque). 1×/arc.',
                ],
                $link('Localiser une créature'),
                $link('Scrutation'),
                $link('Alarme'),
                $link('Petite hutte'),
                $link('Liberté de mouvement'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Langue des quais',
                    'type' => 'Contextuelle (ports)',
                    'effect' => 'Marins, contrebandiers, pêcheurs te parlent comme à un pair. Avantage social dans les ports, pas à la cour de Bonta (voir Courtisan·e).',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, la mer s’en souvient.',
            'capacities' => [
                [
                    'name' => 'Légende des quais',
                    'type' => $guaranteed,
                    'effect' => 'Un port, une flotte ou un vieux capitaine te doit une traversée impossible (blocus, tempête, île taboue). Avec le MJ, une fois. Pas un sort de contrôle du climat (Érudit haut niveau).',
                ],
                $link('Respiration aquatique'),
                $link('Marche sur l\'eau'),
                $link('Divination'),
                $link('Scrutation'),
                $link('Liberté de mouvement'),
            ],
        ],
    ],
];