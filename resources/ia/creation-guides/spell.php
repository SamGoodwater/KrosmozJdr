<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : sorts (classe et créature).
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    $pa = $k['pa'];
    $pm = $k['pm'];
    $po = $k['po'];
    $force = $k['force'];
    $intel = $k['intel'];
    $agi = $k['agi'];
    $chance = $k['chance'];
    $sagesse = $k['sagesse'];

    return [
        'entity' => 'spell',
        'title' => 'Sorts',
        'slug' => 'creation-sorts',
        'icon' => 'fa-solid fa-wand-sparkles',
        'menu_order' => 2,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Sorts</h2>'
                    .'<p>Un sort a <strong>une job</strong> : dégâts, soin, contrôle, placement ou utilité. Le coût en '.$pa.' / '.$pm.' / Wakfu paie la puissance ; la fréquence (cooldown, 1/combat) empêche les combos abusifs. '.$k['kSortsEq'].' · '.$k['kSortsLivre'].'.</p>'
                    .'<p>En <strong>conversion Dofus</strong> : on garde nom, classe, élément, image et fantasy. On réécrit le texte d’effet en <strong>1 effet principal + 0 à 2 secondaires</strong> jouables à table. On n’invente pas de type d’effet hors catalogue. Les états pointent vers les cinq états JDR (Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli) — '.$k['kEtats'].'.</p>'
                    .'<p>Deux familles : <strong>sort de classe</strong> (grimoire d’un PJ / PNJ) et <strong>sort de créature</strong> (action d’un monstre, 1 à 2 par fiche). On ne mélange pas.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Une job.</strong> Un sort qui tape, soigne et stun à la fois casse la table. L’identité de classe (téléport, piège, invocation) n’est pas un 3ᵉ DPS.</li>'
                    .'<li><strong>Élément ↔ caractéristique.</strong> Terre → '.$force.', Feu → '.$intel.', Eau → '.$chance.', Air → '.$agi.'. Un sort Terre sur une créature à Force nulle est une erreur.</li>'
                    .'<li><strong>Budget de tour.</strong> Au niveau 1, vise ~6 '.$pa.' (ex. 3+3). Une action à 5 '.$pa.' est souvent la seule du tour.</li>'
                    .'<li><strong>Au plus 3 effets JDR</strong> (1 + 0–2). Les combos MMO (runes, masques, bombes empilées) se simplifient.</li>'
                    .'<li><strong>Catégorie.</strong> 0 = classe, 1 = créature, 2 = apprenable, 3 = consommable. Un sort-créature ne va pas dans un grimoire de classe.</li>'
                    .'<li><strong>Deux variantes d’un emplacement</strong> restent équivalentes (même coût, même bande de puissance, fantasy différente).</li>'
                    .'<li>Publie seulement en jouable quand le texte se lit à voix haute en 10 secondes.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Dégâts et soins (ligne neutre)</h3>'
                    .'<table><thead><tr><th>Niveau</th><th>Dégâts</th><th>Soins / bouclier</th></tr></thead><tbody>'
                    .'<tr><td>1–5</td><td>1d6+mod à 2d6+mod</td><td>1d4+mod à 2d4+mod</td></tr>'
                    .'<tr><td>6–10</td><td>2d6+mod à 3d6+mod</td><td>2d4+mod à 3d4+mod</td></tr>'
                    .'<tr><td>11–15</td><td>3d6+mod à 4d6+mod</td><td>3d4+mod à 4d4+mod</td></tr>'
                    .'<tr><td>16–20</td><td>4d6+mod à 5d6+mod</td><td>4d4+mod à 5d4+mod</td></tr>'
                    .'</tbody></table>'
                    .'<p>Régulateurs : 5+ '.$pa.' → +1 puissance ; zone large → −1 par cible touchée. Chartes vivantes plus bas.</p>'
                    .'<h3>Coûts</h3>'
                    .'<ul>'
                    .'<li>Action simple : 3–4 '.$pa.' (souvent 2×/tour).</li>'
                    .'<li>Action forte : 5 '.$pa.' (souvent 1×/tour).</li>'
                    .'<li>Bonus / identité : 2–3 '.$pa.' (3 si l’effet compte vraiment).</li>'
                    .'<li>Déplacement : 1 '.$pm.' / case ; téléport / charge = coût dédié, pas gratuit.</li>'
                    .'</ul>'
                    .'<h3>Fréquence et durées</h3>'
                    .'<ul>'
                    .'<li>Cooldown court 1–3 tours, moyen 4–6, long 7–10, très long = repos.</li>'
                    .'<li>Contrôle (stun, immobilisation) : sauvegarde + 1–2 tours, jamais 10 tours sans sortie.</li>'
                    .'<li>Buff : durée courte ; pas de cumul du même bonus.</li>'
                    .'</ul>'
                    .'<h3>Kit de classe (rappel)</h3>'
                    .'<p>24 sorts = 12 emplacements × 2 variantes, 12 appris. Niveau 1 : 3 emplacements (attaque 1d6+mod à 3 '.$pa.', fort 4–5 '.$pa.', identité 2–3 '.$pa.'). Puis 3, 4, 5, 7, 8, 10, 11, 13, 14. Soins en <strong>d4</strong>, dégâts en <strong>d6</strong>. Détail : '.$k['kSortsEq'].'.</p>'
                    .'<h3>Propriétés de fiche (conversion)</h3>'
                    .'<ul>'
                    .'<li><code>name</code>, <code>description</code>, image — identité, figée si source Dofus.</li>'
                    .'<li><code>effect</code> — texte JDR (c’est le delta de conversion).</li>'
                    .'<li><code>level</code> — niveau d’apprentissage (classe) ou de la créature.</li>'
                    .'<li><code>category</code> — 0 classe · 1 créature · 2 apprenable · 3 consommable.</li>'
                    .'<li><code>element</code> — masque (Terre / Feu / Eau / Air / Neutre), aligné sur les sous-effets.</li>'
                    .'<li><code>pa</code>, <code>po_min</code>, <code>po_max</code>, <code>po_editable</code> — coût et portée.</li>'
                    .'<li><code>sight_line</code>, <code>cast_in_line</code>, <code>cast_in_diagonal</code> — géométrie.</li>'
                    .'<li><code>cast_per_turn</code>, <code>cast_per_target</code> — plafonds par tour / par cible.</li>'
                    .'<li><code>target_type</code> — direct, <code>trap</code> ou <code>glyph</code>.</li>'
                    .'<li><code>max_stack</code>, <code>global_cooldown</code>, <code>number_between_two_cast</code>, <code>duration</code>.</li>'
                    .'<li><code>is_magic</code>, <code>powerful</code>, <code>ritual_available</code>, <code>casting_time</code>.</li>'
                    .'<li><code>resolution_mode</code>, <code>attack_characteristic_key</code> (ex. <code>strong</code>, <code>intel</code>).</li>'
                    .'<li><code>save_characteristic_key</code>, <code>save_dc_formula</code>, <code>save_success_note</code>, <code>auto_success_if_willing_target</code>, <code>allows_reaction</code>.</li>'
                    .'<li>Sous-effets : slugs du catalogue (frapper, soigner, pousser, invoquer…). Invocation : lier le monstre <code>jdr:summon:…</code>.</li>'
                    .'</ul>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Lis le sort à voix haute. S’il faut un schéma MMO, coupe.</li>'
                    .'<li>Si c’est trop fort chaque tour : baisse les dés, monte le coût, ou ajoute un cooldown.</li>'
                    .'<li>Portée mini 2 sur les flèches (Crâ) : collé, tu recules ou tu prends tes distances — ce n’est pas un sort de mêlée.</li>'
                    .'<li>Invocation = créature fragile (1d4), pas un second PJ.</li>'
                    .'<li>Ne copie pas un passif de classe dans le sort (la Fureur s’occupe du +1 Iop ; Concentration ne rajoute pas un dé).</li>'
                    .'<li>Sort-créature : 1 attaque calée sur les '.$pa.' Dofus du monstre ; une 2ᵉ option seulement si le monstre a assez de '.$pa.' pour ne pas tout lancer chaque tour.</li>'
                    .'<li>Compare toujours à la charte au niveau du sort, ligne neutre, puis régulateurs.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> dégâts de sort fort + large zone + pas de cooldown ; contrôle sans sauvegarde ; jeton Dofus brut à la place d’un état JDR ; 8 sorts sur un sbire.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons jouables</h3>'
                    .'<ul>'
                    .'<li><strong>Pression</strong> (Iop, niv. 1) — Terre, 3 '.$pa.', 2×/tour, '.$po.' 1, 1d6+'.$force.', mêlée. C’est l’attaque de base.</li>'
                    .'<li><strong>Fendoir</strong> (Iop, niv. 1) — Terre, 5 '.$pa.', 1×/tour, petite zone, sauvegarde, 1d6+'.$force.'. C’est le sort fort.</li>'
                    .'<li><strong>Bond</strong> (Iop, niv. 1) — 3 '.$pa.' bonus, téléport 1–4, pas de dégâts. Combo du tour : Bond + Pression.</li>'
                    .'<li><strong>Mot Vivifiant</strong> (Eniripsa, niv. 1) — 3 '.$pa.', 1d4+'.$sagesse.' (soin en d4, pas d6).</li>'
                    .'<li><strong>Béco du Tofu</strong> (Tofu Chimérique) — sort-créature Air, 3 '.$pa.', 1d6, une action. Le monstre n’a pas de grimoire de classe.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-spell|Sorts]]. Classes : chaque fiche a ses 24 sorts (dégrossissage à relire).</p>',
            ],
        ],
        'cms_extras' => [
            [
                'slug' => 'creation-sorts-catalog',
                'title' => 'Chartes de sorts',
                'template' => 'characteristic_norms_catalog',
                'group' => 'spell',
            ],
        ],
    ];
};
