<?php

declare(strict_types=1);

/**
 * Artiste — essai d'avant le barème de points.
 *
 * La fiche de création (private/game/rules/.../2.4.6) a un budget par palier.
 * Ce seed suit encore l'enveloppe précédente : à recaler après validation du barème.
 * Ton : docs/IA/TON.md.
 *
 * @return array<string, mixed>
 */
$pick = 'choix';

$link = static function (string $name, string $slot = 'choix'): array {
    return [
        'name' => $name,
        'type' => $slot,
        'link' => true,
    ];
};

return [
    'playable' => true,
    'envelope' => '2.4.6',
    'name' => 'Artiste',
    'importPageSlug' => 'import-specialization-artiste',
    'importPageTitle' => 'Spécialisation Artiste',
    'sectionSlugPrefix' => 'import-artiste',
    'shortDescription' => 'Tu joues, tu chantes, tu jongles ou tu tires les cartes. Socle commun au niveau 1, style au niveau 3.',
    'description' => 'Tu gagnes ta croûte sur une scène : une voix, un geste, une toile ou un jeu de cartes. Taverne d’Astrub, place de Bonta, rue de Brâkmar… on te regarde, et parfois on t’écoute vraiment.',
    'identity' => 'Tu es Artiste. Tu sais tenir l’attention d’une salle, d’une garde ou d’un allié. Tu encourages, tu détournes le regard, tu fais passer une émotion. Au niveau 1, tout le monde part du même socle. Au niveau 3, tu choisis ton style : Musicien, Saltimbanque, Troubadour ou Voyant.',
    'focus' => 'Spectacle, encouragement (Applaudissement), création, tavernes et rues',
    'characteristics' => 'Chance en premier. La seconde dépend de ton style au niveau 3.',
    'idealFor' => 'Ecaflip, Eniripsa, Pandawa, Roublard, Zobal — ou n’importe qui qui veut un métier de scène.',
    'difference' => 'Tu n’es pas là pour crocheter comme un Voleur, signer comme un Négociant, tenir une cour comme un Courtisan ou soigner une table comme un Eniripsa. Tes effets restent modestes.',
    'synergies' => [
        'Ecaflip' => 'Les cartes collent bien au Voyant.',
        'Eniripsa' => 'La voix : Musicien ou Troubadour.',
        'Pandawa' => 'Une taverne, c’est déjà une bonne scène.',
        'Roublard' => 'Détour et entrée : plutôt Saltimbanque.',
        'Zobal' => 'Masque et rôle, ça aide pour se déguiser.',
    ],
    'signature' => [
        'name' => 'Applaudissement',
        'summary' => 'Coût : 1 PA depuis la réserve de Wakfu. Un allié qui t’entend ou te voit (6 cases) gagne un dé sur son prochain jet d’attaque, de sauvegarde ou de compétence, jusqu’à la fin de son prochain tour. 1×/tour. Pas de cumul. Dé : 1d4 (1d6 au niveau 9, 1d8 au niveau 15). Au niveau 3, ton style change un peu l’usage.',
    ],
    'progression' => [
        1 => ['Outils de scène', '1 sauvegarde (Chance)', '2 compétences', '1 aptitude', '2 capacités'],
        3 => ['Style', '2ᵉ sauvegarde', '2 compétences ou 1 langue', '1 capacité'],
        6 => ['1 compétence', '1 aptitude ou 1 métier', '1 capacité', 'rituel (selon style)'],
        9 => ['1 compétence → expertise ou 1 compétence', '1 langue ou 1 compétence', '1 capacité'],
        12 => ['1 aptitude', '1 capacité', '1 métier ou 1 capacité'],
        15 => ['1 compétence → expertise ou 1 compétence', '1 capacité'],
        20 => ['1 compétence → expertise', '2 capacités'],
    ],
    'styles_at' => 3,
    'styles' => [
        'musicien' => [
            'name' => 'Musicien',
            'blurb' => 'Tu joues d’un instrument ou tu chantes. Le rythme porte la salle.',
            'save' => 'Sagesse',
            'applaudissement' => 'L’allié peut aussi ajouter le dé à un jet de dégâts (à la place d’un autre type de jet). Une seule fois par Applaudissement.',
            'ritual' => true,
        ],
        'saltimbanque' => [
            'name' => 'Saltimbanque',
            'blurb' => 'Tu jongles, tu tombes exprès, tu te faufiles. Le corps fait le spectacle.',
            'save' => 'Agilité',
            'applaudissement' => 'Tu peux te cibler pour un jet d’Acrobaties, d’Escamotage ou de Discrétion.',
            'ritual' => false,
        ],
        'troubadour' => [
            'name' => 'Troubadour',
            'blurb' => 'Tu racontes, tu écris, tu trouves la phrase qui ouvre une porte.',
            'save' => 'Intelligence',
            'applaudissement' => 'L’allié peut voir le résultat du jet avant de dépenser le dé (avant que le MJ dise si c’est réussi ou raté).',
            'ritual' => false,
        ],
        'voyant' => [
            'name' => 'Voyant',
            'blurb' => 'Tu tires les cartes ou les runes. Tu donnes des indices — pas la vérité absolue.',
            'save' => 'Sagesse',
            'applaudissement' => '1×/scène, en réaction, tu peux dépenser un Applaudissement pour soustraire le dé au jet d’une créature qui t’entend (6 cases).',
            'ritual' => true,
        ],
    ],
    'levels' => [
        1 => [
            'gains' => 'Outils de scène · 1 sauvegarde (Chance) · 2 compétences · 1 aptitude · 2 capacités',
            'flavor' => 'Tu poses ton instrument, ton masque, ton carnet ou tes cartes. La salle se tait une seconde.',
            'masteries' => [
                'Outils' => 'Un accessoire de scène : instrument, masque, carnet, maquillage ou jeu de cartes. Tu précises lequel.',
                'Jets de sauvegarde' => 'Chance.',
                'Compétences' => 'Choisis-en deux parmi : Représentation, Persuasion, Supercherie, Escamotage, Perspicacité.',
                'Métiers' => 'Aucun pour l’instant.',
                'Langues' => 'Aucune.',
            ],
            'aptitude_pick' => 1,
            'aptitudes' => [
                [
                    'name' => 'Présence de scène',
                    'type' => $pick,
                    'effect' => '1×/scène, hors combat : avantage à un test de Chance pour qu’on t’écoute ou qu’on te laisse commencer ta représentation.',
                ],
                [
                    'name' => 'Habitude de la route',
                    'type' => $pick,
                    'effect' => 'En ville ou sur la route, avantage aux tests de Chance pour trouver un toit simple ou un repas contre une courte représentation. Ça ne garantit pas qu’on te paie bien.',
                ],
                [
                    'name' => 'Main légère',
                    'type' => $pick,
                    'effect' => 'Quand tu rates un test d’Escamotage ou de Supercherie lié à un tour de scène, tu peux relancer le dé une fois (garder le second). 1×/scène.',
                ],
            ],
            'capacity_pick' => 2,
            'capacities' => [
                [
                    'name' => 'Applaudissement',
                    'type' => $pick,
                    'pa' => '1',
                    'effect' => 'Un allié qui t’entend ou te voit (9 m) gagne 1d4 sur son prochain jet d’attaque, de sauvegarde ou de compétence, jusqu’à la fin de son prochain tour. 1×/tour. Pas de cumul. Le dé passe à 1d6 au niveau 9 et à 1d8 au niveau 15.',
                ],
                $link('Illusion mineure'),
                $link('Message'),
                $link('Texte illusoire'),
                $link('Déguisement'),
                $link('Assistance'),
            ],
        ],
        3 => [
            'gains' => 'Style · 2ᵉ sauvegarde · 2 compétences ou 1 langue · 1 capacité',
            'flavor' => 'On commence à te reconnaître. Tu choisis comment tu te produis.',
            'unlock_style' => true,
            'masteries' => [
                'Style' => 'Choisis Musicien, Saltimbanque, Troubadour ou Voyant. Ce choix est définitif.',
                'Jets de sauvegarde' => 'Tu gagnes la 2ᵉ sauvegarde de ton style (Sagesse, Agilité ou Intelligence).',
                'Compétences ou langue' => 'Soit 2 compétences supplémentaires parmi la liste du niveau 1 (ou Acrobaties, Athlétisme, Discrétion, Histoire, Arcanes, Religion selon ton style), soit 1 langue.',
            ],
            'capacity_pick' => 1,
            'capacities' => [
                [
                    'name' => 'Numéro de scène',
                    'type' => $pick,
                    'archetype_variants' => [
                        'musicien' => [
                            'name' => 'Contre-refrain',
                            'pa' => '1',
                            'effect' => 'Réaction. Toi et un allié dans 9 m qui t’entendent : avantage aux sauvegardes contre la peur et le charme jusqu’à la fin de ton prochain tour. 1×/scène.',
                        ],
                        'saltimbanque' => [
                            'name' => 'Culbute',
                            'pa' => '1',
                            'effect' => 'Tu te déplaces de 5 m. Le prochain test pour te tacle ou te saisir a un désavantage. 1×/tour.',
                        ],
                        'troubadour' => [
                            'name' => 'Vers qui ouvre',
                            'effect' => 'Après une minute de récit ou de lecture, avantage au prochain test d’Histoire, d’Investigation ou de Perspicacité lié à ce qui se passe. 1×/scène.',
                        ],
                        'voyant' => [
                            'name' => 'Petit tirage',
                            'pa' => '1',
                            'effect' => 'Le MJ te donne un présage court (oui / non / vague) sur une action qui arrive bientôt. 1×/scène.',
                        ],
                    ],
                ],
                $link('Déguisement'),
                $link('Illusion mineure'),
                $link('Bouche magique'),
                $link('Suggestion'),
                $link('Charme personne'),
            ],
        ],
        6 => [
            'gains' => '1 compétence · 1 aptitude ou 1 métier · 1 capacité · rituel (selon style)',
            'flavor' => 'Tu n’as plus besoin de forcer. Les gens viennent d’eux-mêmes.',
            'masteries' => [
                'Compétences' => '1 compétence (liste du niveau 1 ou du style).',
                'Aptitude ou métier' => 'Soit 1 aptitude ci-dessous, soit 1 métier d’artisanat de scène (Tailleur, Sculpteur, Bijoutier…). Pas de métier de récolte.',
            ],
            'ritual' => 'Si tu es Musicien ou Voyant : tu peux lancer une capacité d’Artiste compatible en rituel (10 minutes de concentration, sans coût en PA). Saltimbanque et Troubadour : pas encore.',
            'aptitude_pick' => 1,
            'aptitudes' => [
                [
                    'name' => 'Oreille du public',
                    'type' => $pick,
                    'effect' => 'Sur un test d’outil ou de compétence que tu ne maîtrises pas, tu n’as pas le désavantage du « jamais vu ».',
                ],
                [
                    'name' => 'Troupe familière',
                    'type' => $pick,
                    'effect' => 'Quand tu te produis avec au moins un allié qui t’aide, vous avez tous l’avantage au premier test de Représentation de la scène.',
                ],
            ],
            'capacity_pick' => 1,
            'capacities' => [
                $link('Silence'),
                $link('Invisibilité'),
                $link('Image majeure'),
                $link('Suggestion'),
                $link('Déguisement'),
                $link('Zone de vérité'),
            ],
        ],
        9 => [
            'gains' => '1 compétence → expertise ou 1 compétence · 1 langue ou 1 compétence · 1 capacité',
            'flavor' => 'Ton Applaudissement porte plus loin. Ton numéro à toi devient une habitude.',
            'masteries' => [
                'Compétence' => 'Soit une expertise sur une compétence que tu maîtrises déjà, soit 1 nouvelle compétence.',
                'Langue ou compétence' => 'Soit 1 langue, soit 1 compétence de plus.',
                'Applaudissement' => 'Ton dé d’Applaudissement passe à 1d6.',
            ],
            'capacity_pick' => 1,
            'capacities' => [
                [
                    'name' => 'Tour de maître',
                    'type' => $pick,
                    'archetype_variants' => [
                        'musicien' => [
                            'name' => 'Entracte',
                            'effect' => 'Pendant un repos court, 10 minutes de musique ou de chant : jusqu’à 6 allié·e·s qui t’entendent gagnent 1d6 PV temporaires. 1×/repos.',
                        ],
                        'saltimbanque' => [
                            'name' => 'Jonglage de détournement',
                            'pa' => '2',
                            'effect' => 'Coût : 2 PA depuis la réserve de Wakfu. Une créature qui te voit a un désavantage à sa prochaine attaque contre un allié (pas contre toi). 1×/scène.',
                        ],
                        'troubadour' => [
                            'name' => 'Contre-réplique',
                            'pa' => '1',
                            'effect' => 'Réaction. Coût : 1 PA depuis la réserve de Wakfu. Un allié dans 6 cases relance un jet de sauvegarde de Sagesse contre la peur ou le charme. 1×/scène.',
                        ],
                        'voyant' => [
                            'name' => 'Mauvais présage',
                            'pa' => '2',
                            'effect' => 'Coût : 2 PA depuis la réserve de Wakfu. Une créature qui t’entend a un désavantage à son prochain jet d’attaque ou de sauvegarde. 1×/scène.',
                        ],
                    ],
                ],
                $link('Invisibilité'),
                $link('Image majeure'),
                $link('Suggestion'),
                $link('Détection des pensées'),
                $link('Charme personne'),
            ],
        ],
        12 => [
            'gains' => '1 aptitude · 1 capacité · 1 métier ou 1 capacité',
            'flavor' => 'On raconte déjà des choses sur toi. À toi de choisir lesquelles restent.',
            'masteries' => [
                'Métier ou capacité' => 'Soit 1 métier d’artisanat de scène, soit une 2ᵉ capacité parmi la liste ci-dessous (en plus de ton choix principal).',
            ],
            'aptitude_pick' => 1,
            'aptitudes' => [
                [
                    'name' => 'Manipulateur subtil',
                    'type' => $pick,
                    'effect' => 'Devant une salle, une cour ou une table : avantage aux tests de Chance (Persuasion, Supercherie) pour faire croire une émotion. Pas pour faire signer un contrat.',
                ],
                [
                    'name' => 'Légende vivante',
                    'type' => $pick,
                    'effect' => '1×/jour, quand quelqu’un a entendu parler de toi, avantage au premier test social de la scène avec cette personne.',
                ],
            ],
            'capacity_pick' => 1,
            'capacities' => [
                $link('Confusion'),
                $link('Image majeure'),
                $link('Lévitation'),
                $link('Envoi de message'),
                $link('Invisibilité'),
                $link('Zone de vérité'),
            ],
        ],
        15 => [
            'gains' => '1 compétence → expertise ou 1 compétence · 1 capacité',
            'flavor' => 'Un geste, et la salle se lève. Un autre, et elle s’assoit.',
            'masteries' => [
                'Compétence' => 'Soit une expertise, soit 1 nouvelle compétence.',
                'Applaudissement' => 'Ton dé d’Applaudissement passe à 1d8.',
            ],
            'capacity_pick' => 1,
            'capacities' => [
                [
                    'name' => 'Grande première',
                    'type' => $pick,
                    'archetype_variants' => [
                        'musicien' => [
                            'name' => 'Salle entière en chœur',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Jusqu’à six créatures qui t’entendent : même effet qu’Accroche, un seul jet pour le groupe (le plus haut). 1×/scène.',
                        ],
                        'saltimbanque' => [
                            'name' => 'Cascade finale',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Tu te déplaces jusqu’à ta vitesse. Les tests de tacle contre toi ont un désavantage. Un allié adjacent qui t’entend gagne un Applaudissement sans en payer le coût. 1×/scène.',
                        ],
                        'troubadour' => [
                            'name' => 'Mot d’ordre',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Jusqu’à six créatures qui te comprennent : avantage à leur prochain test de Persuasion ou d’Intimidation lié à ta phrase. 1×/scène.',
                        ],
                        'voyant' => [
                            'name' => 'Fil du destin',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Un allié dans 6 cases peut relancer un jet d’attaque, de sauvegarde ou de compétence qu’il vient de faire. Il garde le second. 1×/scène.',
                        ],
                    ],
                ],
                $link('Vol'),
                $link('Image majeure'),
                $link('Suggestion'),
                $link('Scrutation'),
                $link('Métamorphose'),
            ],
        ],
        20 => [
            'gains' => '1 compétence → expertise · 2 capacités',
            'flavor' => 'Deux gestes, et c’est réglé. Le reste, tu l’as déjà joué mille fois.',
            'masteries' => [
                'Expertise' => '1 expertise sur une compétence que tu maîtrises déjà.',
            ],
            'capacity_pick' => 2,
            'capacities' => [
                [
                    'name' => 'Apothéose',
                    'type' => $pick,
                    'effect' => '1×/jour. Pendant une minute, les alliés qui te voient ou t’entendent ont l’avantage à tous leurs jets, et la première attaque contre toi rate.',
                ],
                $link('Petite hutte'),
                $link('Scrutation'),
                $link('Divination'),
                $link('Métamorphose'),
                $link('Image majeure'),
            ],
        ],
    ],
];
