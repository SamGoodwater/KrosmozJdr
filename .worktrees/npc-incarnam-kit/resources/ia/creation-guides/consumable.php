<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : consommables.
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    $pa = $k['pa'];
    $pv = $k['pv'];

    return [
        'entity' => 'consumable',
        'title' => 'Consommables',
        'slug' => 'creation-consommables',
        'icon' => 'fa-solid fa-flask',
        'menu_order' => 8,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Consommables</h2>'
                    .'<p>C’est un <strong>coup de pouce ponctuel</strong>, pas un équipement que tu bois. En combat : en général <strong>1 '.$pa.'</strong>. Même type d’effet : pas de cumul, le meilleur gagne. Types différents : cumul OK. '.$k['kConsoLivre'].'.</p>'
                    .'<p>Conversion : garder nom, type, image. Réécrire <code>effect</code> en une phrase de table (quand, combien, combien de temps, usage unique). Les soins hors combat suivent une <strong>échelle</strong> (pain / poisson / viande / potion), pas un jet de sort.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Un effet.</strong> Soin, buff, antidote, téléport, bouclier — pas les cinq.</li>'
                    .'<li><strong>Plus faible qu’un équipement</strong> du même niveau (souvent une ligne en dessous sur la charte objet).</li>'
                    .'<li><strong>Durée.</strong> Combat = court. Hors combat, beaucoup de buffs tiennent jusqu’au <strong>repos long</strong> (8 h max) puis disparaissent.</li>'
                    .'<li><strong>Usage unique</strong> sauf mention contraire. Parchemin de sortilège : détruit seulement si le sort réussit.</li>'
                    .'<li>Type « visible en jeu ». Recette : 10 × ressource de palier pour l’échelle de soins (prix ressource = prix conso / 10).</li>'
                    .'<li>Les consommables <code>playable</code> gardent leur <code>price_custom</code> au recalcul de masse.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Soins hors combat (échelle)</h3>'
                    .'<p>11 paliers : 1, 3, 5, 7, 10, 12, 15, 17, 20, 25, 30 '.$pv.' rendus. Prix indicatifs 20 → 10 000 kamas. Quatre familles : pain, poisson, viande, potion — même chiffre de soin au même palier.</p>'
                    .'<h3>Utilitaires (repères de prix)</h3>'
                    .'<ul>'
                    .'<li>Rappel (zaap connu) : 800.</li>'
                    .'<li>Antidote (retire Empoisonné) : 1 500, 1 '.$pa.' en combat.</li>'
                    .'<li>Bière / café : +1 compétence jusqu’au repos long, ~200–250.</li>'
                    .'<li>Élixir de '.$k['wakfu'].' : +2 points, 3 500, hors combat.</li>'
                    .'<li>Bouclier temporaire / PV temp : palier du niveau, pas un sort d’Eniripsa.</li>'
                    .'<li>Renaissance (reste à 1 '.$pv.') : ~10 000, très rare, une fois.</li>'
                    .'</ul>'
                    .'<h3>Parchemins de caractéristique</h3>'
                    .'<p>Respec (4 caracs × 6 paliers), sans recette, <code>price_custom</code> 1k / 3k / 5k / 10k selon le palier. Ce n’est pas un buff de combat.</p>'
                    .'<h3>Propriétés de fiche</h3>'
                    .'<ul>'
                    .'<li><code>name</code>, <code>description</code>, <code>consumable_type_id</code>, <code>level</code>, <code>rarity</code>.</li>'
                    .'<li><code>effect</code> — la règle JDR en une phrase (quand / combien / durée / unique).</li>'
                    .'<li><code>recipe</code>, <code>price_calculated</code> (somme recette), <code>price_custom</code>.</li>'
                    .'<li><code>official_id</code> / <code>dofusdb_id</code>, <code>auto_update</code>.</li>'
                    .'</ul>'
                    .'<p>Pas de grille de normes dédiée « consommable » : on se cale sur la charte <strong>objet</strong>, une ligne plus bas.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Écris l’effet comme tu le dirais au joueur : « Hors combat. Rend 5 PV. Usage unique. »</li>'
                    .'<li>Un buff de caractéristique < +1 accessoire du même niveau, et il expire au repos.</li>'
                    .'<li>Ne transforme pas une potion Dofus à 8 lignes en cape permanente.</li>'
                    .'<li>Soin en combat ≠ pain : si tu en fais un, 1 '.$pa.' et des dés inférieurs à un sort de soin du même niveau.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> deux potions de Force qui se stackent ; consommable sans coût d’action en combat ; soin hors combat hors échelle (37 PV « parce que Dofus »).</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons jouables</h3>'
                    .'<ul>'
                    .'<li><strong>Pain d’Incarnam</strong> — palier 0, rend 1 '.$pv.', 20 kamas, recette 10 Blé.</li>'
                    .'<li><strong>Carasau</strong> / <strong>Briochette</strong> — même famille, paliers suivants.</li>'
                    .'<li><strong>Antidote</strong> — retire Empoisonné, 1 '.$pa.' ou hors combat, 1 500 kamas.</li>'
                    .'<li><strong>Potion de Rappel</strong> — zaap déjà visité, hors combat, 800 kamas.</li>'
                    .'<li><strong>Bière d’Amakna</strong> — +1 Supercherie jusqu’au repos long.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-consumable|Consommables]].</p>',
            ],
        ],
        'cms_extras' => [
            [
                'slug' => 'creation-consommables-catalog',
                'title' => 'Chartes objet',
                'template' => 'characteristic_norms_catalog',
                'group' => 'object',
            ],
        ],
    ];
};
