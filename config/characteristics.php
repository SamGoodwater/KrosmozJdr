<?php

declare(strict_types=1);

/**
 * Configuration des caractéristiques du jeu KrosmozJDR.
 *
 * Chaque caractéristique est identifiée par un id (clé du tableau) et définit :
 * - Source de vérité : nom, nom abrégé, description, icône, couleur
 * - Type et contraintes : int, string, array
 * - Par entité (monster, class, item) : formules, min/max, requis, message d'erreur.
 *   Player et NPC utilisent l'entité config "class" (alias en ValidationService).
 *   Item : bonus que peut apporter un objet (min, max, formule avec [level] = niveau de l'objet).
 *
 * Ce fichier définit les VALEURS LIMITES (min/max) pour la validation et la génération.
 * Les formules/fonctions qui convertissent les données DofusDB → KrosmozJDR sont ailleurs :
 * - Données de mapping : config/dofusdb_conversion.php
 * - Code des formules : App\Services\Scrapping\V2\Conversion\FormatterApplicator et DofusDbConversionFormulas
 * - Doc : docs/50-Fonctionnalités/Scrapping/Refonte/CONVERSION_FORMULAS_PLACEMENT.md
 *
 * Syntaxe des formules (exploitables et affichage) :
 * - [id] : référence à une autre caractéristique (ex. [vitality], [level], [strength]).
 * - [mod_<carac>] : modificateur d'une caractéristique primaire (mod_strength, mod_intelligence, etc.) ; défini comme caractéristiques dérivées (formula = floor(([<carac>]-10)/2)). Permet de ne pas recalculer le mod et de vérifier les limites.
 * - ndX : dés (n dés à X faces), ex. 1d6, 2d10.
 * - Math : + - * / ( ) ; floor() et ceil() pour arrondis.
 *
 * Formules par entité (pas au niveau global) :
 * - formula : chaîne exploitable (calcul mathématique uniquement, sans équipement ni forgemagie). Présente dans entities.class et/ou entities.monster.
 * - formula_display : chaîne lisible pour l'UI ; idem.
 * - Item : pas de formula ni formula_display (bonus bruts).
 *
 * Résistances (res_*) et dommages fixes (do_fixe_*, res_fixe_*) : bases communes (RES_BASE, etc.) ; formula_display dans chaque entity.
 *
 * Compétences : formula = 1d20 + [mod_characteristic] + [competence_mastery] * [master_bonus].
 * - [characteristic] : résolu via la clé 'characteristic' de la compétence (ex. strength pour Athlétisme) → [mod_characteristic] = mod de cette carac.
 * - [competence_mastery] : variable contextuelle. Lors de l'évaluation d'un jet de compétence, l'évaluateur injecte la valeur de maîtrise de l'entité pour cette compétence : 0 = aucune, 1 = maîtrisé (+1×master_bonus), 2 = expertise (+2×master_bonus). Source : competences_mastery.<id_compétence> ou équivalent selon le modèle de données.
 *
 * Monstres : min/max ~ ±50 % par rapport à la classe ; formula peut différer (ex. life : [vitality]*7 pour monstre, [vitality]*10+[level]*2 pour classe).
 *
 * Forgemagie : forgemagie (allowed, max) au niveau de la caractéristique. Équipement et max par niveau : config/equipment_characteristics.php.
 */

/*
|--------------------------------------------------------------------------
| Bases réutilisables : résistances (res_*)
|--------------------------------------------------------------------------
*/
$RES_BASE = [
    'type' => 'int',
    'unit' => null,
    'icon' => null,
    'description' => 'Résistance fixe aux dégâts. Règles : 0 à 10 (équipement +10 max, forgemagie +3 max).',
    'forgemagie' => ['allowed' => true, 'max' => 3],
    'applies_to' => ['monster', 'class', 'item'],
    'entities' => [
        'monster' => ['min' => -20, 'max' => 20, 'formula' => null, 'formula_display' => '0 à 100% + équipement + forgemagie', 'default' => 0, 'required' => false],
        'class' => ['min' => -20, 'max' => 13, 'formula' => null, 'formula_display' => '0 à 100% + équipement + forgemagie', 'default' => 0, 'required' => false],
        'item' => [
            'min' => 0,
            'max' => 10,
            'formula' => null,
            'default' => 0,
            'required' => false,
            'validation_message' => 'Le bonus Résistance de l\'objet doit être entre :min et :max (équipement +10, forgemagie +3 max).',
        ],
    ],
];

$RES_ELEMENTS = [
    'res_neutre' => ['name' => 'Résistance Neutre', 'short_name' => 'Res. neutre', 'color' => 'neutral', 'db_column' => 'res_neutre', 'order' => 24],
    'res_terre' => ['name' => 'Résistance Terre', 'short_name' => 'Res. terre', 'color' => 'brown', 'db_column' => 'res_terre', 'order' => 25],
    'res_feu' => ['name' => 'Résistance Feu', 'short_name' => 'Res. feu', 'color' => 'red', 'db_column' => 'res_feu', 'order' => 26],
    'res_air' => ['name' => 'Résistance Air', 'short_name' => 'Res. air', 'color' => 'green', 'db_column' => 'res_air', 'order' => 27],
    'res_eau' => ['name' => 'Résistance Eau', 'short_name' => 'Res. eau', 'color' => 'blue', 'db_column' => 'res_eau', 'order' => 28],
];

$resCharacteristics = [];
foreach ($RES_ELEMENTS as $id => $overrides) {
    $resCharacteristics[$id] = array_merge($RES_BASE, $overrides);
}

