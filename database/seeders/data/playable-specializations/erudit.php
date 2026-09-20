<?php

declare(strict_types=1);

/**
 * Fiche modèle de spécialisation (gabarit 2.4.2.6) : l’Érudit, jouable.
 *
 * 7 paliers, 2 garanties au palier 1 puis 1, 2 options à chaque palier,
 * 5 compétences au palier 1 (en prendre 3), exactement 3 aptitudes aux paliers 3 / 9 / 15.
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
                    'effect' => 'En 10 minutes (1 PA en combat), tu nommes un objet, un texte ou une trace de Wakfu : usage, rareté, danger évident. Tu ne lis pas les secrets volontairement cachés.',
                ],
                [
                    'name' => 'Recherche approfondie',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : une heure dans une archive, une bibliothèque ou un lieu chargé, tu extrais un fait utile à la scène en cours. Le MJ tranche si le lieu n’a rien à dire.',
                ],
                [
                    'name' => 'Compréhension des langues',
                    'type' => $freeSlot,
                    'effect' => 'Pendant une scène, tu comprends le sens littéral de tout langage parlé. Pour l’écrit, tu dois toucher la surface. Une page = une minute. Les codes et glyphes non linguistiques restent opaques.',
                ],
                [
                    'name' => 'Main du mage',
                    'type' => $freeSlot,
                    'effect' => 'Une main spectrale à portée (9 m), 5 kg, n’attaque pas. Tu ouvres, verses, déplaces. 1 PA par action en combat. Hors combat : réserve de Wakfu, une scène.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On te reçoit. Tu sais déjà comment te tenir, et tu commences à lire ce qui n’est pas écrit.',
            'capacities' => [
                [
                    'name' => 'Analyse',
                    'type' => $guaranteed,
                    'effect' => 'Sur un objet, une créature ou une scène (1 minute hors combat, 1 PA en combat), tu dégages une propriété utile : faiblesse évidente, mensonge de surface, usage caché d’un outil. Un seul fait, pas une fiche complète.',
                ],
                [
                    'name' => 'Localiser un objet',
                    'type' => $freeSlot,
                    'effect' => 'Tu nommes un objet que tu as déjà vu ou dont tu as une description précise. Pendant 10 minutes, tu sais dans quelle direction il se trouve s’il est dans un rayon d’un kilomètre, sauf magie qui le dérobe.',
                ],
                [
                    'name' => 'Voir l’invisible',
                    'type' => $freeSlot,
                    'effect' => 'Une scène : tu perçois les créatures et objets invisibles dans ton champ de vision, comme une brume. Ne perce pas les murs ni l’obscurité magique.',
                ],
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
                [
                    'name' => 'Langues',
                    'type' => $freeSlot,
                    'effect' => 'Pendant une scène, tu parles et tu comprends une langue que tu entends. Tu ne l’écris pas parfaitement ; les codes restent des codes.',
                ],
                [
                    'name' => 'Lecture en diagonale',
                    'type' => $freeSlot,
                    'effect' => 'En 10 minutes sur un livre, un dossier ou une stèle, tu extrais un fait utile à la scène. Tu ne lis pas ce qui est chiffré ou volontairement caché. 1×/scène.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tes sorts ont des poches. Tes textes ont des plis. Tu décides qui est dedans.',
            'capacities' => [
                [
                    'name' => 'Décryptage',
                    'type' => $guaranteed,
                    'effect' => 'Tu attaques un texte chiffré, un glyphe ou un contrat à double fond. 10 minutes, test d’Intelligence (Arcanes ou Investigation). Un succès livre le sens utile, pas forcément l’auteur.',
                ],
                [
                    'name' => 'Sanctuaire privé',
                    'type' => $freeSlot,
                    'effect' => 'Tu scelles une pièce ou un campement pour 8 heures : les oreilles indiscrètes et les scrutations banales glissent. Une créature qui force l’entrée te réveille. 1 réserve de Wakfu.',
                ],
                [
                    'name' => 'Localiser une créature',
                    'type' => $freeSlot,
                    'effect' => 'Tu nommes une créature que tu as déjà vue. Pendant 10 minutes, tu sais dans quelle direction elle se trouve dans un rayon d’un kilomètre, sauf magie qui la dérobe.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Façonneur de sorts',
                    'type' => 'Réactive (tes zones)',
                    'effect' => 'Quand tu lances un sort de zone, tu peux épargner des créatures choisies : elles ne subissent pas l’effet. 1 créature à ce palier, 2 au palier 15, 3 au palier 20.',
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
                    'effect' => 'Tes sorts de classe et tes capacités d’Érudit n’ont plus besoin de composante vocale. En combat, tu peux aussi dépenser 1 PA pour qu’un sort déjà lancé ne laisse pas d’éclat visible hors de la case cible.',
                ],
                [
                    'name' => 'Zaap de poche',
                    'type' => $freeSlot,
                    'effect' => 'Une fois par jour, tu ouvres un zaap temporaire vers un lieu que tu as déjà foulé sur le même continent. Le groupe (6 créatures) passe. Échec possible si le Wakfu local est instable — le MJ tranche.',
                ],
                [
                    'name' => 'Télékinésie',
                    'type' => $freeSlot,
                    'effect' => 'Pendant une scène, tu déplaces un objet ou une créature consentante dans un rayon de 12 cases. En combat : 2 PA, 1×/tour, pas d’attaque. Hors combat : réserve de Wakfu.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Le Wakfu n’est plus une langue étrangère. Tu le contrés, tu le lis, tu t’en sers pour partir.',
            'capacities' => [
                [
                    'name' => 'Analyse profonde',
                    'type' => $guaranteed,
                    'effect' => 'Comme Analyse, mais tu tires trois faits, y compris une propriété magique cachée ou une faille d’un sort déjà lancé. 1 minute hors combat, 2 PA en combat.',
                ],
                [
                    'name' => 'Téléportation',
                    'type' => $freeSlot,
                    'effect' => 'Tu emmènes le groupe (6 créatures) vers un lieu que tu connais, même sur un autre continent. Une fois par semaine. Un lieu jamais vu exige un ancrage (objet, carte, témoin).',
                ],
                [
                    'name' => 'Manoir somptueux',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un intérieur sûr pour 24 heures : portes, lits, table, silence extérieur. Rien de ce qui est créé ne survit à la fin. 1 réserve de Wakfu, 1×/jour.',
                ],
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
                [
                    'name' => 'Demi-plan',
                    'type' => $freeSlot,
                    'effect' => 'Tu ouvres une pièce hors du Monde, ancrée à toi, pour 24 heures. Ce qui y reste à la fermeture est perdu, sauf ce que tu tiens. Une fois par mois. Ce n’est pas un souhait.',
                ],
                [
                    'name' => 'Cabinet de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un bureau hors du Monde pour 8 heures : table, rayonnages de tes propres livres, silence. Rien de ce qui n’était pas à toi n’y survit. 1 réserve de Wakfu, 1×/semaine.',
                ],
            ],
        ],
    ],
];
