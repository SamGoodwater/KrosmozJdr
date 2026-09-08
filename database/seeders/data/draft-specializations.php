<?php

declare(strict_types=1);

/**
 * Brouillons des spécialisations prévues par les règles mais absentes de la bibliothèque.
 *
 * Ce contenu est une proposition : chiffres, DD, coûts Wakfu/PA et liens vers les
 * fiches capacités restent à écrire. Ne pas passer en jouable sans relecture.
 *
 * @return list<array<string, mixed>>
 */
return [
    [
        'name' => 'Artisan·e',
        'importPageSlug' => 'import-specialization-artisan-e',
        'importPageTitle' => 'Brouillon — Spécialisation Artisan·e',
        'sectionSlugPrefix' => 'draft-artisan-e',
        'shortDescription' => 'Spécialisation tournée vers la fabrication, la réparation et les métiers.',
        'description' => 'BROUILLON — à retravailler. Tu vis du geste et de la matière : recettes, réparations, œil pour un bon minerai. Un métier, tout le monde peut en apprendre ; toi, tu en fais une identité.',
        'identity' => 'Tu es de celles et ceux qui transforment le monde plutôt que de seulement le traverser. Guildes, ateliers de Bonta, forges de Brâkmar, établis de village : tu as un lieu, des outils, et la réputation qui va avec.',
        'focus' => 'Création, fabrication, métiers',
        'characteristics' => 'Intelligence ou Force (au choix — à calibrer)',
        'idealFor' => 'Forgerons, tailleurs, bijoutiers, bricoleurs, Forgelance, personnages qui veulent vivre de leurs mains.',
        'difference' => 'Un personnage peut apprendre jusqu’à 6 métiers sans cette spécialisation. L’Artisan·e n’est pas « quelqu’un qui a un métier » : c’est quelqu’un dont le métier change la table (qualité, temps, recettes, réparations).',
        'synergies' => [
            'Forgelance' => 'Le forgeron de légende, lame et métier collés l’un à l’autre.',
            'Xélor / Bricoleur' => 'Mécanismes, horloges, pièges, objets bizarres.',
            'Enutrof' => 'La chaîne complète : minerai, lingot, vente.',
            'Iop' => 'Moins évident, très fun : le combattant qui entretient tout l’équipement du groupe.',
        ],
        'todo' => [
            'Créer les fiches capacités et les lier par palier.',
            'Calibrer DD, durées, réserve de Wakfu et usage éventuel en combat.',
            'Décider si le bonus de métier (qualité / temps) se cumule avec les outils et l’atelier.',
            'Vérifier l’équilibre avec la section 4.3 Métiers (6 métiers, 5 niveaux, rareté).',
        ],
        'levels' => [
            1 => [
                'flavor' => 'Tu poses tes outils et tu choisis ton premier métier sérieux. Ce n’est pas encore de la magie : c’est de l’habitude.',
                'masteries' => [
                    'Outils' => 'Outils d’un métier d’artisanat au choix (forge, bijouterie, couture, etc.).',
                    'Jets de sauvegarde' => 'Intelligence, Force (à confirmer).',
                    'Compétences' => 'Choisis-en deux parmi : Investigation, Perception, Arcanes (objets magiques), Athlétisme.',
                    'Métiers' => 'Un métier d’artisanat au niveau 1 (Commun). La forgemagie n’est pas offerte d’office — à discuter.',
                    'Langues' => 'Aucune langue bonus pour l’instant (à discuter : jargon des guildes ?).',
                ],
            ],
            3 => [
                'choice' => '1 aptitude',
                'aptitudes' => [
                    [
                        'name' => 'Œil du matériau',
                        'effect' => 'En 10 minutes, tu estimes rareté, défauts et usage d’une ressource ou d’un objet non magique. (DD et limites à écrire.)',
                    ],
                    [
                        'name' => 'Réparation de fortune',
                        'effect' => 'Avec tes outils, tu remets en état un objet cassé ou usé. Temps long (heures), hors combat. Une réparation de combat très limitée est une piste, pas une règle.',
                    ],
                    [
                        'name' => 'Esquisse de recette',
                        'effect' => 'Après avoir examiné un objet Commun ou Peu commun, tu peux tenter d’en tirer une recette imparfaite. Échec possible, ressources perdues. À cadrer avec 4.3.3.',
                    ],
                ],
            ],
            6 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Atelier portable',
                        'effect' => 'Tu improvises un mini-atelier (1 heure). Bonus d’outils réduit, mais tu n’es plus bloqué·e hors ville. (Bonus chiffré à calibrer.)',
                    ],
                    [
                        'name' => 'Main sûre',
                        'effect' => 'Une fois par jour, tu relances un test de fabrication ou de réparation raté (sauf 1 naturel ? à décider).',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Finition soignée',
                        'type' => 'Passive',
                        'effect' => 'Tes objets fabriqués ont une finition d’un cran supérieur sur la table de qualité (4.3.3.3), sauf échec. Trop fort ? À nerfer ou à limiter par rareté.',
                    ],
                    [
                        'name' => 'Réseau d’ateliers',
                        'type' => 'Contextuelle (ville)',
                        'effect' => 'En ville, tu trouves un établi et un contact de métier sans jet, sauf quartier vraiment hostile.',
                    ],
                ],
            ],
            9 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Signature d’artisan',
                        'effect' => 'Tu marques une pièce : on te reconnaît, bonus social auprès des guildes, malus si tu as vendu de la camelote. Purement narratif pour l’instant.',
                    ],
                    [
                        'name' => 'Amélioration mineure',
                        'effect' => 'Hors combat, tu ajoutes un petit bonus temporaire à un objet (un combat ? une scène ?). Ne doit pas remplacer la forgemagie.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Gestes économiques',
                        'type' => 'Passive',
                        'effect' => 'Réduction du temps de fabrication (piste : −25 %) ou des ressources (piste : une ressource commune en moins). Un seul des deux, pas les deux.',
                    ],
                ],
            ],
            12 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Prototype',
                        'effect' => 'Tu tentes un objet d’une rareté au-dessus de ton niveau de métier, avec un DD sévère et un risque d’échec critique. Exception, pas une routine.',
                    ],
                    [
                        'name' => 'Réparer l’irréparable',
                        'effect' => 'Artefact fêlé, rune qui saute, mécanisme xélor : tu as une chance, longue et coûteuse, de le sauver. MJ adjudicateur.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Maître d’un geste',
                        'type' => 'Passive',
                        'effect' => 'Un métier d’artisanat que tu possèdes est traité comme +1 niveau pour la rareté max, plafonné à 5. Très fort : à valider.',
                    ],
                ],
            ],
            15 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Pièce de maître',
                        'effect' => 'Une fois par palier / par mois (à choisir), tu vises une finition « pièce de maître » sans dépendre du 20 naturel. Coût et rareté à verrouiller.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Réputation de qualité',
                        'type' => 'Contextuelle (social / commerce)',
                        'effect' => 'Les marchands sérieux te paient mieux tes pièces, et certains PNJ artisans te parlent comme à un pair.',
                    ],
                ],
            ],
            18 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Œuvre unique',
                        'effect' => 'Piste de quête : tu peux concevoir un objet Unique avec le MJ (pas une recette catalogue). Long, cher, mémorable.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'L’établi dans la tête',
                        'type' => 'Passive',
                        'effect' => 'Sans atelier, tes malus de fabrication improvisée disparaissent presque. Toujours besoin d’outils.',
                    ],
                ],
            ],
            20 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Légende vivante de l’atelier',
                        'effect' => 'Capstone à écrire : un chef-d’œuvre, une technique enseignable, un bonus de groupe sur l’équipement que tu as touché. Doit rester plus faible qu’un sort de niveau 20.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Rien ne se perd',
                        'type' => 'Passive',
                        'effect' => 'Sur un échec de fabrication (sauf critique), tu récupères une partie des ressources. Chiffre à poser.',
                    ],
                ],
            ],
        ],
    ],
    [
        'name' => 'Négociant·e',
        'importPageSlug' => 'import-specialization-negociant-e',
        'importPageTitle' => 'Brouillon — Spécialisation Négociant·e',
        'sectionSlugPrefix' => 'draft-negociant-e',
        'shortDescription' => 'Spécialisation axée sur le commerce, la persuasion et les réseaux.',
        'description' => 'BROUILLON — à retravailler. Les kamas, c’est de l’information et des faveurs. Tu sais ce que vaut un objet, qui le veut, et ce que l’autre peut vraiment payer.',
        'identity' => 'Tu n’es pas forcément riche. Tu es quelqu’un qui transforme une conversation en accord, un accord en route, une route en opportunité. L’Artiste émeut, le Voleur prend, toi tu échanges — parfois proprement, parfois moins.',
        'focus' => 'Commerce, persuasion, affaires',
        'characteristics' => 'Chance (à calibrer ; Intelligence en second choix ?)',
        'idealFor' => 'Marchands, diplomates, Enutrof, personnages sociaux, courtiers d’information.',
        'difference' => 'Ce n’est pas la spécialisation « je suis riche ». Un Iop Négociant·e peut être fauché et redoutable en marchandage. L’Artiste joue la scène ; toi, tu joues le contrat.',
        'synergies' => [
            'Enutrof' => 'Le classique : chasse au trésor et rachat au meilleur prix.',
            'Roublard / Sram' => 'Marché gris, dettes, chantage léger — à ne pas fusionner avec Voleur·euse.',
            'Féca / Steamer' => 'Logistique, convois, protections de caravane.',
            'Cra' => 'Moins évident : messager·ère, contrat de chasse, prime.',
        ],
        'todo' => [
            'Chiffrer les réductions de prix (un % plat casse l’économie de table).',
            'Définir ce qu’est un « contact » (PNJ nommé, table aléatoire, ou narratif).',
            'Séparer clairement Négociant·e et Voleur·euse (escroquerie vs vol).',
            'Langues commerciales : voir 4.1.4.',
        ],
        'levels' => [
            1 => [
                'flavor' => 'Tu as déjà un carnet, une réputation minuscule, et l’habitude de demander « et le prix ami·e ? ».',
                'masteries' => [
                    'Outils' => 'Matériel de marchand (balance, carnet, sceau). Piste : outils de jeu.',
                    'Jets de sauvegarde' => 'Chance, Intelligence (à confirmer).',
                    'Compétences' => 'Choisis-en deux parmi : Persuasion, Supercherie, Perspicacité, Investigation.',
                    'Métiers' => 'Aucun métier d’artisanat offert. Piste : un métier de récolte « pour avoir de quoi vendre ».',
                    'Langues' => 'Une langue régionale ou commerciale au choix (à aligner sur 4.1.4).',
                ],
            ],
            3 => [
                'choice' => '1 aptitude',
                'aptitudes' => [
                    [
                        'name' => 'Cote du jour',
                        'effect' => 'Dans un marché, tu évalues le prix « juste » d’un objet courant sans jet, ou avec avantage sur les pièces rares. (Seuils à écrire.)',
                    ],
                    [
                        'name' => 'Le bon mot',
                        'effect' => 'Une fois par scène sociale, tu relances un test de Persuasion ou tu transformes un échec en « pas encore, reviens demain » plutôt qu’en porte qui claque.',
                    ],
                    [
                        'name' => 'Passe-droit mineur',
                        'effect' => 'Tu obtiens un rendez-vous, une autorisation de stand, un laissez-passer de quartier — pas un accès de donjon. Narratif, 10 minutes de palabre.',
                    ],
                ],
            ],
            6 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Réseau de la ville',
                        'effect' => 'En arrivant dans une ville connue du Monde des Douze, tu nommes 1 contact (marchand, garde achetable, scribe). Le MJ peut le refuser s’il n’a aucun sens.',
                    ],
                    [
                        'name' => 'Contre-offre',
                        'effect' => 'Quand on te propose un prix ou un marché, tu forces un second round de négoce même après un refus. Une fois par interlocuteur et par jour ?',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Remise de comptoir',
                        'type' => 'Contextuelle (achat en ville)',
                        'effect' => 'Piste : −10 % sur le matériel courant, pas sur le légendaire, pas sur les PNJ unique. À plafonner.',
                    ],
                    [
                        'name' => 'Visage connu',
                        'type' => 'Contextuelle (villes visitées)',
                        'effect' => 'Dans une ville où tu as déjà négocié, les gardes et boutiquiers te reconnaissent. Bonus aux tests sociaux « officiels », pas aux infiltrations.',
                    ],
                ],
            ],
            9 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Contrat clair',
                        'effect' => 'Tu rédiges un accord que les deux parties comprennent. Tricher ensuite laisse des traces narratives (réputation, guildes). Utile pour le MJ.',
                    ],
                    [
                        'name' => 'Lire la salle',
                        'effect' => 'En 1 minute d’observation, tu cibles qui a de l’argent, qui ment sur le prix, qui n’est pas le vrai décideur. Jet de Perspicacité, DD à poser.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Crédit rotatif',
                        'type' => 'Passive',
                        'effect' => 'Tu peux « devoir » une petite somme à un marchand plutôt que payer cash, une fois par ville. Abuse, et le réseau se ferme.',
                    ],
                ],
            ],
            12 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Caravane',
                        'effect' => 'Tu organises un transport (biens, personnes, message) entre deux villes. Coût, délais, risques de route : table MJ à écrire.',
                    ],
                    [
                        'name' => 'Offre qu’on n’ose pas refuser',
                        'effect' => 'Social tendu : tu transformes une négociation en test d’Intimidation ou l’inverse, selon le style. Pas un sort de charme.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Oreilles partout',
                        'type' => 'Passive',
                        'effect' => 'Une rumeur de marché (prix, pénurie, prime) te tombe dessus quand le groupe arrive en ville. Le MJ choisit laquelle.',
                    ],
                ],
            ],
            15 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Courtier d’impossible',
                        'effect' => 'Tu trouves acheteur ou vendeur pour un objet très rare, en plusieurs jours, avec une commission. Pas de garantie sur l’Unique.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Réputation de place',
                        'type' => 'Contextuelle (grande ville)',
                        'effect' => 'À Bonta, Brâkmar, Sufokia, Astrub : on te reçoit. Bonus social fort, obligations en face (faveurs à rendre).',
                    ],
                ],
            ],
            18 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Conseil des kamas',
                        'effect' => 'Tu peux peser sur une décision de guilde, de milice marchande ou de noble fauché. Une fois par arc, avec le MJ.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Jamais fauché longtemps',
                        'type' => 'Passive',
                        'effect' => 'Après un repos long en ville, tu dégages de quoi vivre (pas de quoi s’acheter un Dofus). Montant à plafonner pour ne pas casser l’aventure.',
                    ],
                ],
            ],
            20 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'La place te doit un service',
                        'effect' => 'Capstone : un marché, une ville ou une compagnie te doit une faveur majeure (sauf suicide politique). À écrire avec le MJ, pas en bonus chiffré.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Prix du sang et de l’or',
                        'type' => 'Réactive',
                        'effect' => 'Piste : quand un marché tourne mal (embuscade, arnaque), tu as déjà un plan B (sortie, otage économique, rançon inverse). Très narratif.',
                    ],
                ],
            ],
        ],
    ],
    [
        'name' => 'Sylvain·e',
        'importPageSlug' => 'import-specialization-sylvain-e',
        'importPageTitle' => 'Brouillon — Spécialisation Sylvain·e',
        'sectionSlugPrefix' => 'draft-sylvain-e',
        'shortDescription' => 'Spécialisation liée à la nature, aux animaux et aux milieux sauvages habités.',
        'description' => 'BROUILLON — à retravailler. L’Explorateur·rice traverse la forêt ; toi, tu y vis. Les bêtes et les plantes sont un voisinage, pas du décor.',
        'identity' => 'Tu n’es pas un ranger de fantasy générique recollé sur Dofus. Tu es du côté des saisons, des meutes, des racines. La ville te fatigue ; la canopée te range les idées.',
        'focus' => 'Nature, animaux, environnement',
        'characteristics' => 'Sagesse (à calibrer)',
        'idealFor' => 'Osamodas, Sadida, rangers, ermites, personnages qui veulent parler aux bêtes plus qu’aux milices.',
        'difference' => 'L’Explorateur·rice sait ne pas se perdre et désamorcer un piège. Le Sylvain·e sait qui habite l’endroit, quoi ne pas cueillir, et comment demander la permission à un sanglier. Chevauchement volontaire : à trancher sur Orientation / Survie.',
        'synergies' => [
            'Osamodas' => 'Invocations et langage animal dans le même sac.',
            'Sadida' => 'Plantes, poison, territoire vivant.',
            'Cra' => 'Chasse, piste, arc — attention à ne pas recoller Explorateur.',
            'Eniripsa' => 'Soins par les plantes plutôt que par les larmes.',
        ],
        'todo' => [
            'Trancher le recouvrement Explorateur·rice (Survie, Perception, pièges).',
            'Décider si « parler aux animaux » est une aptitude magique (Wakfu) ou un talent.',
            'Aligner avec Alchimiste / Chasseur / Pêcheur (métiers de récolte).',
            'Limiter l’appel de créatures pour ne pas voler l’Osamodas.',
        ],
        'levels' => [
            1 => [
                'flavor' => 'Tu reconnais une piste, une baie comestible, un silence de trop. La nature n’est pas gentille. Elle est lisible.',
                'masteries' => [
                    'Outils' => 'Kit de herboriste ou de pisteur (au choix).',
                    'Jets de sauvegarde' => 'Sagesse, Constitution si elle existe en jeu — sinon Force (à confirmer).',
                    'Compétences' => 'Choisis-en deux parmi : Nature, Dressage, Survie, Médecine, Perception.',
                    'Métiers' => 'Piste : un métier de récolte au niveau 1 (Alchimiste, Chasseur, Pêcheur, Bûcheron… au choix).',
                    'Langues' => 'Piste : un dialecte animal / sylvestre narratif, pas une langue de ville.',
                ],
            ],
            3 => [
                'choice' => '1 aptitude',
                'aptitudes' => [
                    [
                        'name' => 'Langage des bêtes simples',
                        'effect' => 'Tu communiques des intentions simples (danger, nourriture, « pars ») avec un animal non magique. Pas d’interrogatoire philosophique. Durée et Wakfu à poser.',
                    ],
                    [
                        'name' => 'Cueillette sûre',
                        'effect' => 'Tu distingues plante utile, toxique, sacrée. Avantage aux tests d’Alchimiste / Nature pour identifier une ressource végétale.',
                    ],
                    [
                        'name' => 'Pas dans la mousse',
                        'effect' => 'En milieu naturel, tu te déplaces sans laisser de traces évidentes. Bonus à la Discrétion seulement en nature, pas en donjon de pierre.',
                    ],
                ],
            ],
            6 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Soins de sous-bois',
                        'effect' => 'Hors combat, avec des plantes, tu soignes des dégâts légers ou un état naturel (poison de bête, fièvre). Moins fort qu’un Eniripsa. Quantité à calibrer.',
                    ],
                    [
                        'name' => 'Territoire lu',
                        'effect' => 'Après 10 minutes, tu sais si la zone est chassée, sacrée, malade, ou occupée par quelque chose de trop grand. Pas une carte complète du donjon.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Les bêtes te jaugent',
                        'type' => 'Passive',
                        'effect' => 'Les animaux non hostiles de base ne te fuient pas d’office. Les prédateurs te testent avant de charger. Les invocations ennemies : hors sujet.',
                    ],
                    [
                        'name' => 'Peau des saisons',
                        'type' => 'Contextuelle (nature)',
                        'effect' => 'Résistance aux intempéries, au froid mou, à la fatigue de marche en terrain naturel. Pas une résistance aux sorts de glace.',
                    ],
                ],
            ],
            9 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Appel discret',
                        'effect' => 'Tu attires un animal local (messager, sentinelle, diversion). Pas un tank de combat. L’Osamodas reste le roi des invocations.',
                    ],
                    [
                        'name' => 'Venin et antidote',
                        'effect' => 'Tu prépares une dose (poison ou antidote naturel) pendant un repos. Rareté et DD alignés sur l’Alchimiste, version terrain.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Connaissance des racines',
                        'type' => 'Passive',
                        'effect' => 'Bonus fixe aux tests de Nature et Dressage (chiffre à poser). Trop proche d’une expertise : choisir l’un ou l’autre.',
                    ],
                ],
            ],
            12 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Le bois répond',
                        'effect' => 'En forêt / jungle / marais, tu crées un obstacle naturel (racines, brouillard de pollen, essaim gênant) le temps d’une fuite ou d’une embuscade. Usage combat optionnel, faible.',
                    ],
                    [
                        'name' => 'Piste d’âme',
                        'effect' => 'Tu suis une créature à travers la nature même après la pluie, tant qu’elle n’a pas pris un zaap. Limites anti-quête à poser avec le MJ.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Estomac des bois',
                        'type' => 'Passive',
                        'effect' => 'Tu te nourris et tu t’abreuves en nature sans jet, et tu peux nourrir le groupe au ralenti. En donjon stérile : ça ne marche plus.',
                    ],
                ],
            ],
            15 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Cercle de saison',
                        'effect' => 'Rituel (1 heure) : un camp en nature devient plus sûr (veille, météo, bêtes curieuses). Pas un mur de Féca.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Sang contre poison',
                        'type' => 'Passive',
                        'effect' => 'Résistance (avantage ? réduction ?) aux poisons et maladies naturelles. Les poisons magiques / alchimiques raffinés : à décider.',
                    ],
                ],
            ],
            18 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Seigneur·e d’un lieu',
                        'effect' => 'Tu te lies à un territoire (forêt, île, marais). Dedans, tu as des avantages forts. Dehors, plus rien. Idéal campagne sédentaire, à refuser en one-shot.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Silence vert',
                        'type' => 'Contextuelle (canopée)',
                        'effect' => 'En milieu végétal dense, tu es très difficile à pister. Les villes et les mines t’annulent ce don.',
                    ],
                ],
            ],
            20 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Le Monde des Douze te reconnaît',
                        'effect' => 'Capstone narratif : un esprit de nature, un vieux Bouftou mythique, une Sadida ancestrale te doit une audience. Pas un sort de contrôle de masse.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Jamais vraiment perdu',
                        'type' => 'Passive',
                        'effect' => 'En nature, tu retrouves toujours une issue (pas forcément la bonne). Recouvrement avec Explorateur·rice : n’en garder qu’un des deux au capstone.',
                    ],
                ],
            ],
        ],
    ],
    [
        'name' => 'Marin·e',
        'importPageSlug' => 'import-specialization-marin-e',
        'importPageTitle' => 'Brouillon — Spécialisation Marin·e',
        'sectionSlugPrefix' => 'draft-marin-e',
        'shortDescription' => 'Spécialisation tournée vers la mer, les navires, l’équipage et Sufokia.',
        'description' => 'BROUILLON — à retravailler. L’Explorateur·rice lit la carte ; toi, tu lis la houle. Sufokia, l’île de Moon, un pont qui penche : tu vis du large, pas du sentier.',
        'identity' => 'Tu as le sel dans les coutures et un nœud de marin plus fiable qu’un serment de Brâkmar. Pirate, pêcheur·euse, corsaire de Bonta ou simple matelot d’Astrub : tu sais ce qu’un bateau peut encaisser, et ce qu’un équipage ne pardonnera pas.',
        'focus' => 'Mer, navires, équipage, météo',
        'characteristics' => 'Agilité ou Chance (au choix — à calibrer)',
        'idealFor' => 'Pandawa, Roublard, Enutrof chasseurs de cartes, personnages de Sufokia, pirates, escortes de convois maritimes.',
        'difference' => 'L’Explorateur·rice oriente, piste, désamorce. Le Marin·e manœuvre un navire, lit le ciel, tient un équipage et nage quand ça coule. La « Navigation » de l’Explorateur·rice reste surtout terrestre / donjon ; ici, c’est le pont et la crique.',
        'synergies' => [
            'Pandawa' => 'Rhum, mer, équilibre douteux sur un pont mouillé.',
            'Roublard / Sram' => 'Abordage, contrebande, criques sans douane.',
            'Enutrof' => 'Chasse au trésor, cartes mouillées, épaves.',
            'Steamer / Forgelance' => 'Navire comme machine, abordage comme formation.',
        ],
        'todo' => [
            'Tranchir le recouvrement Explorateur·rice (Navigation, Survie, Perception).',
            'Décider si un navire est un lieu de campagne (règles de manœuvre) ou du pur narratif.',
            'Aligner Pêcheur / Poissonnier (métiers) sans obliger le craft alimentaire.',
            'Calibrer nage, tempête et naufrage : ne pas voler le combat de classe.',
        ],
        'levels' => [
            1 => [
                'flavor' => 'Tu reconnais un gréement pourri, un nœud qui va lâcher, une odeur de tempête. La mer n’est pas un décor : c’est un patron.',
                'masteries' => [
                    'Outils' => 'Matériel de navigation (sextant / boussole, cartes marines, cordages).',
                    'Jets de sauvegarde' => 'Agilité, Chance (à confirmer).',
                    'Compétences' => 'Choisis-en deux parmi : Athlétisme (nage), Acrobaties, Perception, Survie.',
                    'Métiers' => 'Piste : Pêcheur ou Poissonnier au niveau 1. Pas d’artisanat d’armes offert.',
                    'Langues' => 'Piste : jargon des ports (Sufokia, Moon) — narratif, à aligner sur 4.1.4.',
                ],
            ],
            3 => [
                'choice' => '1 aptitude',
                'aptitudes' => [
                    [
                        'name' => 'Pied marin',
                        'effect' => 'Sur un pont, une jetée, un sol qui bouge, tu ignores le malus d’équilibre du terrain agité. (Chiffre à poser ; pas un bonus en donjon stable.)',
                    ],
                    [
                        'name' => 'Lire la houle',
                        'effect' => '10 minutes d’observation : tu prévois météo côtière, courant, ou si un navire à l’horizon file ou chasse. Pas une divination magique.',
                    ],
                    [
                        'name' => 'Nœud qui tient',
                        'effect' => 'Tu attaches, hisses, répares un gréement simple en quelques minutes. Utile pour ponts de corde, filets, sangles d’équipement. DD à écrire.',
                    ],
                ],
            ],
            6 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'À l’eau',
                        'effect' => 'Tu nages longtemps, tu aides un·e allié·e à ne pas couler, tu récupères un objet à faible profondeur. Hors combat d’abord ; version combat très limitée (PM / Athlétisme) à discuter.',
                    ],
                    [
                        'name' => 'Cri de pont',
                        'effect' => 'Une fois par scène, tu donnes un ordre clair à un petit groupe (hisser, abattre, larguer). Hors navire : ça marche pour une manœuvre de groupe simple (reculer un chariot, tenir une porte).',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Estomac de mer',
                        'type' => 'Passive',
                        'effect' => 'Tu ignores mal de mer, roulis, et une partie de la fatigue de veilles en mer. En carrosse : optionnel. En donjon : rien.',
                    ],
                    [
                        'name' => 'Port d’attache',
                        'type' => 'Contextuelle (villes portuaires)',
                        'effect' => 'À Sufokia, Moon, un quai d’Astrub : tu trouves un bateau, un passage, ou un marin qui te doit une bière. Le MJ peut refuser un vaisseau de guerre.',
                    ],
                ],
            ],
            9 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Abordage calculé',
                        'effect' => 'Tu prépares une approche (angles morts, grappins, qui tient la barre). Avantage au premier round d’un abordage / d’une intrusion par les toits ? Trop combat : à nerfer ou à garder narratif.',
                    ],
                    [
                        'name' => 'Sondeur d’épave',
                        'effect' => 'Tu estimes si une épave, une cale, une crique cache de l’air, du Wakfu pourri, ou un trou qui aspire. Pas un sort de détection magique complet (Érudit).',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Yeux de vigie',
                        'type' => 'Contextuelle (horizon / mer)',
                        'effect' => 'Bonus à la Perception pour voiles, récifs, silhouettes sur l’eau. En forêt dense : annulé.',
                    ],
                ],
            ],
            12 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Tenir la barre',
                        'effect' => 'Pendant une tempête ou une poursuite navale, tu peux transformer un échec collectif en « on encaisse, on ne chavire pas ». Une fois par traversée ? Table MJ à écrire.',
                    ],
                    [
                        'name' => 'Cale sèche improvisée',
                        'effect' => 'Tu répares un navire ou un gros engin (roue, gouvernail, pompe) avec du temps et des ressources. Recouvrement Artisan·e : ici, c’est le bateau, pas l’épée.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Langue des quais',
                        'type' => 'Contextuelle (ports)',
                        'effect' => 'Marins, contrebandiers, pêcheurs te parlent comme à un pair. Bonus social dans les ports, pas à la cour de Bonta (voir Courtisan·e).',
                    ],
                ],
            ],
            15 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Route secrète',
                        'effect' => 'Tu connais une passe, un brouillard, un horaire de douane qui raccourcit ou cache une traversée. Le MJ pose le prix (temps, bakchich, risque).',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Jamais noyé longtemps',
                        'type' => 'Réactive',
                        'effect' => 'Quand tu tombes à l’eau ou qu’un navire coule, tu as déjà un réflexe (bouée, cordage, crique). Pas une immunité aux dégâts de chute dans un puits.',
                    ],
                ],
            ],
            18 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Capitaine d’occasion',
                        'effect' => 'Tu prends le commandement d’un équipage PNJ le temps d’une traversée. Moral, mutinerie, manœuvre : le MJ joue l’équipage, toi tu as les leviers.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'La mer te connaît',
                        'type' => 'Passive',
                        'effect' => 'En mer, tu n’es jamais « perdu·e » au sens carte : tu retrouves une côte. Pas la bonne crique, pas un zaap. Recouvrement Explorateur·rice à surveiller.',
                    ],
                ],
            ],
            20 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Légende des quais',
                        'effect' => 'Capstone narratif : un port, une flotte ou un vieux capitaine te doit une traversée impossible (blocus, tempête, île taboue). Pas un sort de contrôle du climat (Érudit haut niveau).',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Équipage dans le sang',
                        'type' => 'Passive',
                        'effect' => 'Les allié·e·s qui ont voyagé avec toi en mer gagnent un petit bonus de manœuvre / nage tant que tu es là. Chiffre bas, ou purement narratif.',
                    ],
                ],
            ],
        ],
    ],
    [
        'name' => 'Courtisan·e',
        'importPageSlug' => 'import-specialization-courtisan-e',
        'importPageTitle' => 'Brouillon — Spécialisation Courtisan·e',
        'sectionSlugPrefix' => 'draft-courtisan-e',
        'shortDescription' => 'Spécialisation tournée vers les cours, l’étiquette et l’intrigue politique.',
        'description' => 'BROUILLON — à retravailler. Le Négociant·e tient le comptoir ; toi, le salon. Bonta, Brâkmar, un couloir trop silencieux : tu vis de l’étiquette et des non-dits.',
        'identity' => 'Tu sais qui saluer en premier, qui ne jamais contredire en public, et quelle rumeur faire circuler pour qu’elle arrive « toute seule ». Noble fauché·e, secrétaire de milice, espion·ne en habit clair : le palais est ton donjon.',
        'focus' => 'Cours, étiquette, intrigue, réputation',
        'characteristics' => 'Chance ou Intelligence (au choix — à calibrer)',
        'idealFor' => 'Eniripsa de cour, Sram en habit, Féca diplomatiques, personnages alignés Bonta/Brâkmar, espions mondains.',
        'difference' => 'Le Négociant·e vend et achète. L’Artiste monte sur scène. Le Voleur·euse s’infiltre par les toits. Le Courtisan·e s’infiltre par l’invitation, tue par le protocole, et perd tout si on le·la démasque en public.',
        'synergies' => [
            'Sram / Roublard' => 'Le poignard sous la cape, mais la cape est brodée.',
            'Eniripsa / Eliotrope' => 'Soin, image, parole qui porte à la cour.',
            'Féca / Steamer' => 'Garde du corps officiel, bouclier diplomatique.',
            'Ecaflip' => 'Paris sociaux, réputation qui bascule en une phrase.',
        ],
        'todo' => [
            'Tranchir le recouvrement Négociant·e (Persuasion, Supercherie, Perspicacité) et Artiste (Représentation).',
            'Ne pas en faire un « charme de sort » : l’étiquette n’est pas un Eniripsa.',
            'Aligner avec les langues de cour (4.1.4) et les alignements Bonta / Brâkmar.',
            'Définir ce qu’est une « faveur de cour » (une fois par ville ? par arc ?).',
        ],
        'levels' => [
            1 => [
                'flavor' => 'Tu as appris à sourire sans montrer les dents. Un salon, c’est un champ de bataille où l’on ne sort pas l’épée — pas tout de suite.',
                'masteries' => [
                    'Outils' => 'Trousse de toilette / sceaux / éventail ou carnet de protocoles. Piste : un jeu de société de cour.',
                    'Jets de sauvegarde' => 'Chance, Intelligence (à confirmer).',
                    'Compétences' => 'Choisis-en deux parmi : Persuasion, Perspicacité, Supercherie, Histoire, Représentation.',
                    'Métiers' => 'Aucun métier d’artisanat offert. Piste : rien — la cour n’est pas un établi.',
                    'Langues' => 'Une langue de cour ou de capitale au choix (à aligner sur 4.1.4).',
                ],
            ],
            3 => [
                'choice' => '1 aptitude',
                'aptitudes' => [
                    [
                        'name' => 'Le bon titre',
                        'effect' => 'Tu formules une adresse, un salut, une excuse protocolaire. Avantage (ou succès auto sur les cas simples) pour entrer dans un lieu « sur invitation ». Pas un laissez-passer de donjon militaire.',
                    ],
                    [
                        'name' => 'Lire le salon',
                        'effect' => '1 minute : tu repères qui s’ennuie, qui ment par politesse, qui a le vrai pouvoir dans la pièce. Jet de Perspicacité, DD à poser.',
                    ],
                    [
                        'name' => 'Rumeur légère',
                        'effect' => 'Tu plantes une information anodine qui circule d’ici le soir. Pas une calomnie de fin de campagne. Le MJ décide qui l’entend.',
                    ],
                ],
            ],
            6 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Invitation de rechange',
                        'effect' => 'Tu improvises une raison d’être là (cousin, secrétaire, artiste invitée). Une fois par lieu et par jour. Échec = humiliation narrative, pas des dégâts.',
                    ],
                    [
                        'name' => 'Duel de mots',
                        'effect' => 'Tu forces un échange social en public : l’autre doit répondre ou perdre la face. Remplace un jet d’Intimidation maladroit. Pas un sort de charme.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Tenue correcte exigée',
                        'type' => 'Contextuelle (cours, salons, temples riches)',
                        'effect' => 'Tant que tu es « présentable », bonus aux tests sociaux officiels. En haillons, en donjon, en forêt : annulé.',
                    ],
                    [
                        'name' => 'On ne touche pas à ça',
                        'type' => 'Réactive',
                        'effect' => 'Une fois par scène, tu désamorces une insulte ou un geste déplacé par le protocole (« pas devant les témoins »). N’arrête pas une lame déjà sortie.',
                    ],
                ],
            ],
            9 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Faveur mineure',
                        'effect' => 'Tu obtiens un rendez-vous, un sauf-conduit mondain, une place à une table. Pas les clés de la milice. Une fois par ville et par séjour ?',
                    ],
                    [
                        'name' => 'Masque de cour',
                        'effect' => 'Pendant une scène, tu joues un rôle social (allié·e d’une maison, neutre, envoyé·e). Les PNJ de rang croient le masque tant que personne ne te démasque avec des faits.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Mémoire des blasons',
                        'type' => 'Passive',
                        'effect' => 'Tu retenez maisons, dettes d’honneur, alliances récentes. Bonus à Histoire / Perspicacité dès qu’il s’agit de « qui doit quoi à qui ».',
                    ],
                ],
            ],
            12 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Scandale dirigé',
                        'effect' => 'Tu fais porter le blâme sur quelqu’un d’autre — ou tu l’évites de justesse. Arc social, pas un combat. Abuse, et la cour se ferme.',
                    ],
                    [
                        'name' => 'Oreille derrière le rideau',
                        'effect' => 'Tu places ou tu es un·e écouteur·euse (serviteur, musicien, garde). Tu apprends un secret de salon. Recouvrement Voleur·euse : ici, pas de crochetage, que de la présence.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Nom qui ouvre',
                        'type' => 'Contextuelle (Bonta, Brâkmar, capitales)',
                        'effect' => 'On te reçoit. Obligations en face : tu dois des politesses, des cadeaux, des silences. Pas un bonus au marché aux poissons (Négociant·e).',
                    ],
                ],
            ],
            15 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Conseil du prince',
                        'effect' => 'Tu pèses sur une décision d’un PNJ de rang (milice, temple, maison). Une fois par arc, avec le MJ. Échec possible, et ça se sait.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Réputation blindée',
                        'type' => 'Passive',
                        'effect' => 'Une rumeur contre toi a besoin de preuves, ou elle glisse. Trop fort contre un Sram enquêteur : limiter aux rumeurs mondaines, pas aux crimes flagrants.',
                    ],
                ],
            ],
            18 => [
                'choice' => '1 aptitude ou 1 capacité',
                'aptitudes' => [
                    [
                        'name' => 'Changer de camp sans bouger',
                        'effect' => 'Tu survives à un retournement politique (purge, changement d’alignement de ville) en te recasant. Narratif de fin d’arc. Pas une téléportation.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'La cour te doit un sourire',
                        'type' => 'Contextuelle (grande ville alignée)',
                        'effect' => 'À Bonta ou Brâkmar, un PNJ de rang te reçoit même après une gaffe, une fois. La deuxième fois, il faut payer.',
                    ],
                ],
            ],
            20 => [
                'choice' => '1 aptitude ou 1 capacité ; expertise possible',
                'aptitudes' => [
                    [
                        'name' => 'Une parole pour la ville',
                        'effect' => 'Capstone : tu fais basculer une décision publique (trêve, décret, mariage politique, embargo). Avec le MJ, une fois. Ça change la campagne, pas un jet de dégâts.',
                    ],
                ],
                'capacities' => [
                    [
                        'name' => 'Intouchable en public',
                        'type' => 'Réactive',
                        'effect' => 'Tant que des témoins « comme il faut » sont là, t’attaquer ouvertement coûte cher politiquement à l’agresseur. N’empêche pas un assassinat dans l’ombre.',
                    ],
                ],
            ],
        ],
    ],
];
