<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : équipements.
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    $ca = $k['ca'];
    $force = $k['force'];
    $intel = $k['intel'];
    $agi = $k['agi'];
    $chance = $k['chance'];

    return [
        'entity' => 'item',
        'title' => 'Équipements',
        'slug' => 'creation-equipements',
        'icon' => 'fa-solid fa-shield-halved',
        'menu_order' => 6,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Équipements</h2>'
                    .'<p>Un objet occupe <strong>un emplacement</strong> et pousse <strong>un axe</strong> (dégâts, '.$ca.', une carac). Huit slots se cumulent : si chaque pièce est « très fort », le PJ explose les plafonds. '.$k['kEquipEq'].' · '.$k['kCaracs'].'.</p>'
                    .'<p>En conversion Dofus, l’<strong>identité est figée</strong> (nom, type, image, description). Les bonus JDR vivent dans <code>bonus</code> (le front les affiche aussi sur <code>effect</code>). L’algo (normes, grille <code>ia:equipment-grid</code>) fait le gros du travail ; l’IA n’intervient que pour un <strong>unique de scénario</strong> ou un cas que l’algo ne tranche pas.</p>'
                    .'<p>Un type d’objet n’a que <strong>3 ou 4 caractéristiques possibles</strong>. Après conversion, beaucoup d’items Dofus collapsent vers la même signature : on garde un représentant, on ne publie pas les 40 doublons.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Un slot, un axe.</strong> Arme → dégâts ; armure / bouclier → '.$ca.' ; accessoire → une carac de voie.</li>'
                    .'<li><strong>Voie.</strong> Terre '.$force.' · Feu '.$intel.' · Eau '.$chance.' · Air '.$agi.'. L’élément n’existe en JDR que sur cape (carac) et armes (dégâts fixes), via le pivot type d’objet.</li>'
                    .'<li><strong>Rareté = prix dans la tranche</strong>, pas le nombre de lignes de bonus.</li>'
                    .'<li><strong>Un effet spécial max</strong>, et tu baisses un bonus numérique.</li>'
                    .'<li>Type « visible en jeu » pour la bibliothèque. Monture / familier : bonus situationnel, hors plafonds globaux.</li>'
                    .'<li>Unique : Dofus ou quête, jamais un loot de champ.</li>'
                    .'<li>Écrire les bonus JDR dans <code>bonus</code> et poser <code>auto_update = false</code> pour ne pas se faire écraser par le scrap.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Bonus par palier (livre 5.2.4)</h3>'
                    .'<table><thead><tr><th>Niveau</th><th>Arme (dégâts)</th><th>Armure ('.$ca.')</th><th>Accessoire (1 carac)</th></tr></thead><tbody>'
                    .'<tr><td>1–5</td><td>+1 à +2</td><td>+1 à +2</td><td>+1</td></tr>'
                    .'<tr><td>6–10</td><td>+2 à +3</td><td>+2 à +3</td><td>+2</td></tr>'
                    .'<tr><td>11–15</td><td>+3 à +4</td><td>+3 à +4</td><td>+3</td></tr>'
                    .'<tr><td>16–20</td><td>+4 à +5</td><td>+4 à +5</td><td>+4</td></tr>'
                    .'</tbody></table>'
                    .'<p>Le tableau vivant plus bas projette les plafonds par emplacement et caractéristique (formules objet). Un tiret = pas encore débloqué. On corrige la caractéristique si un chiffre cloche, pas cette page.</p>'
                    .'<h3>Plafonds toutes sources (ordre de grandeur)</h3>'
                    .'<ul>'
                    .'<li>'.$ca.' : +10 max (pièces + set).</li>'
                    .'<li>Dégâts : +10 max.</li>'
                    .'<li>Par caractéristique : +4 max (forgemagie +2).</li>'
                    .'</ul>'
                    .'<h3>Rareté</h3>'
                    .'<ul>'
                    .'<li>0 Commun — plus à partir du niveau 5.</li>'
                    .'<li>1 Peu commun — plus à partir du niveau 9.</li>'
                    .'<li>2 Rare — plus à partir du niveau 15 (ensuite très rare / légendaire).</li>'
                    .'<li>3 Très rare · 4 Légendaire · 5 Unique (Dofus / quête seulement).</li>'
                    .'</ul>'
                    .'<h3>Prix</h3>'
                    .'<p>Σ(bonus × prix unitaire) + 150×niveau + 200×rareté = <code>price_calculated</code>. <code>price_custom</code> ajuste. <code>price</code> = total.</p>'
                    .'<h3>Propriétés de fiche</h3>'
                    .'<ul>'
                    .'<li><code>name</code>, <code>description</code>, <code>item_type_id</code>, <code>level</code>, <code>rarity</code>, image — identité.</li>'
                    .'<li><code>bonus</code> — JSON des bonus JDR (source d’affichage et de prix).</li>'
                    .'<li><code>effect</code> — souvent miroir d’affichage ; ne pas y mettre un roman Dofus.</li>'
                    .'<li><code>recipe</code> — ressources liées ; une fois jouable, les ingrédients passent jouables (plancher 1 kama).</li>'
                    .'<li><code>price_calculated</code>, <code>price_custom</code>.</li>'
                    .'<li><code>official_id</code> / <code>dofusdb_id</code>, <code>auto_update</code>.</li>'
                    .'<li>Panoplies liées : le set pose le thème, il ne remplit pas les slots d’un PNJ.</li>'
                    .'</ul>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Compare au tableau des plafonds (même slot, même bande) puis à la charte objet, ligne neutre.</li>'
                    .'<li>Rare / légendaire : ligne <em>fort</em>, pas très fort sur trois stats à la fois.</li>'
                    .'<li>Une pièce doit tenir <em>seule</em> (un PJ peut ne porter que celle-là). Le set n’est pas une béquille.</li>'
                    .'<li>Trophée / Dofus : effet exceptionnel, conditionnel, rare — pas un +5 partout.</li>'
                    .'<li>Pour la grille algo : une case = niveau × slot × voie. Un représentant Dofus suffit.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> +5 partout dès le niveau 8 ; Unique hors Dofus / quête ; un accessoire qui copie une arme ; republier 12 capes identiques.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons jouables</h3>'
                    .'<ul>'
                    .'<li><strong>Cape du Piou Vert</strong> / <strong>Chapeau du Piou Violet</strong> — set bas niveau, un axe, porté par les PNJ d’Incarnam.</li>'
                    .'<li><strong>Marteau du Bouftou</strong> — arme de palier, dégâts, pas six lignes de caracs.</li>'
                    .'<li>Kit d’étalons niveau 8 : 32 pièces (4 éléments × 4 raretés), capes + armes seulement — relire avant de s’en servir de few-shot.</li>'
                    .'<li>Panoplies or (Piou, Bouftou, Blop…) : en général +1 compétence de thème au palier, pas un second set de stats.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-item|Équipements]] · [[kref:page:creation-panoplies|Panoplies]].</p>',
            ],
        ],
        'cms_extras' => [
            [
                'slug' => 'creation-equipements-table',
                'title' => 'Tableau des bonus',
                'template' => 'equipment_bonus_table',
            ],
            [
                'slug' => 'creation-equipements-rarete',
                'title' => 'Rareté',
                'html' => '<h2>Rareté</h2>'
                    .'<p>La rareté se déduit du <strong>prix</strong> dans la tranche de niveau.</p>'
                    .'<ul>'
                    .'<li>Plus de <strong>commun</strong> à partir du niveau 5</li>'
                    .'<li>Plus de <strong>peu commun</strong> à partir du niveau 9</li>'
                    .'<li>Plus de <strong>rare</strong> à partir du niveau 15 (très rare / légendaire)</li>'
                    .'<li>Jamais <strong>unique</strong>, sauf Dofus et cas de quête</li>'
                    .'</ul>',
            ],
            [
                'slug' => 'creation-equipements-catalog',
                'title' => 'Chartes objet',
                'template' => 'characteristic_norms_catalog',
                'group' => 'object',
            ],
        ],
    ];
};
