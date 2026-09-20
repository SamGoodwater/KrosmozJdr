<?php

declare(strict_types=1);

/**
 * Fiche jouable de spécialisation (gabarit 2.4.2.6) : l’Explorateur·rice.
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
    'name' => 'Explorateur·rice',
    'importPageSlug' => 'import-specialization-explorateur-rice',
    'importPageTitle' => 'Spécialisation Explorateur·rice',
    'sectionSlugPrefix' => 'import-explorateur-rice',
    'shortDescription' => 'Spécialisation orientée découverte, terrain et autonomie.',
    'description' => 'Tu lis le sol, le vent, les traces. Incarnam, routes d’Amakna, crêtes des Montagnes : tu sais où tu vas, depuis combien de temps, et ce qui t’a précédé.',
    'identity' => 'En tant qu’Explorateur·rice, tu n’es pas un Crâ de plus. Tes capacités servent à ne pas te perdre, à voir le piège, à faire passer le groupe. On vient te chercher pour la piste, pas pour la forêt habitée (c’est le Sylvain) ni pour la mer (c’est le Marin).',
    'focus' => 'Orientation, survie, traces, pièges, cartes',
    'characteristics' => 'Agilité (principale). Sagesse pour lire le terrain.',
    'idealFor' => 'Crâ, Ouginak, Forgelance, Sram ; tout personnage qui veut un métier de piste hors des sorts de classe.',
    'difference' => 'L’Explorateur·rice n’est pas un ranger D&D, ni un Sylvain. Tes capacités tiennent sur une carte, une trace, un camp. Tu ne parles pas aux bêtes, tu ne commandes pas un navire.',
    'synergies' => [
        'Crâ' => 'L’œil long et la piste : tu vois arriver, tu sais par où rentrer.',
        'Ouginak' => 'Le nez et la carte : tu suis, tu ne t’égares pas.',
        'Forgelance' => 'L’avant-garde qui plante la lance et le camp.',
        'Sram' => 'Les traces qu’on ne veut pas laisser, tu les lis quand même.',
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses une boussole, une corde, un carnet. Le premier geste, c’est de savoir dans quelle direction tu vas.',
            'masteries' => [
                'Outils' => 'Boussole, corde, carnet de cartes. Selon tes routes : grappin, lanterne, filtre à eau.',
                'Jets de sauvegarde' => 'Agilité, Sagesse.',
                'Compétences' => 'Choisis-en trois parmi : Survie, Perception, Nature, Athlétisme, Investigation.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus.',
            ],
            'capacities' => [
                [
                    'name' => 'Orientation',
                    'type' => $guaranteed,
                    'effect' => 'Hors combat, tu sais toujours le nord et le temps écoulé depuis le dernier camp, tant que tu es à l’air libre. En 10 minutes tu traces un itinéraire simple. Le MJ tranche si le ciel est magiquement brouillé.',
                ],
                [
                    'name' => 'Lecture de traces',
                    'type' => $guaranteed,
                    'effect' => 'Variante de garantie : 10 minutes sur un sol. Tu dis qui a passé (taille, nombre, fraîcheur), pas ce qu’il pensait. Un succès de Survie suffit. La magie qui efface les traces te ferme la porte.',
                ],
                [
                    'name' => 'Détection de pièges',
                    'type' => $freeSlot,
                    'effect' => 'En avançant à vitesse réduite, tu as l’avantage pour voir les pièges non magiques dans 3 cases. En combat : 1 PA pour inspecter une case adjacente.',
                ],
                [
                    'name' => 'Feu de camp',
                    'type' => $freeSlot,
                    'effect' => 'En 10 minutes tu poses un camp sommaire : feu, abri contre la pluie, veille. Le groupe ignore le désavantage du froid simple pour un repos. 1×/jour.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'Tu ne te perds plus. Les autres, parfois, si tu les lâches.',
            'capacities' => [
                [
                    'name' => 'Cachette de fortune',
                    'type' => $guaranteed,
                    'effect' => 'En 10 minutes tu trouves ou improvises un abri pour le groupe (6 créatures) : hors vue d’une route, d’une patrouille banale. Une nuit. La magie qui cherche encore te trouve.',
                ],
                [
                    'name' => 'Rappel de piste',
                    'type' => $freeSlot,
                    'effect' => 'Tu marques un lieu foulé (cairn, entaille, nœud). Pendant 24 heures tu sais dans quelle direction il se trouve dans un rayon d’un kilomètre. 1×/jour.',
                ],
                [
                    'name' => 'Marque de sentier',
                    'type' => $freeSlot,
                    'effect' => 'Tu laisses trois signes que seuls tes allié·e·s lisent sans jet. Ils restent jusqu’au prochain repos long, ou jusqu’à la pluie battante. 1 minute.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Jamais vraiment perdu',
                    'type' => 'Passive',
                    'effect' => 'Tant que tu voyages à l’air libre, tu sais dans quelle direction tu vas et depuis combien de temps tu marches. Terrain difficile, nuit sans étoiles ou itinéraire brouillé ne t’égarent pas, sauf magie explicite.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Tu dessines pour que les autres reviennent. Pas pour décorer.',
            'capacities' => [
                [
                    'name' => 'Cartographe',
                    'type' => $guaranteed,
                    'effect' => 'Après une journée de marche, tu poses une carte utilisable : le groupe a l’avantage pour retracer le même chemin. Une région à la fois. 1×/voyage.',
                ],
                [
                    'name' => 'Passage discret',
                    'type' => $freeSlot,
                    'effect' => 'Pendant une heure, le groupe (6) voyage sans laisser de traces évidentes. Vitesse réduite. Ça ne trompe pas un nez magique.',
                ],
                [
                    'name' => 'Fil d’Ariane',
                    'type' => $freeSlot,
                    'effect' => 'Pendant une heure, tu retrouves le chemin déjà parcouru aujourd’hui, même dans le noir ou la brume. Magie qui brouille : le MJ tranche.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Un geste, un cercle, un silence. Le camp tient.',
            'capacities' => [
                [
                    'name' => 'Camp sûr',
                    'type' => $guaranteed,
                    'effect' => 'Tu scelles un campement pour 8 heures : les oreilles indiscrètes et les bêtes banales glissent. Une créature qui force l’entrée te réveille. 1 réserve de Wakfu.',
                ],
                [
                    'name' => 'Corde et grappin',
                    'type' => $freeSlot,
                    'effect' => 'Tu ouvres un passage vertical (falaise, mur, puits) pour le groupe. 10 minutes, test de Survie ou d’Athlétisme si c’est vilain. Une fois le passage posé, les autres n’ont pas à le refaire.',
                ],
                [
                    'name' => 'Ration de marche',
                    'type' => $freeSlot,
                    'effect' => 'Avec 10 minutes et ce que le terrain offre, tu improvises un repas pour six. Ça calme la faim, pas un repos. 1×/jour.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Rituel',
                    'type' => 'Contextuelle (repos)',
                    'effect' => 'Quand le groupe pose un camp sous ta direction, le repos court suffit là où d’autres auraient besoin d’un repos long pour la fatigue de marche (pas pour les sorts). 1×/jour.',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'Tu as déjà foulé ce continent. Il te le rend.',
            'capacities' => [
                [
                    'name' => 'Piste ouverte',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par jour, tu trouves un raccourci : le voyage du jour prend la moitié du temps, ou évite une rencontre banale. Le MJ tranche si le terrain refuse.',
                ],
                [
                    'name' => 'Veille',
                    'type' => $freeSlot,
                    'effect' => 'Tu montes la garde. Tant que tu es éveillé·e, le groupe ne peut pas être surpris par ce que tu pourrais voir ou entendre. Un repos, tu dors moins : pas d’autre bénéfice de veille.',
                ],
                [
                    'name' => 'Abri de toile',
                    'type' => $freeSlot,
                    'effect' => 'En 10 minutes tu poses un abri contre pluie et vent pour six. Tient jusqu’au prochain repos. Pas un fortin.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Tu vois le campement d’hier, le piège d’avant-hier, et celui qu’on n’a pas encore tendu.',
            'capacities' => [
                [
                    'name' => 'Guide du groupe',
                    'type' => $guaranteed,
                    'effect' => 'Pendant un voyage, tes allié·e·s ont l’avantage aux tests de Survie et de Perception que tu diriges. Ils n’ont pas le tien : tu montres, ils suivent.',
                ],
                [
                    'name' => 'Traversée impossible',
                    'type' => $freeSlot,
                    'effect' => 'Une fois par semaine, tu fais passer le groupe (6) un obstacle qui devrait les arrêter : crête, marais, éboulis. Une heure, 1 réserve de Wakfu. Un mur de magie tient encore.',
                ],
                [
                    'name' => 'Relais de piste',
                    'type' => $freeSlot,
                    'effect' => 'Tu caches un sac (1 jour de rations, une corde, une lanterne) en un lieu déjà foulé. Tu le retrouves plus tard. 1 cache à la fois.',
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Œil de l’éclaireur·euse',
                    'type' => 'Passive',
                    'effect' => 'En terrain naturel, tu repères sans jet les traces de passage récentes, les campements abandonnés et les pièges non dissimulés par magie. Le groupe ne peut pas être surpris tant que tu es éveillé·e et en mesure de voir.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Le Monde a des plis. Tu les as tous marchés, une fois.',
            'capacities' => [
                [
                    'name' => 'Carte du Monde',
                    'type' => $guaranteed,
                    'effect' => 'Une fois par mois, tu ouvres un chemin vers un lieu que tu as déjà foulé, même sur un autre continent. Le groupe (6) arrive au plus près praticable. Un lieu jamais vu exige un ancrage (objet, carte, témoin).',
                ],
                [
                    'name' => 'Refuge de fin de piste',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un camp sûr pour 24 heures : toiles, feu, silence extérieur. Rien de créé ne survit. 1 réserve de Wakfu, 1×/jour.',
                ],
                [
                    'name' => 'Bivouac de poche',
                    'type' => $freeSlot,
                    'effect' => 'Tu déplies un camp sûr pour 8 heures : tentes, feu, silence extérieur. 1 réserve de Wakfu, 1×/jour.',
                ],
            ],
        ],
    ],
];
