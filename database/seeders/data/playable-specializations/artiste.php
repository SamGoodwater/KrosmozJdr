<?php

declare(strict_types=1);

/**
 * Fiche jouable modèle (archétypes) : l’Artiste — gabarit 2.4.2.6 + 2.4.2.7.
 *
 * Ambiance (identity, flavor, blurbs) ≠ règles (effects, masteries, signature).
 * Ton : docs/IA/TON.md.
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
    'name' => 'Artiste',
    'importPageSlug' => 'import-specialization-artiste',
    'importPageTitle' => 'Spécialisation Artiste',
    'sectionSlugPrefix' => 'import-artiste',
    'shortDescription' => 'Tu joues, tu chantes, tu jongles ou tu tires les cartes. Quatre façons de le faire : Musicien, Saltimbanque, Troubadour, Voyant.',
    'description' => 'Tu gagnes ta croûte sur une scène : une voix, un geste, une toile ou un jeu de cartes. Taverne d’Astrub, place de Bonta, rue de Brâkmar… on te regarde, et parfois on t’écoute vraiment.',
    'identity' => 'Tu es Artiste. Tu sais tenir l’attention d’une salle, d’une garde ou d’un allié. Tu encourages, tu détournes le regard, tu fais passer une émotion. Au niveau 1, tu choisis comment tu te produis : Musicien, Saltimbanque, Troubadour ou Voyant.',
    'thread' => 'Tu captives les gens autour de toi. Tes capacités aident surtout hors combat, et donnent un coup de pouce en combat sans remplacer tes sorts de classe.',
    'focus' => 'Spectacle, encouragement (Applaudissement), création, tavernes et rues',
    'characteristics' => 'Chance en premier. La seconde dépend de ton choix : Sagesse, Agilité ou Intelligence.',
    'idealFor' => 'Ecaflip, Eniripsa, Pandawa, Roublard, Zobal — ou n’importe qui qui veut un vrai métier de scène.',
    'difference' => 'Tu n’es pas là pour crocheter comme un Voleur, signer comme un Négociant, tenir une cour comme un Courtisan ou soigner une table comme un Eniripsa. Tes effets restent modestes : du spectacle utile, pas un raid à toi tout seul.',
    'synergies' => [
        'Ecaflip' => 'Les cartes et le hasard collent bien au Voyant.',
        'Eniripsa' => 'La voix qui soigne et celle qui fait rire : Musicien ou Troubadour.',
        'Pandawa' => 'Une taverne, c’est déjà une bonne scène.',
        'Roublard' => 'Détour et entrée en scène : plutôt Saltimbanque.',
        'Zobal' => 'Masque et rôle, ça aide pour se déguiser.',
    ],
    'signature' => [
        'name' => 'Applaudissement',
        'summary' => 'Coût : 1 PA depuis la réserve de Wakfu. Un allié qui t’entend ou te voit (6 cases) gagne un dé sur son prochain jet d’attaque, de sauvegarde ou de compétence, jusqu’à la fin de son prochain tour. 1×/tour. Pas de cumul. Dé : 1d4 (1d6 au niveau 9, 1d8 au niveau 15). Ton choix Musicien / Saltimbanque / Troubadour / Voyant change un peu l’usage (voir plus bas).',
    ],
    'archetypes' => [
        'musicien' => [
            'name' => 'Musicien',
            'blurb' => 'Tu joues d’un instrument ou tu chantes. Le rythme porte la salle et calme un peu les peurs.',
            'thread' => 'Le refrain qui revient et qui aide.',
            'applaudissement' => 'L’allié peut aussi ajouter le dé à un jet de dégâts (à la place d’un jet d’attaque, de sauvegarde ou de compétence). Une seule fois par Applaudissement.',
            'masteries' => [
                'Outils' => 'Un instrument (luth, flûte, tambour…) — ou ta voix seule.',
                'Jets de sauvegarde' => 'Chance, Sagesse.',
                'Compétences' => 'Choisis-en trois parmi : Représentation, Persuasion, Perspicacité, Histoire, Religion.',
            ],
        ],
        'saltimbanque' => [
            'name' => 'Saltimbanque',
            'blurb' => 'Tu jongles, tu tombes exprès, tu te faufiles. Le corps fait le spectacle.',
            'thread' => 'Le numéro physique qui détourne le regard.',
            'applaudissement' => 'Tu peux te cibler pour un jet d’Acrobaties, d’Escamotage ou de Discrétion.',
            'masteries' => [
                'Outils' => 'Accessoires de cirque (balles, masque, corde, maquillage).',
                'Jets de sauvegarde' => 'Chance, Agilité.',
                'Compétences' => 'Choisis-en trois parmi : Représentation, Acrobaties, Escamotage, Discrétion, Athlétisme.',
            ],
        ],
        'troubadour' => [
            'name' => 'Troubadour',
            'blurb' => 'Tu racontes, tu écris, tu trouves la phrase qui ouvre une porte ou qui ferme une bouche.',
            'thread' => 'Les mots au bon moment.',
            'applaudissement' => 'L’allié peut voir le résultat du jet avant de dépenser le dé (avant que le MJ dise si c’est réussi ou raté).',
            'masteries' => [
                'Outils' => 'Un carnet et une plume — ou une bonne voix de conteur.',
                'Jets de sauvegarde' => 'Chance, Intelligence.',
                'Compétences' => 'Choisis-en trois parmi : Représentation, Persuasion, Supercherie, Histoire, Perspicacité.',
            ],
        ],
        'voyant' => [
            'name' => 'Voyant',
            'blurb' => 'Tu tires les cartes ou les runes. Tu donnes des indices sur ce qui pourrait arriver — pas la vérité absolue.',
            'thread' => 'Un petit coup d’œil sur ce qui vient.',
            'applaudissement' => '1×/scène, en réaction, tu peux dépenser un Applaudissement pour soustraire le dé au jet d’attaque, de sauvegarde ou de compétence d’une créature qui t’entend (6 cases).',
            'masteries' => [
                'Outils' => 'Un jeu de cartes, des runes ou un autre accessoire de tirage.',
                'Jets de sauvegarde' => 'Chance, Sagesse.',
                'Compétences' => 'Choisis-en trois parmi : Représentation, Perspicacité, Supercherie, Arcanes, Religion.',
            ],
        ],
    ],
    'levels' => [
        1 => [
            'choice' => $choices[1],
            'flavor' => 'Tu poses ton instrument, ton masque, ton carnet ou tes cartes. La salle se tait une seconde.',
            'masteries' => [
                'Style' => 'Choisis Musicien, Saltimbanque, Troubadour ou Voyant. Ce choix est définitif. Il fixe tes outils, tes jets de sauvegarde et ta liste de compétences.',
                'Métiers' => 'Aucun métier offert. Tu peux en apprendre un comme n’importe qui (section 4.3).',
                'Langues' => 'Aucune langue bonus.',
            ],
            'capacities' => [
                [
                    'name' => 'Applaudissement',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'Coût : 1 PA depuis la réserve de Wakfu. Un allié qui t’entend ou te voit (6 cases) gagne 1d4 sur son prochain jet d’attaque, de sauvegarde ou de compétence, jusqu’à la fin de son prochain tour. 1×/tour. Pas de cumul. Le dé passe à 1d6 au niveau 9 et à 1d8 au niveau 15. Selon Musicien / Saltimbanque / Troubadour / Voyant, l’usage change un peu (voir présentation).',
                ],
                [
                    'name' => 'Première scène',
                    'type' => $guaranteed,
                    'effect' => 'Autre garantie possible : 10 minutes de représentation (chanson, conte, acrobatie, tirage…). Tu as l’avantage au prochain test de Chance pour obtenir un toit, un repas, un silence ou une info légère. 1×/scène.',
                ],
                [
                    'name' => 'Illusion de scène',
                    'type' => $freeSlot,
                    'pa' => '1',
                    'effect' => 'Tu crées un petit son ou une petite image (objet, voix, flamme) dans 6 cases, pendant une minute. Ça ne blesse pas. Un test de Perspicacité attentif la perce. En combat : 1 PA depuis la réserve de Wakfu.',
                ],
                [
                    'name' => 'Affiche magique',
                    'type' => $freeSlot,
                    'effect' => 'Tu écris une phrase sur une surface. Elle reste jusqu’à la fin de la scène. Tu choisis qui peut la lire (des gens nommés, ou tout le monde). Une page. Les codes restent illisibles.',
                ],
            ],
        ],
        3 => [
            'choice' => $choices[3],
            'flavor' => 'On commence à te reconnaître d’une ville à l’autre.',
            'capacities' => [
                [
                    'name' => 'Déguisement de scène',
                    'type' => $guaranteed,
                    'pa' => '1',
                    'effect' => 'En 10 minutes tu changes visage, voix et habits. Ça tient le temps d’une scène, ou 1 heure si tu parles peu. Si on te scrute : Supercherie contre Perspicacité. En combat pour le tenir : 1 PA depuis la réserve de Wakfu.',
                ],
                [
                    'name' => 'Fausse note utile',
                    'type' => $freeSlot,
                    'pa' => '1',
                    'effect' => 'Tu rates exprès. Un public, une garde ou un hôte détourne le regard. Un allié a l’avantage à son prochain test de Discrétion ou d’Escamotage. Coût : 1 PA depuis la réserve de Wakfu. 1×/scène.',
                ],
                [
                    'name' => 'Numéro de scène',
                    'type' => $freeSlot,
                    'archetype_variants' => [
                        'musicien' => [
                            'name' => 'Contre-refrain',
                            'pa' => '1',
                            'effect' => 'Réaction. Coût : 1 PA depuis la réserve de Wakfu. Toi et un allié dans 6 cases qui t’entendent : avantage aux sauvegardes contre la peur et le charme jusqu’à la fin de ton prochain tour. 1×/scène.',
                        ],
                        'saltimbanque' => [
                            'name' => 'Culbute',
                            'pa' => '1',
                            'effect' => 'Coût : 1 PA depuis la réserve de Wakfu. Tu te déplaces de 3 cases. Le prochain test pour te tacle ou te saisir a un désavantage. 1×/tour.',
                        ],
                        'troubadour' => [
                            'name' => 'Vers qui ouvre',
                            'effect' => 'Après une minute de récit ou de lecture, tu as l’avantage au prochain test d’Histoire, d’Investigation ou de Perspicacité lié à ce qui se passe. 1×/scène.',
                        ],
                        'voyant' => [
                            'name' => 'Petit tirage',
                            'pa' => '1',
                            'effect' => 'Coût : 1 PA depuis la réserve de Wakfu. Le MJ te donne un présage court (oui / non / vague) sur une action qui arrive bientôt. 1×/scène. Ce n’est pas une certitude.',
                        ],
                    ],
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Spectacle ambulant',
                    'type' => 'Contextuelle (ville, taverne)',
                    'effect' => 'En ville ou dans une auberge, tu trouves toujours une scène, un repas et un lit contre une représentation. Avantage aux tests de Chance pour qu’on t’invite. Ça ne garantit pas qu’on te paie bien.',
                ],
            ],
        ],
        6 => [
            'choice' => $choices[6],
            'flavor' => 'Tu n’as plus besoin de forcer. Les gens viennent d’eux-mêmes.',
            'capacities' => [
                [
                    'name' => 'Accroche',
                    'type' => $guaranteed,
                    'pa' => '2',
                    'effect' => 'Une créature qui t’entend et te comprend : tu lui souffles une action simple (approche, attends, regarde ailleurs). Elle résiste avec un jet de Sagesse. Coût : 2 PA depuis la réserve de Wakfu. 1×/scène. Pas de suicide, pas de trahison évidente.',
                ],
                [
                    'name' => 'Image de salle',
                    'type' => $freeSlot,
                    'pa' => '2',
                    'effect' => 'Tu crées une image ou un décor dans un cube de 4 cases, pour une scène. Ça ne blesse pas. Un examen attentif la perce. Coût : 2 PA depuis la réserve de Wakfu.',
                ],
                [
                    'name' => 'Rappel de refrain',
                    'type' => $freeSlot,
                    'effect' => 'Tu reprends une émotion déjà plantée. Relance d’un test de Représentation ou de Persuasion raté, sauf sur un 1 naturel. 1×/scène.',
                ],
            ],
        ],
        9 => [
            'choice' => $choices[9],
            'flavor' => 'Ton Applaudissement porte plus loin. Ton numéro à toi devient une habitude.',
            'capacities' => [
                [
                    'name' => 'Visage d’emprunt',
                    'type' => $guaranteed,
                    'pa' => '3',
                    'effect' => 'Tu prends l’apparence d’une personne que tu as vue. Durée : 1 heure, ou une scène en combat. Les proches ont l’avantage pour te démasquer. Coût : 3 PA depuis la réserve de Wakfu.',
                ],
                [
                    'name' => 'Costume qui tient',
                    'type' => $freeSlot,
                    'effect' => 'Un déguisement que tu poses tient 8 heures sans retouche, même sous la pluie. Un examen attentif peut encore te démasquer. 10 minutes pour l’habiller.',
                ],
                [
                    'name' => 'Tour de maître',
                    'type' => $freeSlot,
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
            ],
            'aptitudes' => [
                [
                    'name' => 'Oreille du public',
                    'type' => 'Passive',
                    'effect' => 'Ton dé d’Applaudissement passe à 1d6. Sur un test d’outil ou de compétence que tu ne maîtrises pas, tu n’as pas le désavantage du « jamais vu ».',
                ],
            ],
        ],
        12 => [
            'choice' => $choices[12],
            'flavor' => 'On raconte déjà des choses sur toi. À toi de choisir lesquelles restent.',
            'capacities' => [
                [
                    'name' => 'Légende',
                    'type' => $guaranteed,
                    'effect' => 'Tu nommes une personne, un lieu ou un objet connus. En 10 minutes, tu récupères ce que la rumeur dit — assez pour jouer avec, pas un dossier complet. 1×/scène.',
                ],
                [
                    'name' => 'Double de scène',
                    'type' => $freeSlot,
                    'pa' => '2',
                    'effect' => 'Tu laisses une image de toi sur place et tu te fais discret. Tu n’es pas invisible : on te rate si on ne te cherche pas. Durée : 1 minute. Coût : 2 PA depuis la réserve de Wakfu. 1×/scène.',
                ],
                [
                    'name' => 'Ombres de coulisse',
                    'type' => $freeSlot,
                    'pa' => '2',
                    'effect' => 'Tu disparais dans un décor, une foule ou un rideau. Jusqu’à ton prochain tour, on t’a perdu de vue. Coût : 2 PA depuis la réserve de Wakfu. 1×/scène. En terrain nu, ça ne marche pas.',
                ],
            ],
        ],
        15 => [
            'choice' => $choices[15],
            'flavor' => 'Un geste, et la salle se lève. Un autre, et elle s’assoit.',
            'capacities' => [
                [
                    'name' => 'Festin des héros',
                    'type' => $guaranteed,
                    'pa' => '4',
                    'effect' => 'Une heure de table et de spectacle : mets, boissons, chaleur. Jusqu’à 8 créatures. Chacune ignore un niveau de fatigue et gagne 1d4 PV temporaires jusqu’au prochain repos. Coût : 4 PA depuis la réserve de Wakfu. 1×/jour.',
                ],
                [
                    'name' => 'Public conquis',
                    'type' => $freeSlot,
                    'effect' => 'Après au moins 10 minutes de représentation, jusqu’à six spectateurs ordinaires te doivent une faveur mineure (ouvrir une porte, se taire, te raccompagner). Pas un noble, pas un contrat. 1×/jour.',
                ],
                [
                    'name' => 'Grande première',
                    'type' => $freeSlot,
                    'archetype_variants' => [
                        'musicien' => [
                            'name' => 'Salle entière en chœur',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Jusqu’à six créatures qui t’entendent : même effet qu’Accroche, un seul jet pour le groupe (on garde le plus haut). 1×/scène.',
                        ],
                        'saltimbanque' => [
                            'name' => 'Cascade finale',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Tu te déplaces jusqu’à ta vitesse. Les tests de tacle contre toi ont un désavantage. Un allié adjacent qui t’entend gagne un Applaudissement sans en payer le coût. 1×/scène.',
                        ],
                        'troubadour' => [
                            'name' => 'Mot d’ordre',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Jusqu’à six créatures qui te comprennent ont l’avantage à leur prochain test de Persuasion ou d’Intimidation lié à ta phrase. 1×/scène.',
                        ],
                        'voyant' => [
                            'name' => 'Fil du destin',
                            'pa' => '3',
                            'effect' => 'Coût : 3 PA depuis la réserve de Wakfu. Un allié dans 6 cases peut relancer un jet d’attaque, de sauvegarde ou de compétence qu’il vient de faire. Il garde le second résultat. 1×/scène.',
                        ],
                    ],
                ],
            ],
            'aptitudes' => [
                [
                    'name' => 'Manipulateur subtil',
                    'type' => 'Contextuelle (scène sociale)',
                    'effect' => 'Ton dé d’Applaudissement passe à 1d8. Devant une salle, une cour ou une table, tu as l’avantage aux tests de Chance (Persuasion, Supercherie) pour faire croire une émotion. Pas pour faire signer un contrat. On peut encore te démasquer.',
                ],
            ],
        ],
        20 => [
            'choice' => $choices[20],
            'flavor' => 'Deux gestes, et c’est réglé. Le reste, tu l’as déjà joué mille fois.',
            'capacities' => [
                [
                    'name' => 'Apothéose',
                    'type' => $guaranteed,
                    'effect' => '1×/jour. Pendant une minute, les allié·e·s qui te voient ou t’entendent ont l’avantage à tous leurs jets, et la première attaque contre toi rate. Ensuite tout le monde s’en souvient un peu trop bien.',
                ],
                [
                    'name' => 'Théâtre de poche',
                    'type' => $freeSlot,
                    'pa' => '6',
                    'effect' => 'Tu déplies une salle pour 8 heures : planches, lumières, silence dehors. Rien de créé ne reste après. Coût : 6 PA depuis la réserve de Wakfu. 1×/jour.',
                ],
                [
                    'name' => 'Loge de poche',
                    'type' => $freeSlot,
                    'pa' => '6',
                    'effect' => 'Tu déplies une loge pour 8 heures : miroir, costumes, silence. Rien de créé ne reste après. Coût : 6 PA depuis la réserve de Wakfu. 1×/jour.',
                ],
            ],
        ],
    ],
];