/*
|--------------------------------------------------------------------------
| Bases réutilisables : dommages fixes (do_fixe_*)
|--------------------------------------------------------------------------
*/
$DO_FIXE_BASE = [
    'type' => 'int',
    'unit' => null,
    'icon' => null,
    'description' => 'Dommage fixe ajouté aux attaques/sorts. Règles : 0 à 10 (équipement +10 max, forgemagie +5 max).',
    'forgemagie' => ['allowed' => true, 'max' => 5],
    'applies_to' => ['monster', 'class', 'item'],
    'entities' => [
        'monster' => ['min' => -10, 'max' => 20, 'formula' => null, 'formula_display' => '0 + équipement + forgemagie', 'default' => 0, 'required' => false],
        'class' => ['min' => -10, 'max' => 15, 'formula' => null, 'formula_display' => '0 + équipement + forgemagie', 'default' => 0, 'required' => false],
        'item' => [
            'min' => 0,
            'max' => 10,
            'formula' => null,
            'default' => 0,
            'required' => false,
            'validation_message' => 'Le bonus Dommage fixe de l\'objet doit être entre :min et :max (équipement +10, forgemagie +5 max).',
        ],
    ],
];

$DO_FIXE_ELEMENTS = [
    'do_fixe_neutre' => ['name' => 'Dommage fixe Neutre', 'short_name' => 'DO neutre', 'color' => 'neutral', 'db_column' => 'do_fixe_neutre', 'order' => 29],
    'do_fixe_terre' => ['name' => 'Dommage fixe Terre', 'short_name' => 'DO terre', 'color' => 'brown', 'db_column' => 'do_fixe_terre', 'order' => 30],
    'do_fixe_feu' => ['name' => 'Dommage fixe Feu', 'short_name' => 'DO feu', 'color' => 'red', 'db_column' => 'do_fixe_feu', 'order' => 31],
    'do_fixe_air' => ['name' => 'Dommage fixe Air', 'short_name' => 'DO air', 'color' => 'green', 'db_column' => 'do_fixe_air', 'order' => 32],
    'do_fixe_eau' => ['name' => 'Dommage fixe Eau', 'short_name' => 'DO eau', 'color' => 'blue', 'db_column' => 'do_fixe_eau', 'order' => 33],
];

$doFixeCharacteristics = [];
foreach ($DO_FIXE_ELEMENTS as $id => $overrides) {
    $doFixeCharacteristics[$id] = array_merge($DO_FIXE_BASE, $overrides);
}

/*
|--------------------------------------------------------------------------
| Bases réutilisables : résistances fixes (res_fixe_*)
|--------------------------------------------------------------------------
*/
$RES_FIXE_BASE = [
    'type' => 'int',
    'unit' => null,
    'icon' => null,
    'description' => 'Résistance fixe (seuil). Règles : 0 à 10 (équipement +10 max, forgemagie +3 max).',
    'forgemagie' => ['allowed' => true, 'max' => 3],
    'applies_to' => ['monster', 'class', 'item'],
    'entities' => [
        'monster' => ['min' => -20, 'max' => 20, 'formula' => null, 'formula_display' => '0 + équipement + forgemagie', 'default' => 0, 'required' => false],
        'class' => ['min' => -20, 'max' => 13, 'formula' => null, 'formula_display' => '0 + équipement + forgemagie', 'default' => 0, 'required' => false],
        'item' => [
            'min' => 0,
            'max' => 10,
            'formula' => null,
            'default' => 0,
            'required' => false,
            'validation_message' => 'Le bonus Résistance fixe de l\'objet doit être entre :min et :max.',
        ],
    ],
];

$RES_FIXE_ELEMENTS = [
    'res_fixe_neutre' => ['name' => 'Résistance fixe Neutre', 'short_name' => 'Res. fixe neutre', 'color' => 'neutral', 'db_column' => 'res_fixe_neutre', 'order' => 34],
    'res_fixe_terre' => ['name' => 'Résistance fixe Terre', 'short_name' => 'Res. fixe terre', 'color' => 'brown', 'db_column' => 'res_fixe_terre', 'order' => 35],
    'res_fixe_feu' => ['name' => 'Résistance fixe Feu', 'short_name' => 'Res. fixe feu', 'color' => 'red', 'db_column' => 'res_fixe_feu', 'order' => 36],
    'res_fixe_air' => ['name' => 'Résistance fixe Air', 'short_name' => 'Res. fixe air', 'color' => 'green', 'db_column' => 'res_fixe_air', 'order' => 37],
    'res_fixe_eau' => ['name' => 'Résistance fixe Eau', 'short_name' => 'Res. fixe eau', 'color' => 'blue', 'db_column' => 'res_fixe_eau', 'order' => 38],
];

$resFixeCharacteristics = [];
foreach ($RES_FIXE_ELEMENTS as $id => $overrides) {
    $resFixeCharacteristics[$id] = array_merge($RES_FIXE_BASE, $overrides);
}

/*
|--------------------------------------------------------------------------
| Compétences (même structure que les caractéristiques)
|--------------------------------------------------------------------------
| bonus (int) : entities avec min/max. mastery (int) : 0 = aucune, 1 = maîtrisé, 2 = expertise.
*/
$COMPETENCE_BASE = [
    'type' => 'int',
    'unit' => null,
    'icon' => null,
    'forgemagie' => ['allowed' => true, 'max' => 3],
    'applies_to' => ['monster', 'class', 'item'],
    'is_competence' => true,
    'mastery_value_available' => [0, 1, 2],
    'mastery_labels' => [
        0 => 'Aucune',
        1 => 'Maîtrisé',
        2 => 'Expertise',
    ],
    'entities' => [
        'monster' => [
            'min' => -5,
            'max' => 15,
            'formula' => '1d20 + [mod_characteristic] + [competence_mastery] * [master_bonus]',
            'formula_display' => '1d20 + Mod. caractéristique + maîtrise/expertise + équipement + forgemagie',
            'default' => 0,
            'required' => false,
        ],
        'class' => [
            'min' => 0,
            'max' => 8,
            'formula' => '1d20 + [mod_characteristic] + [competence_mastery] * [master_bonus]',
            'formula_display' => '1d20 + Mod. caractéristique + maîtrise/expertise + équipement + forgemagie',
            'default' => 0,
            'required' => false,
            'validation_message' => 'Le bonus à la compétence doit être entre :min et :max (équipement +5 max, forgemagie +3 max).',
        ],
        'item' => [
            'min' => 0,
            'max' => 5,
            'formula' => null,
            'default' => 0,
            'required' => false,
            'validation_message' => 'Le bonus à la compétence de l\'objet doit être entre :min et :max (équipement +5, forgemagie +3 max).',
        ],
    ],
];

