<?php

declare(strict_types=1);

/**
 * Fiches de présentation des 19 classes : identité, pas un dump de passif.
 *
 * Une section CMS « Texte » par classe. Le passif, les voies et les sorts restent
 * sur la fiche Full ; ici on fait imaginer au joueur ou à la joueuse un historique,
 * une façon de jouer, une identité. Gabarit : `presentation`, quelques paragraphes
 * simples pour les 19 classes. Féca a servi d’étalon de ton.
 * Le Crâ ajoute une enveloppe `class_progression` (tableau + section par niveau).
 *
 * @return list<array<string, mixed>>
 */
return [
    [
        'name' => 'Iop',
        'passive' => 'Tête brûlée',
        'presentation' => [
            'Tu es Iop. Épée en avant, tu fonces. On te dit tête brûlée. Toi, tu dis que tu n’as pas peur.',
            'En combat, tu te prépares, tu bondis au contact et tu frappes. Ta voie dit comment : la Terre frappe, l’Air place, le Feu renforce, l’Eau protège.',
            'Côté spécialisation, Milicien·ne tient le front. Explorateur·rice va bien au casse-cou. Un Iop Érudit ou Courtisan, c’est un contre-emploi.',
        ],
    ],
    [
        'name' => 'Crâ',
        'passive' => 'Œil de Crâ',
        'presentation' => [
            'Tu es Crâ. Arc en main, tu tires de loin. Plus à l’aise à trente mètres qu’au contact.',
            'Ton Crâ a grandi dans une tour de guet, chassé pour le village, ou tiré pour une milice. Tu observes, tu choisis, tu ne lâches plus.',
            'En combat, tu restes en retrait, tu repousses ce qui s’approche et tu tires. Ta voie dit comment : l’Air place, la Terre frappe, le Feu renforce tes tirs, l’Eau ralentit. Hors combat, tu es les yeux du groupe.',
            'Côté spécialisation, Explorateur·rice ou Voleur·euse prolongent l’œil. Milicien·ne donne un Crâ de garnison, posté au créneau.',
        ],
        'class_progression' => [
            'hit_die' => 'd6',
            'hp_random' => '1d6',
            'hp_stable' => '6',
            'levels' => [
                1 => [
                    'flavor' => 'Tu poses ton arc, tu choisis trois sorts, tu répartis tes points. L’œil de Crâ est déjà là.',
                    'spells' => [
                        ['Flèche harcelante', 'Flèche enflammée', 'Flèche d’immobilisation', 'Flèche du vagabond'],
                        ['Flèche évasive', 'Tir de repli'],
                        ['Tir éloigné', 'Tir perçant'],
                    ],
                    'extra' => 'Tu répartis 10 points de caractéristique (toutes à 8). Option : une carac à 6 pour récupérer 2 points (voir répartition des points).',
                    'recap' => [
                        '[[kref:entity:spells:Flèche harcelante|Flèche harcelante]] / [[kref:entity:spells:Flèche enflammée|Flèche enflammée]] / [[kref:entity:spells:Flèche d’immobilisation|Flèche d’immobilisation]] / [[kref:entity:spells:Flèche du vagabond|Flèche du vagabond]]',
                        '[[kref:entity:spells:Flèche évasive|Flèche évasive]] / [[kref:entity:spells:Tir de repli|Tir de repli]]',
                        '[[kref:entity:spells:Tir éloigné|Tir éloigné]] / [[kref:entity:spells:Tir perçant|Tir perçant]]',
                        '10 pts carac',
                    ],
                ],
                2 => [
                    'spells' => [
                        ['Vendetta', 'Œil pour œil'],
                    ],
                    'recap' => [
                        '[[kref:entity:spells:Vendetta|Vendetta]] / [[kref:entity:spells:Œil pour œil|Œil pour œil]]',
                    ],
                ],
                3 => [
                    'class_capability' => 'Flèche guidée par le Wakfu',
                    'recap' => [
                        '[[kref:entity:capabilities:Flèche guidée par le Wakfu|Flèche guidée par le Wakfu]]',
                    ],
                ],
                4 => [
                    'spells' => [
                        ['Flèche de recul', 'Flèche dévorante', 'Œil de taupe', 'Flèche punitive'],
                    ],
                    'carac_or_trait' => true,
                    'recap' => [
                        '[[kref:entity:spells:Flèche de recul|Flèche de recul]] / [[kref:entity:spells:Flèche dévorante|Flèche dévorante]] / [[kref:entity:spells:Œil de taupe|Œil de taupe]] / [[kref:entity:spells:Flèche punitive|Flèche punitive]]',
                        '2 pts carac ou 1 trait',
                    ],
                ],
                5 => [
                    'spells' => [
                        ['Tir puissant', 'Représailles'],
                    ],
                    'recap' => [
                        '[[kref:entity:spells:Tir puissant|Tir puissant]] / [[kref:entity:spells:Représailles|Représailles]]',
                    ],
                ],
                6 => [
                    'spells' => [
                        ['Balise tactique', 'Balise de rappel'],
                    ],
                    'recap' => [
                        '[[kref:entity:spells:Balise tactique|Balise tactique]] / [[kref:entity:spells:Balise de rappel|Balise de rappel]]',
                    ],
                ],
                7 => [
                    'spells' => [
                        ['Flèche boomerang', 'Flèche explosive', 'Flèche ralentissante', 'Flèche massacrante'],
                    ],
                    'recap' => [
                        '[[kref:entity:spells:Flèche boomerang|Flèche boomerang]] / [[kref:entity:spells:Flèche explosive|Flèche explosive]] / [[kref:entity:spells:Flèche ralentissante|Flèche ralentissante]] / [[kref:entity:spells:Flèche massacrante|Flèche massacrante]]',
                    ],
                ],
                8 => [
                    'spells' => [
                        ['Flèche écrasante', 'Acuité absolue'],
                    ],
                    'recap' => [
                        '[[kref:entity:spells:Flèche écrasante|Flèche écrasante]] / [[kref:entity:spells:Acuité absolue|Acuité absolue]]',
                    ],
                ],
                9 => [
                    'class_capability' => 'Pistage',
                    'recap' => [
                        '[[kref:entity:capabilities:Pistage|Pistage]]',
                    ],
                ],
                10 => [
                    'spells' => [
                        ['Pas chassé', 'Flèche empoisonnée'],
                    ],
                    'carac_or_trait' => true,
                    'recap' => [
                        '[[kref:entity:spells:Pas chassé|Pas chassé]] / [[kref:entity:spells:Flèche empoisonnée|Flèche empoisonnée]]',
                        '2 pts carac ou 1 trait',
                    ],
                ],
                11 => [
                    'spells' => [
                        ['Sentinelle', 'Flèches amoureuses'],
                    ],
                    'recap' => [
                        '[[kref:entity:spells:Sentinelle|Sentinelle]] / [[kref:entity:spells:Flèches amoureuses|Flèches amoureuses]]',
                    ],
                ],
                12 => [
                    'spells' => [
                        ['Pluie de flèches', 'Flèche tyrannique', 'Flèche paralysante', 'Carreaux destructeurs'],
                    ],
                    'extra' => 'Les 12 sorts de classe sont appris.',
                    'recap' => [
                        '[[kref:entity:spells:Pluie de flèches|Pluie de flèches]] / [[kref:entity:spells:Flèche tyrannique|Flèche tyrannique]] / [[kref:entity:spells:Flèche paralysante|Flèche paralysante]] / [[kref:entity:spells:Carreaux destructeurs|Carreaux destructeurs]]',
                    ],
                ],
                13 => [
                    'recap' => ['+1 DV'],
                ],
                14 => [
                    'class_capability' => 'Tir parfait',
                    'recap' => [
                        '[[kref:entity:capabilities:Tir parfait|Tir parfait]]',
                    ],
                ],
                15 => [
                    'recap' => ['+1 DV'],
                ],
                16 => [
                    'carac_or_trait' => true,
                    'recap' => ['2 pts carac ou 1 trait'],
                ],
                17 => [
                    'recap' => ['+1 DV'],
                ],
                18 => [
                    'recap' => ['+1 DV'],
                ],
                19 => [
                    'recap' => ['+1 DV'],
                ],
                20 => [
                    'recap' => ['+1 DV'],
                ],
            ],
        ],
    ],
    [
        'name' => 'Féca',
        'passive' => 'Glyphes',
        'presentation' => [
            'Tu es Féca. Bâton en main, signes lumineux autour des poignets, glyphes au sol. L’armure élémentaire se voit : un éclat devant toi, un rempart autour des tiens. À Astrub comme à Bonta, on te reconnaît avant le premier coup.',
            'En combat, tu poses le terrain, tu couvres les allié·e·s, puis tu déclenches au bon moment. Ta voie dit comment : l’Air place, le Feu entrave, l’Eau protège, la Terre frappe.',
            'Côté spécialisation, Milicien·ne tient le seuil à côté de tes glyphes. Dévot va bien si tu veux une garde plus posée.',
        ],
    ],
    [
        'name' => 'Osamodas',
        'passive' => 'Troupeau',
        'presentation' => [
            'Tu es Osamodas. Une bête à tes côtés dès le premier combat. Tu appelles, et parfois elle répond.',
            'En combat, tu poses la horde et tu la fais tenir. Ta voie dit comment : l’Air place, le Feu soigne, l’Eau frappe, la Terre entrave.',
            'Côté spécialisation, Sylvain·e ou Explorateur·rice vont bien à celui qui voyage avec une créature.',
        ],
    ],
    [
        'name' => 'Enutrof',
        'passive' => 'Pelle',
        'presentation' => [
            'Tu es Enutrof. Pelle à la main, tu ralentis et tu ramasses. Les coffres ne restent pas fermés longtemps.',
            'En combat, tu coupes le pas, puis tu frappes ce qui ne bouge plus. Ta voie dit comment : l’Air entrave, la Terre frappe, l’Eau renforce, le Feu soigne.',
            'Côté spécialisation, Négociant·e ou Voleur·euse s’occupent de ce que tu as trouvé.',
        ],
    ],
    [
        'name' => 'Sram',
        'passive' => 'Pièges',
                'presentation' => [
            'Tu es Sram. Tu poses, tu disparais, tu reviens. On ne te voit qu’au moment du coup.',
            'En combat, le sol ment. Ta voie dit comment : l’Air entrave, la Terre frappe, l’Eau renforce, le Feu place.',
            'Côté spécialisation, Voleur·euse ou Explorateur·rice prolongent la discrétion.',
        ],
    ],
    [
        'name' => 'Xélor',
        'passive' => 'Sablier',
                'presentation' => [
            'Tu es Xélor. Horloge au poignet, tu joues avec l’heure. Tu ralentis, tu téléportes, tu arrives pile.',
            'En combat, tu gardes un geste d’avance. Ta voie dit comment : l’Air place, la Terre frappe, l’Eau entrave, le Feu renforce.',
            'Côté spécialisation, Érudit va bien à celui qui lit le temps. Artisan·e va bien si tu aimes les mécanismes.',
        ],
    ],
    [
        'name' => 'Ecaflip',
        'passive' => 'Retomber sur ses pattes',
        'presentation' => [
            'Tu es Ecaflip. Tu paries, tu griffes, tu retombes sur tes pattes. La chance, tu la provoques.',
            'En combat, tu lances la pièce et le coup suit la face. Ta voie dit comment : l’Eau frappe, le Feu soigne, la Terre entrave, l’Air place.',
            'Côté spécialisation, Artiste, Négociant·e ou Voleur·euse prolongent le bluff. Courtisan·e donne un Ecaflip des salons.',
        ],
    ],
    [
        'name' => 'Eniripsa',
        'passive' => 'Encouragement',
                'presentation' => [
            'Tu es Eniripsa. Un mot, et le groupe tient debout. Ailes, Lapino, parfois une fée qui porte le sort plus loin.',
            'En combat, tu soignes, tu couvres, tu encourages. Ta voie dit comment : l’Air renforce, la Terre entrave, l’Eau protège, le Feu soigne.',
            'Côté spécialisation, Dévot ou Artiste prolongent la voix qui relève.',
        ],
    ],
    [
        'name' => 'Sadida',
        'passive' => 'Jardin',
                'presentation' => [
            'Tu es Sadida. Poupées de sève, ronces, poison. La forêt avance avec toi.',
            'En combat, tu plantes, puis tu fais jouer le terrain. Ta voie dit comment : l’Air frappe, la Terre protège, l’Eau soigne, le Feu entrave.',
            'Côté spécialisation, Sylvain·e va bien à celui qui vit déjà dans les bois.',
        ],
    ],
    [
        'name' => 'Sacrieur',
        'passive' => 'Martyre',
                'presentation' => [
            'Tu es Sacrieur. On te frappe, tu rends. Plus tu as mal, plus tu tiens.',
            'En combat, tu prends le coup, puis tu le rends. Ta voie dit comment : l’Air place, la Terre encaisse, l’Eau frappe, le Feu entrave.',
            'Côté spécialisation, Milicien·ne ou Dévot tiennent la ligne avec toi.',
        ],
    ],
    [
        'name' => 'Pandawa',
        'passive' => 'Portage',
                'presentation' => [
            'Tu es Pandawa. Tu portes un allié, un tonneau, parfois un ennemi. Le lait de bambou reste un choix.',
            'En combat, tu soulèves, tu lances, tu changes la carte. Ta voie dit comment : l’Air place, la Terre frappe, l’Eau entrave, le Feu encaisse.',
            'Côté spécialisation, Artiste ou Marin·e vont bien à celui qui vit déjà entre taverne et quai.',
        ],
    ],
    [
        'name' => 'Roublard',
        'passive' => 'Mèche',
                'presentation' => [
            'Tu es Roublard. Poudre, mèche, bombes qu’on voit. Tu fais sauter le plan.',
            'En combat, tu poses, tu charges, tu déclenches. Ta voie dit comment : l’Air place, la Terre renforce, l’Eau entrave, le Feu frappe.',
            'Côté spécialisation, Voleur·euse ou Artiste prolongent le détour et le spectacle.',
        ],
    ],
    [
        'name' => 'Zobal',
        'passive' => 'Masque',
                'presentation' => [
            'Tu es Zobal. Tu changes de masque. Le rôle change, les sorts restent.',
            'En combat, tu choisis un visage, puis tu frappes ou tu couvres. Ta voie dit comment : l’Air protège, la Terre entrave, l’Eau place, le Feu encaisse.',
            'Côté spécialisation, Artiste ou Courtisan·e vont bien à celui qui joue déjà un rôle.',
        ],
    ],
    [
        'name' => 'Steamer',
        'passive' => 'Tourelle',
                'presentation' => [
            'Tu es Steamer. Scaphandre et tourelles. Tes machines tirent pendant que tu calibres.',
            'En combat, tu poses une tourelle, puis tu la fais évoluer. Ta voie dit comment : l’Air place, la Terre frappe, l’Eau renforce, le Feu soigne.',
            'Côté spécialisation, Artisan·e va bien à celui qui répare déjà ce qu’il pose.',
        ],
    ],
    [
        'name' => 'Eliotrope',
        'passive' => 'Faille',
                'presentation' => [
            'Tu es Eliotrope. Tu disparais d’une case et tu repars plus loin. Les portails plient le chemin.',
            'En combat, tu te téléportes, puis le coup part d’ailleurs. Ta voie dit comment : l’Air entrave, la Terre place, l’Eau frappe, le Feu soigne.',
            'Côté spécialisation, Explorateur·rice va bien à celui qui ne reste pas sur la même case.',
        ],
    ],
    [
        'name' => 'Huppermage',
        'passive' => 'Contraste',
                'presentation' => [
            'Tu es Huppermage. Tu changes de couleur en plein coup. Les runes restent au sol.',
            'En combat, changer d’élément renforce le coup d’après. Ta voie dit comment : l’Air frappe, la Terre entrave, l’Eau place, le Feu renforce.',
            'Côté spécialisation, Érudit va bien à celui qui lit les éléments. Artisan·e va bien si les runes sont déjà un outil.',
        ],
    ],
    [
        'name' => 'Ouginak',
        'passive' => 'Proie',
                'presentation' => [
            'Tu es Ouginak. Truffe au vent, poil brillant, tu colles une proie. La première blessure la désigne.',
            'En combat, tu marques, puis tu ne lâches plus. Ta voie dit comment : l’Air frappe, la Terre encaisse, l’Eau renforce, le Feu entrave.',
            'Côté spécialisation, Voleur·euse ou Explorateur·rice prolongent la piste.',
        ],
    ],
    [
        'name' => 'Forgelance',
        'passive' => 'Estoc',
                'presentation' => [
            'Tu es Forgelance. Lance en avant, tu charges. Après le bond, le coup porte plus juste.',
            'En combat, tu te téléportes avec la lance, puis tu frappes. Ta voie dit comment : l’Air entrave, la Terre place, l’Eau protège, le Feu frappe.',
            'Côté spécialisation, Artisan·e ou Milicien·ne vont bien à la lance et à la ligne.',
        ],
    ],
];
