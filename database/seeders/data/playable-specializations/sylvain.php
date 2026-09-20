<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : le Sylvain·e.
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
                    'name' => 'Langage des bêtes simples',
                    'type' => $guaranteed,
                    'effect' => 'Tu communiques des intentions simples (danger, nourriture, « pars ») avec un animal non magique. Une scène. 1 PA en combat. Pas d’interrogatoire philosophique.',
                ],
                [
                    'name' => 'Sentier lu',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : 10 minutes en milieu naturel, tu sais par où on est passé aujourd’hui (bêtes, gens, charrettes). Pas une carte de donjon de pierre.',
                ],
                [
                    'name' => 'Cueillette sûre',
                    'type' => $freeSlot,
                    'effect' => 'Tu distingues plante utile, toxique, sacrée. Avantage aux tests d’Alchimiste et de Nature pour identifier une ressource végétale. 10 minutes.',
                ],
                [
                    'name' => 'Eau claire',
                    'type' => $freeSlot,
                    'effect' => 'Tu trouves de l’eau potable en nature (10 minutes) ou tu rends potable une gourde douteuse (1 minute). Suffisant pour le groupe, pas pour une armée.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'Tes pas ne font plus de bruit dans la mousse. Les bêtes te jaugent avant de fuir.',
            'capacities' => [
                [
                    'name' => 'Pas dans la mousse',
                    'type' => $guaranteed,
                    'effect' => 'En milieu naturel, tu te déplaces sans laisser de traces évidentes. Avantage à la Discrétion seulement en nature, pas en donjon de pierre. Tant que tu ne cours pas.',
                ],
                [
                    'name' => 'Territoire lu',
                    'type' => $freeSlot,
                    'effect' => 'Après 10 minutes, tu sais si la zone est chassée, sacrée, malade, ou occupée par quelque chose de trop grand. Un fait, pas une carte complète.',
                ],
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
                [
                    'name' => 'Venin et antidote',
                    'type' => $freeSlot,
                    'effect' => 'Pendant un repos, tu prépares une dose (poison ou antidote naturel). Poison : 1d4, une blessure. Antidote : comme Soins de sous-bois contre Empoisonné. 1 dose, 1×/jour.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Tu n’invoques pas. Tu appelles. La différence tient dans le mot « local ».',
            'capacities' => [
                [
                    'name' => 'Appel discret',
                    'type' => $guaranteed,
                    'effect' => 'Tu attires un animal local (messager, sentinelle, diversion) pour une scène. Pas un tank de combat. 1 réserve de Wakfu. L’Osamodas reste le roi des invocations.',
                ],
                [
                    'name' => 'Racines gênantes',
                    'type' => $freeSlot,
                    'effect' => 'En nature, 2 PA : une case devient terrain difficile jusqu’à la fin de ton prochain tour. 1×/tour. Pas un mur de Féca.',
                ],
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
                    'effect' => 'En forêt, jungle ou marais : racines, brouillard de pollen ou essaim gênant, un cube de 4 cases, 1 minute. Les créatures dans la zone ont désavantage à la Perception. 2 PA, 1 réserve de Wakfu, 1×/scène.',
                ],
                [
                    'name' => 'Piste d’âme',
                    'type' => $freeSlot,
                    'effect' => 'Tu suis une créature à travers la nature même après la pluie, tant qu’elle n’a pas pris un zaap. 1 heure de piste, 1×/jour. Le MJ coupe si ça casse une quête.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Tu poses un cercle. Le camp tient. Les bêtes curieuses passent leur chemin.',
            'capacities' => [
                [
                    'name' => 'Cercle de saison',
                    'type' => $guaranteed,
                    'effect' => 'Rituel, 1 heure : un camp en nature devient plus sûr pour 8 heures (veille, météo, bêtes curieuses). Pas un mur de Féca. 1 réserve de Wakfu, 1×/jour.',
                ],
                [
                    'name' => 'Seigneur·e d’un lieu',
                    'type' => $freeSlot,
                    'effect' => 'Tu te lies à un territoire (forêt, île, marais) en une nuit. Dedans : avantage aux tests de Sagesse et de Nature. Dehors : plus rien. Idéal campagne sédentaire ; à refuser en one-shot.',
                ],
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
                [
                    'name' => 'Canopée de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un abri vivant pour 8 heures : ombre, fruits, silence des prédateurs. Rien de créé ne survit. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
