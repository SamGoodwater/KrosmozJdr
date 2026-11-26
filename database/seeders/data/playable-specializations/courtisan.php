<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Courtisan·e.
 *
 * Signature : Le bon titre. Capacités catalogue sociales et de vérité.
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
    'name' => 'Courtisan·e',
    'importPageSlug' => 'import-specialization-courtisan-e',
    'importPageTitle' => 'Spécialisation Courtisan·e',
    'sectionSlugPrefix' => 'import-courtisan-e',
    'shortDescription' => 'Spécialisation tournée vers les cours, l’étiquette et l’intrigue politique.',
    'description' => 'Le Négociant·e tient le comptoir ; toi, le salon. Bonta, Brâkmar, un couloir trop silencieux : tu vis de l’étiquette et des non-dits. Noble fauché·e, secrétaire, espion·ne en habit clair : le palais est ton donjon.',
    'identity' => 'En tant que Courtisan·e, tu sais qui saluer en premier, qui ne jamais contredire en public, et quelle rumeur faire circuler pour qu’elle arrive « toute seule ». Tes capacités servent à entrer, à lire, à faire perdre la face. On te cherche pour l’invitation, pas pour le crochetage.',
    'focus' => 'Cours, étiquette, intrigue, réputation',
    'characteristics' => 'Chance (principale). Intelligence pour retenir qui doit quoi à qui.',
    'idealFor' => 'Eniripsa, Sram, Féca, Ecaflip, Zobal ; personnages alignés Bonta ou Brâkmar, espions mondains.',
    'difference' => 'Le Négociant·e vend et achète. L’Artiste monte sur scène. Le Voleur·euse s’infiltre par les toits. Le Courtisan·e s’infiltre par l’invitation, tue par le protocole, et perd tout si on le·la démasque en public.',
    'synergies' => [
        'Sram' => 'Le poignard sous la cape, mais la cape est brodée.',
        'Eniripsa' => 'Soin, image, parole qui porte à la cour.',
        'Féca' => 'Garde du corps officiel, bouclier diplomatique.',
        'Ecaflip' => 'Paris sociaux, réputation qui bascule en une phrase.',
    ],
    'styles_at' => 3,
    'styles' => [
        'secretaire' => [
            'name' => 'Secrétaire',
            'blurb' => 'Le protocole est écrit. Tu sais l’ordre des salutations, et qui ne jamais contredire en public.',
            'save' => 'l’Intelligence',
        ],
        'emissaire' => [
            'name' => 'Émissaire',
            'blurb' => 'Tu parles pour quelqu’un. Refuser de t’entendre est déjà une offense.',
            'save' => 'la Force',
        ],
        'favori-te' => [
            'name' => 'Favori·te',
            'blurb' => 'Tu te tiens auprès du pouvoir. Tu vois qui ment avant la fin de la phrase.',
            'save' => 'la Sagesse',
        ],
        'espion-ne' => [
            'name' => 'Espion·ne',
            'blurb' => 'L’habit est clair, l’oreille est derrière le rideau. Tu es déjà dans le couloir d’à côté.',
            'save' => 'l’Agilité',
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu as appris à sourire sans montrer les dents. Un salon, c’est un champ de bataille où l’on ne sort pas l’épée — pas tout de suite.',
            'masteries' => [
                'Outils' => 'Trousse de toilette, sceaux, carnet de protocoles. Un jeu de société de cour au choix.',
                'Jets de sauvegarde' => 'Chance, Intelligence.',
                'Compétences' => 'Choisis-en trois parmi : Persuasion, Perspicacité, Supercherie, Histoire, Représentation.',
                'Métiers' => 'Aucun métier d’artisanat offert. La cour n’est pas un établi.',
                'Langues' => 'Une langue de cour ou de capitale au choix (section 4.1.4).',
            ],
            'capacities' => [
                [
                    'name' => 'Le bon titre',
                    'type' => $guaranteed,
                    'effect' => 'Tu formules une adresse, un salut, une excuse protocolaire. Avantage pour entrer dans un lieu « sur invitation ». Succès automatique sur les cas simples (garde ennuyé, liste mal tenue). Pas un laissez-passer de donjon militaire.',
                ],
                $link('Déguisement'),
                $link('Message'),
                $link('Suggestion'),
                $link('Détection des pensées'),
                $link('Apaisement des émotions'),
                $link('Zone de vérité'),
            ],
        ],
        3 => [
            'unlock_style' => true,
            'choice' => $choices[3],
            'flavor' => 'Une rumeur part. Une invitation de rechange te sort d’un huis clos.',
            'capacities' => [
                [
                    'name' => 'Rumeur légère',
                    'type' => $guaranteed,
                    'effect' => 'Tu plantes une information anodine qui circule d’ici le soir. Pas une calomnie de fin de campagne. Le MJ décide qui l’entend. 1×/jour.',
                ],
                $link('Suggestion'),
                $link('Message'),
                $link('Déguisement'),
                $link('Apaisement des émotions'),
                $link('Compréhension des langues'),
                $link('Charme personne')
            ],
            'aptitudes' => [
                [
                    'name' => 'Tenue correcte exigée',
                    'type' => 'Contextuelle (cours, salons, temples riches)',
                    'effect' => 'Tant que tu es présentable, avantage aux tests sociaux officiels. En haillons, en donjon, en forêt : annulé.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Tu forces un échange en public. L’autre doit répondre, ou perdre la face.',
            'capacities' => [
                [
                    'name' => 'Duel de mots',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Tu forces un échange social en public : l’autre doit répondre ou perdre la face. Remplace un jet d’Intimidation maladroit. 1 PA depuis la réserve de Wakfu si ça se joue en combat social. Pas un sort de charme.',
                ],
                $link('Zone de vérité'),
                $link('Suggestion'),
                $link('Détection des pensées'),
                $link('Message'),
                $link('Apaisement des émotions'),
                $link('Charme personne')
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tu joues un rôle. Les blasons, tu les as déjà dans la tête.',
            'capacities' => [
                [
                    'name' => 'Masque de cour',
                    'type' => $guaranteed,
                    'pa' => '3',
                    'effect' => 'Pendant une scène, tu joues un rôle social (allié·e d’une maison, neutre, envoyé·e). Les PNJ de rang croient le masque tant que personne ne te démasque avec des faits. 3 PA depuis la réserve de Wakfu.',
                ],
                $link('Déguisement'),
                $link('Détection des pensées'),
                $link('Suggestion'),
                $link('Illusion mineure'),
                $link('Image majeure'),
                $link('Zone de vérité'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Mémoire des blasons',
                    'type' => 'Passive',
                    'effect' => 'Tu retiens maisons, dettes d’honneur, alliances récentes. Avantage à Histoire et Perspicacité dès qu’il s’agit de « qui doit quoi à qui ».',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Le blâme glisse. L’oreille derrière le rideau, c’est encore de la présence, pas un crochet.',
            'capacities' => [
                [
                    'name' => 'Scandale dirigé',
                    'type' => $guaranteed,
                    'effect' => 'Tu fais porter le blâme sur quelqu’un d’autre — ou tu l’évites de justesse. Arc social, pas un combat. 1×/arc. Abuse, et la cour se ferme.',
                ],
                $link('Message'),
                $link('Envoi de message'),
                $link('Suggestion'),
                $link('Détection des pensées'),
                $link('Apaisement des émotions'),
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Tu pèses sur une décision de rang. Tu survis à une purge en te recasant.',
            'capacities' => [
                [
                    'name' => 'Conseil du prince',
                    'type' => $guaranteed,
                    'effect' => 'Tu pèses sur une décision d’un PNJ de rang (milice, temple, maison). Une fois par arc, avec le MJ. Échec possible, et ça se sait.',
                ],
                $link('Zone de vérité'),
                $link('Scrutation'),
                $link('Divination'),
                $link('Détection des pensées'),
                $link('Suggestion'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Nom qui ouvre',
                    'type' => 'Contextuelle (Bonta, Brâkmar, capitales)',
                    'effect' => 'On te reçoit. Avantage aux tests de Chance pour obtenir une audience. Obligations en face : politesses, cadeaux, silences. Pas un bonus au marché aux poissons (Négociant·e).',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, la ville s’en souvient.',
            'capacities' => [
                [
                    'name' => 'Une parole pour la ville',
                    'type' => $guaranteed,
                    'effect' => 'Tu fais basculer une décision publique (trêve, décret, mariage politique, embargo). Avec le MJ, une fois. Ça change la campagne, pas un jet de dégâts.',
                ],
                $link('Suggestion'),
                $link('Zone de vérité'),
                $link('Détection des pensées'),
                $link('Métamorphose'),
                $link('Image majeure'),
            ],
        ],
    ],
];