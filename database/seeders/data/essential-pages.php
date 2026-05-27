<?php

declare(strict_types=1);

/**
 * Pages « L'Essentiel » : résumés joueur·euse et aide MJ.
 *
 * Shortcodes {@code [[kref:…]]} convertis en spans `.kref` par {@see PageSeeder} via {@see KrefShortcodeReplacer}.
 *
 * @return array<string, array{
 *   title: string,
 *   slug: string,
 *   icon: string|null,
 *   menu_order: int,
 *   intro_title: string,
 *   intro_html: string,
 *   sections: list<array{slug: string, title: string, html: string}>,
 *   include_reference_table?: bool
 * }>
 */
return [
    'bien-demarrer' => [
        'title' => 'Bien démarrer',
        'slug' => 'essentiels-bien-demarrer',
        'icon' => null,
        'menu_order' => 10,
        'intro_title' => 'En bref',
        'intro_html' => '<p>Tu connais le d20 ? Parfait. Krosmoz, c\'est du JdR tactique façon Dofus : tu annonces, tu lances, tu dépenses tes [[kref:characteristic:action_points_creature|PA]] et [[kref:characteristic:movement_points_creature|PM]], et quand ça part en vrille… initiative.</p>'
            .'<p>[[kref:characteristic:action_points_creature|PA]] · [[kref:characteristic:movement_points_creature|PM]] · [[kref:characteristic:range_creature|PO]] · d20 · exploration ↔ combat. Le détail complet : [[kref:page:regles-1-introduction|Règles]].</p>',
        'sections' => [
            [
                'slug' => 'concept',
                'title' => 'De quoi il s\'agit',
                'html' => '<ul>'
                    .'<li><strong>Incertitude</strong> : d20 + mod. + [[kref:characteristic:mastery_bonus_creature|maîtrise]]</li>'
                    .'<li><strong>Tactique</strong> : grille, [[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]]/[[kref:characteristic:range_creature|PO]], [[kref:characteristic:tackle_creature|tacle]] plutôt qu\'attaques d\'opportunité</li>'
                    .'<li><strong>Tables</strong> : classes, sorts, matos → [[kref:page:bibliotheque-breed|Bibliothèques]]</li>'
                    .'</ul>',
            ],
            [
                'slug' => 'jets',
                'title' => 'Jets de dés',
                'html' => '<p><strong>1d20 + mod. + [[kref:characteristic:mastery_bonus_creature|maîtrise]]</strong> · DD courant <strong>15</strong> (5 trivial → 30 quasi impossible) · 1 = échec, 20 = réussite critique · opposé = le plus haut gagne.</p>'
                    .'<p><em>Ex.</em> Escalade DD 15, [[kref:characteristic:strength_creature|Force]] +3, [[kref:characteristic:athletics_creature|Athlétisme]] +2 → 1d20+5.</p>'
                    .'<p>→ [[kref:page:regles-1-2-concepts-de-base|Concepts de base]] · [[kref:page:regles-3-5-competences|Compétences]]</p>',
            ],
            [
                'slug' => 'ressources',
                'title' => 'Tes ressources',
                'html' => '<table>'
                    .'<thead><tr><th></th><th>Base</th><th>Max</th><th>En combat</th><th>Hors combat</th></tr></thead>'
                    .'<tbody>'
                    .'<tr><td>[[kref:characteristic:action_points_creature|PA]]</td><td>6</td><td>12</td><td>Full chaque tour</td><td>Via [[kref:characteristic:wakfu_reserve_creature|Wakfu]]</td></tr>'
                    .'<tr><td>[[kref:characteristic:movement_points_creature|PM]]</td><td>3</td><td>6</td><td>Full chaque tour</td><td>Libre</td></tr>'
                    .'<tr><td>[[kref:characteristic:range_creature|PO]]</td><td>0</td><td>6</td><td>Portée + PO</td><td>—</td></tr>'
                    .'<tr><td>[[kref:characteristic:wakfu_reserve_creature|Wakfu]]</td><td>[[kref:characteristic:mastery_bonus_creature|Maîtrise]]+</td><td>—</td><td>Non consommé</td><td>1 pt = 1× [[kref:characteristic:action_points_creature|PA]] max</td></tr>'
                    .'</tbody></table>'
                    .'<p>Initiative : 1d20 + [[kref:characteristic:intelligence_creature|Intelligence]] · [[kref:characteristic:armor_class_creature|CA]] = 10 + [[kref:characteristic:vitality_creature|Vitalité]] + bouclier · Maîtrise = 1 + ⌊Niv./4⌋.</p>',
            ],
            [
                'slug' => 'boucle',
                'title' => 'Une session type',
                'html' => '<p>Exploration → test si doute → combat ([[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]], [[kref:characteristic:tackle_creature|tacle]]) → loot &amp; repos. Temps : round 6 s · minutes en donjon · heures en ville · jours en voyage.</p>',
            ],
        ],
    ],
    'creation' => [
        'title' => 'Créer son personnage (rapide)',
        'slug' => 'essentiels-creation-personnage',
        'icon' => null,
        'menu_order' => 20,
        'intro_title' => 'Ordre recommandé',
        'intro_html' => '<p>Classe → Spé → Caracs → Perso → Matos. Choisis un rôle clair (burst, soin, contrôle…) et monte 2–3 stats, pas six demi-mesures.</p>'
            .'<p>→ [[kref:page:regles-2-1-introduction-a-la-creation|Création complète]]</p>',
        'sections' => [
            [
                'slug' => 'etapes',
                'title' => 'Les cinq étapes',
                'html' => '<ol>'
                    .'<li><strong>Classe</strong> — [[kref:page:bibliotheque-breed|Bibliothèque]] : 3 sorts parmi 6, dé de vie</li>'
                    .'<li><strong>Spé</strong> — [[kref:page:bibliotheque-specialization|spécialisations]] : maîtrises, aptitudes, capacités</li>'
                    .'<li><strong>Caracs</strong> — 6×8, <strong>10 pts</strong> (option : 1 score à 6 → +2 pts)</li>'
                    .'<li><strong>Perso</strong> — alignement, historique, quête</li>'
                    .'<li><strong>Matos</strong> — équipement de classe + kamas</li>'
                    .'</ol>',
            ],
            [
                'slug' => 'caracs',
                'title' => 'Caractéristiques',
                'html' => '<p>Mod. = ⌊(score − 10) / 2⌋ · Six stats : [[kref:characteristic:vitality_creature|Vita]], [[kref:characteristic:strength_creature|For]], [[kref:characteristic:agility_creature|Agi]], [[kref:characteristic:intelligence_creature|Int]], [[kref:characteristic:wisdom_creature|Sag]], [[kref:characteristic:chance_creature|Cha]].</p>'
                    .'<p>Au niv. 1 : [[kref:characteristic:action_points_creature|PA]] 6, [[kref:characteristic:movement_points_creature|PM]] 3, [[kref:characteristic:range_creature|PO]] 0. Plafond mod. base ⌊Niv./2⌋+1. +1 carac. aux niveaux pairs.</p>'
                    .'<p>Bornes : [[kref:page:caracteristiques|Caractéristiques]] · [[kref:page:regles-2-2-les-caracteristiques|Règles caracs]]</p>',
            ],
            [
                'slug' => 'classe-spe',
                'title' => 'Classe &amp; spé',
                'html' => '<p>La <strong>classe</strong> te donne identité, sorts auto, passifs — [[kref:page:regles-2-3-choisir-sa-classe|Choisir sa classe]].</p>'
                    .'<p>La <strong>spé</strong> précise ton rôle (remplace race/historique D&amp;D) et file des maîtrises + [[kref:page:bibliotheque-capability|capacités]] — [[kref:page:regles-2-4-choisir-sa-specialisation|Choisir sa spé]].</p>',
            ],
            [
                'slug' => 'equipement',
                'title' => 'Matos de départ',
                'html' => '<p>Prends ce qui colle à ta classe. Les kamas du livre sont <strong>indicatifs</strong> — ton MJ tranche. Panoplies : bonus si plusieurs pièces compatibles → [[kref:page:bibliotheque-panoply|Panoplies]].</p>'
                    .'<p>→ [[kref:page:regles-2-6-sequiper|S\'équiper]]</p>',
            ],
        ],
    ],
    'actions-hors-combat' => [
        'title' => 'Actions en jeu (hors combat)',
        'slug' => 'essentiels-actions-hors-combat',
        'icon' => null,
        'menu_order' => 30,
        'intro_title' => 'Hors combat',
        'intro_html' => '<p>Annonce ce que tu fais et combien de temps ça prend. Pas besoin de lancer des dés pour ouvrir une porte déverrouillée.</p>'
            .'<p>→ [[kref:page:regles-3-1-partir-a-laventure|Partir à l\'aventure]]</p>',
        'sections' => [
            [
                'slug' => 'exploration',
                'title' => 'Explorer &amp; interagir',
                'html' => '<ul>'
                    .'<li><strong>Observer</strong> — [[kref:characteristic:perception_creature|Perception]] (passive ou active)</li>'
                    .'<li><strong>Marcher</strong> — rythme rapide / normal / lent → [[kref:pageSection:essentiels-actions-hors-combat@essentiels-actions-hors-combat-temps|Temps &amp; repos]]</li>'
                    .'<li><strong>Fouiller, crocheter, parler</strong> — le MJ fixe compétence + DD</li>'
                    .'<li><strong>Sorts</strong> — ça puise dans ta [[kref:characteristic:wakfu_reserve_creature|réserve de Wakfu]]</li>'
                    .'</ul>',
            ],
            [
                'slug' => 'competences',
                'title' => 'Tests de compétences',
                'html' => '<p><strong>1d20 + mod. + [[kref:characteristic:mastery_bonus_creature|maîtrise]]</strong> · DD base 15 · avantage/désavantage = relance le d20.</p>'
                    .'<p>Maîtrise 1+⌊Niv./4⌋ · expertise = double (max 3 au niv. 20).</p>'
                    .'<p>→ [[kref:page:regles-3-5-competences|Compétences]]</p>',
            ],
            [
                'slug' => 'reactions',
                'title' => 'Surprise &amp; social',
                'html' => '<ul>'
                    .'<li><strong>Surprise</strong> — [[kref:characteristic:stealth_creature|Discrétion]] vs [[kref:characteristic:perception_creature|Perception]] : le surpris ne joue pas au 1er tour</li>'
                    .'<li><strong>Social</strong> — [[kref:characteristic:persuasion_creature|Persuasion]] vs [[kref:characteristic:wisdom_creature|Sagesse]] si ça coince</li>'
                    .'</ul>',
            ],
            [
                'slug' => 'temps',
                'title' => 'Temps &amp; repos',
                'html' => '<p>Marche : 45 / 36 / 27 km/j (rapide / normal / lent). Repos court 1 h (2×/j) · repos long 8 h (1×/j) → [[kref:characteristic:life_points_creature|PV]] + [[kref:characteristic:wakfu_reserve_creature|Wakfu]] au complet.</p>',
            ],
        ],
    ],
    'combat' => [
        'title' => 'Combat (résumé pratique)',
        'slug' => 'essentiels-combat',
        'icon' => null,
        'menu_order' => 40,
        'intro_title' => 'Combat',
        'intro_html' => '<p>Initiative, [[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]], [[kref:characteristic:tackle_creature|tacle]], états. Annonce tes actions dans l\'ordre — et pense aux effets qui traînent d\'un tour à l\'autre.</p>'
            .'<p>→ [[kref:page:regles-3-2-combat|Combat complet]]</p>',
        'sections' => [
            [
                'slug' => 'mise-en-place',
                'title' => 'Lancer le combat',
                'html' => '<ol>'
                    .'<li>Initiative : 1d20 + [[kref:characteristic:intelligence_creature|Intelligence]] (1 jet par groupe de mobs identiques)</li>'
                    .'<li>Surprise ? → [[kref:pageSection:essentiels-actions-hors-combat@essentiels-actions-hors-combat-reactions|Surprise]]</li>'
                    .'<li>Place les tokens, rappelle les [[kref:page:conditions|états]] déjà actifs</li>'
                    .'</ol>',
            ],
            [
                'slug' => 'tour',
                'title' => 'Un tour de combat',
                'html' => '<p><strong>Début</strong> : [[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]] au max, effets persistants, sauvegardes.</p>'
                    .'<table>'
                    .'<thead><tr><th>Action</th><th>Coût</th></tr></thead>'
                    .'<tbody>'
                    .'<tr><td>Attaque / sort standard</td><td>3–4 [[kref:characteristic:action_points_creature|PA]]</td></tr>'
                    .'<tr><td>Gros effet / zone</td><td>5 [[kref:characteristic:action_points_creature|PA]]</td></tr>'
                    .'<tr><td>Bonus</td><td>2–3 [[kref:characteristic:action_points_creature|PA]] (1×/round)</td></tr>'
                    .'<tr><td>Case</td><td>1 [[kref:characteristic:movement_points_creature|PM]]</td></tr>'
                    .'<tr><td>Esquiver</td><td>3 [[kref:characteristic:action_points_creature|PA]] + 2 [[kref:characteristic:movement_points_creature|PM]]</td></tr>'
                    .'</tbody></table>'
                    .'<p><strong>Fin</strong> : effets de fin, tour suivant.</p>',
            ],
            [
                'slug' => 'reactions',
                'title' => 'Tacle &amp; fuite',
                'html' => '<p>Pas d\'attaques d\'opportunité — le <strong>[[kref:characteristic:tackle_creature|tacle]]</strong> est automatique au corps-à-corps (pas en diagonale). Pour fuir : jet Fuite vs [[kref:characteristic:tackle_creature|Tacle]] adverse. Réactions spéciales : 2–3 [[kref:characteristic:action_points_creature|PA]], 1×/round.</p>',
            ],
            [
                'slug' => 'sante-etats',
                'title' => 'PV &amp; états',
                'html' => '<p>Dégâts : boucliers → [[kref:characteristic:life_points_creature|PV]] temp → [[kref:characteristic:life_points_creature|PV]]. À 0 [[kref:characteristic:life_points_creature|PV]] : jets contre la mort (10+, 3 succès/échecs).</p>'
                    .'<p>États : [[kref:page:conditions|Liste]] · [[kref:page:bibliotheque-condition|Bibliothèque]] · détail → [[kref:pageSection:essentiels-sante-etats@essentiels-sante-etats-pv-absorption|Absorption]] · [[kref:pageSection:essentiels-sante-etats@essentiels-sante-etats-etats|États]]</p>',
            ],
        ],
    ],
    'sante-etats' => [
        'title' => 'Santé, états et repos',
        'slug' => 'essentiels-sante-etats',
        'icon' => null,
        'menu_order' => 45,
        'intro_title' => 'Survie',
        'intro_html' => '<p>Boucliers, puis [[kref:characteristic:life_points_creature|PV]] temp, puis [[kref:characteristic:life_points_creature|PV]]. Note tes états sur la fiche — tu les oublieras sinon.</p>',
        'sections' => [
            [
                'slug' => 'pv',
                'title' => 'PV &amp; boucliers',
                'html' => '<p>Ordre : <strong>boucliers</strong> → <strong>[[kref:characteristic:life_points_creature|PV]] temp</strong> (non cumulables, non soignables) → <strong>[[kref:characteristic:life_points_creature|PV]]</strong>.</p>'
                    .'<p>Max [[kref:characteristic:life_points_creature|PV]] = classe + niv. + [[kref:characteristic:vitality_creature|Vitalité]] + équip. Soins plafonnés au max (sauf temp).</p>',
            ],
            [
                'slug' => 'zero-pv',
                'title' => 'À 0 PV',
                'html' => '<p>Mort instantanée si dégâts restants ≥ max [[kref:characteristic:life_points_creature|PV]]. Sinon inconscience + jets contre la mort (d20, 10+) : 3 succès = stable, 3 échecs = mort. 1 = 2 échecs, 20 = +1 [[kref:characteristic:life_points_creature|PV]]. Stabiliser : [[kref:characteristic:medicine_creature|Médecine]] DD 10.</p>',
            ],
            [
                'slug' => 'etats',
                'title' => 'États',
                'html' => '<p>Buffs, malédictions, DOT — dissipation par sauvegarde, durée ou désenvoutement. Fiches : [[kref:page:conditions|États]] · [[kref:page:bibliotheque-condition|Bibliothèque]]. Certains ne se cumulent pas.</p>',
            ],
            [
                'slug' => 'repos',
                'title' => 'Repos',
                'html' => '<p>Court 1 h (2×/j, dés de vie) · Long 8 h (1×/j, [[kref:characteristic:life_points_creature|PV]] + [[kref:characteristic:wakfu_reserve_creature|Wakfu]] full). Soins : [[kref:page:bibliotheque-spell|sorts]], [[kref:page:bibliotheque-consumable|consommables]], [[kref:characteristic:medicine_creature|Médecine]].</p>',
            ],
        ],
    ],
    'sorts-aptitudes' => [
        'title' => 'Sorts, aptitudes, capacités',
        'slug' => 'essentiels-sorts-aptitudes',
        'icon' => null,
        'menu_order' => 50,
        'intro_title' => 'Pouvoirs',
        'intro_html' => '<p>Avant de lancer : as-tu les [[kref:characteristic:action_points_creature|PA]], la portée, la ligne de vue ? Touche ou sauvegarde ?</p>'
            .'<p>→ [[kref:page:regles-3-3-sorts|Sorts]] · [[kref:page:regles-3-4-aptitudes-et-capacites|Aptitudes]]</p>',
        'sections' => [
            [
                'slug' => 'typologie',
                'title' => 'Ce que tu peux lancer',
                'html' => '<ul>'
                    .'<li><strong>Sorts de classe</strong> — 3 au départ, déblocages aux niv. 3–14 → [[kref:page:bibliotheque-spell|Bibliothèque]]</li>'
                    .'<li><strong>Appris</strong> — parchemin ou maître</li>'
                    .'<li><strong>Aptitudes / capacités</strong> — passif vs actif → [[kref:page:bibliotheque-capability|Capacités]]</li>'
                    .'</ul>'
                    .'<p>En début de campagne : 2–3 sorts signature + ta spé, le reste viendra.</p>',
            ],
            [
                'slug' => 'lancement',
                'title' => 'Lancer un sort',
                'html' => '<p><strong>Physique</strong> : 1d20 + mod vs [[kref:characteristic:armor_class_creature|CA]] · <strong>Magique</strong> : sauvegarde [[kref:characteristic:wisdom_creature|Sagesse]] vs DD 8 + mod + [[kref:characteristic:mastery_bonus_creature|maîtrise]].</p>'
                    .'<p>Vérifie : coût [[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]], portée + [[kref:characteristic:range_creature|PO]], cible, [[kref:characteristic:tackle_creature|tacle]] adverse.</p>',
            ],
            [
                'slug' => 'wakfu',
                'title' => 'Wakfu hors combat',
                'html' => '<p>1 pt de [[kref:characteristic:wakfu_reserve_creature|réserve]] = une fois tes [[kref:characteristic:action_points_creature|PA]] max hors combat. En combat « sérieux », les [[kref:characteristic:action_points_creature|PA]] reviennent chaque tour sans toucher à la réserve. Récup : repos long.</p>'
                    .'<p><em>Ex.</em> 4 pts de réserve, 6 [[kref:characteristic:action_points_creature|PA]] max → 24 [[kref:characteristic:action_points_creature|PA]] dispos hors combat.</p>',
            ],
        ],
    ],
    'economie-progression' => [
        'title' => 'Économie, équipement, progression',
        'slug' => 'essentiels-economie-progression',
        'icon' => null,
        'menu_order' => 60,
        'intro_title' => 'Progression',
        'intro_html' => '<p>Un build cohérent bat trois bonus isolés. Vérifie les plafonds sur [[kref:page:caracteristiques|Caractéristiques]] avant d\'acheter ou de forger.</p>',
        'sections' => [
            [
                'slug' => 'loot',
                'title' => 'Loot &amp; rareté',
                'html' => '<p>Bonus attendus : niv. 1–5 (+1–2) · 6–10 (+2–3) · 11–15 (+3–4) · 16–20 (+4–5). Un [[kref:page:bibliotheque-item|objet]] hors charte casse la campagne.</p>'
                    .'<p>→ [[kref:page:regles-5-2-principes-dequilibrage|Équilibrage]] · [[kref:page:regles-5-3-tables-de-reference|Tables]]</p>',
            ],
            [
                'slug' => 'equip',
                'title' => 'Équipement &amp; panoplies',
                'html' => '<p>8 emplacements (arme, chapeau, cape, amulette, anneau, ceinture, bottes, bouclier/Dofus). Panoplie = bonus si plusieurs pièces → [[kref:page:bibliotheque-panoply|Panoplies]].</p>'
                    .'<p>→ [[kref:page:regles-2-6-sequiper|S\'équiper]]</p>',
            ],
            [
                'slug' => 'forgemagie',
                'title' => 'Forgemagie',
                'html' => '<p>Runes sur ton [[kref:page:bibliotheque-item|équipement]] ([[kref:characteristic:action_points_creature|PA]] amulette, [[kref:characteristic:movement_points_creature|PM]] bottes…). Plafonds <code>max</code> + <code>forgemagie_max</code> séparés. Échec = perte · casse = adieu l\'objet.</p>'
                    .'<p>→ [[kref:page:regles-4-3-les-metiers|Métiers]]</p>',
            ],
            [
                'slug' => 'achats',
                'title' => 'Achats',
                'html' => '<p>Prix indicatifs. Priorise [[kref:characteristic:action_points_creature|PA]], [[kref:characteristic:movement_points_creature|PM]] et tes stats clés (amulette, bottes…) avant le bling.</p>',
            ],
        ],
    ],
    'caracteristiques' => [
        'title' => 'Caractéristiques',
        'slug' => 'caracteristiques',
        'icon' => null,
        'menu_order' => 70,
        'intro_title' => 'Référence rapide',
        'intro_html' => '<p>Bornes min/max, formules, équipement et forgemagie — pour valider un PJ, un [[kref:page:bibliotheque-item|objet]] ou un sort avant de dire « oui ».</p>',
        'sections' => [],
        'include_reference_table' => true,
    ],
];
