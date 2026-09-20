<?php

declare(strict_types=1);

use Database\Seeders\CreationPagesSeeder;

/**
 * Atelier MJ « Création » : guides de conception par type d’entité.
 *
 * Shortcodes {@code [[kref:…]]} convertis par {@see CreationPagesSeeder}.
 * Sorts, monstres, équipements, conso, capacités, traits et ressources :
 * source unique {@code resources/ia/creation-guides/} (CMS + prompt de conversion).
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
$kEquil = '[[kref:page:regles-5-2-principes-dequilibrage|Équilibrage]]';
$kPnjMonstres = '[[kref:pageSection:regles-5-1-ressources-mj@regle-5-1-2-creation-de-pnj-et-monstres|Création de PNJ et monstres]]';
$kClassesEq = '[[kref:pageSection:regles-5-2-principes-dequilibrage@regle-5-2-2-classes-et-specialisations|Classes et spécialisations]]';
$kSortsEq = '[[kref:pageSection:regles-5-2-principes-dequilibrage@regle-5-2-3-sorts-et-capacites|Sorts et capacités]]';
$kEquipEq = '[[kref:pageSection:regles-5-2-principes-dequilibrage@regle-5-2-4-equipements-et-panoplies|Équipements et panoplies]]';
$kClassesLivre = '[[kref:page:regles-2-3-choisir-sa-classe|Choisir sa classe]]';
$kSpeLivre = '[[kref:page:regles-2-4-choisir-sa-specialisation|Choisir sa spécialisation]]';
$kTraitsLivre = '[[kref:pageSection:regles-3-2-combat@regle-3-2-5-traits-et-etats|Traits et états]]';
$pa = '[[kref:characteristic:action_points_creature|PA]]';
$pv = '[[kref:characteristic:life_points_creature|PV]]';
$ca = '[[kref:characteristic:armor_class_creature|CA]]';
$intel = '[[kref:characteristic:intelligence_creature|Intelligence]]';
$chance = '[[kref:characteristic:chance_creature|Chance]]';

/**
 * @param  array<string, mixed>  $guide
 * @return array{title: string, slug: string, icon: string, menu_order: int, sections: list<array<string, mixed>>}
 */
$toCmsPage = static function (array $guide): array {
    $slug = (string) ($guide['slug'] ?? '');
    $sections = [];
    foreach ($guide['sections'] ?? [] as $section) {
        if (! is_array($section)) {
            continue;
        }
        $id = (string) ($section['id'] ?? '');
        if ($id === '') {
            continue;
        }
        $sections[] = [
            'slug' => $slug.'-'.$id,
            'title' => (string) ($section['title'] ?? $id),
            'html' => (string) ($section['html'] ?? ''),
        ];
    }
    foreach ($guide['cms_extras'] ?? [] as $extra) {
        if (is_array($extra)) {
            $sections[] = $extra;
        }
    }

    return [
        'title' => (string) ($guide['title'] ?? $slug),
        'slug' => $slug,
        'icon' => (string) ($guide['icon'] ?? ''),
        'menu_order' => (int) ($guide['menu_order'] ?? 0),
        'sections' => $sections,
    ];
};

$iaGuides = require resource_path('ia/creation-guides/index.php');
if (! is_array($iaGuides)) {
    $iaGuides = [];
}

