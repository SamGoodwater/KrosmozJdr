<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Marin·e.
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
                    'name' => 'Pied marin',
                    'type' => $guaranteed,
                    'effect' => 'Sur un pont, une jetée, un sol qui bouge, tu ignores le malus d’équilibre du terrain agité. Pas un bonus en donjon stable. Permanent tant que tu es sur ce sol.',
                ],
                [
                    'name' => 'Lire le gréement',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : 10 minutes, tu sais si un navire, un pont de corde ou un filet va tenir la journée. Un défaut évident, pas une fiche complète. 1 PA en combat si tu touches le cordage.',
                ],
                [
                    'name' => 'Nœud qui tient',
                    'type' => $freeSlot,
                    'effect' => 'Tu attaches, hisses, répares un gréement simple en 10 minutes. Utile pour ponts de corde, filets, sangles. DD 10 hors combat, 2 PA en combat pour sangler une créature consentante ou déjà maîtrisée.',
                ],
                [
                    'name' => 'Gourde salée',
                    'type' => $freeSlot,
                    'effect' => 'Tu trouves de l’eau ou du poisson pour le groupe en 1 heure de côte ou de pont. Suffisant pour un repos, pas pour une flotte.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'Tu nages. Tu lis la houle. L’estomac suit.',
            'capacities' => [
                [
                    'name' => 'Lire la houle',
                    'type' => $guaranteed,
                    'effect' => '10 minutes d’observation : tu prévois météo côtière, courant, ou si un navire à l’horizon file ou chasse. Pas une divination magique. 1×/scène.',
                ],
                [
                    'name' => 'À l’eau',
                    'type' => $freeSlot,
                    'effect' => 'Tu nages longtemps (avantage à Athlétisme dans l’eau). Tu aides un·e allié·e à ne pas couler (1 PA en combat, tu partages ta case). Tu récupères un objet à faible profondeur hors combat (1 minute).',
                ],
                [
                    'name' => 'Écope',
                    'type' => $freeSlot,
                    'effect' => 'Tu vides, colmates ou tiens une voie d’eau le temps d’une scène. Bateau, cale, cave inondée. 1×/scène. Ça ne répare pas la coque.',
                ],
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
                    'effect' => 'Une fois par scène, tu donnes un ordre clair à un petit groupe (hisser, abattre, larguer, reculer un chariot, tenir une porte). Jusqu’à 6 créatures qui t’entendent : +1 à leur prochain jet lié à cet ordre. 1 PA.',
                ],
                [
                    'name' => 'Sondeur d’épave',
                    'type' => $freeSlot,
                    'effect' => 'Tu estimes si une épave, une cale, une crique cache de l’air, du Wakfu pourri, ou un trou qui aspire. 10 minutes. Pas un sort de détection magique complet (Érudit).',
                ],
                [
                    'name' => 'Voix de vigie',
                    'type' => $freeSlot,
                    'effect' => 'Un cri porte à 300 m sur l’eau, 60 m en ville. Tes allié·e·s qui l’entendent savent où tu es. 1×/scène.',
                ],
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
                [
                    'name' => 'Grappin de fortune',
                    'type' => $freeSlot,
                    'effect' => 'Tu lances un grappin (6 cases). 2 PA. Accroche un rebord, un navire, une créature Grande ou plus (jet d’Athlétisme contre elle). 1×/tour.',
                ],
                [
                    'name' => 'Filet lancé',
                    'type' => $freeSlot,
                    'effect' => 'Tu entangles une créature à 3 cases (jet d’Athlétisme). Hors combat : tu ramènes un colis à la surface. 2 PA, 1×/scène.',
                ],
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
                [
                    'name' => 'Cale sèche improvisée',
                    'type' => $freeSlot,
                    'effect' => 'Tu répares un navire ou un gros engin (roue, gouvernail, pompe) en 8 heures, avec des ressources. Ici, c’est le bateau, pas l’épée (voir Artisan·e).',
                ],
                [
                    'name' => 'Quille qui tient',
                    'type' => $freeSlot,
                    'effect' => '10 minutes : tu improvises une réparation de coque, de rames ou de cordage qui tient jusqu’au prochain port. Ensuite ça lâche.',
                ],
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
                [
                    'name' => 'Capitaine d’occasion',
                    'type' => $freeSlot,
                    'effect' => 'Tu prends le commandement d’un équipage PNJ le temps d’une traversée. Moral, mutinerie, manœuvre : le MJ joue l’équipage, toi tu as les leviers. Avantage aux tests de Chance pour les faire tenir.',
                ],
                [
                    'name' => 'Moussaillon d’un jour',
                    'type' => $freeSlot,
                    'effect' => 'Tu donnes un ordre de pont à jusqu’à six personnes. Avantage à leur prochain test d’Athlétisme ou d’Acrobaties lié au navire. Une scène, 1×/jour.',
                ],
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
                [
                    'name' => 'Cale de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies une cale sèche pour 8 heures : outils, pompe, silence de la houle. Rien de créé ne survit. 1 réserve de Wakfu, 1×/jour.',
                ],
                [
                    'name' => 'Chaloupe de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies une embarcation pour six, 8 heures, eau calme. Elle n’affronte pas la tempête. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
