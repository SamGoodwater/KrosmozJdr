<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Milicien·ne.
 *
 * Signature : Formation tactique. Les autres capacités sont des liens catalogue
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
    'name' => 'Milicien·ne',
    'importPageSlug' => 'import-specialization-milicien-ne',
    'importPageTitle' => 'Spécialisation Milicien·ne',
    'sectionSlugPrefix' => 'import-milicien-ne',
    'shortDescription' => 'Spécialisation axée sur l’ordre, la protection et le combat structuré.',
    'description' => 'Tu as appris à tenir une ligne, à lire une rue, à faire tenir les autres. Milice de Bonta, patrouille de Brâkmar, garde de village : on te reconnaît à la façon dont tu occupes l’espace.',
    'identity' => 'En tant que Milicien·ne, tu n’es pas un Iop de plus. Tu es celui ou celle qui fait tenir le groupe : un ordre, un geste, une épaule contre l’épaule. On te cherche quand ça déborde, pas seulement quand ça frappe.',
    'focus' => 'Ordre, protection, combat structuré, rues et remparts',
    'characteristics' => 'Force ou Agilité (principale). Constitution pour tenir la ligne.',
    'idealFor' => 'Iop, Sacrieur, Féca, Pandawa ; tout combattant qui veut un métier hors du duel.',
    'difference' => 'Le Milicien·ne n’est pas un 13ᵉ sort de classe, ni un tank automatique. Tes capacités servent à tenir, à ranger, à faire tenir les autres. Tu ne remplaces pas le Dévot pour soigner, ni le Voleur pour filer.',
    'synergies' => [
        'Iop' => 'Le fer et la discipline : tu frappes, et les autres frappent avec toi.',
        'Féca' => 'Boucliers et ligne : tu tiens, on ne passe pas.',
        'Sacrieur' => 'La douleur partagée devient un ordre, pas un chaos.',
        'Pandawa' => 'Le corps-à-corps qui pousse, porte et ramène.',
    ],
    'styles_at' => 3,
    'styles' => [
        'bonta' => [
            'name' => 'Bonta',
            'blurb' => 'Les remparts sont blancs. Tu ouvres le passage, tu couvres la foule, tu fais les choses dans l’ordre.',
            'save' => 'la Sagesse',
        ],
        'brakmar' => [
            'name' => 'Brâkmar',
            'blurb' => 'Les rues sont noires. On ne discute pas longtemps. Tu pousses, tu encaisses, tu dégages.',
            'save' => 'la Vitalité',
        ],
        'astrub' => [
            'name' => 'Astrub',
            'blurb' => 'Entre les deux cités, l’insigne est neutre. Tu lis la ruelle avant le coup.',
            'save' => 'l’Agilité',
        ],
        'amakna' => [
            'name' => 'Amakna',
            'blurb' => 'Le bourg, le chemin, la place. Tu connais chaque porte, et c’est toi qui écris le rapport.',
            'save' => 'l’Intelligence',
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'On te donne un insigne, ou tu t’en fabriques un. Le premier geste, c’est de faire tenir deux personnes au même endroit.',
            'masteries' => [
                'Outils' => 'Insigne, menottes, corde. Selon ta ville : étendard de poche, sifflet, lanternon.',
                'Jets de sauvegarde' => 'Force.',
                'Compétences' => 'Choisis-en trois parmi : Athlétisme, Intimidation, Perception, Perspicacité, Persuasion.',
                'Métiers' => 'Aucun métier offert.',
                'Langues' => 'Aucune langue bonus.',
            ],
            'capacities' => [
                [
                    'name' => 'Formation tactique',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Une fois par scène, tu places jusqu’à deux alliés adjacents : chacun gagne +1 CA jusqu’à son prochain tour, ou se décale de 1,5 m sans s’exposer.',
                ],
                $link('Alarme'),
                $link('Aide'),
                $link('Assistance'),
                $link('Lumière'),
                $link('Message'),
            ],
        ],
        3 => [
            'unlock_style' => true,
            'choice' => $choices[3],
            'flavor' => 'On ne te laisse plus seul·e au premier rang. Tu choisis ta cité, et tu apprends à lire qui te fait face.',
            'capacities' => [
                [
                    'name' => 'Tenir la ligne',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Toi et un allié adjacent : vous ne pouvez pas être déplacés de force tant que vous restez côte à côte. Une scène.',
                ],
                $link('Connais ta cible'),
                $link('Sanctuaire'),
                $link('Voir l\'invisible'),
                $link('Zone de vérité'),
                $link('Apaisement des émotions'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Frères d’armes',
                    'type' => 'Contextuelle (allié adjacent)',
                    'effect' => 'Quand un allié adjacent de toi réussit une attaque ou un jet de sauvegarde, tu as l’avantage au tien suivant du même type, jusqu’à la fin de ton prochain tour. Pas de cumul.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'La peur, tu la connais. Tu ne la laisses plus décider.',
            'capacities' => [
                [
                    'name' => 'Discipline',
                    'type' => $guaranteed,
                    'pa' => '2',
                    'effect' => 'Toi et les alliés qui t’entendent (9 m) : avantage aux jets contre la peur et le charme jusqu’à la fin de la scène. 1×/scène.',
                ],
                $link('Passage sans trace'),
                $link('Bouche magique'),
                $link('Détection des pensées'),
                $link('Localiser un objet'),
                $link('Suggestion'),
                $link('Silence'),
                $link('Bourrasque')
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'On t’écoute avant de frapper. Parfois ça suffit.',
            'capacities' => [
                [
                    'name' => 'Commandement',
                    'type' => $guaranteed,
                    'pa' => '2',
                    'effect' => 'Un allié à 9 m agit tout de suite : une attaque, un déplacement ou une capacité déjà connue, à son coût. 1×/tour.',
                ],
                $link('Localiser une créature'),
                $link('Image majeure'),
                $link('Petite hutte'),
                $link('Cécité/Surdité'),
                $link('Marche sur l\'eau'),
            ],
            'aptitudes' => [
                [
                    'name' => 'Position d’autorité',
                    'type' => 'Contextuelle (ordre public)',
                    'effect' => 'Face à des gardes, une milice, une foule ou quiconque reconnaît un uniforme, tu as l’avantage au premier test social de la scène pour faire tenir un ordre simple.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Trois boucliers valent une muraille, si quelqu’un compte les pas.',
            'capacities' => [
                [
                    'name' => 'Phalange',
                    'type' => $guaranteed,
                    'pa' => '2',
                    'effect' => 'Toi et jusqu’à deux alliés adjacents : +1 CA et vous ne pouvez pas être séparés de force jusqu’à la fin de ton prochain tour. 1×/scène.',
                ],
                $link('Respiration aquatique'),
                $link('Divination'),
                $link('Scrutation'),
                $link('Liberté de mouvement'),
                $link('Banissement')
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Un mot, et la charge part. Un autre, et elle s’arrête.',
            'capacities' => [
                [
                    'name' => 'Ordre de charge',
                    'type' => $guaranteed,
                    'pa' => '3',
                    'effect' => 'Jusqu’à trois alliés à 9 m se déplacent de 5 m vers une cible que tu nommes. 1×/scène. Chacun paie encore ses PA d’attaque.',
                ],
                $link('Aide'),
                $link('Sanctuaire'),
                $link('Zone de vérité'),
                $link('Localiser une créature'),
                $link('Scrutation'),
                $link('Banissement')
            ],
            'aptitudes' => [
                [
                    'name' => 'Général',
                    'type' => 'Passive',
                    'effect' => 'Au premier round d’un combat que tu as vu venir, tes alliés à 9 m ne peuvent pas être surpris. Hors combat : avantage pour poser un plan de 10 minutes.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'La dernière ligne, c’est toi. Après, il n’y a plus de ville.',
            'capacities' => [
                [
                    'name' => 'Dernière ligne',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par jour, tant que tu es debout, les alliés à 9 m ne peuvent pas tomber à 0 PV : ils restent à 1 jusqu’à la fin de ton prochain tour.',
                ],
                $link('Divination'),
                $link('Scrutation'),
                $link('Petite hutte'),
                $link('Alarme'),
            ],
        ],
    ],
];
