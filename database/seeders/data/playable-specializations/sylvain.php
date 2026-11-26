<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Sylvain·e.
 *
 * Signature : Territoire lu. Capacités liées aux animaux et au passage discret.
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
    'name' => 'Sylvain·e',
    'importPageSlug' => 'import-specialization-sylvain-e',
    'importPageTitle' => 'Spécialisation Sylvain·e',
    'sectionSlugPrefix' => 'import-sylvain-e',
    'shortDescription' => 'Spécialisation liée à la nature, aux animaux et aux milieux sauvages habités.',
    'description' => 'L’Explorateur·rice traverse la forêt ; toi, tu y vis. Les bêtes et les plantes sont un voisinage, pas du décor. Canopée, marais, lisière d’Astrub : tu demandes la permission avant de cueillir.',
    'identity' => 'En tant que Sylvain·e, tu es du côté des saisons, des meutes, des racines. Tes capacités servent à parler aux bêtes, à lire un territoire, à soigner avec ce qui pousse. On te cherche pour habiter un lieu, pas pour ne pas s’y perdre.',
    'focus' => 'Nature, animaux, plantes, territoires',
    'characteristics' => 'Sagesse (principale). Intelligence pour nommer ce qui pousse.',
    'idealFor' => 'Osamodas, Sadida, Eniripsa, Crâ ; tout personnage qui veut parler aux bêtes plus qu’aux milices.',
    'difference' => 'L’Explorateur·rice sait ne pas se perdre et désamorcer un piège. Le Sylvain·e sait qui habite l’endroit, quoi ne pas cueillir, et comment demander la permission à un sanglier. Tu ne voles pas l’Osamodas pour invoquer un tank.',
    'synergies' => [
        'Osamodas' => 'Invocations et langage animal dans le même sac.',
        'Sadida' => 'Plantes, poison, territoire vivant.',
        'Crâ' => 'Chasse, piste, arc — sans recoller Explorateur.',
        'Eniripsa' => 'Soins par les plantes plutôt que par les larmes.',
    ],
    'styles_at' => 3,
    'styles' => [
        'meute' => [
            'name' => 'Meute',
            'blurb' => 'Tu demandes. La bête répond, ou elle s’en va. Tu n’invoques pas un tank.',
            'save' => 'la Chance',
        ],
        'racine' => [
            'name' => 'Racine',
            'blurb' => 'Tu nommes ce qui pousse. Tu sais quoi laisser en terre.',
            'save' => 'l’Intelligence',
        ],
        'saison' => [
            'name' => 'Saison',
            'blurb' => 'L’année est sur ta peau. La pluie, le gel, la marche.',
            'save' => 'la Vitalité',
        ],
        'taniere' => [
            'name' => 'Tanière',
            'blurb' => 'Un lieu te connaît. Tu te couches là où il veut bien.',
            'save' => 'la Force',
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu reconnais une piste, une baie comestible, un silence de trop. La nature n’est pas gentille. Elle est lisible.',
            'masteries' => [
                'Outils' => 'Kit de herboriste ou de pisteur (au choix).',
                'Jets de sauvegarde' => 'Sagesse, Force.',
                'Compétences' => 'Choisis-en trois parmi : Nature, Dressage, Survie, Médecine, Perception.',
                'Métiers' => 'Un métier de récolte au niveau 1 (Alchimiste, Chasseur, Pêcheur ou Bûcheron).',
                'Langues' => 'Aucune langue de ville. Tu parles déjà assez fort aux bêtes.',
            ],
            'capacities' => [
                [
                    'name' => 'Territoire lu',
                    'type' => $guaranteed,
                    'effect' => 'Après 10 minutes en milieu naturel, tu sais si la zone est chassée, sacrée, malade, ou occupée par quelque chose de trop grand. Un fait, pas une carte complète.',
                ],
                $link('Communication avec les animaux'),
                $link('Amitié avec les animaux'),
                $link('Baies nourricières'),
                $link('Passage sans trace'),
                $link('Aide'),
            ],
        ],
        3 => [
            'unlock_style' => true,
            'choice' => $choices[3],
            'flavor' => 'Tes pas ne font plus de bruit dans la mousse. Les bêtes te jaugent avant de fuir.',
            'capacities' => [
                [
                    'name' => 'Pas dans la mousse',
                    'type' => $guaranteed,
                    'effect' => 'En milieu naturel, tu te déplaces sans laisser de traces évidentes. Avantage à la Discrétion seulement en nature, pas en donjon de pierre. Tant que tu ne cours pas.',
                ],
                $link('Communication avec les animaux'),
                $link('Amitié avec les animaux'),
                $link('Passage sans trace'),
                $link('Baies nourricières'),
                $link('Localiser une créature'),
                $link('Charme personne')
            ],
            'aptitudes' => [
                [
                    'name' => 'Les bêtes te jaugent',
                    'type' => 'Passive',
                    'effect' => 'Les animaux non hostiles de base ne te fuient pas d’office. Les prédateurs te testent avant de charger (désavantage à leur premier jet d’Intimidation contre toi). Les invocations ennemies : hors sujet.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Tu soignes avec ce qui pousse. Moins fort qu’un Eniripsa. Suffisant pour rentrer.',
            'capacities' => [
                [
                    'name' => 'Soins de sous-bois',
                    'type' => $guaranteed,
                    'effect' => 'Hors combat, 10 minutes, des plantes : tu soignes 1d4 PV ou tu retires Empoisonné d’origine naturelle. 1×/créature/repos. Moins fort qu’un Mot.',
                ],
                $link('Baies nourricières'),
                $link('Communication avec les animaux'),
                $link('Amitié avec les animaux'),
                $link('Passage sans trace'),
                $link('Assistance'),
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tu n’invoques pas. Tu appelles. La différence tient dans le mot « local ».',
            'capacities' => [
                [
                    'name' => 'Appel discret',
                    'type' => $guaranteed,
                    'pa' => '3',
                    'effect' => 'Tu attires un animal local (messager, sentinelle, diversion) pour une scène. Pas un tank de combat. 3 PA depuis la réserve de Wakfu. L’Osamodas reste le roi des invocations.',
                ],
                $link('Amitié avec les animaux'),
                $link('Communication avec les animaux'),
                $link('Localiser une créature'),
                $link('Passage sans trace'),
                $link('Petite hutte'),
                $link('Communication avec les plantes')
            ],
            'aptitudes' => [
                [
                    'name' => 'Peau des saisons',
                    'type' => 'Contextuelle (nature)',
                    'effect' => 'Tu ignores le froid mou, la pluie, la fatigue de marche en terrain naturel. Pas une résistance aux sorts de glace, pas un bonus en donjon.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Le bois répond. Pas toujours gentiment. Suffisamment pour fuir ou tendre une embuscade.',
            'capacities' => [
                [
                    'name' => 'Le bois répond',
                    'type' => $guaranteed,
                    'pa' => '3',
                    'effect' => 'En forêt, jungle ou marais : racines, brouillard de pollen ou essaim gênant, un cube de 4 cases, 1 minute. Les créatures dans la zone ont désavantage à la Perception. 3 PA depuis la réserve de Wakfu, 1×/scène.',
                ],
                $link('Passage sans trace'),
                $link('Baies nourricières'),
                $link('Communication avec les animaux'),
                $link('Localiser une créature'),
                $link('Illusion mineure'),
                $link('Communication avec les plantes')
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Tu poses un cercle. Le camp tient. Les bêtes curieuses passent leur chemin.',
            'capacities' => [
                [
                    'name' => 'Cercle de saison',
                    'type' => $guaranteed,
                    'pa' => '4',
                    'effect' => 'Rituel, 1 heure : un camp en nature devient plus sûr pour 8 heures (veille, météo, bêtes curieuses). Pas un mur de Féca. 4 PA depuis la réserve de Wakfu, 1×/jour.',
                ],
                $link('Petite hutte'),
                $link('Amitié avec les animaux'),
                $link('Passage sans trace'),
                $link('Alarme'),
                $link('Assistance'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Sang contre poison',
                    'type' => 'Passive',
                    'effect' => 'Avantage aux jets de sauvegarde contre les poisons et maladies naturelles. Les poisons magiques et alchimiques raffinés : pas d’avantage, tu les reconnais juste (Œil du matériau n’est pas à toi).',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, les racines s’en souviennent.',
            'capacities' => [
                [
                    'name' => 'Le Monde des Douze te reconnaît',
                    'type' => $guaranteed,
                    'effect' => 'Un esprit de nature, un vieux Bouftou mythique ou une Sadida ancestrale te doit une audience. Avec le MJ, une fois. Pas un sort de contrôle de masse.',
                ],
                $link('Communication avec les animaux'),
                $link('Amitié avec les animaux'),
                $link('Baies nourricières'),
                $link('Passage sans trace'),
                $link('Liberté de mouvement'),
            ],
        ],
    ],
];