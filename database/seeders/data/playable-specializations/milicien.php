<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Milicien·ne.
 *
 * @return array<string, mixed>
 */
$guaranteed = 'garantie';
$freeSlot = 'emplacement libre';

$choices = [
    1 => '1 capacité garantie + 1 emplacement libre · 3 compétences',
    3 => '1 aptitude (automatique) · 1 capacité garantie + 1 emplacement libre · 1 compétence',
    6 => '1 capacité garantie + 1 emplacement libre · 1 compétence',
    9 => '1 aptitude (automatique) · 1 capacité garantie + 1 emplacement libre · 1 compétence (expertise possible)',
    12 => '1 capacité garantie + 1 emplacement libre (trait possible) · 1 compétence',
    15 => '1 aptitude (automatique) · 1 capacité garantie + 1 emplacement libre (trait possible) · 1 compétence (expertise possible)',
    20 => '1 capacité garantie + 1 emplacement libre (trait possible) · 1 compétence (expertise possible)',
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
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'On te donne un insigne, ou tu t’en fabriques un. Le premier geste, c’est de faire tenir deux personnes au même endroit.',
            'masteries' => [
                'Outils' => 'Insigne, menottes, corde. Selon ta ville : étendard de poche, sifflet, lanternon.',
                'Jets de sauvegarde' => 'Force, Constitution.',
                'Compétences' => 'Choisis-en trois parmi : Athlétisme, Intimidation, Perception, Perspicacité.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus.',
            ],
            'capacities' => [
                [
                    'name' => 'Formation tactique',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par scène, tu places jusqu’à deux allié·e·s adjacents : chacun gagne +1 CA jusqu’à son prochain tour, ou se décale d’une case sans provoquer d’attaque. 1 PA en combat.',
                ],
                [
                    'name' => 'Cri de ralliement',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : un cri net. Un allié à 6 cases qui est à terre, apeuré ou figé relance son jet, ou se relève. 1 PA, 1×/scène.',
                ],
                [
                    'name' => 'Garde du seuil',
                    'type' => $freeSlot,
                    'effect' => 'Tu bloques une porte, un passage d’une case ou un allié au sol. Tant que tu ne bouges pas, la première créature qui passe à travers toi s’arrête. 1 PA pour poser, 0 pour tenir.',
                ],
                [
                    'name' => 'Menottes de fortune',
                    'type' => $freeSlot,
                    'effect' => 'Sur une créature déjà maîtrisée ou consentante : tu l’entraves (mains ou pieds). Hors combat : 1 minute. En combat : 2 PA, jet d’Athlétisme contre elle.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On ne te laisse plus seul·e au premier rang. Quelqu’un colle à toi, et tu colles à lui.',
            'capacities' => [
                [
                    'name' => 'Garde rapprochée',
                    'type' => $guaranteed,
                    'effect' => 'Tu désignes un allié adjacent. Jusqu’à la fin de ton prochain tour, les attaques qui le visent te visent toi si tu le veux. 1 PA, pas de cumul.',
                ],
                [
                    'name' => 'Tenir la ligne',
                    'type' => $freeSlot,
                    'effect' => 'Toi et un allié adjacent : vous ne pouvez pas être déplacés de force tant que vous restez côte à côte. Une scène, ou 1 réserve de Wakfu en combat (1 PA).',
                ],
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
                    'effect' => 'Toi et les allié·e·s qui t’entendent (6 cases) : avantage aux jets contre la peur et le charme jusqu’à la fin de la scène. 1 réserve de Wakfu, 1×/scène.',
                ],
                [
                    'name' => 'Ronde',
                    'type' => $freeSlot,
                    'effect' => 'Hors combat, en 10 minutes, tu lis une rue, une caserne ou un camp : une issue, une sentinelle, un détail qui cloche. Le MJ tranche si le lieu n’a rien à dire.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'On t’écoute avant de frapper. Parfois ça suffit.',
            'capacities' => [
                [
                    'name' => 'Commandement',
                    'type' => $guaranteed,
                    'effect' => 'Un allié à 6 cases agit tout de suite : une attaque, un déplacement ou une capacité déjà connue, à son coût. 2 PA, 1×/tour. Ce n’est pas ton tour, c’est le sien avancé.',
                ],
                [
                    'name' => 'Insigne levé',
                    'type' => $freeSlot,
                    'effect' => 'En ville ou face à une milice, une garde, une troupe : avantage aux tests de Chance (Persuasion) et de Force (Intimidation) pour faire ouvrir, céder ou patienter. Une scène.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Position d’autorité',
                    'type' => 'Contextuelle (ordre public)',
                    'effect' => 'Face à des gardes, une milice, une foule ou quiconque reconnaît un uniforme, tu as l’avantage aux tests de Chance et de Force pour faire tenir un ordre simple (halte, passage, dégager).',
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
                    'effect' => 'Toi et jusqu’à deux allié·e·s adjacents : +1 CA et vous ne pouvez pas être séparés de force. Jusqu’à la fin de ton prochain tour. 2 PA, 1×/scène.',
                ],
                [
                    'name' => 'Rempart vivant',
                    'type' => $freeSlot,
                    'effect' => 'Tu interposes ton corps : les dégâts d’une attaque ou d’une zone qui touche un allié adjacent te sont transférés. 1 réaction. Tu peux encore tomber.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Un mot, et la charge part. Un autre, et elle s’arrête.',
            'capacities' => [
                [
                    'name' => 'Ordre de charge',
                    'type' => $guaranteed,
                    'effect' => 'Jusqu’à trois allié·e·s à 6 cases se déplacent de 3 cases vers une cible que tu nommes et peuvent attaquer s’ils arrivent au contact. 3 PA, 1×/scène. Chacun paie encore ses PA d’attaque.',
                ],
                [
                    'name' => 'Ralliement',
                    'type' => $freeSlot,
                    'effect' => 'Les allié·e·s à 6 cases qui t’entendent : fin de peur, de charme ou d’étourdissement mineur, et 1d4 PV. 1 réserve de Wakfu, 1×/scène.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Général',
                    'type' => 'Passive',
                    'effect' => 'Au premier round d’un combat que tu as vu venir, tes allié·e·s à 6 cases ne peuvent pas être surpris et gagnent +1 aux jets d’initiative. Hors combat : avantage pour poser un plan de 10 minutes.',
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
                    'effect' => 'Une fois par jour, tant que tu es debout, les allié·e·s à 6 cases ne peuvent pas tomber à 0 PV : ils restent à 1. Ça dure jusqu’à la fin de ton prochain tour. Ensuite, les comptes se règlent.',
                ],
                [
                    'name' => 'Bannière',
                    'type' => $freeSlot,
                    'effect' => 'Tu plantes un étendard (1 case). Tant qu’il tient (1 heure ou jusqu’à destruction), les allié·e·s dans 6 cases ont l’avantage contre la peur et +1 aux jets d’attaque. 1 réserve de Wakfu.',
                ],
            ],
        ],
    ],
];
