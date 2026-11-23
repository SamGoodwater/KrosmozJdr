<?php

declare(strict_types=1);

/**
 * Guide de création : aptitudes de spécialisation (distinctes des capacités).
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    return [
        'entity' => 'aptitude',
        'title' => 'Aptitudes',
        'slug' => 'creation-aptitudes',
        'icon' => 'fa-solid fa-sparkles',
        'menu_order' => 4,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Aptitudes</h2>'
                    .'<p>Une <strong>aptitude</strong> est un bonus <em>naturel</em> de spécialisation : on l’<em>a</em>, on ne la <em>lance</em> pas. Pas de coût en PA, pas d’élément, pas de Wakfu. Les <strong>capacités</strong> actives (et les passifs de classe) restent des Capacités.</p>'
                    .'<p>Sur une fiche de spé, les aptitudes vivent dans le texte des paliers (krefs <code>aptitudes</code>). Elles sont liées via le pivot <code>aptitude_specialization.level</code>.</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À renseigner</h3>'
                    .'<ol>'
                    .'<li><strong>Réutilisation</strong> : illimité, repos court, repos long, ou délai (quantité + unité).</li>'
                    .'<li><strong>Durée</strong> : instantané, temporisé, permanent, ou « tant que… » (note courte).</li>'
                    .'<li><strong>Portée</strong> : soi, distance (m/km), ou sens (ouïe / vue / toucher + note).</li>'
                    .'<li><strong>Effet</strong> : une phrase claire, ton « tu », sans grille de dés de sort.</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites',
                'html' => '<p>Si ça ressemble à un sort (dégâts, zone, contrôle avec PA), c’est une <strong>capacité</strong> ou un sort — pas une aptitude. Les passifs de classe restent sur <code>Capability</code> + <code>breed_capability</code>.</p>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<p>Préfère un effet toujours vrai ou conditionnel simple (« tant qu’il y a une foule ») plutôt qu’un mini-sort gratuit. Compare la puissance aux autres aptitudes du même palier, pas aux sorts.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<ul>'
                    .'<li><em>Expertise en Wakfu</em> (Érudit) — avantage pour dissiper / contrer.</li>'
                    .'<li><em>Présence de scène</em> (Artiste) — avantage social une fois par scène.</li>'
                    .'</ul>',
            ],
        ],
    ];
};