$COMPETENCE_LIST = [
    'athletisme' => ['name' => 'Athlétisme', 'short_name' => 'Athl.', 'description' => 'Sauter, escalader, nager, soulever, forcer.', 'characteristic' => 'strength', 'alternative_characteristic' => null, 'skill_type' => 'physique', 'color' => 'brown', 'order' => 40],
    'intimidation' => ['name' => 'Intimidation', 'short_name' => 'Intim.', 'description' => 'Menacer, effrayer, imposer par la force ou la présence.', 'characteristic' => 'strength', 'alternative_characteristic' => 'chance', 'skill_type' => 'physique', 'color' => 'brown', 'order' => 41],
    'acrobaties' => ['name' => 'Acrobaties', 'short_name' => 'Acro.', 'description' => 'Équilibre, voltige, roulade, souplesse.', 'characteristic' => 'agility', 'alternative_characteristic' => null, 'skill_type' => 'physique', 'color' => 'green', 'order' => 42],
    'discretion' => ['name' => 'Discrétion', 'short_name' => 'Discr.', 'description' => 'Se cacher, se faufiler, éviter la détection.', 'characteristic' => 'agility', 'alternative_characteristic' => null, 'skill_type' => 'physique', 'color' => 'green', 'order' => 43],
    'escamotage' => ['name' => 'Escamotage', 'short_name' => 'Escam.', 'description' => 'Voler, crocheter, manipuler des objets avec précision.', 'characteristic' => 'agility', 'alternative_characteristic' => null, 'skill_type' => 'physique', 'color' => 'green', 'order' => 44],
    'arcanes' => ['name' => 'Arcanes', 'short_name' => 'Arc.', 'description' => 'Connaissance de la magie, des sorts, des objets magiques.', 'characteristic' => 'intelligence', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'red', 'order' => 45],
    'histoire' => ['name' => 'Histoire', 'short_name' => 'Hist.', 'description' => 'Connaissance de l\'histoire, des civilisations, des événements passés.', 'characteristic' => 'intelligence', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'red', 'order' => 46],
    'investigation' => ['name' => 'Investigation', 'short_name' => 'Inves.', 'description' => 'Examiner, analyser, déduire, résoudre des énigmes.', 'characteristic' => 'intelligence', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'red', 'order' => 47],
    'nature' => ['name' => 'Nature', 'short_name' => 'Nat.', 'description' => 'Connaissance de la nature, des plantes, des animaux, des écosystèmes.', 'characteristic' => 'intelligence', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'red', 'order' => 48],
    'religion' => ['name' => 'Religion', 'short_name' => 'Rel.', 'description' => 'Connaissance des religions, des dieux, des rites et symboles.', 'characteristic' => 'intelligence', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'red', 'order' => 49],
    'dressage' => ['name' => 'Dressage', 'short_name' => 'Dress.', 'description' => 'Manipuler, entraîner et communiquer avec les animaux.', 'characteristic' => 'wisdom', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'violet', 'order' => 50],
    'medecine' => ['name' => 'Médecine', 'short_name' => 'Méd.', 'description' => 'Soigner, diagnostiquer, stabiliser un blessé.', 'characteristic' => 'wisdom', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'violet', 'order' => 51],
    'perception' => ['name' => 'Perception', 'short_name' => 'Perc.', 'description' => 'Remarquer, écouter, observer, détecter dangers et pièges.', 'characteristic' => 'wisdom', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'violet', 'order' => 52],
    'perspicacite' => ['name' => 'Perspicacité', 'short_name' => 'Persp.', 'description' => 'Détecter les mensonges, comprendre les motivations, lire les personnes.', 'characteristic' => 'wisdom', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'violet', 'order' => 53],
    'survie' => ['name' => 'Survie', 'short_name' => 'Surv.', 'description' => 'Suivre des pistes, chasser, s\'orienter, survivre dans la nature.', 'characteristic' => 'wisdom', 'alternative_characteristic' => null, 'skill_type' => 'mental', 'color' => 'violet', 'order' => 54],
    'persuasion' => ['name' => 'Persuasion', 'short_name' => 'Persu.', 'description' => 'Convaincre, négocier, influencer, diplomatie.', 'characteristic' => 'chance', 'alternative_characteristic' => null, 'skill_type' => 'social', 'color' => 'blue', 'order' => 55],
    'representation' => ['name' => 'Représentation', 'short_name' => 'Repré.', 'description' => 'Jouer, chanter, danser, divertir un public.', 'characteristic' => 'chance', 'alternative_characteristic' => null, 'skill_type' => 'social', 'color' => 'blue', 'order' => 56],
    'supercherie' => ['name' => 'Supercherie', 'short_name' => 'Superch.', 'description' => 'Mentir, se déguiser, tromper, bluffer.', 'characteristic' => 'chance', 'alternative_characteristic' => null, 'skill_type' => 'social', 'color' => 'blue', 'order' => 57],
    'artisanat' => ['name' => 'Artisanat', 'short_name' => 'Artis.', 'description' => 'Créer, réparer, améliorer des objets (forge, couture, alchimie, etc.).', 'characteristic' => 'intelligence', 'alternative_characteristic' => null, 'skill_type' => 'technique', 'color' => 'teal', 'order' => 58],
    'herbaliste' => ['name' => 'Herbaliste', 'short_name' => 'Herb.', 'description' => 'Identifier les plantes, récolter, préparer remèdes et poisons.', 'characteristic' => 'wisdom', 'alternative_characteristic' => 'intelligence', 'skill_type' => 'technique', 'color' => 'teal', 'order' => 59],
    'connaissance_creatures' => ['name' => 'Connaissance des créatures', 'short_name' => 'Conn. créat.', 'description' => 'Identifier les créatures, connaître comportements et faiblesses.', 'characteristic' => 'intelligence', 'alternative_characteristic' => 'wisdom', 'skill_type' => 'technique', 'color' => 'teal', 'order' => 60],
];

