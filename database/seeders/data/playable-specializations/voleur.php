<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Voleur·euse.
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
    'name' => 'Voleur·euse',
    'importPageSlug' => 'import-specialization-voleur-euse',
    'importPageTitle' => 'Spécialisation Voleur·euse',
    'sectionSlugPrefix' => 'import-voleur-euse',
    'shortDescription' => 'Spécialisation tournée vers la discrétion, la ruse et la finesse.',
    'description' => 'Tu vis de ce que les autres ne regardent pas : une poche, une serrure, une ruelle. Astrub, égouts de Bonta, toits de Brâkmar : tu passes, tu prends, tu n’es déjà plus là.',
    'identity' => 'En tant que Voleur·euse, tu n’es pas un Sram de plus. Tes capacités servent à ouvrir, à filer, à mentir juste assez. On vient te chercher pour la porte close et la sortie, pas pour un piège de classe.',
    'focus' => 'Discrétion, ruse, crochetage, toits et poches',
    'characteristics' => 'Agilité (principale). Chance pour le bluff et les mains.',
    'idealFor' => 'Sram, Roublard, Ecaflip, Crâ ; tout personnage qui veut un métier de ruelle hors des sorts de classe.',
    'difference' => 'Le Voleur·euse n’est pas un assassin automatique, ni un Négociant. Tes capacités tiennent sur une serrure, une ombre, un mensonge court. Tu ne commandes pas une salle (Artiste), tu ne signes pas un contrat (Négociant).',
    'synergies' => [
        'Sram' => 'L’ombre et la main : tu ouvres, le piège attend.',
        'Roublard' => 'Poudre, détour, sortie : le plan B est déjà un plan A.',
        'Ecaflip' => 'Le bluff et la poche : tu joues, tu ramasses.',
        'Crâ' => 'Toits et distance : tu entres sans bruit, tu sors plus haut.',
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses un crochet, une cape, une seconde paire de semelles. Le premier geste, c’est d’ouvrir ce qui ne t’est pas destiné.',
            'masteries' => [
                'Outils' => 'Crochets, cape sombre, sac souple. Selon tes rues : fausse clé, craie, semelles feutrées.',
                'Jets de sauvegarde' => 'Agilité, Chance.',
                'Compétences' => 'Choisis-en trois parmi : Discrétion, Escamotage, Supercherie, Acrobaties.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus. L’argot vient avec l’aptitude, pas avec une liste.',
            ],
            'capacities' => [
                [
                    'name' => 'Crochetage',
                    'type' => $guaranteed,
                    'effect' => 'Tu ouvres une serrure non magique en 1 minute (hors combat) ou 2 PA (en combat, une tentative). Test d’Escamotage. Un échec ne casse pas l’outil, mais fait du bruit.',
                ],
                [
                    'name' => 'Vol à la tire',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : tu prends un objet accessible (poche, table, ceinture) sans qu’on te voie. Test d’Escamotage contre Perception. En combat : 1 PA, seulement si la cible ne te voit pas ou est occupée.',
                ],
                [
                    'name' => 'Ombre de ruelle',
                    'type' => $freeSlot,
                    'effect' => 'Tant que tu es dans une ombre, une foule ou un décor chargé, tu as l’avantage à Discrétion pour te faufiler, pas pour rester invisible au milieu d’une place. Une scène.',
                ],
                [
                    'name' => 'Fausse clé',
                    'type' => $freeSlot,
                    'effect' => 'En 10 minutes tu copies une clé que tu as en main. Ça ouvre cette serrure-là, pas la porte d’à côté. Une copie à la fois.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On te parle comme à un des leurs. Même si tu ne l’es pas encore.',
            'capacities' => [
                [
                    'name' => 'Ombre collée',
                    'type' => $guaranteed,
                    'effect' => 'Tu suis une créature en ville sans qu’elle te remarque, tant que tu restes à 6 cases et dans la foule ou l’ombre. Test de Discrétion si elle se retourne vraiment. Une scène.',
                ],
                [
                    'name' => 'Poche profonde',
                    'type' => $freeSlot,
                    'effect' => 'Un objet de poche (une main) devient introuvable sur toi sauf fouille magique ou déshabillage. Une scène, ou jusqu’à ce que tu le sors.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Argot des voleurs',
                    'type' => 'Contextuelle (milieu)',
                    'effect' => 'Dans une ruelle, une prison, un marché gris, tu parles et tu entends l’argot. Avantage aux tests de Chance (Supercherie, Persuasion) pour qu’on te prenne pour un des leurs, pas pour qu’on te fasse confiance.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Le papier ment aussi bien que la bouche. Parfois mieux.',
            'capacities' => [
                [
                    'name' => 'Contrefaçon',
                    'type' => $guaranteed,
                    'effect' => 'En une heure tu imites un sceau, une signature, un laissez-passer vu. Test d’Escamotage ou de Supercherie contre Investigation si on l’examine. Une pièce, pas un livre.',
                ],
                [
                    'name' => 'Fuite par les toits',
                    'type' => $freeSlot,
                    'effect' => 'En ville, tu ouvres une sortie verticale : toit, corniche, gouttière. 1 PA en combat pour toi, 10 minutes pour faire passer le groupe. Test d’Acrobaties si c’est vilain.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'On te cherche. On te rate. C’est presque la même chose.',
            'capacities' => [
                [
                    'name' => 'Serrure impossible',
                    'type' => $guaranteed,
                    'effect' => 'Tu ouvres une serrure magique ou un verrou de qualité, en 10 minutes. Test d’Escamotage difficile. Un échec alerte, ça ne te cloue pas. 1×/scène.',
                ],
                [
                    'name' => 'Dérobade urbaine',
                    'type' => $freeSlot,
                    'effect' => 'Quand on te voit, tu disparais dans la foule ou l’ombre adjacente. Réaction, 1×/scène. Tu te retrouves à 3 cases, hors vue banale. Un regard magique tient encore.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Furtivité suprême',
                    'type' => 'Passive',
                    'effect' => 'Tu peux te cacher même quand on t’a déjà vu, dès qu’il y a une ombre, une foule ou un décor. Tu n’es pas invisible : tu es juste déjà ailleurs quand on se retourne.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Un nom, un visage, une porte. Ce n’est plus tout à fait toi.',
            'capacities' => [
                [
                    'name' => 'Identité volée',
                    'type' => $guaranteed,
                    'effect' => 'Tu portes le nom et les papiers d’une personne vue. Une journée en ville, avantage à Supercherie contre qui ne la connaît pas vraiment. Les proches ont avantage pour te percer. 1 réserve de Wakfu.',
                ],
                [
                    'name' => 'Cache d’équipe',
                    'type' => $freeSlot,
                    'effect' => 'Tu poses une planque (coffre, trou, grenier) que le groupe retrouve. Elle tient une semaine, hors magie qui cherche. Une à la fois.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'On ferme la main. Il n’y a plus rien. Toi non plus.',
            'capacities' => [
                [
                    'name' => 'Disparition',
                    'type' => $guaranteed,
                    'effect' => 'Tu cesses d’être là pour une minute : pas d’odeur, pas de trace, pas de bruit. On peut encore te percuter. 2 PA, 1 réserve de Wakfu, 1×/scène.',
                ],
                [
                    'name' => 'Réseau de receleurs',
                    'type' => $freeSlot,
                    'effect' => 'En ville, tu trouves en une heure quelqu’un qui achète, cache ou échange un objet chaud. Avantage au test de Chance. Le MJ tranche le prix et le risque.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Insaisissable',
                    'type' => 'Réactive (on t’attrape)',
                    'effect' => 'La première fois par scène qu’on te saisit, t’entrave ou te coinçe, tu te libères sans action. Ça ne marche pas contre la magie qui t’immobilise vraiment, ni contre une cellule déjà fermée.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes de toujours, sans y penser. Le reste, tu l’as déjà volé.',
            'capacities' => [
                [
                    'name' => 'Coup du siècle',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par mois, tu ouvres une porte qui ne devrait pas : chambre forte, salle du trône, coffre-fort de légende. Une heure de préparation, 1 réserve de Wakfu. Le MJ pose le prix, jamais un souhait.',
                ],
                [
                    'name' => 'Planque',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un intérieur sûr pour 24 heures : portes, lits, silence, une sortie de plus. Rien de créé ne survit. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
