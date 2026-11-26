<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Dévot.
 *
 * Garantie palier 1 : Bénédiction (lien catalogue). Signatures aux paliers
 * suivants. Bénédiction reste disponible en choix catalogue.
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
    'styles_at' => 3,
    'styles' => [
        'officiant' => [
            'name' => 'Officiant',
            'blurb' => 'Tu lis, tu chantes, tu tiens l’assemblée. Le rite a un ordre.',
            'save' => 'l’Intelligence',
        ],
        'soignant' => [
            'name' => 'Soignant',
            'blurb' => 'Tu poses les mains, tu bandes, tu restes quand les autres sortent.',
            'save' => 'la Vitalité',
        ],
        'pelerin' => [
            'name' => 'Pèlerin',
            'blurb' => 'Tu marches. Le chemin, l’auberge et le seuil sont déjà une prière.',
            'save' => 'l’Agilité',
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses un symbole, un chapelet, un flacon d’eau. Le premier geste, c’est de nommer ce qui fait mal.',
            'masteries' => [
                'Outils' => 'Symbole sacré, flacon, bandelettes. Selon ton culte : encens, goupillon, petit livre.',
                'Jets de sauvegarde' => 'Sagesse, Intelligence.',
                'Compétences' => 'Choisis-en trois parmi : Médecine, Religion, Perspicacité, Persuasion, Histoire.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus. Les langues liturgiques s’apprennent, elles ne s’offrent pas.',
            ],
            'capacities' => [
                [
                    'name' => 'Bénédiction',
                    'type' => $guaranteed,
                    'link' => true,
                ],
                $link('Sanctuaire'),
                $link('Aide'),
                $link('Lumière'),
                $link('Apaisement des émotions'),
                $link('Assistance'),
            ],
        ],
        3 => [
            'unlock_style' => true,
            'choice' => $choices[3],
            'flavor' => 'On t’ouvre les portes des hospices. Tu sais déjà où poser le tapis.',
            'capacities' => [
                [
                    'name' => 'Consolation',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Un allié qui t’entend (6 cases) : fin de peur ou d’un chagrin de scène, et +1 au prochain jet de Sagesse. 1 PA depuis la réserve de Wakfu, 1×/scène.',
                ],
                $link('Apaisement des émotions'),
                $link('Sanctuaire'),
                $link('Aide'),
                $link('Lumière'),
                $link('Zone de vérité'),
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
                    'pa' => '2',
                    'effect' => 'Tu touches une créature : tu retires Empoisonné, Affaibli ou un mal mineur du même ordre. 10 minutes hors combat, 2 PA depuis la réserve de Wakfu en combat. 1×/scène.',
                ],
                $link('Bénédiction'),
                $link('Lumière'),
                $link('Voir l\'invisible'),
                $link('Sanctuaire'),
                $link('Apaisement des émotions'),
                $link('Silence')
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Le champ, la ruelle, le lit de camp : tu soignes là où ça tombe.',
            'capacities' => [
                [
                    'name' => 'Bénédiction majeure',
                    'type' => $guaranteed,
                    'pa' => '2',
                    'effect' => 'Jusqu’à trois créatures qui t’entendent : +1 aux jets d’attaque et de sauvegarde jusqu’à la fin de ton prochain tour. 2 PA depuis la réserve de Wakfu, 1×/scène. Pas de cumul avec le sort Bénédiction.',
                ],
                $link('Bénédiction'),
                $link('Sanctuaire'),
                $link('Aide'),
                $link('Zone de vérité'),
                $link('Divination'),
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
                $link('Sanctuaire'),
                $link('Bénédiction'),
                $link('Liberté de mouvement'),
                $link('Petite hutte'),
                $link('Assistance'),
                $link('Banissement')
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Ta voix porte. Ce n’est plus seulement une prière : c’est une salle qui se lève.',
            'capacities' => [
                [
                    'name' => 'Dernier souffle',
                    'type' => $guaranteed,
                    'pa' => '4',
                    'effect' => 'Tu touches une créature morte depuis moins d’une heure, si le corps est entier. Elle revient à 1 PV, épuisée. 4 PA depuis la réserve de Wakfu, 1×/semaine. Ce n’est pas une résurrection de légende.',
                ],
                $link('Bénédiction'),
                $link('Sanctuaire'),
                $link('Apaisement des émotions'),
                $link('Divination'),
                $link('Scrutation'),
                $link('Banissement')
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
                $link('Bénédiction'),
                $link('Sanctuaire'),
                $link('Divination'),
                $link('Scrutation'),
                $link('Zone de vérité'),
            ],
        ],
    ],
];