<?php

declare(strict_types=1);

use Database\Seeders\CreationPagesSeeder;

/**
 * Atelier MJ « Création » : guides de conception par type d’entité.
 *
 * Shortcodes {@code [[kref:…]]} convertis par {@see CreationPagesSeeder}.
 * Les chiffres viennent des chartes ; la philosophie reprend le livre (5.1.2, 5.2).
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
$kSortsEq = '[[kref:pageSection:regles-5-2-principes-dequilibrage@regle-5-2-3-sorts-et-aptitudes|Sorts et aptitudes]]';
$kEquipEq = '[[kref:pageSection:regles-5-2-principes-dequilibrage@regle-5-2-4-equipements-et-panoplies|Équipements et panoplies]]';
$kClassesLivre = '[[kref:page:regles-2-3-choisir-sa-classe|Choisir sa classe]]';
$kSpeLivre = '[[kref:page:regles-2-4-choisir-sa-specialisation|Choisir sa spécialisation]]';
$kCapaLivre = '[[kref:pageSection:regles-2-4-choisir-sa-specialisation@regle-2-4-4-capacites|Capacités]]';
$kSortsLivre = '[[kref:page:regles-3-3-sorts|Sorts]]';
$kTraitsLivre = '[[kref:pageSection:regles-3-2-combat@regle-3-2-5-traits-et-etats|Traits et états]]';
$kMetiers = '[[kref:page:les-metiers|Les métiers]]';
$kConsoLivre = '[[kref:page:regles-4-4-ressources-et-consommables|Ressources et consommables]]';
$kCaracs = '[[kref:page:caracteristiques|Caractéristiques]]';
$pa = '[[kref:characteristic:action_points_creature|PA]]';
$pm = '[[kref:characteristic:movement_points_creature|PM]]';
$pv = '[[kref:characteristic:life_points_creature|PV]]';
$ca = '[[kref:characteristic:armor_class_creature|CA]]';
$intel = '[[kref:characteristic:intelligence_creature|Intelligence]]';
$chance = '[[kref:characteristic:chance_creature|Chance]]';

return [
    'hub_intro' => '<h2>Atelier de création</h2>'
        .'<p>Ici tu conçois du contenu <strong>jouable</strong> : une identité (ce que la fiche fait à la table), puis des chiffres qui collent aux chartes. Les tableaux de cette section sont une <strong>projection</strong> du système de caractéristiques : si un chiffre est faux, on corrige la fiche ou la définition de caractéristique, pas une grille morte sur la page.</p>'
        .'<h3>Comment lire une charte</h3>'
        .'<ol>'
        .'<li>Ouvre la caractéristique (PV, dégâts, Force…).</li>'
        .'<li>Choisis le <strong>niveau</strong> 1–20 de la fiche.</li>'
        .'<li>Pars de la ligne <strong>neutre</strong> (créature / sort / objet « normal » pour ce niveau).</li>'
        .'<li>Active les <strong>régulateurs</strong> qui correspondent (coût en '.$pa.', zone, rareté…) : la valeur recommandée se décale d’une ligne de puissance ou d’une colonne de niveau.</li>'
        .'</ol>'
        .'<p>Très faible → faible → neutre → fort → très fort. Un boss, un unique ou un sort à 5 '.$pa.' peut monter d’une ligne ; un sbire, un commun ou un sort en large zone doit descendre.</p>'
        .'<h3>États d’une fiche</h3>'
        .'<p><strong>Brut</strong> (import) → <strong>Brouillon</strong> (tu travailles) → <strong>Auto</strong> (proposition à relire) → <strong>Jouable</strong> → <strong>Archivé</strong>. Les joueurs ne voient que le jouable. Publie seulement quand identité + chiffres tiennent. Livre : '.$kEquil.'.</p>',

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
                        .'<p>La spé <strong>oriente</strong> une [[kref:page:creation-classes|classe]] : elle ne la remplace pas. Aptitudes, capacités, parfois objets ou sorts liés. Un PJ « dans le rôle » de la spé est au maximum ; un PJ qui l’ignore reste jouable, juste plus faible dans ce registre. '.$kSpeLivre.' · '.$kClassesEq.'.</p>',
                ],
                [
                    'slug' => 'creation-specialisations-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Nomme le fantasy (« tank sacré », « piégeur à distance ») en lien avec la classe parente.</li>'
                        .'<li>Niveaux 1–5 : effets simples ; 6–10 modérés ; 11–15 puissants ; 16–20 exceptionnels — jamais un passif de palier 16 dès le niveau 3.</li>'
                        .'<li>Chaque aptitude a un coût ('.$pa.', Wakfu, fréquence). Une capacité est plutôt passive / contextuelle (voir [[kref:page:creation-capacites|Capacités]]).</li>'
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
        [
            'title' => 'Sorts',
            'slug' => 'creation-sorts',
            'icon' => 'fa-solid fa-wand-sparkles',
            'menu_order' => 2,
            'sections' => [
                [
                    'slug' => 'creation-sorts-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>Sorts</h2>'
                        .'<p>Un sort a <strong>une job</strong> : dégâts, soin, contrôle, placement ou utilité. Le coût en '.$pa.' / '.$pm.' / Wakfu paie la puissance ; la fréquence (cooldown, 1/combat) empêche les combos abusifs. '.$kSortsEq.' · '.$kSortsLivre.'.</p>'
                        .'<p>Les états infligés pointent vers les cinq états JDR jouables (Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli), pas vers chaque jeton Dofus en Brut — [[kref:page:creation-etats|États]].</p>',
                ],
                [
                    'slug' => 'creation-sorts-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Fixe le niveau du sort et son rôle (une phrase).</li>'
                        .'<li>Choisis un coût : action simple 3–4 '.$pa.' ; forte 5 '.$pa.' (souvent une seule par tour) ; bonus 2 '.$pa.' (3 si l’effet est important).</li>'
                        .'<li>Lis la charte de dégâts / soins / bouclier : ligne neutre au niveau, puis régulateurs (5+ '.$pa.' → +1 puissance ; zone large → −1 par cible).</li>'
                        .'<li>Borne les durées : plus c’est fort, plus c’est court. Contrôle (entrave, stun) = sauvegarde + durée limitée.</li>'
                        .'<li>Si c’est trop fort à chaque tour, ajoute un cooldown (1–3 / 4–6 / 7–10 tours) ou une limite par combat.</li>'
                        .'</ol>'
                        .'<h3>Ordres de grandeur (dégâts / soins)</h3>'
                        .'<table><thead><tr><th>Niveau</th><th>Dégâts</th><th>Soins</th></tr></thead><tbody>'
                        .'<tr><td>1–5</td><td>1d6+mod à 2d6+mod</td><td>1d4+mod à 2d4+mod</td></tr>'
                        .'<tr><td>6–10</td><td>2d6+mod à 3d6+mod</td><td>2d4+mod à 3d4+mod</td></tr>'
                        .'<tr><td>11–15</td><td>3d6+mod à 4d6+mod</td><td>3d4+mod à 4d4+mod</td></tr>'
                        .'<tr><td>16–20</td><td>4d6+mod à 5d6+mod</td><td>4d4+mod à 5d4+mod</td></tr>'
                        .'</tbody></table>'
                        .'<p>Ouvre une charte ci-dessous, choisis le niveau, active les régulateurs, compare à ta fiche. Catalogue : [[kref:page:bibliotheque-spell|Sorts]].</p>'
                        .'<p><strong>À éviter :</strong> dégâts de sort fort + large zone + pas de cooldown ; un contrôle sans sauvegarde ; un état Dofus brut à la place d’un état JDR.</p>',
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
                    'title' => 'Philosophie',
                    'html' => '<h2>Capacités</h2>'
                        .'<p>Une capacité est un <strong>avantage passif ou contextuel</strong> : elle tourne toute seule (ou se déclenche), en général sans dépenser de '.$pa.'. Une aptitude / un sort, toi tu les lances. À chaque palier de spé on choisit souvent aptitude <em>ou</em> capacité. '.$kCapaLivre.'.</p>'
                        .'<p>Pas de grille de normes dédiée : pour des dégâts ou un coût, compare aux [[kref:page:creation-sorts|chartes de sorts]], en restant plus faible qu’un sort actif du même niveau (c’est « toujours là »).</p>',
                ],
                [
                    'slug' => 'creation-capacites-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Décide : passive (toujours), contextuelle (situation), réactive (déclencheur).</li>'
                        .'<li>Si ça ressemble à un sort (dégâts, zone, contrôle), c’est probablement un sort — ou alors baisse nettement la puissance et ajoute une condition rare.</li>'
                        .'<li>Plafonds : un bonus de caractéristique ≈ ligne neutre d’un accessoire du même niveau, pas d’une arme + panoplie.</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-capability|Capacités]].</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> un passif équivalent à 5 '.$pa.' de dégâts chaque tour ; une capacité « je choisis l’effet au moment du besoin » sans limite.</p>',
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
                    'title' => 'Philosophie',
                    'html' => '<h2>Monstres</h2>'
                        .'<p>Un monstre est une <strong>rencontre</strong> : danger, rythme, butin — pas un PJ. La fiche publique (Monster) porte nom, race, image ; les stats, sorts et équipements sont sur la <strong>créature</strong> liée. '.$kPnjMonstres.'.</p>'
                        .'<p>Sur ce site, la référence chiffrée est la <strong>charte</strong> (ligne neutre au niveau du groupe). Le livre propose aussi des gabarits rapides ; s’il y a écart, tu suis la charte, puis tu ajustes d’une ligne selon le rôle (sbire / élite / boss).</p>',
                ],
                [
                    'slug' => 'creation-monstres-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Niveau ≈ niveau du groupe. Rôle : sbire (très faible / faible), standard (neutre), élite (fort), boss (très fort + mécaniques).</li>'
                        .'<li>Remplis le gabarit : 6 caracs, '.$pv.', '.$ca.', '.$pa.' / '.$pm.', 1–3 attaques ou sorts, éventuellement un passif lisible en une ligne.</li>'
                        .'<li>Pour chaque stat importante, ouvre la charte, niveau du monstre, ligne du rôle. Un sbire n’a pas les '.$pv.' d’un boss.</li>'
                        .'<li>Dégâts d’attaque alignés sur les mêmes bandes que les sorts (1d6+mod … 5d6+mod selon le palier). Un boss : +50 à 100 % de dégâts <em>ou</em> des phases, pas les deux à fond.</li>'
                        .'<li>Résistances : un élément fort, souvent une vulnérabilité opposée. Pas six immunités.</li>'
                        .'<li>Butin : 1–2 ressources du thème, rareté du palier. Catalogue : [[kref:page:bibliotheque-monster|Monstres]].</li>'
                        .'</ol>'
                        .'<h3>Rôles et puissance</h3>'
                        .'<ul>'
                        .'<li><strong>Sbire</strong> — meurt vite, menace si nombreux ; ligne faible ; peu de sorts.</li>'
                        .'<li><strong>Standard</strong> — un pour un PJ à peu près ; ligne neutre.</li>'
                        .'<li><strong>Élite</strong> — deux ou trois PJ ; ligne fort ; 1 capacité signature.</li>'
                        .'<li><strong>Boss</strong> — table entière ; ligne très fort ; phases (100–50 %, 50–25 %, 25–0 %), éventuellement sbires ou terrain — voir '.$kPnjMonstres.'. Coche <strong>boss</strong> et inscris les <strong>PA légendaires</strong> (pool hors tour, recharge en fin de tour du boss).</li>'
                        .'</ul>'
                        .'<p><strong>À éviter :</strong> un « loup niveau 3 » avec les '.$pv.' d’un boss 10 ; copier un PJ (24 sorts, panoplie) ; tout en Neutre sans identité élémentaire.</p>',
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
        [
            'title' => 'Équipements',
            'slug' => 'creation-equipements',
            'icon' => 'fa-solid fa-shield-halved',
            'menu_order' => 6,
            'sections' => [
                [
                    'slug' => 'creation-equipements-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>Équipements</h2>'
                        .'<p>Un objet occupe <strong>un emplacement</strong> et pousse <strong>un axe</strong> (dégâts, '.$ca.', une carac). Huit slots se cumulent : si chaque pièce est « très fort », le PJ explose les plafonds. '.$kEquipEq.' · '.$kCaracs.'.</p>'
                        .'<p>Le tableau vivant ci-dessous projette les plafonds par emplacement et par caractéristique (formules objet). Un tiret = pas encore débloqué. On corrige la caractéristique si un chiffre cloche, pas cette page.</p>',
                ],
                [
                    'slug' => 'creation-equipements-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Type (arme, chapeau, cape…) + niveau. Le type doit être « visible en jeu » pour apparaître en bibliothèque.</li>'
                        .'<li>Bonus par palier : niv. 1–5 (+1–2) · 6–10 (+2–3) · 11–15 (+3–4) · 16–20 (+4–5). Arme → dégâts ; armure → '.$ca.' ; accessoire → une caractéristique.</li>'
                        .'<li>Compare au tableau des plafonds (même slot, même bande de niveau) puis à la charte objet, ligne neutre. Rare / légendaire : ligne fort, pas très fort sur trois stats à la fois.</li>'
                        .'<li>La rareté suit le <strong>prix</strong> dans la tranche, pas le nombre de lignes de bonus (détail plus bas).</li>'
                        .'<li>Effet spécial : un seul, et tu baisses un bonus numérique. Catalogue : [[kref:page:bibliotheque-item|Équipements]].</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> +5 partout dès le niveau 8 ; Unique hors Dofus / quête ; un accessoire qui copie une arme.</p>',
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
        ],
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
        [
            'title' => 'Consommables',
            'slug' => 'creation-consommables',
            'icon' => 'fa-solid fa-flask',
            'menu_order' => 8,
            'sections' => [
                [
                    'slug' => 'creation-consommables-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>Consommables</h2>'
                        .'<p>C’est un <strong>coup de pouce ponctuel</strong>, pas un équipement que tu bois. En combat : en général <strong>1 '.$pa.'</strong>. Même type d’effet : pas de cumul, le meilleur gagne. Types différents : cumul OK. '.$kConsoLivre.'.</p>',
                ],
                [
                    'slug' => 'creation-consommables-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Un effet (soin, buff, antidote). Durée courte en combat.</li>'
                        .'<li>Bonus inférieurs à un [[kref:page:creation-equipements|équipement]] permanent du même niveau (souvent une ligne en dessous sur la charte objet).</li>'
                        .'<li>Parchemin de sortilège : détruit seulement si le sort réussit.</li>'
                        .'<li>Type « visible en jeu ». Catalogue : [[kref:page:bibliotheque-consumable|Consommables]].</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> potion = cape permanente ; deux potions de Force qui se stackent ; consommable sans coût d’action en combat.</p>',
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
                    'title' => 'Philosophie',
                    'html' => '<h2>Ressources</h2>'
                        .'<p>Une ressource nourrit un <strong>métier</strong>, pas un build. Type, niveau, rareté, prix — rarement un bonus de combat. '.$kMetiers.' · '.$kConsoLivre.'.</p>',
                ],
                [
                    'slug' => 'creation-ressources-methode',
                    'title' => 'Comment la créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Branche métier (récolte / artisanat / rune) et bande de niveau (1–4, 5–8, … 17–20 pour la récolte).</li>'
                        .'<li>Rareté alignée sur ce que le métier peut produire à ce palier. Unique : pas craft, c’est de la quête.</li>'
                        .'<li>Prix cohérent avec les ressources voisines du même palier. Pas de charte de combat : l’économie suffit.</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-resource|Ressources]]. Les types hors jeu (quêtes, souvenirs…) restent en base mais hors bibliothèques.</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> une ressource « +3 Force » ; un minerai niveau 2 au prix d’un légendaire 18.</p>',
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
        [
            'title' => 'Traits',
            'slug' => 'creation-traits',
            'icon' => 'fa-solid fa-fingerprint',
            'menu_order' => 11,
            'sections' => [
                [
                    'slug' => 'creation-traits-intro',
                    'title' => 'Philosophie',
                    'html' => '<h2>Traits</h2>'
                        .'<p>Un trait est <strong>permanent</strong> (Lourd, petite taille, vol…) : identité de race, de classe ou de monstre, pas un buff de combat. Les états, eux, sont temporaires. '.$kTraitsLivre.'.</p>',
                ],
                [
                    'slug' => 'creation-traits-methode',
                    'title' => 'Comment le créer',
                    'html' => '<h3>Marche à suivre</h3>'
                        .'<ol>'
                        .'<li>Une phrase d’identité (« ne peut pas être déplacé », « voit dans le noir »).</li>'
                        .'<li>Si ça donne une stat, reste ligne neutre créature, et ce n’est pas un duplicata d’équipement du même niveau.</li>'
                        .'<li>Un monstre : 0–2 traits lisibles. Une classe : le passif suffit souvent ; le trait ne le double pas.</li>'
                        .'<li>Catalogue : [[kref:page:bibliotheque-creature-trait|Traits]].</li>'
                        .'</ol>'
                        .'<p><strong>À éviter :</strong> trait = cape +2 permanente ; cinq traits de combat sur un sbire.</p>',
                ],
            ],
        ],
    ],
];
