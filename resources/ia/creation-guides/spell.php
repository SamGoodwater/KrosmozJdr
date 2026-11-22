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
    $vitalite = $k['vitalite'];

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
                    .'<p>Un sort, c’est <strong>une chose claire</strong> à la table : dégâts au contact ou à distance, soin, protection, amélioration, entrave, placement ou invocation. Le coût en '.$pa.' et la fréquence (combien de fois par tour) paient la puissance. '.$k['kSortsEq'].' · '.$k['kSortsLivre'].'.</p>'
                    .'<p>Il y a <strong>deux familles</strong> — ne les mélange pas :</p>'
                    .'<ul>'
                    .'<li><strong>Sort de classe</strong> (PJ / PNJ) : grand grimoire, voies, variantes. On part de Dofus, on adapte pour le JDR.</li>'
                    .'<li><strong>Sort de créature</strong> (monstre) : peu d’actions. On part des sorts Dofus de la créature, on simplifie.</li>'
                    .'</ul>'
                    .'<p>En convertissant un sort Dofus : garde le nom, l’élément et l’image. Réécris l’effet en <strong>1 effet principal + au plus 2 secondaires</strong> qu’on peut jouer en 10 secondes. Les états pointent vers le catalogue JDR (Pesanteur, Empoisonné…) — '.$k['kEtats'].'.</p>',
            ],
            [
                'id' => 'methode',
                'title' => 'Comment écrire un sort',
                'html' => '<h3>Marche à suivre</h3>'
                    .'<ol>'
                    .'<li><strong>Choisis la famille</strong> : classe ou créature.</li>'
                    .'<li><strong>Une job</strong> : dis en une phrase ce que fait le sort.</li>'
                    .'<li><strong>Deux textes</strong> : <em>Général</em> = le geste (sans chiffres, tutoiement) ; <em>Effets</em> = ce qu’on lance au tour (dés, portée, relance).</li>'
                    .'<li><strong>Remplis le tableau</strong> : PA, PO, zone, lancers par tour, dégâts / autres colonnes. Une case vide = défaut (souvent 3 PA, contact, 2/tour).</li>'
                    .'<li><strong>Calibre</strong> sur la charte du niveau (tableau plus bas). Lis à voix haute. Publie en jouable seulement si c’est clair.</li>'
                    .'</ol>'
                    .'<p>Pour une classe entière : [[kref:page:creation-classes|Classes]]. Pour un monstre : [[kref:page:creation-monstres|Monstres]].</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Une job.</strong> Pas un sort qui tape, soigne et étourdit à la fois.</li>'
                    .'<li><strong>Élément ↔ caractéristique.</strong> Terre → '.$force.', Feu → '.$intel.', Eau → '.$chance.', Air → '.$agi.'.</li>'
                    .'<li><strong>Budget de tour.</strong> Au niveau 1, vise environ 6 '.$pa.' (ex. deux sorts à 3). Un sort à 5 '.$pa.' est souvent le seul du tour.</li>'
                    .'<li><strong>Simplifie Dofus.</strong> Au plus 3 effets jouables. Les combos MMO se coupent.</li>'
                    .'<li><strong>Bon catalogue.</strong> Un sort de monstre ne va pas dans le grimoire d’une classe, et inversement.</li>'
                    .'<li><strong>Publie</strong> seulement quand le texte se lit en dix secondes.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'classe',
                'title' => 'Sorts de classe',
                'html' => '<h3>Sorts de classe</h3>'
                    .'<p>Une classe a un passif, des voies (Air / Feu / Eau / Terre) et environ <strong>30 sorts</strong> répartis en choix (souvent 2 variantes, parfois 4 sur les voies). Le joueur n’en apprend qu’une variante par choix.</p>'
                    .'<ul>'
                    .'<li><strong>Voies</strong> : sorts qui font des dégâts de l’élément, aux niveaux 1, 4, 7 et 12. Le niveau 1 frappe simple ; les effets forts viennent plus tard.</li>'
                    .'<li><strong>Généraux</strong> : sorts utiles (placement, protection…) sans forcément d’élément. Les deux variantes d’un choix ne font pas la même chose.</li>'
                    .'<li>Deux sorts d’une même voie ne doivent pas être des copies.</li>'
                    .'</ul>'
                    .'<p>Détail de la conversion d’une classe : [[kref:page:creation-classes|Classes]]. En combat, seuls les sorts de classe utilisent les '.$pa.' rendus chaque tour ; le reste puise dans la réserve de Wakfu.</p>',
            ],
            [
                'id' => 'creature',
                'title' => 'Sorts de créature',
                'html' => '<h3>Sorts de créature (monstres)</h3>'
                    .'<p>Reprends les sorts Dofus de la créature, puis <strong>coupe</strong> jusqu’à un kit court. Ce sont des repères, pas des quotas figés.</p>'
                    .'<table><thead><tr><th>Puissance</th><th>Repère</th><th>Idée</th></tr></thead><tbody>'
                    .'<tr><td>Sbire / peu puissant</td><td>1–2</td><td>1 dégât ± placement ou entrave</td></tr>'
                    .'<tr><td>Normal</td><td>2</td><td>1 dégât + 1 autre</td></tr>'
                    .'<tr><td>Élite / puissant</td><td>~3</td><td>1–2 attaques + 1–2 autres</td></tr>'
                    .'<tr><td>Boss</td><td>plafond ≈ 4</td><td>distance + càc + outil + placement</td></tr>'
                    .'</tbody></table>'
                    .'<ul>'
                    .'<li>Garde les coûts '.$pa.' proches de Dofus (rythme du tour).</li>'
                    .'<li><strong>Invocation</strong> : rare — moins d’entités = plus simple pour le MJ.</li>'
                    .'<li>Pas de « variantes de classe » : une action = un sort.</li>'
                    .'</ul>'
                    .'<p>Suite : [[kref:page:creation-monstres|Monstres]].</p>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites (chiffres)',
                'html' => '<h3>Dégâts et soins (ligne neutre)</h3>'
                    .'<table><thead><tr><th>Niveau</th><th>Dégâts</th><th>Soins / bouclier</th></tr></thead><tbody>'
                    .'<tr><td>1–5</td><td>1d6+mod à 2d6+mod</td><td>1d4+mod à 2d4+mod</td></tr>'
                    .'<tr><td>6–10</td><td>2d6+mod à 3d6+mod</td><td>2d4+mod à 3d4+mod</td></tr>'
                    .'<tr><td>11–15</td><td>3d6+mod à 4d6+mod</td><td>3d4+mod à 4d4+mod</td></tr>'
                    .'<tr><td>16–20</td><td>4d6+mod à 5d6+mod</td><td>4d4+mod à 5d4+mod</td></tr>'
                    .'</tbody></table>'
                    .'<p>Sort à 5+ '.$pa.' → un cran plus fort. Large zone → un cran plus faible par cible. Créature faible −20–30 % ; boss +50–100 % <em>ou</em> des phases (pas les deux à fond).</p>'
                    .'<h3>Coûts usuels</h3>'
                    .'<ul>'
                    .'<li>Action simple : 3–4 '.$pa.'</li>'
                    .'<li>Action forte : 5 '.$pa.' (souvent 1×/tour)</li>'
                    .'<li>Petit bonus / déplacement : 2–3 '.$pa.'</li>'
                    .'</ul>'
                    .'<p>Les chartes vivantes sont en bas de cette page. Contrôle / buff : durée courte, sauvegarde si besoin, pas de cumul du même bonus.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Lis le sort à voix haute. S’il faut un schéma MMO, coupe.</li>'
                    .'<li>Le <em>Général</em> raconte le geste ; les chiffres sont dans <em>Effets</em> / le tableau.</li>'
                    .'<li>Trop fort chaque tour ? Baisse les dés, monte le coût, ou ajoute une relance.</li>'
                    .'<li>Une invocation = créature fragile, pas un second PJ.</li>'
                    .'<li>Ne mets pas un passif de classe dans un sort.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> gros dégâts + large zone + pas de relance ; contrôle sans sauvegarde ; 8 sorts sur un sbire ; grimoire de classe collé à un monstre.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons</h3>'
                    .'<ul>'
                    .'<li><strong>Pression</strong> (Iop) — attaque de base de classe, 3 '.$pa.', mêlée.</li>'
                    .'<li><strong>Fendoir</strong> (Iop) — sort fort, 5 '.$pa.', 1×/tour.</li>'
                    .'<li><strong>Bond</strong> (Iop) — placement, pas de dégâts.</li>'
                    .'<li><strong>Mot Vivifiant</strong> (Eniripsa) — soin (dés en d4).</li>'
                    .'<li><strong>Béco du Tofu</strong> — une action de monstre faible.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-spell|Sorts]]. Les 19 classes jouables servent de modèles.</p>',
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