$competenceCharacteristics = [];
foreach ($COMPETENCE_LIST as $id => $overrides) {
    $competenceCharacteristics[$id] = array_merge($COMPETENCE_BASE, [
        'db_column' => $id,
        'name' => $overrides['name'],
        'short_name' => $overrides['short_name'],
        'description' => $overrides['description'],
        'characteristic' => $overrides['characteristic'],
        'alternative_characteristic' => $overrides['alternative_characteristic'],
        'skill_type' => $overrides['skill_type'],
        'color' => $overrides['color'],
        'order' => $overrides['order'],
    ]);
}

return [
    /*
    |--------------------------------------------------------------------------
    | Définitions des caractéristiques (source de vérité)
    |--------------------------------------------------------------------------
    |
    | Clé = id de la caractéristique (utilisable en base, API, stockage).
    | db_column : nom de la colonne en BDD si différent de l'id.
    | res_*, do_fixe_*, res_fixe_*, compétences sont fusionnés en fin de tableau (bases ci-dessus).
    | Compétences : is_competence=true, mastery_value_available [0,1,2], mastery_labels (0=Aucune, 1=Maîtrisé, 2=Expertise).
    |
    */
    'characteristics' => array_merge([
    /*
    |--------------------------------------------------------------------------
    | Caractéristiques définies manuellement
    |--------------------------------------------------------------------------
    */
        'name' => [
            'db_column' => 'name',
            'name' => 'Nom',
            'short_name' => null,
            'description' => 'Nom de l\'entité',
            'icon' => null,
            'color' => null,
            'type' => 'string',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class', 'item'],
            'validation' => [
                'max' => 255,
                'min' => 1,
            ],
            'default' => null,
            'entities' => [
                'monster' => ['required' => true, 'validation_message' => 'Le nom du monstre est obligatoire.'],
                'class' => ['required' => true, 'validation_message' => 'Le nom du personnage / PNJ / classe est obligatoire.'],
                'item' => ['required' => true, 'validation_message' => 'Le nom de l\'objet est obligatoire.'],
            ],
            'order' => 0,
        ],

        'description' => [
            'db_column' => 'description',
            'name' => 'Description',
            'short_name' => null,
            'description' => 'Description textuelle de l\'entité',
            'icon' => null,
            'color' => null,
            'type' => 'string',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class', 'item'],
            'validation' => [
                'max' => 65535,
            ],
            'default' => null,
            'entities' => [
                'monster' => ['required' => false],
                'class' => ['required' => false],
                'item' => ['required' => true, 'validation_message' => 'La description de l\'objet est obligatoire.'],
            ],
            'order' => 1,
        ],

        'level' => [
            'db_column' => 'level',
            'name' => 'Niveau',
            'short_name' => 'Niv.',
            'description' => 'Niveau de l\'entité',
            'icon' => null,
            'color' => 'zinc',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 1,
                    'max' => 200,
                    'formula' => null,
                    'default' => 1,
                    'required' => true,
                    'validation_message' => 'Le niveau du monstre doit être entre :min et :max.',
                ],
                'class' => [
                    'min' => 1,
                    'max' => 200,
                    'formula' => null,
                    'default' => 1,
                    'required' => true,
                    'validation_message' => 'Le niveau du personnage / PNJ / classe doit être entre :min et :max.',
                ],
                'item' => [
                    'min' => 1,
                    'max' => 20,
                    'formula' => null,
                    'default' => 1,
                    'required' => true,
                    'validation_message' => 'Le niveau de l\'objet doit être entre :min et :max.',
                ],
            ],
            'order' => 2,
        ],

        'life' => [
            'db_column' => 'life',
            'name' => 'Points de vie',
            'short_name' => 'PV',
            'description' => 'Points de vie actuels ou maximum. Règles : joueur = Vitalité × 10 + dés de vie (classe) ; monstre = Vitalité × 5 à 10.',
            'icon' => 'heart',
            'color' => 'lime',
            'type' => 'int',
            'unit' => 'PV',
            'forgemagie' => ['allowed' => true, 'max' => 20],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 1,
                    'max' => 10000,
                    'formula' => '[vitality] * 7',
                    'formula_display' => 'Vitalité × 7 + équipement + forgemagie',
                    'default' => 70,
                    'required' => true,
                    'validation_message' => 'Les points de vie du monstre doivent être entre :min et :max.',
                ],
                'class' => [
                    'min' => 1,
                    'max' => 2000,
                    'formula' => '[vitality] * 10 + [level] * 2',
                    'formula_display' => 'Vitalité × 10 + Niveau × 2 + équipement + forgemagie',
                    'default' => '[life_dice] + [vitality] * [level] + 1d[life_dice]',
                    'required' => true,
                    'validation_message' => 'Les points de vie du personnage / PNJ / classe doivent être entre :min et :max.',
                ],
                'item' => [
                    'min' => 0,
                    'max' => 100,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus PV de l\'objet doit être entre :min et :max (équipement illimité en kamas, plafond raisonnable ici).',
                ],
            ],
            'order' => 3,
        ],

        'life_dice' => [
            'db_column' => 'life_dice',
            'name' => 'Dés de vie',
            'short_name' => 'Dés',
            'description' => 'Dés de vie utilisés pour les points de vie (type selon classe : d6 à d12)',
            'icon' => null,
            'color' => 'lime',
            'type' => 'string',
            'entities' => [
                'monster' => [
                    'required' => false,
                    'validation_message' => 'Le dé qui représente la vie du monstre.',
                    'default' => 'd8',
                    'min' => 4,
                    'max' => 20,
                    'formula' => '1d[life_dice]',
                    'formula_display' => '1dX par ',
                ],
                'class' => [
                    'required' => true,
                    'validation_message' => 'Le dé qui représente la vie du personnage / PNJ / classe.',
                    'default' => 'd8',
                    'min' => 6,
                    'max' => 12,
                    'formula' => '1d[life_dice]',
                    'formula_display' => '1dX',
                ],
                'item' => ['required' => false],
            ],
            'order' => 4,
        ],

        'pa' => [
            'db_column' => 'pa',
            'name' => 'Points d\'action',
            'short_name' => 'PA',
            'description' => 'Points d\'action par tour. Règles : 6 de base, max 12 (équipement +6, forgemagie +1).',
            'icon' => null,
            'color' => 'blue',
            'type' => 'int',
            'unit' => 'PA',
            'forgemagie' => ['allowed' => true, 'max' => 1],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 20,
                    'formula' => '6 + floor([level] / 3)',
                    'formula_display' => '6 (base) + Niveau / 3',
                    'default' => 6,
                    'required' => false,
                    'validation_message' => 'Les PA du monstre doivent être entre :min et :max.',
                ],
                'class' => [
                    'min' => 0,
                    'max' => 12,
                    'formula' => '6',
                    'formula_display' => '6 (base) + équipement + forgemagie',
                    'default' => 6,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 6,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus PA de l\'objet doit être entre :min et :max (équipement +6 max).',
                ],
            ],
            'order' => 4,
        ],

        'pm' => [
            'db_column' => 'pm',
            'name' => 'Points de mouvement',
            'short_name' => 'PM',
            'description' => 'Points de mouvement par tour. Règles : 3 de base (classe), 4 (monstre type), max 6 (équipement +3, forgemagie +1, monture +1).',
            'icon' => null,
            'color' => 'green',
            'type' => 'int',
            'unit' => 'PM',
            'forgemagie' => ['allowed' => true, 'max' => 1],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 20,
                    'formula' => '3 + floor([level] / 5)',
                    'formula_display' => '3 (base) + Niveau / 5',
                    'default' => 4,
                    'required' => false,
                ],
                'class' => [
                    'min' => 0,
                    'max' => 6,
                    'formula' => '3',
                    'formula_display' => '3 (base) + équipement + forgemagie (+ monture)',
                    'default' => 3,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 3,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus PM de l\'objet doit être entre :min et :max (équipement +3 max).',
                ],
            ],
            'order' => 5,
        ],

        'strength' => [
            'db_column' => 'strong',
            'name' => 'Force',
            'short_name' => null,
            'description' => 'Capacité physique et musculaire',
            'icon' => null,
            'color' => 'brown',
            'type' => 'int',
            'unit' => 'points',
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 1000,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 10,
                    'required' => true,
                    'validation_message' => 'La force du monstre doit être entre :min et :max.',
                ],
                'class' => [
                    'min' => 6,
                    'max' => 31,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 8,
                    'required' => true,
                    'validation_message' => 'La force du personnage / PNJ doit être entre :min et :max (score 6-31, mod cap ⌊niveau/2⌋+1).',
                ],
                'item' => [
                    'min' => 0,
                    'max' => 4,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Force de l\'objet doit être entre :min et :max (équipement +4 max).',
                ],
            ],
            'order' => 10,
        ],

        'intelligence' => [
            'db_column' => 'intel',
            'name' => 'Intelligence',
            'short_name' => 'Int.',
            'description' => 'Capacité mentale et magique',
            'icon' => null,
            'color' => 'red',
            'type' => 'int',
            'unit' => 'points',
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 1000,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 10,
                    'required' => true,
                    'validation_message' => 'L\'intelligence du monstre doit être entre :min et :max.',
                ],
                'class' => [
                    'min' => 6,
                    'max' => 31,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 8,
                    'required' => true,
                    'validation_message' => 'L\'intelligence du personnage / PNJ doit être entre :min et :max (score 6-31).',
                ],
                'item' => [
                    'min' => 0,
                    'max' => 4,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Intelligence de l\'objet doit être entre :min et :max (équipement +4 max).',
                ],
            ],
            'order' => 11,
        ],

        'agility' => [
            'db_column' => 'agi',
            'name' => 'Agilité',
            'short_name' => 'Agi.',
            'description' => 'Souplesse et rapidité',
            'icon' => null,
            'color' => 'green',
            'type' => 'int',
            'unit' => 'points',
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 1000,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 10,
                    'required' => true,
                    'validation_message' => 'L\'agilité du monstre doit être entre :min et :max.',
                ],
                'class' => [
                    'min' => 6,
                    'max' => 31,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 8,
                    'required' => true,
                    'validation_message' => 'L\'agilité du personnage / PNJ doit être entre :min et :max (score 6-31).',
                ],
                'item' => [
                    'min' => 0,
                    'max' => 4,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Agilité de l\'objet doit être entre :min et :max (équipement +4 max).',
                ],
            ],
            'order' => 12,
        ],

        'chance' => [
            'db_column' => 'chance',
            'name' => 'Chance',
            'short_name' => null,
            'description' => 'Facteur chance et hasard (synonyme de luck)',
            'icon' => null,
            'color' => 'blue',
            'type' => 'int',
            'unit' => 'points',
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 1000,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 10,
                    'required' => true,
                    'validation_message' => 'La chance du monstre doit être entre :min et :max.',
                ],
                'class' => [
                    'min' => 6,
                    'max' => 31,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 8,
                    'required' => true,
                    'validation_message' => 'La chance du personnage / PNJ doit être entre :min et :max (score 6-31).',
                ],
                'item' => [
                    'min' => 0,
                    'max' => 4,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Chance de l\'objet doit être entre :min et :max (équipement +4 max).',
                ],
            ],
            'order' => 13,
        ],

        'wisdom' => [
            'db_column' => 'sagesse',
            'name' => 'Sagesse',
            'short_name' => 'Sag.',
            'description' => 'Connaissance et expérience',
            'icon' => null,
            'color' => 'violet',
            'type' => 'int',
            'unit' => 'points',
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 1000,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 10,
                    'required' => true,
                    'validation_message' => 'La sagesse du monstre doit être entre :min et :max.',
                ],
                'class' => [
                    'min' => 6,
                    'max' => 31,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 8,
                    'required' => true,
                    'validation_message' => 'La sagesse du personnage / PNJ doit être entre :min et :max (score 6-31).',
                ],
                'item' => [
                    'min' => 0,
                    'max' => 4,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Sagesse de l\'objet doit être entre :min et :max (équipement +4 max).',
                ],
            ],
            'order' => 14,
        ],

        'vitality' => [
            'db_column' => 'vitality',
            'name' => 'Vitalité',
            'short_name' => 'Vit.',
            'description' => 'Endurance et constitution',
            'icon' => null,
            'color' => 'amber',
            'type' => 'int',
            'unit' => 'points',
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 1000,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 10,
                    'required' => false,
                ],
                'class' => [
                    'min' => 6,
                    'max' => 31,
                    'formula' => null,
                    'formula_display' => '8 (base) ou 6 si spécialisation ; Mod = ⌊(score−10)/2⌋ ; base + équipement + forgemagie',
                    'default' => 8,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 4,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Vitalité de l\'objet doit être entre :min et :max (équipement +4 max).',
                ],
            ],
            'order' => 15,
        ],

        'mod_strength' => [
            'db_column' => 'mod_strength',
            'name' => 'Modificateur Force',
            'short_name' => 'Mod. Force',
            'description' => 'Modificateur dérivé : floor((Force − 10) / 2). Permet de vérifier les limites sans recalcul.',
            'icon' => null,
            'color' => 'brown',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([strength]-10)/2)', 'formula_display' => '⌊(Force − 10) / 2⌋', 'default' => 0, 'required' => false],
                'class' => ['min' => -2, 'max' => 11, 'formula' => 'floor(([strength]-10)/2)', 'formula_display' => '⌊(Force − 10) / 2⌋ (cap ⌊Niveau/2⌋+1)', 'default' => 0, 'required' => false],
            ],
            'order' => 16,
        ],

        'mod_intelligence' => [
            'db_column' => 'mod_intelligence',
            'name' => 'Modificateur Intelligence',
            'short_name' => 'Mod. Int.',
            'description' => 'Modificateur dérivé : floor((Intelligence − 10) / 2).',
            'icon' => null,
            'color' => 'red',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([intelligence]-10)/2)', 'formula_display' => '⌊(Intelligence − 10) / 2⌋', 'default' => 0, 'required' => false],
                'class' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([intelligence]-10)/2)', 'formula_display' => '⌊(Intelligence − 10) / 2⌋ (cap ⌊Niveau/2⌋+1)', 'default' => 0, 'required' => false],
            ],
            'order' => 17,
        ],

        'mod_agility' => [
            'db_column' => 'mod_agility',
            'name' => 'Modificateur Agilité',
            'short_name' => 'Mod. Agi.',
            'description' => 'Modificateur dérivé : floor((Agilité − 10) / 2).',
            'icon' => null,
            'color' => 'green',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([agility]-10)/2)', 'formula_display' => '⌊(Agilité − 10) / 2⌋', 'default' => 0, 'required' => false],
                'class' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([agility]-10)/2)', 'formula_display' => '⌊(Agilité − 10) / 2⌋ (cap ⌊Niveau/2⌋+1)', 'default' => 0, 'required' => false],
            ],
            'order' => 18,
        ],

        'mod_chance' => [
            'db_column' => 'mod_chance',
            'name' => 'Modificateur Chance',
            'short_name' => 'Mod. Chance',
            'description' => 'Modificateur dérivé : floor((Chance − 10) / 2).',
            'icon' => null,
            'color' => 'blue',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([chance]-10)/2)', 'formula_display' => '⌊(Chance − 10) / 2⌋', 'default' => 0, 'required' => false],
                'class' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([chance]-10)/2)', 'formula_display' => '⌊(Chance − 10) / 2⌋ (cap ⌊Niveau/2⌋+1)', 'default' => 0, 'required' => false],
            ],
            'order' => 19,
        ],

        'mod_wisdom' => [
            'db_column' => 'mod_wisdom',
            'name' => 'Modificateur Sagesse',
            'short_name' => 'Mod. Sag.',
            'description' => 'Modificateur dérivé : floor((Sagesse − 10) / 2).',
            'icon' => null,
            'color' => 'violet',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([wisdom]-10)/2)', 'formula_display' => '⌊(Sagesse − 10) / 2⌋', 'default' => 0, 'required' => false],
                'class' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([wisdom]-10)/2)', 'formula_display' => '⌊(Sagesse − 10) / 2⌋ (cap ⌊Niveau/2⌋+1)', 'default' => 0, 'required' => false],
            ],
            'order' => 20,
        ],

        'mod_vitality' => [
            'db_column' => 'mod_vitality',
            'name' => 'Modificateur Vitalité',
            'short_name' => 'Mod. Vit.',
            'description' => 'Modificateur dérivé : floor((Vitalité − 10) / 2).',
            'icon' => null,
            'color' => 'amber',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([vitality]-10)/2)', 'formula_display' => '⌊(Vitalité − 10) / 2⌋', 'default' => 0, 'required' => false],
                'class' => ['min' => -5, 'max' => 11, 'formula' => 'floor(([vitality]-10)/2)', 'formula_display' => '⌊(Vitalité − 10) / 2⌋ (cap ⌊Niveau/2⌋+1)', 'default' => 0, 'required' => false],
            ],
            'order' => 21,
        ],

        'po' => [
            'db_column' => 'po',
            'name' => 'Portée / Points d\'opportunité',
            'short_name' => 'PO',
            'description' => 'Portée des sorts et aptitudes. Règles : 0 de base, max 6 (équipement +6, forgemagie +1). 1 PO = 1,5 m.',
            'icon' => null,
            'color' => 'cyan',
            'type' => 'int',
            'unit' => 'PO',
            'forgemagie' => ['allowed' => true, 'max' => 1],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 10,
                    'formula' => '0 + floor([mod_wisdom] / 3)',
                    'formula_display' => '0 (base) + Mod. Sagesse + équipement + forgemagie',
                    'default' => 1,
                    'required' => false,
                ],
                'class' => [
                    'min' => 0,
                    'max' => 6,
                    'formula' => '0',
                    'formula_display' => '0 (base) + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 6,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus PO de l\'objet doit être entre :min et :max (équipement +6 max).',
                ],
            ],
            'order' => 6,
        ],

        'ini' => [
            'db_column' => 'ini',
            'name' => 'Initiative',
            'short_name' => 'Ini.',
            'description' => 'Ordre d\'action au combat. Calcul : 1d20 + Mod Intelligence + équipement. Règles : illimité, forgemagie +3 max.',
            'icon' => null,
            'color' => 'violet',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 3],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => -10,
                    'max' => 30,
                    'formula' => '1d20 + [mod_intelligence]',
                    'formula_display' => '1d20 + Mod. Intelligence + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'class' => [
                    'min' => -10,
                    'max' => 30,
                    'formula' => '1d20 + [mod_intelligence]',
                    'formula_display' => '1d20 + Mod. Intelligence + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 3,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Initiative (forgemagie) doit être entre :min et :max.',
                ],
            ],
            'order' => 7,
        ],

        'ca' => [
            'db_column' => 'ca',
            'name' => 'Classe d\'armure',
            'short_name' => 'CA',
            'description' => 'Difficulté à être touché. Calcul : 10 + Mod Vitalité + bouclier. Règles : max 21 + 5 (équipement).',
            'icon' => null,
            'color' => 'gray',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 30,
                    'formula' => '10 + [mod_vitality]',
                    'formula_display' => '10 + Mod. Vitalité + équipement',
                    'default' => 10,
                    'required' => false,
                ],
                'class' => [
                    'min' => 0,
                    'max' => 26,
                    'formula' => '10 + [mod_vitality]',
                    'formula_display' => '10 + Mod. Vitalité + équipement',
                    'default' => 10,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 5,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus CA (bouclier) doit être entre :min et :max (équipement +5 max).',
                ],
            ],
            'order' => 8,
        ],

        'touch' => [
            'db_column' => 'touch',
            'name' => 'Bonus de touche',
            'short_name' => null,
            'description' => 'Bonus aux jets d\'attaque. Calcul selon le sort : 1d20 + Mod(carac du sort). Règles : max 11 + 5 (équipement).',
            'icon' => null,
            'color' => 'gray',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => -5,
                    'max' => 20,
                    'formula' => null,
                    'formula_display' => '1d20 + Mod. (carac du sort) + équipement',
                    'default' => 0,
                    'required' => false,
                ],
                'class' => [
                    'min' => -5,
                    'max' => 16,
                    'formula' => null,
                    'formula_display' => '1d20 + Mod. (carac du sort) + équipement',
                    'default' => 0,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 5,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus de touche de l\'objet doit être entre :min et :max.',
                ],
            ],
            'order' => 9,
        ],

        'fuite' => [
            'db_column' => 'fuite',
            'name' => 'Fuite',
            'short_name' => null,
            'description' => 'Jet pour quitter un corps-à-corps (1d20 + Mod Agilité + équipement). Règles : max 11 + 10 (équipement), forgemagie +2.',
            'icon' => null,
            'color' => 'lime',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => -5,
                    'max' => 25,
                    'formula' => '1d20 + [mod_agility]',
                    'formula_display' => '1d20 + Mod. Agilité + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'class' => [
                    'min' => -5,
                    'max' => 21,
                    'formula' => '1d20 + [mod_agility]',
                    'formula_display' => '1d20 + Mod. Agilité + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 10,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Fuite de l\'objet doit être entre :min et :max (équipement +10 max).',
                ],
            ],
            'order' => 17,
        ],

        'tacle' => [
            'db_column' => 'tacle',
            'name' => 'Tacle',
            'short_name' => null,
            'description' => 'Jet pour empêcher la fuite (1d20 + Mod Chance + équipement). Règles : max 11 + 10 (équipement), forgemagie +2.',
            'icon' => null,
            'color' => 'sky',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => -5,
                    'max' => 25,
                    'formula' => '1d20 + [mod_chance]',
                    'formula_display' => '1d20 + Mod. Chance + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'class' => [
                    'min' => -5,
                    'max' => 21,
                    'formula' => '1d20 + [mod_chance]',
                    'formula_display' => '1d20 + Mod. Chance + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 10,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Tacle de l\'objet doit être entre :min et :max (équipement +10 max).',
                ],
            ],
            'order' => 18,
        ],

        'dodge_pa' => [
            'db_column' => 'dodge_pa',
            'name' => 'Esquive PA',
            'short_name' => null,
            'description' => 'Seuil pour éviter la perte de PA (8 + Mod Sagesse + équipement). Règles : max 19 + 5, forgemagie +2.',
            'icon' => null,
            'color' => 'sky',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 30,
                    'formula' => '8 + [mod_wisdom]',
                    'formula_display' => '8 + Mod. Sagesse + équipement + forgemagie',
                    'default' => 8,
                    'required' => false,
                ],
                'class' => [
                    'min' => 0,
                    'max' => 24,
                    'formula' => '8 + [mod_wisdom]',
                    'formula_display' => '8 + Mod. Sagesse + équipement + forgemagie',
                    'default' => 8,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 5,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Esquive PA de l\'objet doit être entre :min et :max.',
                ],
            ],
            'order' => 19,
        ],

        'dodge_pm' => [
            'db_column' => 'dodge_pm',
            'name' => 'Esquive PM',
            'short_name' => null,
            'description' => 'Seuil pour éviter la perte de PM (8 + Mod Sagesse + équipement). Règles : max 19 + 5, forgemagie +2.',
            'icon' => null,
            'color' => 'emerald',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 30,
                    'formula' => '8 + [mod_wisdom]',
                    'formula_display' => '8 + Mod. Sagesse + équipement + forgemagie',
                    'default' => 8,
                    'required' => false,
                ],
                'class' => [
                    'min' => 0,
                    'max' => 24,
                    'formula' => '8 + [mod_wisdom]',
                    'formula_display' => '8 + Mod. Sagesse + équipement + forgemagie',
                    'default' => 8,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 5,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Esquive PM de l\'objet doit être entre :min et :max.',
                ],
            ],
            'order' => 20,
        ],

        'invocation' => [
            'db_column' => 'invocation',
            'name' => 'Nombre d\'invocations',
            'short_name' => null,
            'description' => 'Créatures invocables simultanément. Règles : base 1, max = bonus maîtrise, équipement +5 max, forgemagie +1.',
            'icon' => null,
            'color' => null,
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 1],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => 0,
                    'max' => 10,
                    'formula' => '0',
                    'formula_display' => '0 (base) + équipement + forgemagie',
                    'default' => 0,
                    'required' => false,
                ],
                'class' => [
                    'min' => 1,
                    'max' => 11,
                    'formula' => '1',
                    'formula_display' => '1 (base) + équipement + forgemagie ; max = bonus maîtrise',
                    'default' => 1,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 5,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Invocation de l\'objet doit être entre :min et :max (équipement +5 max).',
                ],
            ],
            'order' => 21,
        ],

        'kamas' => [
            'db_column' => 'kamas',
            'name' => 'Kamas',
            'short_name' => null,
            'description' => 'Monnaie du Monde des Douze.',
            'icon' => null,
            'color' => 'yellow',
            'type' => 'int',
            'unit' => 'kamas',
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => [
                    'min' => null,
                    'max' => null,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                ],
                'class' => [
                    'min' => null,
                    'max' => null,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                ],
                'item' => [
                    'min' => null,
                    'max' => null,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                ],
            ],
            'order' => 23,
        ],

        'master_bonus' => [
            'db_column' => 'master_bonus',
            'name' => 'Réserve de Wakfu',
            'short_name' => 'Wakfu',
            'description' => 'Points utilisables hors combat pour sorts/aptitudes. Bonus de maîtrise = 1 + floor(Niveau/4). Règles : Bonus de maîtrise + équipement (+3 max).',
            'icon' => null,
            'color' => 'orange',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['class', 'item'],
            'entities' => [
                'class' => [
                    'min' => 0,
                    'max' => 9,
                    'formula' => '1 + floor([level]/4)',
                    'formula_display' => 'Bonus de maîtrise + équipement',
                    'default' => 1,
                    'required' => false,
                ],
                'item' => [
                    'min' => 0,
                    'max' => 3,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Réserve de Wakfu de l\'objet doit être entre :min et :max (équipement +3 max).',
                ],
            ],
            'order' => 24,
        ],

        'do_fixe_multiple' => [
            'db_column' => 'do_fixe_multiple',
            'name' => 'Dommage fixe Multiple',
            'short_name' => 'DO multiple',
            'description' => 'Dommage fixe ajouté (tous éléments). Règles : 0 à 5 (équipement +5 max, forgemagie +2 max).',
            'icon' => null,
            'color' => 'neutral',
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => true, 'max' => 2],
            'applies_to' => ['monster', 'class', 'item'],
            'entities' => [
                'monster' => ['min' => -5, 'max' => 20, 'formula' => null, 'formula_display' => '0 + équipement + forgemagie', 'default' => 0, 'required' => false],
                'class' => ['min' => -5, 'max' => 15, 'formula' => null, 'formula_display' => '0 + équipement + forgemagie', 'default' => 0, 'required' => false],
                'item' => [
                    'min' => 0,
                    'max' => 5,
                    'formula' => null,
                    'default' => 0,
                    'required' => false,
                    'validation_message' => 'Le bonus Dommage fixe multiple de l\'objet doit être entre :min et :max (équipement +5, forgemagie +2 max).',
                ],
            ],
            'order' => 33,
        ],

        'size' => [
            'db_column' => 'size',
            'name' => 'Taille',
            'short_name' => null,
            'description' => 'Catégorie de taille de l\'entité',
            'icon' => null,
            'color' => null,
            'type' => 'array',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'value_available' => ['tiny', 'small', 'medium', 'large', 'huge'],
            'labels' => [
                'tiny' => 'Très petit',
                'small' => 'Petit',
                'medium' => 'Moyen',
                'large' => 'Grand',
                'huge' => 'Énorme',
            ],
            'default' => 'medium',
            'entities' => [
                'monster' => [
                    'required' => false,
                    'validation_message' => 'La taille doit être l\'une des valeurs : :values.',
                ],
                'class' => [
                    'required' => false,
                ],
            ],
            'order' => 20,
        ],

        'hostility' => [
            'db_column' => 'hostility',
            'name' => 'Hostilité',
            'short_name' => null,
            'description' => 'Niveau d\'hostilité (0-4). 0 = neutre, 4 = hostile.',
            'icon' => null,
            'color' => null,
            'type' => 'int',
            'unit' => null,
            'forgemagie' => ['allowed' => false, 'max' => 0],
            'applies_to' => ['monster', 'class'],
            'entities' => [
                'monster' => ['min' => 0, 'max' => 4, 'formula' => null, 'default' => 0, 'required' => false],
                'class' => ['min' => 0, 'max' => 4, 'formula' => null, 'default' => 0, 'required' => false],
            ],
            'order' => 39,
        ],
    ], $resCharacteristics, $doFixeCharacteristics, $resFixeCharacteristics, $competenceCharacteristics),

    /*
    |--------------------------------------------------------------------------
    | Compétences (alias : même entrées que dans characteristics, avec is_competence=true)
    |--------------------------------------------------------------------------
    */
    'competences' => $competenceCharacteristics,
];
