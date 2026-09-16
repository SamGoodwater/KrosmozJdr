<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : ressources (socle plus léger).
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    return [
        'entity' => 'resource',
        'title' => 'Ressources',
        'slug' => 'creation-ressources',
        'icon' => 'fa-solid fa-gem',
        'menu_order' => 9,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Ressources</h2>'
                    .'<p>Une ressource nourrit un <strong>métier</strong> ou un butin, pas un build. Type, niveau, rareté, prix — rarement un bonus de combat. '.$k['kMetiers'].' · '.$k['kConsoLivre'].'.</p>'
                    .'<p>Conversion : on garde nom, type, image Dofus. On ne lui colle pas +3 '.$k['force'].'. Le prix jouable a un <strong>plancher de 1 kama</strong>. Les ressources hors jeu (quêtes, souvenirs) restent en base mais hors bibliothèques.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li>Branche métier (récolte / artisanat / rune) et bande de niveau (1–4, 5–8, … 17–20 pour la récolte).</li>'
                    .'<li>Rareté alignée sur ce que le métier peut produire à ce palier. Unique = quête, pas craft.</li>'
                    .'<li>Prix cohérent avec les voisines du même palier. L’économie suffit : <strong>pas de charte de combat</strong>.</li>'
                    .'<li>Ingrédient d’un objet / conso jouable → la ressource passe jouable (sans réécrire les champs Dofus).</li>'
                    .'<li>1–2 ressources de thème sur un monstre, rareté du palier.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Propriétés de fiche</h3>'
                    .'<ul>'
                    .'<li><code>name</code>, <code>description</code>, <code>resource_type_id</code>, <code>level</code>, <code>rarity</code> (0–5, mêmes libellés que les objets).</li>'
                    .'<li><code>price</code> — prix Dofus ; plancher 1 kama une fois jouable.</li>'
                    .'<li><code>weight</code>, <code>effect</code> (presque toujours vide en JDR).</li>'
                    .'<li><code>official_id</code> / <code>dofusdb_id</code>, <code>auto_update</code>.</li>'
                    .'<li>Liaisons : recettes d’objets / conso / autres ressources ; butin créature ; campagnes (lecture filtrée).</li>'
                    .'</ul>'
                    .'<h3>Repères d’économie (soins)</h3>'
                    .'<p>Pain d’Incarnam = 20 kamas → 10 Blé à 2 kamas. Le palier du consommable fixe le palier de la ressource, pas l’inverse.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Une ressource « pour le lore » sans métier ni recette ni butin n’a pas besoin d’être jouable.</li>'
                    .'<li>Ne crée pas une ressource pour porter un bonus de Force : c’est un consommable ou un objet.</li>'
                    .'<li>Aligne le nom et le thème sur le monstre qui la drop (plume de piou, gelée, blé des champs).</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> minerai niveau 2 au prix d’un légendaire 18 ; ressource combat ; publier tous les souvenirs Dofus dans la bibliothèque.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons</h3>'
                    .'<ul>'
                    .'<li><strong>Blé</strong> — récolte bas palier, recette du Pain d’Incarnam (10 pour 1 pain).</li>'
                    .'<li><strong>Orge</strong> / <strong>Avoine</strong> / <strong>Houblon</strong> — paliers suivants de la même échelle.</li>'
                    .'<li>Butin de bestiaire : 1–2 ressources du thème (piou, bouftou, gelée), rareté commune / peu commune.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-resource|Ressources]].</p>',
            ],
        ],
        'cms_extras' => [],
    ];
};
