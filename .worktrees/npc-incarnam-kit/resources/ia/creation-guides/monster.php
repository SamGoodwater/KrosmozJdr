<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : monstres (bestiaire, pas invocations de classe).
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    $pa = $k['pa'];
    $pm = $k['pm'];
    $pv = $k['pv'];
    $ca = $k['ca'];
    $po = $k['po'];
    $force = $k['force'];
    $intel = $k['intel'];
    $agi = $k['agi'];
    $chance = $k['chance'];
    $vitalite = $k['vitalite'];

    return [
        'entity' => 'monster',
        'title' => 'Monstres',
        'slug' => 'creation-monstres',
        'icon' => 'fa-solid fa-dragon',
        'menu_order' => 4,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Monstres</h2>'
                    .'<p>Un monstre est une <strong>rencontre</strong> : danger, rythme, butin — pas un PJ. La coquille (Monster) porte nom public, race, taille, boss ; les stats, sorts et traits sont sur la <strong>créature</strong> liée. '.$k['kPnjMonstres'].'.</p>'
                    .'<p>Ce n’est <strong>pas</strong> une fiche Dofus + 8 sorts importés. C’est <strong>une fiche + 1 à 2 actions</strong> (3 pour une élite / un boss). PA et noms de sorts suivent Dofus ; les dés suivent la charte JDR.</p>'
                    .'<p>En conversion : les <strong>stats restent figées</strong> par défaut (gabarit 5.1.2 / charte). L’IA propose les sorts-créature dans le <em>même</em> paquet, collés aux caracs déjà là. Distinct des invocations de classe (Tofu Osa, Poupée Sadida…) : celles-là sont des alliés fragiles, pas un bestiaire.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Niveau ≈ groupe.</strong> Sbire = ligne faible ; standard = neutre ; élite = fort ; boss = très fort + mécaniques.</li>'
                    .'<li><strong>1 à 2 sorts</strong> pour la faune (1 si peu de '.$pa.'). Pas un grimoire de 24.</li>'
                    .'<li><strong>Élément des sorts = carac haute.</strong> Bec Air ↔ '.$agi.' ; coup Terre ↔ '.$force.'.</li>'
                    .'<li><strong>0 à 2 traits</strong> déjà au catalogue (Petite taille, Vif, Agile…). On n’invente pas un trait par monstre.</li>'
                    .'<li><strong>Hostilité</strong> 0 amical · 1 curieux · 2 neutre · 3 hostile · 4 agressif. Bestiaire d’Incarnam / Amakna : 3 ou 4.</li>'
                    .'<li><strong>Boss</strong> : cocher la case et remplir les '.$pa.' légendaires (pool hors tour, recharge en fin de tour du boss). Pas de boss dans la faune d’Incarnam / champs d’Astrub.</li>'
                    .'<li>Butin : 1–2 ressources du thème, rareté du palier. Pas une panoplie légendaire sur un piou.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Gabarit 5.1.2 (ordre de grandeur)</h3>'
                    .'<table><thead><tr><th>Niveau</th><th>'.$pv.'</th><th>Dégâts / action</th></tr></thead><tbody>'
                    .'<tr><td>1–5</td><td>20–50</td><td>1d6+mod à 2d6+mod</td></tr>'
                    .'<tr><td>6–10</td><td>50–100</td><td>2d6+mod à 3d6+mod</td></tr>'
                    .'<tr><td>11–15</td><td>100–200</td><td>3d6+mod à 4d6+mod</td></tr>'
                    .'<tr><td>16–20</td><td>200–400</td><td>4d6+mod à 5d6+mod</td></tr>'
                    .'</tbody></table>'
                    .'<p>'.$pv.' ≈ '.$vitalite.' × 5 (faible) / 7 (moyen) / 10 (robuste) / 12–15 (boss). Sbire : −20 à 30 % dégâts. Boss : +50 à 100 % <em>ou</em> des phases, pas les deux à fond. '.$ca.' : 10+'.$vitalite.' sans armure, 12–15+'.$vitalite.' armure naturelle.</p>'
                    .'<h3>Rôles</h3>'
                    .'<ul>'
                    .'<li><strong>Sbire</strong> — meurt vite, menace en nombre ; peu de sorts.</li>'
                    .'<li><strong>Standard</strong> — un pour un PJ ; ligne neutre.</li>'
                    .'<li><strong>Élite</strong> — deux ou trois PJ ; 1 capacité signature.</li>'
                    .'<li><strong>Boss</strong> — table entière ; phases 100–50 / 50–25 / 25–0 % ; éventuellement sbires.</li>'
                    .'</ul>'
                    .'<h3>Propriétés de coquille</h3>'
                    .'<ul>'
                    .'<li><code>official_id</code> bestiaire : <code>jdr:bestiary:…</code> (pas de <code>dofusdb_id</code> sur ces fiches JDR).</li>'
                    .'<li><code>monster_race_id</code>, <code>size</code> 0 minuscule … 5 gigantesque (piou = petit / 1).</li>'
                    .'<li><code>is_boss</code>, <code>boss_pa</code> (seulement si boss).</li>'
                    .'</ul>'
                    .'<h3>Propriétés de créature (intéressantes en rencontre)</h3>'
                    .'<ul>'
                    .'<li>Identité : <code>name</code>, <code>description</code>, <code>location</code>, <code>level</code>, <code>hostility</code>, <code>other_info</code>.</li>'
                    .'<li>Combat : <code>life</code>, <code>pa</code>, <code>pm</code>, <code>po</code>, <code>ini</code>, <code>ca</code>, <code>touch</code>, <code>invocation</code>.</li>'
                    .'<li>Six caracs : <code>vitality</code>, <code>sagesse</code>, <code>strong</code>, <code>intel</code>, <code>agi</code>, <code>chance</code>.</li>'
                    .'<li>Dégâts fixes / résistances : <code>do_fixe_*</code>, <code>res_*</code> — un élément fort, souvent une vulnérabilité opposée, pas six immunités.</li>'
                    .'<li>Esquive / tacle : <code>dodge_pa</code>, <code>dodge_pm</code>, <code>fuite</code>, <code>tacle</code>, <code>critical_hit</code>.</li>'
                    .'<li>Compétences et sauvegardes : seulement si la scène hors combat s’en sert (un piou n’a pas 18 lignes de bonus).</li>'
                    .'<li>Liaisons : sorts (catégorie créature), traits, éventuellement ressources / objets de butin.</li>'
                    .'</ul>'
                    .'<p>La référence chiffrée sur le site est la <strong>charte</strong> (plus bas). S’il y a écart avec le livre, tu suis la charte.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Une phrase d’identité avant les chiffres (« oiseau bruyant qui picore et fuit »).</li>'
                    .'<li>Garde les '.$pa.' / '.$pm.' Dofus : ça donne le rythme (3 '.$pa.' = une action).</li>'
                    .'<li>Les petits (piou, tofu, moustique) : une attaque. Les plus complets (larve, gelée, champignon) : deux options, pas deux tirs par tour si le budget '.$pa.' l’interdit.</li>'
                    .'<li>Ne copie pas un PJ (24 sorts, panoplie, six maîtrises).</li>'
                    .'<li>Invocation de classe (8/12/16 '.$pv.', 1 '.$pm.', 1 sort) : autre modèle, autre JSON (<code>jdr:summon:…</code>).</li>'
                    .'<li>Paquet IA : monstre + sorts dans le même JSON, à relire ensemble.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> un « loup niveau 3 » avec les '.$pv.' d’un boss 10 ; tout en Neutre sans identité élémentaire ; inventer des traits au lieu d’utiliser le catalogue.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons jouables</h3>'
                    .'<ul>'
                    .'<li><strong>Piou Vert</strong> (Incarnam, niv. 2) — 20 '.$pv.', 3 '.$pa.', 3 '.$pm.', '.$ca.' 12, Petite taille + Vif, 1 sort Picore Terre 1d6 à 2 '.$pa.'.</li>'
                    .'<li><strong>Tofu Chimérique</strong> (Incarnam, niv. 2) — 22 '.$pv.', 4 '.$pa.', 6 '.$pm.', Béco Air 1d6 à 3 '.$pa.' (distinct du Tofu invoqué).</li>'
                    .'<li><strong>Boufton Pâlichon</strong> (Incarnam, niv. 3) — 30 '.$pv.', 4 '.$pa.', deux options d’attaque, une par tour.</li>'
                    .'<li>Faune d’Astrub / Amakna : Pious de couleur, Bouftou, gelées, Crabe, Chafer… même recette, <strong>pas de boss</strong>.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-monster|Monstres]].</p>',
            ],
        ],
        'cms_extras' => [
            [
                'slug' => 'creation-monstres-catalog',
                'title' => 'Chartes créature',
                'template' => 'characteristic_norms_catalog',
                'group' => 'creature',
            ],
        ],
    ];
};
