<?php

declare(strict_types=1);

/**
 * Fiche modèle de spécialisation (gabarit 2.4.2.6) : l’Érudit, jouable.
 *
 * Signature : Identification. Capacités catalogue en lien ; Main du mage et
 * Compréhension des langues au palier 1.
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
    'name' => 'Érudit',
    'importPageSlug' => 'import-specialization-erudit',
    'importPageTitle' => 'Spécialisation Érudit',
    'sectionSlugPrefix' => 'import-erudit',
    'shortDescription' => 'Spécialisation centrée sur le savoir, la magie et l’analyse.',
    'description' => 'Tu as une connaissance large des sciences, et un domaine où l’on te cite. Bibliothèques de Bonta, grimoires, débats de cour : tu lis le monde pour le rendre intelligible.',
    'identity' => 'En tant qu’Érudit, tu as une connaissance multiple des sciences, mais tu maîtrises un domaine en particulier. Les autres viennent te chercher pour cette expertise, et te citent en référence. Domaines typiques : physique et parfums ; astronomie et mathématiques ; Wakfu et savoirs interdits ; théologie et histoire ; cultures et géographie ; philosophie et littérature — ou un autre, si le MJ l’accepte.',
    'focus' => 'Savoir, analyse, magie utilitaire, milieux de pouvoir',
    'characteristics' => 'Intelligence (principale). Sagesse pour lire les gens et les signes.',
    'idealFor' => 'Xélor, Huppermage, Eniripsa, tout mage qui veut un métier hors des sorts de classe ; un Iop Érudit qui allie le fer et le livre.',
    'difference' => 'L’Érudit n’est pas un 13ᵉ sort de classe, ni un grimoire D&D recopié. Tes capacités servent à chercher, nommer, décrypter, négocier. Tu ne remplaces pas le Dévot pour soigner, ni le Courtisan pour tenir une salle à toi seul.',
    'synergies' => [
        'Xélor' => 'Mage intellectuel : recherche, horloges, archives temporelles.',
        'Huppermage' => 'Runes, contrastes, lecture du Wakfu.',
        'Eniripsa' => 'Diagnostic, langues, savoir médical.',
        'Iop' => 'Le fer et le livre : tu frappes, puis tu expliques pourquoi ça marchait.',
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses plume, encrier et grimoire. Ton premier geste d’érudit, c’est de nommer ce que tu vois.',
            'masteries' => [
                'Outils' => 'Une plume avec son encrier et un grimoire. Selon ton domaine : compas, lunette, coffret d’échantillons…',
                'Jets de sauvegarde' => 'Sagesse, Intelligence.',
                'Compétences' => 'Choisis-en trois parmi : Arcanes, Histoire, Investigation, Perspicacité, Religion.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus. Décryptage et Compréhension des langues couvrent le reste.',
            ],
            'capacities' => [
                [
                    'name' => 'Identification',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'En 10 minutes (1 PA depuis la réserve de Wakfu en combat), tu nommes un objet, un texte ou une trace de Wakfu : usage, rareté, danger évident. Tu ne lis pas les secrets volontairement cachés.',
                ],
                $link('Main du mage'),
                $link('Compréhension des langues'),
                $link('Lumière'),
                $link('Message'),
                $link('Assistance'),
                $link('Détection de la magie')
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On te reçoit. Tu sais déjà comment te tenir, et tu commences à lire ce qui n’est pas écrit.',
            'capacities' => [
                [
                    'name' => 'Analyse',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Sur un objet, une créature ou une scène (1 minute hors combat, 1 PA depuis la réserve de Wakfu en combat), tu dégages une propriété utile : faiblesse évidente, mensonge de surface, usage caché d’un outil. Un seul fait, pas une fiche complète.',
                ],
                $link('Détection de la magie'),
                $link('Charme personne'),
                $link('Localiser un objet'),
                $link('Voir l\'invisible'),
                $link('Détection des pensées'),
                $link('Compréhension des langues'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Politicien',
                    'type' => 'Contextuelle (pouvoir)',
                    'effect' => 'En haute société, en milieu mondain ou auprès de quiconque détient le pouvoir local, tu as l’avantage aux tests de Chance et aux tests de Sagesse (Perspicacité) pour savoir si on te ment.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Deux compétences deviennent tes terrains. Tu n’improvises plus : tu t’y appuies.',
            'capacities' => [
                [
                    'name' => 'Érudition supérieure',
                    'type' => $guaranteed,
                    'effect' => 'Choisis deux compétences parmi Arcanes, Histoire, Médecine, Nature, Religion et Perspicacité. Sur ces tests, tout d20 inférieur à 10 (sauf un 1) vaut 10. C’est un choix de palier, pas un sort à relancer.',
                ],
                $link('Silence'),
                $link('Invisibilité'),
                $link('Voir l\'invisible'),
                $link('Détection des pensées'),
                $link('Localiser un objet'),
                $link('Oeil magique'),
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tes sorts ont des poches. Tes textes ont des plis. Tu décides qui est dedans.',
            'capacities' => [
                [
                    'name' => 'Façonneur de sorts',
                    'type' => $guaranteed,
                    'effect' => 'Quand tu lances un sort de zone, tu peux épargner des créatures choisies : elles ne subissent pas l’effet. 1 créature à ce palier, 2 au palier 15, 3 au palier 20. Gratuit, lié au lancement (pas de PA ni de Wakfu en plus).',
                ],
                $link('Vol'),
                $link('Voir l\'invisible'),
                $link('Détection des pensées'),
                $link('Localiser un objet'),
                $link('Sanctuaire'),
                $link('Invisibilité'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Mémoire des formules',
                    'type' => 'Passive',
                    'effect' => 'Avantage aux tests d’Intelligence (Arcanes) pour reconnaître un sort, un glyphe ou une capacité que tu as déjà vu·e au moins une fois.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Tu préfères que ça ne fasse pas de bruit. Quand il le faut, ça n’en fait pas.',
            'capacities' => [
                [
                    'name' => 'Tour silencieux',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Tes sorts de classe et tes capacités d’Érudit n’ont plus besoin de composante vocale. En combat, tu peux aussi dépenser 1 PA depuis la réserve de Wakfu pour qu’un sort déjà lancé ne laisse pas d’éclat visible hors de la case cible.',
                ],
                $link('Fabrication'),
                $link('Confusion'),
                $link('Banissement'),
                $link('Lévitation'),
                $link('Oeil magique'),
                $link('Silence'),
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Le Wakfu n’est plus une langue étrangère. Tu le contrés, tu le lis, tu t’en sers pour partir.',
            'capacities' => [
                [
                    'name' => 'Analyse profonde',
                    'type' => $guaranteed,
                    'pa' => '2',
                    'effect' => 'Comme Analyse, mais tu tires trois faits, y compris une propriété magique cachée ou une faille d’un sort déjà lancé. 1 minute hors combat, 2 PA depuis la réserve de Wakfu en combat.',
                ],
                $link('Cercle de téléportation'),
                $link('Banissement'),
                $link('Scrutation'),
                $link('Divination'),
                $link('Métamorphose'),
                $link('Vol'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Expertise en Wakfu',
                    'type' => 'Passive',
                    'effect' => 'Avantage aux tests d’Intelligence pour dissiper ou contrer un sort ou une capacité, et avantage aux jets de sauvegarde pour y résister.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, tu l’as déjà donné.',
            'capacities' => [
                [
                    'name' => 'Maîtrise des capacités',
                    'type' => $guaranteed,
                    'effect' => 'Choisis une capacité d’Érudit de palier 1 et une de palier 3 que tu connais. Tu les lances sans dépenser de PA ni de Wakfu. 8 heures d’étude pour échanger l’une ou les deux contre d’autres du même palier.',
                ],
                $link('Divination'),
                $link('Scrutation'),
                $link('Oeil magique'),
                $link('Lévitation'),
                $link('Identification'),
                $link('Cercle de téléportation')
            ],
        ],
    ],
];