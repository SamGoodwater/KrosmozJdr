<?php

declare(strict_types=1);

/**
 * Pages « L'Essentiel » : aide-mémoire après une première lecture des règles.
 *
 * Aligné sur `private/game/rules` (canons sept. 2026). Court, chapitré, avec
 * lien vers le livre pour le détail. Shortcodes {@code [[kref:…]]} convertis
 * par {@see PageSeeder} via {@see KrefShortcodeReplacer}.
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
        'intro_title' => 'À quoi ça sert',
        'intro_html' => '<p>Tu as lu les règles une fois. Ici tu retrouves les chiffres et les pièges à table, sans rouvrir tout le livre. Chaque page pointe vers le chapitre complet.</p>'
            .'<p>d20 · [[kref:characteristic:action_points_creature|PA]] · [[kref:characteristic:movement_points_creature|PM]] · [[kref:characteristic:range_creature|PO]] · round 6 s. Livre : [[kref:page:regles-1-introduction|Règles]].</p>',
        'sections' => [
            [
                'slug' => 'plan',
                'title' => 'Les chapitres',
                'html' => '<ol>'
                    .'<li>[[kref:page:essentiels-creation-personnage|Créer son personnage]] — budget, classe, spé</li>'
                    .'<li>[[kref:page:essentiels-actions-hors-combat|Hors combat]] — tests, surprise, temps</li>'
                    .'<li>[[kref:page:essentiels-combat|Combat]] — tour, tacle, réaction</li>'
                    .'<li>[[kref:page:essentiels-sante-etats|Santé, états, repos]] — PV, 0 PV, repos</li>'
                    .'<li>[[kref:page:essentiels-sorts-aptitudes|Sorts, aptitudes, capacités]] — lancer + rappels de fiche</li>'
                    .'<li>[[kref:page:essentiels-economie-progression|Équipement et progression]] — loot, conso, monture</li>'
                    .'<li>[[kref:page:caracteristiques|Caractéristiques]] — bornes et formules</li>'
                    .'</ol>'
                    .'<p>Tables de jeu : [[kref:page:bibliotheque-breed|Bibliothèques]].</p>',
            ],
            [
                'slug' => 'jets',
                'title' => 'Jets',
                'html' => '<p><strong>1d20 + mod. + [[kref:characteristic:mastery_bonus_creature|maîtrise]]</strong> (si maîtrisée). DD courant <strong>15</strong> (5 trivial → 30 quasi impossible). 1 = échec auto, 20 = réussite auto. Opposé : le plus haut gagne. Avantage / désavantage : relance le d20.</p>'
                    .'<p>Maîtrise = <strong>1 + ⌊niveau/4⌋</strong> (+1 → +6). Expertise = double, max 3 (niv. 9 / 15 / 20).</p>'
                    .'<p>→ [[kref:page:regles-1-2-concepts-de-base|Concepts]] · [[kref:page:regles-3-5-competences|Compétences]]</p>',
            ],
            [
                'slug' => 'ressources',
                'title' => 'Ressources',
                'html' => '<table>'
                    .'<thead><tr><th></th><th>Base</th><th>Max</th><th>Combat</th><th>Hors combat</th></tr></thead>'
                    .'<tbody>'
                    .'<tr><td>[[kref:characteristic:action_points_creature|PA]]</td><td>6</td><td>12</td><td>Full chaque tour</td><td>Via [[kref:characteristic:wakfu_reserve_creature|Wakfu]]</td></tr>'
                    .'<tr><td>[[kref:characteristic:movement_points_creature|PM]]</td><td>3</td><td>6</td><td>Full chaque tour</td><td>Libre</td></tr>'
                    .'<tr><td>[[kref:characteristic:range_creature|PO]]</td><td>0</td><td>6</td><td>Portée + PO</td><td>—</td></tr>'
                    .'<tr><td>[[kref:characteristic:wakfu_reserve_creature|Wakfu]]</td><td>[[kref:characteristic:mastery_bonus_creature|Maîtrise]]+</td><td>—</td><td>Non consommé</td><td>1 pt = 1× tes [[kref:characteristic:action_points_creature|PA]] max</td></tr>'
                    .'</tbody></table>'
                    .'<p>Initiative : 1d20 + [[kref:characteristic:intelligence_creature|Intelligence]]. [[kref:characteristic:armor_class_creature|CA]] = 10 + [[kref:characteristic:vitality_creature|Vitalité]] + équipement.</p>',
            ],
            [
                'slug' => 'boucle',
                'title' => 'Temps',
                'html' => '<p>Round / tour = <strong>6 s</strong>. Donjon : minutes. Ville / nature : heures. Voyage : jours. Repos → [[kref:page:essentiels-sante-etats|Santé]].</p>',
            ],
        ],
    ],
    'creation' => [
        'title' => 'Créer son personnage',
        'slug' => 'essentiels-creation-personnage',
        'icon' => null,
        'menu_order' => 20,
        'intro_title' => 'Ordre',
        'intro_html' => '<p>Classe → spé → caracs → perso → matos. Monte 2–3 stats, pas six demi-mesures.</p>'
            .'<p>→ [[kref:page:regles-2-1-introduction-a-la-creation|Création complète]]</p>',
        'sections' => [
            [
                'slug' => 'etapes',
                'title' => 'Les cinq étapes',
                'html' => '<ol>'
                    .'<li><strong>Classe</strong> — 3 sorts parmi 6, dé de vie → [[kref:page:bibliotheque-breed|Classes]]</li>'
                    .'<li><strong>Spé</strong> — maîtrises, aptitudes, capacités → [[kref:page:bibliotheque-specialization|Spés]]</li>'
                    .'<li><strong>Caracs</strong> — 6×8, <strong>10 pts</strong> (option : 1 score à 6 → +2 pts)</li>'
                    .'<li><strong>Perso</strong> — alignement, historique, quête</li>'
                    .'<li><strong>Matos</strong> — équipement de classe + kamas (indicatifs)</li>'
                    .'</ol>',
            ],
            [
                'slug' => 'caracs',
                'title' => 'Caractéristiques',
                'html' => '<p>Mod. = ⌊(score − 10) / 2⌋. Six stats : [[kref:characteristic:vitality_creature|Vita]], [[kref:characteristic:strength_creature|For]], [[kref:characteristic:agility_creature|Agi]], [[kref:characteristic:intelligence_creature|Int]], [[kref:characteristic:wisdom_creature|Sag]], [[kref:characteristic:chance_creature|Cha]].</p>'
                    .'<p>Niv. 1 : [[kref:characteristic:action_points_creature|PA]] 6, [[kref:characteristic:movement_points_creature|PM]] 3, [[kref:characteristic:range_creature|PO]] 0. +1 carac. aux <strong>niveaux pairs</strong> (10 pts jusqu’au 20). Plafond du mod. de base : <strong>min(⌊niv./2⌋+1, 7)</strong>.</p>'
                    .'<p>→ [[kref:page:caracteristiques|Tableau]] · [[kref:page:regles-2-2-les-caracteristiques|Règles caracs]]</p>',
            ],
            [
                'slug' => 'classe-spe',
                'title' => 'Classe et spé',
                'html' => '<p><strong>Classe</strong> : identité, sorts auto, passifs — [[kref:page:regles-2-3-choisir-sa-classe|Choisir sa classe]].</p>'
                    .'<p><strong>Spé</strong> : rôle, maîtrises, aptitudes / capacités (niv. 3, 6, 9… : l’un ou l’autre) — [[kref:page:regles-2-4-choisir-sa-specialisation|Choisir sa spé]].</p>',
            ],
            [
                'slug' => 'equipement',
                'title' => 'Matos de départ',
                'html' => '<p>Prends ce qui colle à ta classe. Panoplie = bonus si plusieurs pièces — [[kref:page:bibliotheque-panoply|Panoplies]].</p>'
                    .'<p>→ [[kref:page:regles-2-6-sequiper|S\'équiper]]</p>',
            ],
        ],
    ],
    'actions-hors-combat' => [
        'title' => 'Hors combat',
        'slug' => 'essentiels-actions-hors-combat',
        'icon' => null,
        'menu_order' => 30,
        'intro_title' => 'En bref',
        'intro_html' => '<p>Annonce ce que tu fais et combien de temps ça prend. Pas de dé pour une porte ouverte.</p>'
            .'<p>→ [[kref:page:regles-3-1-partir-a-laventure|Partir à l\'aventure]]</p>',
        'sections' => [
            [
                'slug' => 'exploration',
                'title' => 'Explorer',
                'html' => '<ul>'
                    .'<li><strong>Observer</strong> — [[kref:characteristic:perception_creature|Perception]] (passive = 10 + Sag + maîtrise si maîtrisée)</li>'
                    .'<li><strong>Marcher</strong> — 45 / 36 / 27 km/j (rapide / normal / lent)</li>'
                    .'<li><strong>Fouiller, crocheter, parler</strong> — le MJ fixe compétence + DD</li>'
                    .'<li><strong>Sorts / aptitudes</strong> — ça puise dans la [[kref:characteristic:wakfu_reserve_creature|réserve de Wakfu]]</li>'
                    .'</ul>',
            ],
            [
                'slug' => 'competences',
                'title' => 'Tests',
                'html' => '<p>Même formule que [[kref:pageSection:essentiels-bien-demarrer@essentiels-bien-demarrer-jets|Jets]]. DD 15 sauf si le MJ dit autrement.</p>'
                    .'<p>→ [[kref:page:regles-3-5-competences|Compétences]]</p>',
            ],
            [
                'slug' => 'reactions',
                'title' => 'Surprise et social',
                'html' => '<ul>'
                    .'<li><strong>Surprise</strong> — [[kref:characteristic:agility_creature|Agilité]] ([[kref:characteristic:stealth_creature|Discrétion]]) vs [[kref:characteristic:wisdom_creature|Sagesse]] ([[kref:characteristic:perception_creature|Perception]]) : le surpris saute le 1er tour</li>'
                    .'<li><strong>Social</strong> — [[kref:characteristic:persuasion_creature|Persuasion]] vs [[kref:characteristic:wisdom_creature|Sagesse]] si ça coince</li>'
                    .'</ul>'
                    .'<p>→ [[kref:page:regles-3-1-partir-a-laventure|Situations tendues]]</p>',
            ],
            [
                'slug' => 'temps',
                'title' => 'Temps',
                'html' => '<p>Échelles : minutes (donjon), heures (ville), jours (voyage), rounds (6 s). Repos → [[kref:pageSection:essentiels-sante-etats@essentiels-sante-etats-repos|Santé — Repos]].</p>',
            ],
        ],
    ],
    'combat' => [
        'title' => 'Combat',
        'slug' => 'essentiels-combat',
        'icon' => null,
        'menu_order' => 40,
        'intro_title' => 'En bref',
        'intro_html' => '<p>Initiative, budget [[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]], [[kref:characteristic:tackle_creature|tacle]], 1 réaction / round.</p>'
            .'<p>→ [[kref:page:regles-3-2-combat|Combat complet]]</p>',
        'sections' => [
            [
                'slug' => 'mise-en-place',
                'title' => 'Lancer le combat',
                'html' => '<ol>'
                    .'<li>Initiative : 1d20 + [[kref:characteristic:intelligence_creature|Intelligence]] (1 jet par groupe de mobs identiques)</li>'
                    .'<li>Surprise ? → [[kref:pageSection:essentiels-actions-hors-combat@essentiels-actions-hors-combat-reactions|Surprise]]</li>'
                    .'<li>Place, rappelle les [[kref:page:conditions|états]] déjà actifs</li>'
                    .'</ol>',
            ],
            [
                'slug' => 'tour',
                'title' => 'Un tour',
                'html' => '<p><strong>Début</strong> : [[kref:characteristic:action_points_creature|PA]]/[[kref:characteristic:movement_points_creature|PM]] au max, effets persistants.</p>'
                    .'<table>'
                    .'<thead><tr><th>Action</th><th>Coût</th></tr></thead>'
                    .'<tbody>'
                    .'<tr><td>Attaque / sort standard</td><td>3–4 [[kref:characteristic:action_points_creature|PA]]</td></tr>'
                    .'<tr><td>Gros effet / zone</td><td>5 [[kref:characteristic:action_points_creature|PA]]</td></tr>'
                    .'<tr><td>Bonus</td><td>2–3 [[kref:characteristic:action_points_creature|PA]] (1×/round)</td></tr>'
                    .'<tr><td>Réaction (hors de ton tour)</td><td>2–3 [[kref:characteristic:action_points_creature|PA]] (1×/round)</td></tr>'
                    .'<tr><td>Utiliser un objet / potion</td><td>1 [[kref:characteristic:action_points_creature|PA]]</td></tr>'
                    .'<tr><td>Case</td><td>1 [[kref:characteristic:movement_points_creature|PM]]</td></tr>'
                    .'<tr><td>Esquiver</td><td>3 [[kref:characteristic:action_points_creature|PA]] + 2 [[kref:characteristic:movement_points_creature|PM]]</td></tr>'
                    .'</tbody></table>'
                    .'<p><strong>Fin</strong> : effets de fin de tour, tour suivant.</p>',
            ],
            [
                'slug' => 'reactions',
                'title' => 'Tacle, fuite, réaction',
                'html' => '<p>Pas d’attaque d’opportunité D&amp;D. [[kref:characteristic:tackle_creature|Tacle]] auto au corps-à-corps (pas en diagonale). Fuite : jet Fuite vs [[kref:characteristic:tackle_creature|Tacle]] ; échec = tu restes collé, retente au tour suivant.</p>'
                    .'<p>Réaction de sort / capacité : 1×/round, hors de ton tour, si la fiche le permet.</p>'
                    .'<p>→ [[kref:page:regles-3-2-combat|3.2.3]]</p>',
            ],
            [
                'slug' => 'sante-etats',
                'title' => 'PV',
                'html' => '<p>Dégâts : boucliers → [[kref:characteristic:life_points_creature|PV]] temp → [[kref:characteristic:life_points_creature|PV]]. Le reste : [[kref:page:essentiels-sante-etats|Santé, états, repos]].</p>',
            ],
        ],
    ],
    'sante-etats' => [
        'title' => 'Santé, états, repos',
        'slug' => 'essentiels-sante-etats',
        'icon' => null,
        'menu_order' => 45,
        'intro_title' => 'Survie',
        'intro_html' => '<p>Boucliers, puis [[kref:characteristic:life_points_creature|PV]] temp, puis [[kref:characteristic:life_points_creature|PV]]. Note tes états sur la fiche.</p>'
            .'<p>→ [[kref:page:regles-3-2-combat|Gérer la santé]]</p>',
        'sections' => [
            [
                'slug' => 'pv',
                'title' => 'Absorption',
                'html' => '<p>Ordre : <strong>boucliers</strong> (cumulables, pas à 0 PV, dissipables) → <strong>[[kref:characteristic:life_points_creature|PV]] temp</strong> (pas de cumul, pas de dissipation, ne réveillent pas) → <strong>[[kref:characteristic:life_points_creature|PV]]</strong>.</p>'
                    .'<p>Vol de vie : pas sur les boucliers. Max [[kref:characteristic:life_points_creature|PV]] : classe + niveau + [[kref:characteristic:vitality_creature|Vitalité]] + équipement.</p>',
            ],
            [
                'slug' => 'zero-pv',
                'title' => 'À 0 PV',
                'html' => '<p>Mort instantanée si le surplus ≥ max [[kref:characteristic:life_points_creature|PV]]. Sinon inconscience + jets contre la mort (d20, 10+) : 3 succès = stable, 3 échecs = mort. 1 = 2 échecs, 20 = +1 [[kref:characteristic:life_points_creature|PV]]. Stabiliser : [[kref:characteristic:medicine_creature|Médecine]] DD 10. Les sbires peuvent mourir à 0 sans jets.</p>',
            ],
            [
                'slug' => 'etats',
                'title' => 'Traits et états',
                'html' => '<p><strong>Traits</strong> : permanents, non dissipables. <strong>États</strong> : temporaires, souvent dissipables. Glyphe : si tu dissipes alors que tu restes dessus, l’effet revient.</p>'
                    .'<p>→ [[kref:page:conditions|États]] · [[kref:page:bibliotheque-condition|Bibliothèque]]</p>',
            ],
            [
                'slug' => 'repos',
                'title' => 'Repos',
                'html' => '<p><strong>Court</strong> : 1 h, dés de vie, <strong>un seul entre deux longs</strong>. <strong>Long</strong> : 8 h, 1×/jour, [[kref:characteristic:life_points_creature|PV]] + [[kref:characteristic:wakfu_reserve_creature|Wakfu]] au complet (récupère la moitié des dés de vie, arrondi inf.).</p>'
                    .'<p>→ [[kref:page:regles-3-1-partir-a-laventure|Gestion du temps]]</p>',
            ],
        ],
    ],
    'sorts-aptitudes' => [
        'title' => 'Sorts, aptitudes, capacités',
        'slug' => 'essentiels-sorts-aptitudes',
        'icon' => null,
        'menu_order' => 50,
        'intro_title' => 'Pouvoirs',
        'intro_html' => '<p>Avant de lancer : [[kref:characteristic:action_points_creature|PA]], portée, ligne de vue, touche ou sauvegarde ?</p>'
            .'<p>→ [[kref:page:regles-3-3-sorts|Sorts]] · [[kref:page:regles-3-4-aptitudes-et-capacites|Aptitudes]]</p>',
        'sections' => [
            [
                'slug' => 'typologie',
                'title' => 'Ce que tu as',
                'html' => '<ul>'
                    .'<li><strong>Sorts de classe</strong> — 3 au départ, déblocages niv. 3–14</li>'
                    .'<li><strong>Appris</strong> — parchemin ou maître (2 j × niveau, ÷4 avec maître)</li>'
                    .'<li><strong>Parchemin jetable</strong> — une fois ; détruit si le sort réussit</li>'
                    .'<li><strong>Aptitudes</strong> — surtout hors combat (Wakfu) ; parfois en combat ([[kref:characteristic:action_points_creature|PA]])</li>'
                    .'<li><strong>Capacités</strong> — souvent passives / déclenchées</li>'
                    .'</ul>'
                    .'<p>→ [[kref:page:bibliotheque-spell|Sorts]] · [[kref:page:bibliotheque-capability|Capacités]]</p>',
            ],
            [
                'slug' => 'lancement',
                'title' => 'Lancer',
                'html' => '<p><strong>Physique</strong> : 1d20 + mod vs [[kref:characteristic:armor_class_creature|CA]]. <strong>Magique</strong> : sauvegarde (souvent [[kref:characteristic:wisdom_creature|Sagesse]]) vs DD <strong>8 + mod + [[kref:characteristic:mastery_bonus_creature|maîtrise]]</strong>. Retrait [[kref:characteristic:movement_points_creature|PM]]/[[kref:characteristic:action_points_creature|PA]] : vs esquive, ou save Sag en version simple.</p>'
                    .'<p>Cible consentante : réussite auto si la fiche le dit.</p>'
                    .'<p>→ [[kref:page:regles-3-3-sorts|3.3.2]]</p>',
            ],
            [
                'slug' => 'wakfu',
                'title' => 'Wakfu',
                'html' => '<p>Hors combat (ou combat sans enjeu) : 1 pt de réserve = une fois tes [[kref:characteristic:action_points_creature|PA]] max. Combat sérieux : les [[kref:characteristic:action_points_creature|PA]] reviennent chaque tour, réserve intacte. Soin « gratuit en PA » : seulement tant que le combat a un enjeu. Récup : repos long.</p>',
            ],
            [
                'slug' => 'rappels',
                'title' => 'Rappels de fiche',
                'html' => '<ul>'
                    .'<li><strong>Invocation</strong> — contrôlée, joue à ton tour, 1 h puis disparaît (sauf exception). Ne laisse rien.</li>'
                    .'<li><strong>Piège</strong> — caché (Perception), pas sous une créature. <strong>Glyphe</strong> — visible, début de tour si tu es dessus, cumulable. Si le max est atteint, le plus ancien disparaît. Dissipation de glyphe : l’effet revient si tu restes dessus.</li>'
                    .'<li><strong>Bouclier / PV temp / vol de vie / réaction</strong> — [[kref:page:essentiels-sante-etats|Santé]] et [[kref:page:essentiels-combat|Combat]]</li>'
                    .'</ul>',
            ],
        ],
    ],
    'economie-progression' => [
        'title' => 'Équipement et progression',
        'slug' => 'essentiels-economie-progression',
        'icon' => null,
        'menu_order' => 60,
        'intro_title' => 'Progression',
        'intro_html' => '<p>Un build cohérent bat trois bonus isolés. Vérifie les plafonds sur [[kref:page:caracteristiques|Caractéristiques]] avant d’acheter.</p>',
        'sections' => [
            [
                'slug' => 'loot',
                'title' => 'Loot',
                'html' => '<p>Bonus attendus : niv. 1–5 (+1–2) · 6–10 (+2–3) · 11–15 (+3–4) · 16–20 (+4–5).</p>'
                    .'<p>→ [[kref:page:regles-5-2-principes-dequilibrage|Équilibrage]]</p>',
            ],
            [
                'slug' => 'equip',
                'title' => 'Emplacements',
                'html' => '<p>8 slots (arme, chapeau, cape, amulette, anneau, ceinture, bottes, bouclier/Dofus). Panoplie = bonus de set — [[kref:page:bibliotheque-panoply|Panoplies]].</p>'
                    .'<p><strong>Monture</strong> : bonus (ex. [[kref:characteristic:movement_points_creature|PM]]) hors plafond ; perdus si tu descends. Tu peux avoir monture <em>et</em> familier.</p>'
                    .'<p>→ [[kref:page:regles-2-6-sequiper|S\'équiper]]</p>',
            ],
            [
                'slug' => 'consommables',
                'title' => 'Consommables',
                'html' => '<p>En combat : <strong>1 [[kref:characteristic:action_points_creature|PA]]</strong> en général. Même type d’effet : pas de cumul, le meilleur gagne. Parchemin de sortilège : détruit seulement si le sort réussit.</p>'
                    .'<p>→ [[kref:page:regles-4-4-ressources-et-consommables|Consommables]] · [[kref:page:bibliotheque-consumable|Bibliothèque]]</p>',
            ],
            [
                'slug' => 'forgemagie',
                'title' => 'Forgemagie',
                'html' => '<p>Runes sur l’[[kref:page:bibliotheque-item|équipement]]. Plafonds <code>max</code> et <code>forgemagie_max</code> séparés. Échec = perte · casse = adieu l’objet.</p>'
                    .'<p>→ [[kref:page:regles-4-3-les-metiers|Métiers]]</p>',
            ],
        ],
    ],
    'caracteristiques' => [
        'title' => 'Caractéristiques',
        'slug' => 'caracteristiques',
        'icon' => null,
        'menu_order' => 70,
        'intro_title' => 'Référence',
        'intro_html' => '<p>Bornes min/max, formules, équipement et forgemagie — pour valider un PJ, un [[kref:page:bibliotheque-item|objet]] ou un sort.</p>',
        'sections' => [],
        'include_reference_table' => true,
    ],
];
