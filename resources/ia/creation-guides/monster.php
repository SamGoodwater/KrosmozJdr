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
                    .'<p>Un monstre, c’est une <strong>rencontre</strong> : danger, rythme, butin — pas un personnage complet. '.$k['kPnjMonstres'].'.</p>'
                    .'<p><strong>Ce n’est pas un PNJ.</strong> Pas de classe, pas de spécialisation. Il a un niveau, des caractéristiques, un petit kit de sorts (sans variantes), éventuellement des capacités ou des traits. L’équipement / les conso : seulement dans de très rares cas.</p>'
                    .'<p>On part des sorts Dofus de la créature, on <strong>simplifie</strong>. Ce n’est jamais « la fiche Dofus + 8 sorts ».</p>',
            ],
            [
                'id' => 'methode',
                'title' => 'Comment le créer',
                'html' => '<h3>Marche à suivre</h3>'
                    .'<ol>'
                    .'<li><strong>Identité</strong> : une phrase (« oiseau bruyant qui picore et fuit »).</li>'
                    .'<li><strong>Puissance</strong> : sbire, normal, élite ou boss (voir tableau).</li>'
                    .'<li><strong>Niveau</strong> (ou fourchette) et dé de vie ; calibre les '.$pv.' et la '.$ca.' sur la charte.</li>'
                    .'<li><strong>Sorts</strong> : reprends Dofus, coupe au repère du tableau. Détail : [[kref:page:creation-sorts|Sorts]].</li>'
                    .'<li><strong>Traits</strong> du catalogue (souvent 0–2 ; un de plus s’il colle vraiment).</li>'
                    .'<li><strong>Relire</strong> le paquet monstre + sorts ensemble. Publier en jouable seulement si c’est clair.</li>'
                    .'</ol>'
                    .'<p>Le bestiaire Incarnam / Amakna est encore en <strong>brouillon</strong> : sers-t’en comme modèles, valide avant de republier.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Kit court</strong> selon la puissance (repères souples ci-dessous).</li>'
                    .'<li><strong>Élément des sorts = carac haute</strong> (bec Air ↔ '.$agi.', coup Terre ↔ '.$force.').</li>'
                    .'<li><strong>Invocation</strong> : rare, seulement si ça compte pour le combat. Moins d’entités = plus simple pour le MJ.</li>'
                    .'<li><strong>Hostilité</strong> : hostile ou agressif pour la faune typique.</li>'
                    .'<li><strong>Boss</strong> : case boss + PA légendaires si besoin. Pas de boss dans la petite faune d’Incarnam / Astrub.</li>'
                    .'<li>Butin : 1–2 ressources du thème, pas une panoplie légendaire sur un piou.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites (chiffres et kit)',
                'html' => '<h3>Kit de sorts (repères souples)</h3>'
                    .'<table><thead><tr><th>Puissance</th><th>Repère</th><th>Composition typique</th></tr></thead><tbody>'
                    .'<tr><td>Sbire / peu puissant</td><td>1–2</td><td>1 dégât ± placement / entrave</td></tr>'
                    .'<tr><td>Normal</td><td>2</td><td>1 dégât + 1 autre</td></tr>'
                    .'<tr><td>Élite / puissant</td><td>~3</td><td>1–2 attaques + 1–2 autres</td></tr>'
                    .'<tr><td>Boss</td><td>plafond ≈ 4</td><td>distance + càc + outil + placement</td></tr>'
                    .'</tbody></table>'
                    .'<h3>Ordre de grandeur (gabarit)</h3>'
                    .'<table><thead><tr><th>Niveau</th><th>'.$pv.'</th><th>Dégâts / action</th></tr></thead><tbody>'
                    .'<tr><td>1–5</td><td>20–50</td><td>1d6+mod à 2d6+mod</td></tr>'
                    .'<tr><td>6–10</td><td>50–100</td><td>2d6+mod à 3d6+mod</td></tr>'
                    .'<tr><td>11–15</td><td>100–200</td><td>3d6+mod à 4d6+mod</td></tr>'
                    .'<tr><td>16–20</td><td>200–400</td><td>4d6+mod à 5d6+mod</td></tr>'
                    .'</tbody></table>'
                    .'<p>Sbire : dégâts −20–30 %. Boss : +50–100 % <em>ou</em> phases. '.$ca.' : 10 + mod. de '.$vitalite.' sans armure. Chartes vivantes en bas de page.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Garde les '.$pa.' / '.$pm.' Dofus : ça donne le rythme (3 '.$pa.' = souvent une action).</li>'
                    .'<li>Ne copie pas un PJ (grosse liste de sorts, panoplie complète).</li>'
                    .'<li>Les invocations de classe (Tofu Osa, Arbre Sadida…) sont un autre modèle : allié fragile à 1 action.</li>'
                    .'<li>Équipement sur un monstre : exception (boss de quête), pas la norme.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> un « loup niv. 3 » avec les '.$pv.' d’un boss 10 ; tout en Neutre sans identité ; inventer des traits hors catalogue.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons</h3>'
                    .'<ul>'
                    .'<li><strong>Piou Vert</strong> — sbire, 1 sort.</li>'
                    .'<li><strong>Tofu Chimérique</strong> — une attaque Air.</li>'
                    .'<li><strong>Boufton</strong> — 2 options, une par tour.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-monster|Monstres]]. Pour un PNJ (classe + spe), vois [[kref:page:creation-pnj|PNJ]].</p>',
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
