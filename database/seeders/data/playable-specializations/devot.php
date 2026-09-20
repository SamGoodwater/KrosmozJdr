<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Dévot.
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
    'name' => 'Dévot',
    'importPageSlug' => 'import-specialization-devot',
    'importPageTitle' => 'Spécialisation Dévot',
    'sectionSlugPrefix' => 'import-devot',
    'shortDescription' => 'Spécialisation liée à la foi, au soutien et aux pouvoirs sacrés.',
    'description' => 'Tu portes une parole plus grande que toi : un dieu, un serment, un silence. Temples d’Amakna, chapelles de Bonta, autels oubliés : tu soignes, tu bénis, tu tiens quand les autres flanchent.',
    'identity' => 'En tant que Dévot, tu n’es pas un Eniripsa de plus. Tes gestes sont des prières utiles : une main sur une plaie, un mot qui calme, un seuil que le mal n’aime pas. On vient te chercher pour ça, pas pour un 13ᵉ sort de classe.',
    'focus' => 'Foi, soutien, soins mineurs, seuils sacrés',
    'characteristics' => 'Sagesse (principale). Intelligence pour les textes et les rites.',
    'idealFor' => 'Eniripsa, Féca, Sacrieur, Eliotrope ; tout personnage qui veut un métier de soin hors des sorts de classe.',
    'difference' => 'Le Dévot n’est pas un soigneur de raid, ni un inquisiteur automatique. Tes capacités servent à tenir, à consoler, à bénir. Tu ne remplaces pas l’Eniripsa pour les gros soins, ni l’Érudit pour les archives.',
    'synergies' => [
        'Eniripsa' => 'Le mot qui soigne et la prière qui tient : deux mains sur la même plaie.',
        'Féca' => 'Bouclier et bénédiction : le seuil tient deux fois.',
        'Sacrieur' => 'La douleur offerte, la foi qui la lit autrement.',
        'Eliotrope' => 'Portails et pèlerinage : le chemin aussi est un rite.',
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses un symbole, un chapelet, un flacon d’eau. Le premier geste, c’est de nommer ce qui fait mal.',
            'masteries' => [
                'Outils' => 'Symbole sacré, flacon, bandelettes. Selon ton culte : encens, goupillon, petit livre.',
                'Jets de sauvegarde' => 'Sagesse, Intelligence.',
                'Compétences' => 'Choisis-en trois parmi : Médecine, Religion, Perspicacité, Persuasion.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus. Les langues liturgiques s’apprennent, elles ne s’offrent pas.',
            ],
            'capacities' => [
                [
                    'name' => 'Bénédiction',
                    'type' => $guaranteed,
                    'effect' => 'Tu touches une créature consentante : +1 au prochain jet d’attaque, de sauvegarde ou de compétence. Jusqu’à la fin de son prochain tour. 1 PA, 1×/tour. Pas de cumul.',
                ],
                [
                    'name' => 'Prière',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : une minute hors combat (1 PA en combat), tu poses une question simple à ce que tu sers. Le MJ répond par un oui, un non, ou un silence. 1×/scène.',
                ],
                [
                    'name' => 'Guérison mineure',
                    'type' => $freeSlot,
                    'effect' => 'Tu touches une créature : 1d4 PV. Hors combat, 10 minutes et des bandelettes : 1d4 de plus. 1 PA en combat, 1×/tour. Ce n’est pas un Mot Vivifiant.',
                ],
                [
                    'name' => 'Eau bénite',
                    'type' => $freeSlot,
                    'effect' => 'Tu consacres une gorgée. Jetée (1 case) : une créature corrompue, morte-vivante ou clairement maléfique a désavantage à son prochain jet. Une fiole, 1×/scène.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On t’ouvre les portes des hospices. Tu sais déjà où poser le tapis.',
            'capacities' => [
                [
                    'name' => 'Consolation',
                    'type' => $guaranteed,
                    'effect' => 'Un allié qui t’entend (6 cases) : fin de peur ou d’un chagrin de scène, et +1 au prochain jet de Sagesse. 1 PA, 1×/scène.',
                ],
                [
                    'name' => 'Seuil béni',
                    'type' => $freeSlot,
                    'effect' => 'Tu marques une porte, une tente ou un lit. Pendant 8 heures, la première créature malveillante qui force l’entrée te réveille et subit désavantage à son premier jet. 1 réserve de Wakfu.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Refuge du pèlerin',
                    'type' => 'Contextuelle (hospitalité)',
                    'effect' => 'Dans un temple, un hospice, une auberge qui reconnaît ton symbole, tu obtiens un toit, un repas simple et un silence pour prier. Avantage aux tests de Chance pour qu’on t’ouvre, pas pour qu’on te paie.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Le mal a un goût. Tu commences à le reconnaître avant qu’il parle.',
            'capacities' => [
                [
                    'name' => 'Sacrement',
                    'type' => $guaranteed,
                    'effect' => 'Tu touches une créature : tu retires Empoisonné, Affaibli ou un mal mineur du même ordre. 10 minutes hors combat, 2 PA en combat. 1×/scène.',
                ],
                [
                    'name' => 'Lumière du rite',
                    'type' => $freeSlot,
                    'effect' => 'Une lueur dans 3 cases autour de toi, une scène. Les invisibles non magiques apparaissent comme une brume. Les morts-vivants ont désavantage pour se cacher. 1 réserve de Wakfu.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Le champ, la ruelle, le lit de camp : tu soignes là où ça tombe.',
            'capacities' => [
                [
                    'name' => 'Bénédiction majeure',
                    'type' => $guaranteed,
                    'effect' => 'Jusqu’à trois créatures qui t’entendent : +1 aux jets d’attaque et de sauvegarde jusqu’à la fin de ton prochain tour. 2 PA, 1×/scène. Pas de cumul avec Bénédiction.',
                ],
                [
                    'name' => 'Sanctuaire',
                    'type' => $freeSlot,
                    'effect' => 'Tu scelles une pièce ou un campement pour 8 heures : les oreilles indiscrètes et les esprits banals glissent. Une créature qui force l’entrée te réveille. 1 réserve de Wakfu.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Médecine de terrain',
                    'type' => 'Passive',
                    'effect' => 'Quand tu poses un soin ou un bandage (capacité, sort, kit), tu ignores le désavantage dû au terrain, au sang ou à la précipitation. Hors combat, 10 minutes suffisent là où d’autres mettent une heure.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Le groupe marche. Tu marches avec, et le chemin tient.',
            'capacities' => [
                [
                    'name' => 'Marche des pèlerins',
                    'type' => $guaranteed,
                    'effect' => 'Pendant un voyage d’une journée, le groupe ignore un niveau de fatigue dû à la marche, au froid ou à la faim simple. 1×/semaine. Ça ne remplace pas l’eau.',
                ],
                [
                    'name' => 'Main qui retient',
                    'type' => $freeSlot,
                    'effect' => 'Un allié à 6 cases qui tombe à 0 PV reste à 1. Réaction, 1×/scène. Ensuite, les comptes se règlent.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Ta voix porte. Ce n’est plus seulement une prière : c’est une salle qui se lève.',
            'capacities' => [
                [
                    'name' => 'Dernier souffle',
                    'type' => $guaranteed,
                    'effect' => 'Tu touches une créature morte depuis moins d’une heure, si le corps est entier. Elle revient à 1 PV, épuisée. 1 réserve de Wakfu, 1×/semaine. Ce n’est pas une résurrection de légende.',
                ],
                [
                    'name' => 'Cercle consacré',
                    'type' => $freeSlot,
                    'effect' => 'Un cercle de 3 cases, 10 minutes. Les allié·e·s dedans ont avantage contre la peur et les effets maléfiques. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Haranguer les foules',
                    'type' => 'Contextuelle (assemblée)',
                    'effect' => 'Devant une foule, une troupe ou une congrégation, tu as l’avantage aux tests de Chance (Persuasion) et de Sagesse pour calmer, rassembler ou faire tenir un silence. Une scène, pas un procès magique.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'On t’entend encore quand tu ne parles plus. Le reste, tu l’as déjà donné.',
            'capacities' => [
                [
                    'name' => 'Intercession',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par mois, tu demandes une grâce nette : un mort récent revient (corps entier, un jour), un mal incurable cède, ou un seuil tient 24 heures. Le MJ tranche le prix, jamais un souhait libre.',
                ],
                [
                    'name' => 'Temple de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un intérieur sûr pour 24 heures : lits, silence, eau, un autel. Rien de créé ne survit à la fin. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
