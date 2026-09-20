<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : l’Artisan·e.
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
    'name' => 'Artisan·e',
    'importPageSlug' => 'import-specialization-artisan-e',
    'importPageTitle' => 'Spécialisation Artisan·e',
    'sectionSlugPrefix' => 'import-artisan-e',
    'shortDescription' => 'Spécialisation tournée vers la fabrication, la réparation et les métiers.',
    'description' => 'Tu vis du geste et de la matière : recettes, réparations, œil pour un bon minerai. Guildes de Bonta, forges de Brâkmar, établi de village : un métier, tout le monde peut en apprendre ; toi, tu en fais une identité.',
    'identity' => 'En tant qu’Artisan·e, tu transformes le monde plutôt que de seulement le traverser. Tes capacités servent à estimer, réparer, inventer. On te cherche pour qu’un objet tienne, pas pour un 13ᵉ sort de classe.',
    'focus' => 'Fabrication, réparation, métiers, ateliers',
    'characteristics' => 'Intelligence (principale). Force pour la forge et le bois.',
    'idealFor' => 'Forgelance, Xélor, Enutrof, Steamer ; tout personnage qui veut vivre de ses mains hors des sorts de classe.',
    'difference' => 'Un personnage peut apprendre jusqu’à 6 métiers sans cette spécialisation (section 4.3). L’Artisan·e n’est pas « quelqu’un qui a un métier » : c’est quelqu’un dont le métier change la table. Tu ne remplaces pas le Négociant pour vendre, ni le Forgelance pour frapper.',
    'synergies' => [
        'Forgelance' => 'Le forgeron de légende, lame et métier collés l’un à l’autre.',
        'Xélor' => 'Mécanismes, horloges, objets bizarres.',
        'Enutrof' => 'La chaîne complète : minerai, lingot, vente.',
        'Steamer' => 'La machine et l’établi : deux façons de faire tenir un objet.',
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses tes outils et tu choisis ton premier métier sérieux. Ce n’est pas encore de la magie : c’est de l’habitude.',
            'masteries' => [
                'Outils' => 'Outils d’un métier d’artisanat au choix (forge, bijouterie, couture, menuiserie…).',
                'Jets de sauvegarde' => 'Intelligence, Force.',
                'Compétences' => 'Choisis-en trois parmi : Investigation, Perception, Arcanes, Athlétisme, Histoire.',
                'Métiers' => 'Un métier d’artisanat au niveau 1 (Commun). La forgemagie n’est pas offerte.',
                'Langues' => 'Aucune langue bonus. Le jargon des guildes, tu l’apprends en travaillant.',
            ],
            'capacities' => [
                [
                    'name' => 'Œil du matériau',
                    'type' => $guaranteed,
                    'effect' => 'En 10 minutes (1 PA en combat), tu estimes rareté, défauts et usage d’une ressource ou d’un objet non magique. Tu ne lis pas les enchantements volontairement cachés.',
                ],
                [
                    'name' => 'Établi familier',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : une heure sur un vrai établi, tu fabriques un objet Commun de ton métier sans jet, pourvu que tu aies les ressources. Hors combat. 1×/jour.',
                ],
                [
                    'name' => 'Réparation de fortune',
                    'type' => $freeSlot,
                    'effect' => 'Avec tes outils, tu remets en état un objet cassé ou usé. Hors combat : 1 heure. En combat : 2 PA, l’objet tient jusqu’à la fin de la scène puis lâche. 1×/scène.',
                ],
                [
                    'name' => 'Marque d’atelier',
                    'type' => $freeSlot,
                    'effect' => 'Tu marques une pièce. Les gens de métier te reconnaissent. Avantage aux tests de Chance auprès d’une guilde qui a vu ton travail, désavantage si tu as vendu de la camelote.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On te laisse un établi. Tu commences à copier ce que tu as vu, pas seulement ce qu’on t’a appris.',
            'capacities' => [
                [
                    'name' => 'Esquisse de recette',
                    'type' => $guaranteed,
                    'effect' => 'Après 10 minutes sur un objet Commun ou Peu commun, tu tentes d’en tirer une recette imparfaite (test d’Intelligence, DD 12). Échec : les ressources de l’essai sont perdues. Aligné sur 4.3.3.',
                ],
                [
                    'name' => 'Main sûre',
                    'type' => $freeSlot,
                    'effect' => 'Une fois par jour, tu relances un test de fabrication ou de réparation raté. Un 1 naturel reste un 1.',
                ],
                [
                    'name' => 'Mesure juste',
                    'type' => $freeSlot,
                    'effect' => 'En 1 minute, tu estimes poids, volume, pièce manquante ou défaut d’assemblage d’un objet non magique. Un chiffre utile, pas une recette.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Réseau d’ateliers',
                    'type' => 'Contextuelle (ville)',
                    'effect' => 'En ville, tu trouves un établi et un contact de métier sans jet, sauf quartier vraiment hostile. Avantage pour emprunter des outils courants, pas pour qu’on te donne un artefact.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Tu n’es plus bloqué·e hors des murs. Tes outils tiennent dans un sac, et ta signature commence à circuler.',
            'capacities' => [
                [
                    'name' => 'Atelier portable',
                    'type' => $guaranteed,
                    'effect' => 'En 1 heure tu improvises un mini-atelier. Tu travailles hors ville avec un malus de −2 aux tests d’outils, plus le blocage « pas d’établi ». Tient jusqu’au prochain repos long.',
                ],
                [
                    'name' => 'Signature d’artisan',
                    'type' => $freeSlot,
                    'effect' => 'Tu signes une pièce. On te reconnaît d’une ville à l’autre. Avantage social auprès des guildes qui respectent le geste ; les faussaires qui copient ta marque, tu les vois au premier coup d’œil (pas de jet).',
                ],
                [
                    'name' => 'Coin et colle',
                    'type' => $freeSlot,
                    'effect' => 'Tu improvises une réparation qui tient une scène (porte, roue, attache). Ensuite ça lâche. 10 minutes hors combat, 2 PA en combat. 1×/scène.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tu n’ajoutes plus seulement une couche : tu changes ce que l’objet fait, un peu, pour une scène.',
            'capacities' => [
                [
                    'name' => 'Amélioration mineure',
                    'type' => $guaranteed,
                    'effect' => 'Hors combat, 10 minutes, tes outils : un objet gagne +1 à un jet d’attaque ou de compétence lié à son usage, jusqu’au prochain repos. Pas de cumul, pas de forgemagie. 1×/objet/jour.',
                ],
                [
                    'name' => 'Rien ne se perd',
                    'type' => $freeSlot,
                    'effect' => 'Quand tu fabriques, tu récupères la moitié des ressources communes d’un échec (arrondi inférieur). Les ingrédients rares et magiques, non.',
                ],
                [
                    'name' => 'Recette lue à l’envers',
                    'type' => $freeSlot,
                    'effect' => 'Après 10 minutes sur un objet Commun, tu nommes les ressources évidentes de sa fabrication. Pas les runes, pas le secret d’un maître.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Gestes économiques',
                    'type' => 'Passive',
                    'effect' => 'Le temps de fabrication de ton métier d’Artisan·e est réduit d’un quart (arrondi à l’avantage du joueur, minimum 10 minutes). Pas les deux : le temps, pas les ressources.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Tu tentes ce qui n’est pas encore dans le catalogue. Ça casse parfois. C’est le métier.',
            'capacities' => [
                [
                    'name' => 'Prototype',
                    'type' => $guaranteed,
                    'effect' => 'Tu tentes un objet d’une rareté au-dessus de ton niveau de métier (DD 16, ressources de la rareté visée). Échec critique : l’établi est hors service jusqu’à un repos long. Exception, pas une routine. 1×/semaine.',
                ],
                [
                    'name' => 'Réparer l’irréparable',
                    'type' => $freeSlot,
                    'effect' => 'Artefact fêlé, rune qui saute, mécanisme xélor : 8 heures, tes outils, un test d’Intelligence (DD 15). Succès : ça tient. Échec : tu n’aggrave pas. Le MJ tranche si c’est trop unique.',
                ],
                [
                    'name' => 'Ajustement d’usage',
                    'type' => $freeSlot,
                    'effect' => '10 minutes, tes outils : un objet change d’usage mineur jusqu’au prochain repos (une sangle devient poignée, une lame devient levier). Pas de bonus de combat. 1×/jour.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Une pièce, parfois, sort mieux que le catalogue. Tu le savais déjà. Maintenant tu le vises.',
            'capacities' => [
                [
                    'name' => 'Pièce de maître',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par mois, tu vises une finition « pièce de maître » sans dépendre du 20 naturel : l’objet Commun ou Peu commun que tu sors se comporte comme Rare pour un bonus mineur (au MJ). Coût : ressources de Rare.',
                ],
                [
                    'name' => 'Œuvre unique',
                    'type' => $freeSlot,
                    'effect' => 'Avec le MJ, tu conçois un objet Unique (pas une recette catalogue). Long, cher, mémorable. Une fois par campagne. Ce n’est pas un souhait.',
                ],
                [
                    'name' => 'Copie honnête',
                    'type' => $freeSlot,
                    'effect' => 'En une journée, tu copies l’apparence d’un objet Commun ou Peu commun que tu as sous les yeux. Ça n’a pas la magie de l’original. 1×/semaine.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Maître d’un geste',
                    'type' => 'Passive',
                    'effect' => 'Un métier d’artisanat que tu possèdes est traité comme +1 niveau pour la rareté maximale, plafonné à 5. Un seul métier, choisi quand tu gagnes cette aptitude. 8 heures d’étude pour en changer.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, tu l’as déjà forgé.',
            'capacities' => [
                [
                    'name' => 'Légende vivante de l’atelier',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par mois, tu sors un chef-d’œuvre : un objet de ton métier, rareté Rare, sans jet. Les allié·e·s qui portent quelque chose que tu as touché ce mois-ci ont +1 à un jet d’outil ou d’attaque d’arme, 1×/scène. Moins fort qu’un sort de niveau 20.',
                ],
                [
                    'name' => 'Le geste enseigné',
                    'type' => $freeSlot,
                    'effect' => 'En une semaine, tu transmets une technique à un personnage consentant : iel gagne un métier d’artisanat au niveau 1, ou +1 niveau s’iel l’a déjà (plafond 5). 1×/année.',
                ],
                [
                    'name' => 'Établi de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un mini-atelier pour 8 heures. Tes tests d’outils n’ont plus le malus « pas d’établi ». 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
