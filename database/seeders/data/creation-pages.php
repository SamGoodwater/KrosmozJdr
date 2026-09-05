<?php

declare(strict_types=1);
use Database\Seeders\CreationPagesSeeder;

/**
 * Atelier MJ « Création » : une page d’aide par type d’entité Bibliothèques (+ PNJ).
 *
 * Shortcodes {@code [[kref:…]]} convertis par {@see CreationPagesSeeder}.
 *
 * @return array{
 *   hub_intro: string,
 *   pages: list<array{
 *     title: string,
 *     slug: string,
 *     icon: string,
 *     menu_order: int,
 *     sections: list<array<string, mixed>>
 *   }>
 * }
 */
return [
    'hub_intro' => '<h2>Atelier de création</h2>'
        .'<p>Espace réservé aux MJ pour concevoir et équilibrer le contenu. Chaque sous-page correspond à un type d’entité des [[kref:page:bibliotheque-breed|Bibliothèques]] : une explication, puis le tableau utile (chartes, bonus d’équipement).</p>'
        .'<p>Les chiffres affichés sont une <strong>projection</strong> du système de caractéristiques : on corrige les fiches (ou les définitions de caractéristiques), pas une grille figée sur cette page.</p>'
        .'<p>Pipeline d’une fiche : <strong>Brut</strong> (import) → <strong>Brouillon</strong> (travail) → <strong>Auto</strong> (proposition à relire) → <strong>Jouable</strong> → <strong>Archivé</strong>. Les joueurs ne voient que le jouable (et l’archivé selon les droits). L’auteur et les MJ voient aussi les brouillons.</p>',

    'pages' => [
        [
            'title' => 'Classes',
            'slug' => 'creation-classes',
            'icon' => 'fa-solid fa-people-group',
            'menu_order' => 0,
            'sections' => [
                [
                    'slug' => 'creation-classes-intro',
                    'title' => 'Créer une classe',
                    'html' => '<h2>Classes</h2>'
                        .'<p>Une classe (<code>breed</code>) définit le socle d’un personnage : dé de vie, sorts, capacités, traits. Les stats de combat vivent sur la créature liée, comme pour un [[kref:page:creation-monstres|monstre]] ou un [[kref:page:creation-pnj|PNJ]].</p>'
                        .'<p>Sur une fiche <strong>jouable</strong>, les liaisons (sorts, capacités, traits, PNJ) masquent les brouillons : un sort non publié n’apparaît pas aux joueurs. L’écran Modifier charge toujours toutes les liaisons.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-breed|Classes]]. Les chartes ci-dessous calibrent les caractéristiques créature (PV, caracs, compétences) selon le niveau 1–20.</p>',
                ],
                [
                    'slug' => 'creation-classes-catalog',
                    'title' => 'Chartes créature',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'creature',
                ],
            ],
        ],
        [
            'title' => 'Spécialisations',
            'slug' => 'creation-specialisations',
            'icon' => 'fa-solid fa-star',
            'menu_order' => 1,
            'sections' => [
                [
                    'slug' => 'creation-specialisations-intro',
                    'title' => 'Créer une spécialisation',
                    'html' => '<h2>Spécialisations</h2>'
                        .'<p>Une spécialisation affine une [[kref:page:creation-classes|classe]] : maîtrises, aptitudes, et souvent des sorts, capacités, traits, objets ou consommables liés.</p>'
                        .'<p>Même règle de visibilité que les classes : en lecture, seuls les éléments visibles pour l’utilisateur apparaissent. Un objet brouillon lié à une spé jouable ne fuit pas.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-specialization|Spécialisations]]. Chartes créature pour les bonus de caractéristiques.</p>',
                ],
                [
                    'slug' => 'creation-specialisations-catalog',
                    'title' => 'Chartes créature',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'creature',
                ],
            ],
        ],
        [
            'title' => 'Sorts',
            'slug' => 'creation-sorts',
            'icon' => 'fa-solid fa-wand-sparkles',
            'menu_order' => 2,
            'sections' => [
                [
                    'slug' => 'creation-sorts-intro',
                    'title' => 'Créer un sort',
                    'html' => '<h2>Sorts</h2>'
                        .'<p>Calibre dégâts, soins et boucliers selon le niveau (environ 1d6 au niveau 1 jusqu’à ~5d6+mod au niveau 20) et les principes d’[[kref:page:regles-5-2-principes-dequilibrage|équilibrage]]. Un sort coûteux (5+ [[kref:characteristic:action_points_creature|PA]]) peut monter d’une ligne de puissance ; une zone large devrait baisser les dégâts par cible.</p>'
                        .'<p>Les états infligés doivent pointer vers les cinq états JDR jouables (Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli), pas vers chaque jeton Dofus en Brut — voir [[kref:page:creation-etats|États]].</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-spell|Sorts]]. Ouvre une charte pour lire la grille, choisir le niveau et activer les régulateurs (PA, zone…).</p>',
                ],
                [
                    'slug' => 'creation-sorts-catalog',
                    'title' => 'Chartes de sorts',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'spell',
                ],
            ],
        ],
        [
            'title' => 'Capacités',
            'slug' => 'creation-capacites',
            'icon' => 'fa-solid fa-bolt',
            'menu_order' => 3,
            'sections' => [
                [
                    'slug' => 'creation-capacites-intro',
                    'title' => 'Créer une capacité',
                    'html' => '<h2>Capacités</h2>'
                        .'<p>Une capacité ressemble à un sort (coût, effets, élément) mais n’a <strong>pas</strong> de grille de normes dédiée. Pour l’équilibrage des dégâts et des coûts, appuie-toi sur les chartes des [[kref:page:creation-sorts|sorts]].</p>'
                        .'<p>Comme les sorts, la carte minimale reprend la couleur d’élément en bordure. Les liaisons depuis une classe ou une spécialisation jouable masquent les capacités brouillon.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-capability|Capacités]].</p>',
                ],
            ],
        ],
        [
            'title' => 'Monstres',
            'slug' => 'creation-monstres',
            'icon' => 'fa-solid fa-dragon',
            'menu_order' => 4,
            'sections' => [
                [
                    'slug' => 'creation-monstres-intro',
                    'title' => 'Créer un monstre',
                    'html' => '<h2>Monstres</h2>'
                        .'<p>La fiche publique est une coquille <strong>Monster</strong> : nom, race, image. Les stats, sorts et équipements sont sur la <strong>créature</strong> liée. Un sort brouillon lié n’apparaît pas sur un monstre jouable.</p>'
                        .'<p>Utilise les chartes pour vérifier PV, caracs et bonus selon le niveau 1–20 et la ligne de puissance (très faible → très fort). Point de départ : la ligne <strong>neutre</strong> au niveau visé.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-monster|Monstres]]. Même famille de chartes que les [[kref:page:creation-pnj|PNJ]] et les [[kref:page:creation-classes|classes]].</p>',
                ],
                [
                    'slug' => 'creation-monstres-catalog',
                    'title' => 'Chartes créature',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'creature',
                ],
            ],
        ],
        [
            'title' => 'PNJ',
            'slug' => 'creation-pnj',
            'icon' => 'fa-solid fa-user',
            'menu_order' => 5,
            'sections' => [
                [
                    'slug' => 'creation-pnj-intro',
                    'title' => 'Créer un PNJ',
                    'html' => '<h2>PNJ</h2>'
                        .'<p>Un PNJ partage la même créature que les [[kref:page:creation-monstres|monstres]] : stats, sorts, équipements. La fiche PNJ porte le rôle narratif (marchand, quête, allié) ; les chartes ci-dessous restent celles du groupe créature.</p>'
                        .'<p>Un PNJ « social » n’a pas besoin d’être calibré comme un boss. Descends d’une ou deux lignes de puissance, ou laisse les stats de combat minimales si le combat n’est pas l’enjeu.</p>'
                        .'<p>Les PNJ n’ont pas de page Bibliothèques dédiée ; on les rattache souvent à une [[kref:page:creation-classes|classe]] ou une [[kref:page:creation-specialisations|spécialisation]].</p>',
                ],
                [
                    'slug' => 'creation-pnj-catalog',
                    'title' => 'Chartes créature',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'creature',
                ],
            ],
        ],
        [
            'title' => 'Équipements',
            'slug' => 'creation-equipements',
            'icon' => 'fa-solid fa-shield-halved',
            'menu_order' => 6,
            'sections' => [
                [
                    'slug' => 'creation-equipements-intro',
                    'title' => 'Lire le tableau',
                    'html' => '<h2>Bonus d’équipement</h2>'
                        .'<p>Ce tableau projette les plafonds de bonus par <strong>emplacement</strong> (type d’objet) et par <strong>caractéristique</strong>, d’après la table <em>formula</em> des caractéristiques objet. Ce n’est pas une grille figée : si un chiffre est faux, on corrige la caractéristique, pas cette page.</p>'
                        .'<p>Chaque colonne 1–2, 3–4, … 19–20 indique le plafond au début de la tranche (plus grand seuil de formule ≤ niveau de début). Un tiret signifie que le bonus n’est pas encore débloqué (valeur 0).</p>'
                        .'<p>Les colonnes Prix / unité, FM max et Prix rune viennent du même enregistrement. Catalogue : [[kref:page:bibliotheque-item|Équipements]]. Référence joueur : [[kref:page:caracteristiques|Caractéristiques]].</p>',
                ],
                [
                    'slug' => 'creation-equipements-table',
                    'title' => 'Tableau des bonus',
                    'template' => 'equipment_bonus_table',
                ],
                [
                    'slug' => 'creation-equipements-rarete',
                    'title' => 'Rareté',
                    'html' => '<h2>Rareté</h2>'
                        .'<p>La rareté d’un objet se déduit de son <strong>prix</strong> dans la tranche de niveau, pas du nombre de caractéristiques.</p>'
                        .'<ul>'
                        .'<li>Plus de <strong>commun</strong> à partir du niveau 5</li>'
                        .'<li>Plus de <strong>peu commun</strong> à partir du niveau 9</li>'
                        .'<li>Plus de <strong>rare</strong> à partir du niveau 15 (très rare / légendaire)</li>'
                        .'<li>Jamais <strong>unique</strong>, sauf Dofus et cas spéciaux</li>'
                        .'</ul>'
                        .'<p>Bonus attendus par bande : niv. 1–5 (+1–2) · 6–10 (+2–3) · 11–15 (+3–4) · 16–20 (+4–5) — voir [[kref:page:regles-5-2-principes-dequilibrage|Équilibrage]].</p>',
                ],
                [
                    'slug' => 'creation-equipements-catalog',
                    'title' => 'Chartes objet',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'object',
                ],
            ],
        ],
        [
            'title' => 'Panoplies',
            'slug' => 'creation-panoplies',
            'icon' => 'fa-solid fa-layer-group',
            'menu_order' => 7,
            'sections' => [
                [
                    'slug' => 'creation-panoplies-intro',
                    'title' => 'Créer une panoplie',
                    'html' => '<h2>Panoplies</h2>'
                        .'<p>Une panoplie est un set de pièces d’[[kref:page:creation-equipements|équipement]] : bonus à partir de 2 pièces, puis 3 pièces, etc. En lecture, seules les pièces visibles pour l’utilisateur apparaissent ; un objet brouillon ne fuit pas via un set jouable.</p>'
                        .'<p>Les bonus de set se cumulent avec ceux des pièces, sans dépasser nettement la ligne « fort » des chartes objet. Pas de rareté <strong>unique</strong> sur un set, sauf cas de quête.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-panoply|Panoplies]]. Pour les plafonds par emplacement, utilise le tableau des [[kref:page:creation-equipements|équipements]].</p>',
                ],
            ],
        ],
        [
            'title' => 'Consommables',
            'slug' => 'creation-consommables',
            'icon' => 'fa-solid fa-flask',
            'menu_order' => 8,
            'sections' => [
                [
                    'slug' => 'creation-consommables-intro',
                    'title' => 'Créer un consommable',
                    'html' => '<h2>Consommables</h2>'
                        .'<p>En combat, un consommable coûte en général <strong>1 [[kref:characteristic:action_points_creature|PA]]</strong>. Deux effets du même type ne se cumulent pas : le meilleur gagne. Un parchemin de sortilège n’est détruit que si le sort réussit.</p>'
                        .'<p>Seuls les types cochés « visible en jeu » apparaissent dans le catalogue. Les chartes objet ci-dessous donnent les ordres de grandeur des bonus ; un consommable ponctuel doit rester sous un équipement permanent du même niveau.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-consumable|Consommables]].</p>',
                ],
                [
                    'slug' => 'creation-consommables-catalog',
                    'title' => 'Chartes objet',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'object',
                ],
            ],
        ],
        [
            'title' => 'Ressources',
            'slug' => 'creation-ressources',
            'icon' => 'fa-solid fa-gem',
            'menu_order' => 9,
            'sections' => [
                [
                    'slug' => 'creation-ressources-intro',
                    'title' => 'Créer une ressource',
                    'html' => '<h2>Ressources</h2>'
                        .'<p>Les ressources alimentent les métiers (récolte, artisanat, forgemagie). Elles portent surtout un type, un niveau, une rareté et un prix — rarement des bonus de combat. Les types hors catalogue (quêtes, souvenirs…) restent en base mais n’apparaissent pas dans les bibliothèques.</p>'
                        .'<p>Pas de grille de normes dédiée : l’équilibrage se joue sur le niveau, la rareté et le prix, alignés sur [[kref:page:les-metiers|Les métiers]].</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-resource|Ressources]].</p>',
                ],
            ],
        ],
        [
            'title' => 'États',
            'slug' => 'creation-etats',
            'icon' => 'fa-solid fa-skull',
            'menu_order' => 10,
            'sections' => [
                [
                    'slug' => 'creation-etats-intro',
                    'title' => 'Créer un état',
                    'html' => '<h2>États</h2>'
                        .'<p>Le catalogue JDR repose sur cinq états <strong>jouables</strong> : Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli. L’import Dofus crée des milliers de jetons en <strong>Brut</strong> : le catalogue les masque par défaut. Un sort doit pointer vers le noyau JDR, pas vers chaque jeton scrapé.</p>'
                        .'<p>Les flags mécaniques (ne pas être déplacé, invulnérable…) s’affichent en pastilles. On dit <strong>dissipable</strong>, plus « désenvoûtable ».</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-condition|États]]. Pas de charte de caractéristiques : un état se décrit par ses effets, pas par une grille niveau × puissance.</p>',
                ],
            ],
        ],
        [
            'title' => 'Traits',
            'slug' => 'creation-traits',
            'icon' => 'fa-solid fa-fingerprint',
            'menu_order' => 11,
            'sections' => [
                [
                    'slug' => 'creation-traits-intro',
                    'title' => 'Créer un trait',
                    'html' => '<h2>Traits</h2>'
                        .'<p>Un trait de créature est un avantage ou un handicap narratif / mécanique (résistance, particularité de race, don de classe). Il se rattache à une [[kref:page:creation-classes|classe]], une [[kref:page:creation-specialisations|spécialisation]] ou une créature.</p>'
                        .'<p>Pas de grille de normes : un trait ne doit pas recopier un bonus d’équipement permanent du même niveau. S’il donne une statistique, vérifie les chartes [[kref:page:creation-classes|créature]] pour rester sur la ligne neutre.</p>'
                        .'<p>Catalogue : [[kref:page:bibliotheque-creature-trait|Traits]].</p>',
                ],
            ],
        ],
    ],
];
