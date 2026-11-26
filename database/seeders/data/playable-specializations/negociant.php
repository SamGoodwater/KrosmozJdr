<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Négociant·e.
 *
 * Signature : Cote du jour. Les autres capacités sont des liens catalogue
 * (réutilisées sur plusieurs spés), dont Connais ta cible au niveau 3.
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
    'name' => 'Négociant·e',
    'importPageSlug' => 'import-specialization-negociant-e',
    'importPageTitle' => 'Spécialisation Négociant·e',
    'sectionSlugPrefix' => 'import-negociant-e',
    'shortDescription' => 'Spécialisation axée sur le commerce, la persuasion et les réseaux.',
    'description' => 'Les kamas, c’est de l’information et des faveurs. Tu sais ce que vaut un objet, qui le veut, et ce que l’autre peut vraiment payer. Comptoir d’Astrub, caravane, dettes de Brâkmar : tu échanges — parfois proprement, parfois moins.',
    'identity' => 'En tant que Négociant·e, tu n’es pas forcément riche. Tu transformes une conversation en accord, un accord en route. L’Artiste émeut, le Voleur prend, toi tu échanges. On te cherche pour le contrat, pas pour le spectacle.',
    'focus' => 'Commerce, persuasion, affaires, réseaux',
    'characteristics' => 'Chance (principale). Intelligence pour lire un registre et un mensonge chiffré.',
    'idealFor' => 'Enutrof, Ecaflip, Crâ, tout personnage social qui veut un métier hors des sorts de classe.',
    'difference' => 'Ce n’est pas la spécialisation « je suis riche ». Un Iop Négociant·e peut être fauché et redoutable en marchandage. Tu ne remplaces pas l’Artiste pour tenir une salle, ni le Voleur pour crocheter.',
    'synergies' => [
        'Enutrof' => 'Le classique : chasse au trésor et rachat au meilleur prix.',
        'Roublard' => 'Marché gris, dettes, chantage léger — sans fusionner avec Voleur·euse.',
        'Féca' => 'Logistique, convois, protections de caravane.',
        'Crâ' => 'Messager·ère, contrat de chasse, prime.',
    ],
    'styles_at' => 3,
    'styles' => [
        'comptoir' => [
            'name' => 'Comptoir',
            'blurb' => 'L’étal, la balance, le prix du jour. Tu sais ce que la pièce vaut vraiment.',
            'save' => 'l’Intelligence',
        ],
        'caravanier' => [
            'name' => 'Caravanier',
            'blurb' => 'La caisse part. Elle arrive. Tu sais si la ville d’après peut payer.',
            'save' => 'la Vitalité',
        ],
        'courtier' => [
            'name' => 'Courtier',
            'blurb' => 'Tu ne tiens pas l’étal. Tu trouves qui veut, qui paie, et tu prends la commission.',
            'save' => 'la Sagesse',
        ],
        'marche-gris' => [
            'name' => 'Marché gris',
            'blurb' => 'Le nom n’apparaît pas. Le prix non plus. Tu es déjà parti quand on demande qui a vendu.',
            'save' => 'l’Agilité',
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu as déjà un carnet, une réputation minuscule, et l’habitude de demander « et le prix ami·e ? ».',
            'masteries' => [
                'Outils' => 'Matériel de marchand : balance, carnet, sceau.',
                'Jets de sauvegarde' => 'Chance, Intelligence.',
                'Compétences' => 'Choisis-en trois parmi : Persuasion, Supercherie, Perspicacité, Investigation, Histoire.',
                'Métiers' => 'Aucun métier d’artisanat offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Une langue régionale ou commerciale au choix (section 4.1.4).',
            ],
            'capacities' => [
                [
                    'name' => 'Cote du jour',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Dans un marché, tu évalues le prix juste d’un objet courant sans jet. Pour une pièce Rare ou plus : avantage au test d’Intelligence. 10 minutes, ou 1 PA depuis la réserve de Wakfu en combat si l’objet est sous tes yeux.',
                ],
                $link('Compréhension des langues'),
                $link('Suggestion'),
                $link('Message'),
                $link('Zone de vérité'),
                $link('Détection des pensées'),
                $link('Assistance'),
            ],
        ],
        3 => [
            'unlock_style' => true,
            'choice' => $choices[3],
            'flavor' => 'On te reçoit derrière le comptoir. Pas encore dans le bureau.',
            'capacities' => [
                [
                    'name' => 'Passe-droit mineur',
                    'type' => $guaranteed,
                    'effect' => 'Tu obtiens un rendez-vous, une autorisation de stand, un laissez-passer de quartier. Pas un accès de donjon. 10 minutes de palabre, 1×/lieu/jour.',
                ],
                $link('Connais ta cible'),
                $link('Compréhension des langues'),
                $link('Suggestion'),
                $link('Message'),
                $link('Zone de vérité'),
                $link('Apaisement des émotions'),
                $link('Charme personne')
            ],
            'aptitudes' => [
                [
                    'name' => 'Remise de comptoir',
                    'type' => 'Contextuelle (achat en ville)',
                    'effect' => '−10 % sur le matériel courant (Commun, Peu commun) chez un marchand qui te parle. Pas le légendaire, pas les PNJ uniques, une fois par boutique et par jour.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Un refus n’est plus une fin. Tu relances, tu lis qui décide vraiment.',
            'capacities' => [
                [
                    'name' => 'Contre-offre',
                    'type' => $guaranteed,
                    'effect' => 'Quand on te propose un prix ou un marché, tu forces un second round même après un refus. Une fois par interlocuteur et par jour. Le deuxième jet n’a pas l’avantage : il a le droit d’exister.',
                ],
                $link('Détection des pensées'),
                $link('Suggestion'),
                $link('Zone de vérité'),
                $link('Message'),
                $link('Envoi de message'),
                $link('Divination'),
                $link('Charme personne')
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Le papier commence à valoir plus que la poignée de main. Tu écris pour que ça tienne.',
            'capacities' => [
                [
                    'name' => 'Contrat clair',
                    'type' => $guaranteed,
                    'effect' => 'Tu rédiges un accord que les deux parties comprennent (10 minutes). Tricher ensuite laisse des traces : réputation, guildes, un PNJ qui s’en souvient. Utile pour le MJ, pas un sort de geas.',
                ],
                $link('Scrutation'),
                $link('Divination'),
                $link('Localiser un objet'),
                $link('Connais ta cible'),
                $link('Image majeure'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Oreilles partout',
                    'type' => 'Passive',
                    'effect' => 'Une rumeur de marché (prix, pénurie, prime) te tombe dessus quand le groupe arrive en ville. Le MJ choisit laquelle. Pas une divination : c’est le bruit du comptoir.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Tu ne vends plus un objet. Tu vends un trajet, une faveur, une peur polie.',
            'capacities' => [
                [
                    'name' => 'Caravane',
                    'type' => $guaranteed,
                    'effect' => 'Tu organises un transport (biens, personnes, message) entre deux villes. Coût, délais, risques de route : le MJ pose une table courte. 1 jour de préparation. 1×/semaine.',
                ],
                $link('Envoi de message'),
                $link('Suggestion'),
                $link('Détection des pensées'),
                $link('Divination'),
                $link('Scrutation'),
                $link('Métamorphose'),
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'On te cherche pour l’impossible. Tu prends une commission. Tu ne promets pas l’Unique.',
            'capacities' => [
                [
                    'name' => 'Courtier d’impossible',
                    'type' => $guaranteed,
                    'effect' => 'Tu trouves acheteur ou vendeur pour un objet Très rare, en 1d4 jours, avec 10 % de commission. Pas de garantie sur l’Unique. 1×/mois.',
                ],
                $link('Scrutation'),
                $link('Divination'),
                $link('Localiser un objet'),
                $link('Connais ta cible'),
                $link('Zone de vérité'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Réputation de place',
                    'type' => 'Contextuelle (grande ville)',
                    'effect' => 'À Bonta, Brâkmar, Sufokia, Astrub : on te reçoit. Avantage aux tests de Chance pour obtenir une audience. Obligations en face : faveurs à rendre, pas un bonus au marché aux poissons de village.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, tu l’as déjà signé.',
            'capacities' => [
                [
                    'name' => 'La place te doit un service',
                    'type' => $guaranteed,
                    'effect' => 'Un marché, une ville ou une compagnie te doit une faveur majeure (sauf suicide politique). Avec le MJ, une fois. Ça change une campagne, pas un jet de dégâts.',
                ],
                $link('Divination'),
                $link('Scrutation'),
                $link('Suggestion'),
                $link('Métamorphose'),
                $link('Oeil magique'),
            ],
        ],
    ],
];