return [
    'hub_intro' => '<h2>Atelier de création</h2>'
        .'<p>Ici tu conçois du contenu <strong>jouable</strong> : une identité (ce que la fiche fait à la table), puis des chiffres qui collent aux chartes. Les tableaux de cette section sont une <strong>projection</strong> du système de caractéristiques : si un chiffre est faux, on corrige la fiche ou la définition de caractéristique, pas une grille morte sur la page.</p>'
        .'<p>Sorts, monstres, équipements, consommables, capacités, traits et ressources suivent le même canevas : <strong>philosophie</strong>, <strong>points importants</strong>, <strong>limites et propriétés</strong>, <strong>conseils</strong>, <strong>exemples</strong>. C’est aussi le texte que Laravel enverra à l’IA de conversion (sans l’appeler encore) : <code>php artisan ia:creation-guides</code>.</p>'
        .'<h3>Comment lire une charte</h3>'
        .'<ol>'
        .'<li>Ouvre la caractéristique (PV, dégâts, Force…).</li>'
        .'<li>Choisis le <strong>niveau</strong> 1–20 de la fiche.</li>'
        .'<li>Pars de la ligne <strong>neutre</strong> (créature / sort / objet « normal » pour ce niveau).</li>'
        .'<li>Active les <strong>régulateurs</strong> qui correspondent (coût en '.$pa.', zone, rareté…) : la valeur recommandée se décale d’une ligne de puissance ou d’une colonne de niveau.</li>'
        .'</ol>'
        .'<p>Très faible → faible → neutre → fort → très fort. Un boss, un unique ou un sort à 5 '.$pa.' peut monter d’une ligne ; un sbire, un commun ou un sort en large zone doit descendre.</p>'
        .'<h3>États d’une fiche</h3>'
        .'<p><strong>Brut</strong> (import) → <strong>Brouillon</strong> (tu travailles) → <strong>Auto</strong> (proposition à relire) → <strong>Jouable</strong> → <strong>Archivé</strong>. Les joueurs ne voient que le jouable. Publie seulement quand identité + chiffres tiennent. Livre : '.$kEquil.'. PDF d’atelier : [[kref:page:ressources-mj|Ressources MJ]].</p>',

    'pages' => [
        [
            'title' => 'Classes',
            'slug' => 'creation-classes',
            'icon' => 'fa-solid fa-people-group',
            'menu_order' => 0,
            'sections' => [
                [
                    'slug' => 'creation-classes-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>Classes</h2>'
                        .'<p>Une classe est une <strong>voie</strong>, pas un tas de bonus. Elle fixe 24 sorts (12 appris au niveau 20), un passif unique, un dé de vie et 3 rôles possibles parmi dégâts, protection, soin, amélioration, entrave, placement. '.$kClassesLivre.' · '.$kClassesEq.'.</p>'
                        .'<p>Les stats de combat vivent sur la créature liée (comme un [[kref:page:creation-monstres|monstre]]). Sur une fiche jouable, les liaisons masquent les brouillons.</p>',
                ],
                [
                    'slug' => 'creation-classes-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Écris l’identité en une phrase (ex. « frappe fort au contact, peu de contrôle »).</li>'
                        .'<li>Choisis <strong>un rôle fort</strong> et un rôle secondaire — pas six à fond. Une classe qui tank, soigne et dps autant qu’un spécialiste casse le groupe.</li>'
                        .'<li>Répartis les sorts sur des <strong>voies élémentaires</strong> cohérentes (Feu agressif, Eau soin/contrôle, Terre protection, Air mobilité). Neutre = polyvalence, pas « tout faire ».</li>'
                        .'<li>Calibre le dé de vie et les caracs sur la ligne <strong>neutre</strong> des chartes au niveau 1, puis vérifie un palier 10 et 20.</li>'
                        .'<li>Lie sorts, capacités et traits <em>après</em> les avoir eux-mêmes équilibrés. Catalogue : [[kref:page:bibliotheque-breed|Classes]].</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> recopier une classe existante en +2 partout ; un passif qui vaut un sort de 5 '.$pa.' en continu.</p>',
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
                    'title' => 'Philosophie',
                    'html' => '<h2>Spécialisations</h2>'
                        .'<p>La spé <strong>oriente</strong> une [[kref:page:creation-classes|classe]] : elle ne la remplace pas. Capacités, aptitudes, compétences, parfois métiers ou objets liés. Un PJ « dans le rôle » de la spé est au maximum ; un PJ qui l’ignore reste jouable, juste plus faible dans ce registre. '.$kSpeLivre.' · '.$kClassesEq.'.</p>'
                        .'<p><strong>Structure imposée</strong> : 7 paliers (niveaux 1, 3, 6, 9, 12, 15, 20). Chaque palier donne 1 capacité garantie, 1 emplacement libre (2ᵉ capacité proposée, +2 points de carac, ou 1 trait dès le palier 12) et 1 compétence — 3 au palier 1, 2 seulement si la fiche l’écrit. Les paliers 3, 9 et 15 donnent en plus 1 aptitude automatique, soit 3 au total. Gabarit : [[kref:page:regles-2-4-choisir-sa-specialisation|2.4.2]]. Intensification des sorts : [[kref:page:regles-3-3-sorts|3.3.6]].</p>',
                ],
                [
                    'slug' => 'creation-specialisations-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Nomme le fantasy (« tank sacré », « piégeur à distance ») en lien avec la classe parente.</li>'
                        .'<li>Paliers 1–3 : effets simples ; 6–9 modérés ; 12–15 puissants ; 20 exceptionnel — jamais un effet de palier 20 dès le palier 3.</li>'
                        .'<li>Écris <strong>1 à 2 capacités par palier</strong> : la première est garantie, la seconde n’est proposée que si l’emplacement libre y est dépensé. Chaque capacité a un coût ('.$pa.', Wakfu, fréquence).</li>'
                        .'<li>Écris exactement <strong>3 aptitudes</strong>, aux paliers 3, 9 et 15 : passives ou contextuelles, gratuites, sans choix (voir [[kref:page:creation-capacites|Capacités]]).</li>'
                        .'<li>Les bonus de caractéristiques restent sur la ligne neutre des chartes, éventuellement <em>fort</em> sur <strong>une</strong> stat du rôle, pas sur toutes.</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-specialization|Spécialisations]]. En lecture, les liaisons brouillon ne fuient pas.</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> une spé qui donne l’équivalent d’une panoplie complète ; deux spés de la même classe au même niveau de puissance sur les mêmes axes.</p>',
                ],
                [
                    'slug' => 'creation-specialisations-catalog',
                    'title' => 'Chartes créature',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'creature',
                ],
            ],
        ],
        $toCmsPage($iaGuides['spell'] ?? []),
        $toCmsPage($iaGuides['capability'] ?? []),
        $toCmsPage($iaGuides['monster'] ?? []),
        [
            'title' => 'PNJ',
            'slug' => 'creation-pnj',
            'icon' => 'fa-solid fa-user',
            'menu_order' => 5,
            'sections' => [
                [
                    'slug' => 'creation-pnj-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>PNJ</h2>'
                        .'<p>Le PNJ sert d’abord le <strong>récit</strong> : nom, fonction, un trait, un besoin. Les chiffres viennent après. Les stats de combat sont les mêmes briques qu’un [[kref:page:creation-monstres|monstre]], mais tu ne calibres un combattant que s’il peut vraiment se battre. '.$kPnjMonstres.'.</p>'
                        .'<p>Rôle obligatoire : social, marchand, garde, allié ou ennemi (autre = filet). Classe <strong>et</strong> spécialisation : une spe <em>jouable</em> seulement (Artiste, Dévot, Érudit, Explorateur, Milicien, Voleur). Les 19 classes restent en brouillon : le MJ les voit à l’édition ; un visiteur ne voit pas la classe sur la fiche publique. Niveau ≈ la scène, pas un unique 20 hors quête.</p>',
                ],
                [
                    'slug' => 'creation-pnj-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li><strong>Identité</strong> : nom, fonction, un trait, un besoin.</li>'
                        .'<li><strong>Rôle</strong> : social ('.$chance.', Persuasion, Perspicacité), marchand ('.$intel.', négoce), garde / allié / ennemi (combat).</li>'
                        .'<li><strong>Classe et spe</strong> : rattache une [[kref:page:creation-classes|classe]] et une [[kref:page:creation-specialisations|spécialisation]] jouable. Pas Négociant / Sylvain / Marin / Courtisan tant qu’ils sont brouillon.</li>'
                        .'<li><strong>Gabarit</strong> (niv. 1–5 : 20–50 '.$pv.', 1d6+mod à 2d6+mod). Social / marchand = bas de bande, combat minimal. Garde / allié combattant / ennemi = mêmes briques qu’un monstre, souvent une ligne en dessous — pas un boss.</li>'
                        .'<li><strong>Kit</strong> : objets [[kref:page:bibliotheque-item|jouables]], 1 par emplacement (2 anneaux), niveau ≤ celui du PNJ, voie cohérente avec la classe. Une [[kref:page:creation-panoplies|panoplie]] liée pose le thème, elle ne remplit pas les slots. Catalogue : [[kref:page:creation-equipements|Équipements]].</li>'
                        .'<li><strong>Sorts</strong> : 1–3 sorts de <em>classe</em> jouables, niveau perso ≤ niveau du PNJ. Un social peut n’en avoir qu’un, ou zéro. Pas de sorts-créature de bestiaire.</li>'
                        .'</ol>'
                        .'<p>Étalons : Ganymède, Dathura, Tabach, le Milicien d’Incarnam, Fouduglen (fiches jouables d’Incarnam).</p>'
                        .'<p><strong>À éviter :</strong> stater un aubergiste comme un Iop 12 ; panoplie légendaire / unique hors quête ; spe encore brouillon ; inventer un objet hors catalogue.</p>',
                ],
                [
                    'slug' => 'creation-pnj-catalog',
                    'title' => 'Chartes créature',
                    'template' => 'characteristic_norms_catalog',
                    'group' => 'creature',
                ],
            ],
        ],
        $toCmsPage($iaGuides['item'] ?? []),
        [
            'title' => 'Panoplies',
            'slug' => 'creation-panoplies',
            'icon' => 'fa-solid fa-layer-group',
            'menu_order' => 7,
            'sections' => [
                [
                    'slug' => 'creation-panoplies-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>Panoplies</h2>'
                        .'<p>Le set récompense le <strong>thème</strong> (2 pièces, puis 3, etc.), pas le droit de dépasser tous les plafonds. Les bonus de set + pièces restent sous la ligne « fort » des chartes objet, et sous les caps du livre (ordre de grandeur : +10 '.$ca.' / +10 dégâts / +5 par carac toutes sources). '.$kEquipEq.'.</p>',
                ],
                [
                    'slug' => 'creation-panoplies-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Équilibre d’abord chaque [[kref:page:creation-equipements|pièce]] seule (un PJ peut porter une seule pièce).</li>'
                        .'<li>2p : petit bonus de synchro. 3p+ : l’identité du set (un effet, pas trois).</li>'
                        .'<li>En lecture, seules les pièces visibles apparaissent ; un objet brouillon ne fuit pas via un set jouable.</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-panoply|Panoplies]].</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> set Unique ; 2p déjà au plafond global ; pièces faibles « parce que le set compensera ».</p>',
                ],
            ],
        ],
        $toCmsPage($iaGuides['consumable'] ?? []),
        $toCmsPage($iaGuides['resource'] ?? []),
        [
            'title' => 'États',
            'slug' => 'creation-etats',
            'icon' => 'fa-solid fa-skull',
            'menu_order' => 10,
            'sections' => [
                [
                    'slug' => 'creation-etats-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>États</h2>'
                        .'<p>Un état JDR est une <strong>condition de table</strong> (durée, sauvegarde, dissipable), pas un jeton Dofus. Cinq fiches jouables : Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli. Le scrap crée des milliers de Brut, masqués par défaut. '.$kTraitsLivre.' · '.$kSortsEq.'.</p>',
                ],
                [
                    'slug' => 'creation-etats-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Avant d’en inventer un : un des cinq noyaux suffit-il ? Si oui, le sort pointe vers lui.</li>'
                        .'<li>Sinon : un effet mécanique clair, durée courte, jet de sauvegarde, dissipable ou non (on dit <strong>dissipable</strong>).</li>'
                        .'<li>Stun / gel / brûlure : jamais sans sortie (sauvegarde, immunité après application, durée 1–2 tours).</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-condition|États]]. Pas de charte niveau × puissance.</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> publier un Brut scrapé ; un état « plus d’actions, pas de save, 10 tours ».</p>',
                ],
            ],
        ],
        $toCmsPage($iaGuides['trait'] ?? []),
    ],
];
