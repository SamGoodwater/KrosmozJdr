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
        .'<p>Ces pages aident un MJ (ou un contributeur) à créer du contenu <strong>jouable</strong> pour Krosmoz : d’abord l’identité (« à quoi ça sert à la table ? »), ensuite les chiffres. Tu n’as pas besoin de connaître le code du site.</p>'
        .'<h3>Par où commencer ?</h3>'
        .'<ul>'
        .'<li><strong>Un sort de classe ou de monstre</strong> → [[kref:page:creation-sorts|Sorts]]</li>'
        .'<li><strong>Un monstre / une rencontre</strong> → [[kref:page:creation-monstres|Monstres]]</li>'
        .'<li><strong>Un PNJ</strong> → [[kref:page:creation-pnj|PNJ]] (classe + spécialisation + équipement)</li>'
        .'<li><strong>Une classe / une spe</strong> → [[kref:page:creation-classes|Classes]] · [[kref:page:creation-specialisations|Spécialisations]]</li>'
        .'<li><strong>Un objet, une conso, une ressource…</strong> → pages du même menu</li>'
        .'</ul>'
        .'<h3>Qui a quoi ?</h3>'
        .'<ul>'
        .'<li><strong>Monstre</strong> : niveau, caractéristiques, sorts courts (sans variantes de classe), capacités / traits possibles. Pas de classe ni de spécialisation.</li>'
        .'<li><strong>PNJ</strong> (et plus tard un PJ) : même base (niveau, caracs) + <em>classe</em> + <em>spécialisation</em> + équipements. Ses sorts viennent du grimoire de classe.</li>'
        .'</ul>'
        .'<h3>Comment lire une charte</h3>'
        .'<ol>'
        .'<li>Ouvre la caractéristique (PV, dégâts, Force…).</li>'
        .'<li>Choisis le <strong>niveau</strong> 1–20 de la fiche.</li>'
        .'<li>Pars de la ligne <strong>neutre</strong> (« normal » pour ce niveau).</li>'
        .'<li>Ajuste : un boss ou un sort à 5 '.$pa.' peut monter d’une ligne ; un sbire ou une large zone doit descendre.</li>'
        .'</ol>'
        .'<h3>États d’une fiche</h3>'
        .'<p><strong>Brouillon</strong> (tu travailles) → <strong>Auto</strong> (proposition à relire) → <strong>Jouable</strong> (visible des joueurs) → <strong>Archivé</strong>. Publie seulement quand le texte se lit à voix haute et que les chiffres tiennent. Livre : '.$kEquil.'. PDF : [[kref:page:ressources-mj|Ressources MJ]].</p>',

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
                        .'<p>Une classe de Krosmoz part de sa classe de Dofus. Elle garde le nom, l’univers et les sorts qu’on reconnaît, mais elle se joue autour d’une table : moins de sorts, de vrais choix, des effets qu’on lit en quelques secondes. '.$kClassesLivre.' · '.$kClassesEq.'.</p>'
                        .'<p>Une classe a un passif, trois capacités de classe (aux niveaux 3, 9 et 14), parfois une mécanique à elle (la pièce de l’Ecaflip), et <strong>12 choix de sorts</strong>. À chaque choix, le personnage prend une variante. Il finit avec 12 sorts.</p>'
                        .'<p>Elle a aussi <strong>quatre voies</strong>, une par élément : Feu, Terre, Eau, Air. Chaque voie a une orientation propre à la classe : dégâts, protection, soin, amélioration, entrave, placement… L’élément ne décide pas de l’orientation. Le Crâ entrave par l’Eau, l’Iop s’y protège.</p>'
                        .'<p>Les stats de combat vivent sur la créature liée (comme un [[kref:page:creation-monstres|monstre]]). Sur une fiche jouable, les liaisons masquent les brouillons.</p>',
                ],
                [
                    'slug' => 'creation-classes-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Convertir une classe de Dofus</h3>'
                        .'<ol>'
                        .'<li><strong>L’identité.</strong> Résume la classe en une phrase (« frappe fort au contact et protège les siens »), puis donne une orientation à chacune des quatre voies.</li>'
                        .'<li><strong>Trier ses sorts Dofus.</strong> D’un côté, ceux qui font des dégâts d’un élément. De l’autre, ceux qui ne frappent pas : déplacement, bouclier, bonus, contrôle. C’est l’élément et les dégâts qui rangent un sort, pas son effet. Un sort qui ne frappe pas ne va pas dans une voie, même si son effet lui irait bien.</li>'
                        .'<li><strong>Les sorts élémentaires.</strong> Chaque voie a quatre sorts, appris aux niveaux 1, 4, 7 et 12, du plus simple au plus fort. Ils viennent des sorts Dofus de son élément, et tous font des dégâts. Prends de préférence ceux dont l’effet suit déjà l’orientation (une repousse pour une voie de placement). Sinon, garde un sort qui frappe dans cet élément et change son effet pour qu’il colle à la voie. Les dégâts restent ceux de Dofus, l’effet peut s’en éloigner.</li>'
                        .'<li><strong>Les sorts généraux.</strong> Les autres choix offrent deux variantes chacun. On les prend dans les sorts Dofus qui ne frappent pas et n’ont pas d’élément marqué. Ils doivent servir à plusieurs voies, et les deux variantes d’un même choix ne font pas la même chose : l’une déplace, l’autre protège, par exemple. Le joueur choisit vraiment. Une exception se garde quand l’adaptation la justifie.</li>'
                        .'<li><strong>La progression.</strong> Un sort élémentaire grandit à quelques niveaux. Un sort général grandit peu, parfois pas du tout. Chaque sort a sa fiche, ligne par ligne.</li>'
                        .'<li><strong>Le reste.</strong> Passif, capacités et mécanique viennent ensuite. Calibre le dé de vie et les caractéristiques sur la ligne <strong>neutre</strong> des chartes au niveau 1, puis vérifie les niveaux 10 et 20.</li>'
                        .'<li>Lie sorts, capacités et traits <em>après</em> les avoir eux-mêmes équilibrés. Catalogue : [[kref:page:bibliotheque-breed|Classes]].</li>'
                        .'</ol>'
                        .'<p><strong>Exemple :</strong> chez l’Iop, Pression frappe en Terre : c’est un sort de la voie Terre. Bond ne frappe pas : c’est un sort général, même si la voie Air est une voie de placement.</p>'
                        .'<h3>Résolution et caractéristiques des sorts généraux</h3>'
                        .'<p>Un sort général se résout <strong>automatiquement</strong>, par la [[kref:characteristic:wisdom_creature|Sagesse]] ou par la [[kref:characteristic:vitality_creature|Vitalité]]. Quatre exceptions au plus, une par caractéristique de voie : [[kref:characteristic:strength_creature|Force]], [[kref:characteristic:agility_creature|Agilité]], '.$intel.' et '.$chance.'. On ne transforme jamais un sort automatique en jet juste pour y accrocher une caractéristique.</p>'
                        .'<p>On pose la résolution ou le bonus sur un sort qui a déjà une orientation, donc une voie. Un sort ne porte qu’une caractéristique. Deux variantes d’un même choix n’en portent pas la même. Sur l’ensemble des généraux, Force, Agilité, Intelligence, Chance, Sagesse et Vitalité reviennent le même nombre de fois, à un sort près. L’écart entre le premier et le dernier sort d’une caractéristique tient dans une huitaine de niveaux : on y pense avant d’écrire les effets.</p>'
                        .'<p>Le <strong>bonus de voie</strong> part d’une valeur fixe (ou du bonus de maîtrise). Tout le monde l’obtient. La caractéristique ajoute un petit avantage par-dessus — souvent un seuil (« +1 case si le modificateur d’Agilité vaut au moins 3 ») — ou sert d’effet principal (Tir perçant ajoute le modificateur de Terre). Un même sort peut aussi réagir à plusieurs caractéristiques : portée liée à la Force, franchissement d’obstacles lié à la Chance. Détail des conditions et des formules : [[kref:page:creation-sorts|Sorts]].</p>'
                        .'<p><strong>Pour relire :</strong> chaque sort élémentaire frappe dans l’élément de sa voie ; aucun sort général ne frappe sans raison ; deux variantes d’un même choix ne se ressemblent pas ; les six caractéristiques sont représentées à peu près à égalité sur les généraux.</p>'
                        .'<p><strong>À éviter :</strong> ranger un sort dans une voie pour son effet alors qu’il ne frappe pas ; reprendre tous les sorts de Dofus tels quels ; recopier une classe existante en +2 partout ; un passif qui vaut un sort de 5 '.$pa.' en continu ; trois généraux Force et zéro Chance.</p>',
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
                        .'<p><strong>Structure imposée</strong> : 7 paliers (niveaux 1, 3, 6, 9, 12, 15, 20). Chaque palier pose un choix entre les garanties, un choix entre les options (capacités, ou 1 trait dès le palier 12) et 1 compétence — 3 au palier 1, 2 seulement si la fiche l’écrit. Pas de points de caractéristique. Les paliers 3, 9 et 15 donnent en plus 1 aptitude automatique, soit 3 au total.</p>'
                        .'<p><strong>Fil rouge</strong> : une phrase qui tient toute la fiche. <strong>Archétypes</strong> (optionnels) : sous-choix au niveau 1 (outils, JS, compétences, teinte d’une mécanique signature). Modèle sans archétypes : [[kref:page:bibliotheque-specialization|Érudit]]. Modèle avec : <strong>Artiste</strong> (Musicien, Saltimbanque, Troubadour, Voyant). Règles : [[kref:page:regles-2-4-choisir-sa-specialisation|2.4.2]].</p>',
                ],
                [
                    'slug' => 'creation-specialisations-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li><strong>Fil rouge</strong> en une phrase (« tenir la salle », « tenir la ligne »). Tout ce qui ne sert pas ce fil saute.</li>'
                        .'<li>Décide si tu as besoin d’<strong>archétypes</strong> (2–4 max). Sinon, une seule liste de maîtrises au palier 1 suffit (modèle Érudit).</li>'
                        .'<li>Si oui : une <strong>mécanique signature</strong> payée en '.$pa.' de réserve (pas une 2ᵉ jauge), teintée par archétype ; numéros d’archétype surtout aux paliers 3, 9, 15.</li>'
                        .'<li>Paliers 1–3 : effets simples ; 6–9 modérés ; 12–15 puissants ; 20 exceptionnel — jamais un effet de palier 20 dès le 3.</li>'
                        .'<li>Écris <strong>2 garanties au palier 1</strong> (le joueur en prend une), puis 1 garantie ; et <strong>2 options à chaque palier</strong>. Liste fermée de <strong>5 compétences</strong> au palier 1 (en prendre 3) — une liste par archétype s’il y en a. Chaque capacité a un coût ('.$pa.', fréquence).</li>'
                        .'<li>Écris exactement <strong>3 aptitudes</strong>, aux paliers 3, 9 et 15 : passives ou contextuelles, gratuites, sans choix (voir [[kref:page:creation-capacites|Capacités]]).</li>'
                        .'<li>Inspire-toi de D&D si tu veux (inspiration, manœuvres…), mais <strong>réécris</strong> pour Dofus : court, compatible avec les sorts de classe.</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-specialization|Spécialisations]]. Relis à voix haute. Publie en jouable seulement si c’est clair.</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> une spé qui vaut une panoplie ; copier-coller un collège de barde ; archétypes qui cassent le gabarit à 7 paliers ; effets qui doublent un sort de classe.</p>',
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
                        .'<p>Un PNJ, c’est d’abord quelqu’un dans l’histoire : un nom, un rôle, un besoin. Les chiffres viennent ensuite. '.$kPnjMonstres.'.</p>'
                        .'<p>Pense-le comme un <strong>personnage</strong> (comme un futur PJ), pas comme un monstre :</p>'
                        .'<ul>'
                        .'<li>une <strong>classe</strong> → passif, capacités, sorts avec variantes ;</li>'
                        .'<li>une <strong>spécialisation</strong> → langues, aptitudes, métiers / savoir-faire… ;</li>'
                        .'<li>de l’<strong>équipement</strong> (parfois une panoplie), éventuellement ressources et consommables ;</li>'
                        .'<li>un <strong>niveau</strong> et des caractéristiques (comme un monstre).</li>'
                        .'</ul>'
                        .'<p>Un monstre n’a ni classe ni spe : ses sorts sont courts et sans variantes. Un aubergiste n’a pas besoin du kit complet d’un Iop 12 — tu poses la structure, tu remplis ce que la scène demande.</p>',
                ],
                [
                    'slug' => 'creation-pnj-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>En sept étapes</h3>'
                        .'<ol>'
                        .'<li><strong>Qui est-ce ?</strong> Nom, fonction, un trait de caractère, ce qu’iel veut.</li>'
                        .'<li><strong>Rôle à la table</strong> : social, marchand, garde, allié ou ennemi.</li>'
                        .'<li><strong>Classe + spécialisation</strong> jouables (voir [[kref:page:creation-classes|Classes]] et [[kref:page:creation-specialisations|Spécialisations]]).</li>'
                        .'<li><strong>Niveau</strong> ≈ la scène. Calibre les '.$pv.' et les dégâts sur la charte (bas de bande pour un social ; un cran sous un monstre équivalent pour un garde).</li>'
                        .'<li><strong>Équipement</strong> : objets jouables, 1 par emplacement (2 anneaux), niveau ≤ celui du PNJ. Une panoplie liée donne le thème, elle ne remplit pas les cases toute seule. Voir [[kref:page:creation-equipements|Équipements]].</li>'
                        .'<li><strong>Sorts</strong> : uniquement des sorts de <em>classe</em>. Un combattant en a plusieurs ; un social peut n’en avoir qu’un, ou aucun.</li>'
                        .'<li><strong>Relire</strong> à voix haute. Publier en jouable seulement si ça tient.</li>'
                        .'</ol>'
                        .'<p><strong>Exemples</strong> : Ganymède, Dathura, Tabach, Milicien d’Incarnam, Fouduglen.</p>'
                        .'<p><strong>À éviter :</strong> aubergiste stater comme un boss ; objet inventé hors catalogue ; sorts de monstre collés à un PNJ.</p>',
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
