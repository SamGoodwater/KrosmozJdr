<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : capacités (passifs et capacités de classe, dons de spé).
 *
 * @param  array<string, string>  $k
 * @return array<string, mixed>
 */
return static function (array $k): array {
    $pa = $k['pa'];
    $po = $k['po'];

    return [
        'entity' => 'capability',
        'title' => 'Capacités',
        'slug' => 'creation-capacites',
        'icon' => 'fa-solid fa-bolt',
        'menu_order' => 3,
        'ia_for_conversion' => true,
        'sections' => [
            [
                'id' => 'philosophie',
                'title' => 'Philosophie',
                'html' => '<h2>Capacités</h2>'
                    .'<p>Cette entité couvre trois choses : les <strong>passifs de classe</strong>, les <strong>capacités</strong> de spécialisation (actives, type sort, coût en '.$pa.' ou en Wakfu) et les <strong>aptitudes</strong> de spécialisation (passives ou contextuelles, gratuites). Le champ <code>is_passive</code> fait la différence. '.$k['kCapaLivre'].'.</p>'
                    .'<p>Une capacité, on la <em>lance</em> ; une aptitude, on l’<em>a</em>. Chaque palier de spé (niveaux 1, 3, 6, 9, 12, 15, 20) pose un choix entre les garanties puis un choix entre les options ; les aptitudes tombent automatiquement aux niveaux 3, 9 et 15. Pas de points de caractéristique.</p>'
                    .'<p>Une classe, c’est 12 sorts (avec variantes), une <strong>mécanique</strong> (champ <code>mechanics</code> de la classe, parfois très simple), un <strong>passif</strong> dès le niveau 1 et des <strong>capacités de classe</strong> apprises aux niveaux 3, 9 et 14 (niveaux sans nouveau sort). Le passif est un vrai passif, inspiré des traits de D&D : toujours actif, sans déclenchement à choisir (taille, sens, chute, peur…). Tout ce qui se déclenche « une fois par repos » est une capacité de classe, pas un passif. Gabarit validé : Crâ, Iop, Ecaflip.</p>'
                    .'<p>Pas de grille de normes dédiée : pour des dégâts ou un coût, compare aux '.$k['kSortsPage'].', en restant plus faible qu’un sort actif du même niveau (c’est « toujours là »).</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Décide le mode :</strong> passive (toujours), contextuelle (situation), réactive (déclencheur).</li>'
                    .'<li>Si ça ressemble à un sort (dégâts, zone, contrôle), c’est un sort — ou alors baisse nettement et ajoute une condition rare.</li>'
                    .'<li><strong>Passif ≠ capacité.</strong> Si le joueur choisit quand ça se déclenche, c’est une capacité de classe (ex. Fureur, Iop niveau 3 : une fois par repos court, +2 dégâts de mêlée de classe).</li>'
                    .'<li>Plafond de bonus de carac ≈ ligne neutre d’un <em>accessoire</em> du même niveau, pas d’une arme + panoplie.</li>'
                    .'<li>Les passifs de classe sont <code>is_passive</code>, liés à la classe, jouables. On ne les duplique pas en trait.</li>'
                    .'<li>Limite de zones / pièges / invocations : 2 au niveau 1, puis la table §3.3.1 (jusqu’à 7).</li>'
                    .'</ol>',
            ],
            [
                'id' => 'limites',
                'title' => 'Limites et propriétés',
                'html' => '<h3>Puissance</h3>'
                    .'<ul>'
                    .'<li>Niveau 1 : +1, 1 PV temp, 1 relance / combat, +1 '.$po.' — pas un 1d6 par tour.</li>'
                    .'<li>Niveau 7 : le moteur s’ouvre (2ᵉ invocation, balise, cartes optionnelles, glyphe qui soigne…).</li>'
                    .'<li>Niveau 11 : le chiffre monte d’un cran (1d4, 2 relances, +2 '.$po.'), pas un nouveau kit.</li>'
                    .'</ul>'
                    .'<h3>Propriétés de fiche</h3>'
                    .'<ul>'
                    .'<li><code>name</code>, <code>description</code> — une phrase d’identité.</li>'
                    .'<li><code>effect</code> — HTML court : palier 1, montée, moteur. C’est le texte de table.</li>'
                    .'<li><code>level</code> — niveau d’accès (1 pour le passif de classe).</li>'
                    .'<li><code>is_passive</code> — vrai pour les passifs de classe et les <strong>aptitudes</strong> de spé ; faux pour les <strong>capacités</strong>, qui se lancent.</li>'
                    .'<li><code>pa</code>, <code>po</code>, <code>po_editable</code> — seulement si ce n’est pas un pur passif.</li>'
                    .'<li><code>time_before_use_again</code>, <code>casting_time</code>, <code>duration</code>.</li>'
                    .'<li><code>element</code>, <code>is_magic</code>, <code>ritual_available</code>, <code>powerful</code>.</li>'
                    .'<li>Liaisons : <code>breed_capability</code> (classe), spécialisations. En lecture, les brouillons ne fuient pas.</li>'
                    .'</ul>',
            ],
            [
                'id' => 'conseils',
                'title' => 'Conseils',
                'html' => '<h3>Conseils</h3>'
                    .'<ul>'
                    .'<li>Écris le palier 1 pour qu’un joueur niveau 1 le joue sans fiche de MMO.</li>'
                    .'<li>Le passif ne doit pas valoir un sort de 5 '.$pa.' en continu.</li>'
                    .'<li>Si tu hésites entre capacité et sort : « est-ce que je le lance ? » → sort.</li>'
                    .'<li>Ne crée pas une capacité « je choisis l’effet au moment du besoin » sans limite.</li>'
                    .'</ul>'
                    .'<p><strong>À éviter :</strong> un passif équivalent à 5 '.$pa.' de dégâts chaque tour ; doubler le passif de classe avec un trait ; ouvrir le moteur MMO dès le niveau 1.</p>',
            ],
            [
                'id' => 'exemples',
                'title' => 'Exemples',
                'html' => '<h3>Étalons jouables (passifs de classe)</h3>'
                    .'<ul>'
                    .'<li><strong>Œil de Crâ</strong> (Crâ) — tes attaques à distance ignorent le désavantage d’un obstacle partiel ; avantage en Perception à la vue.</li>'
                    .'<li><strong>Tête brûlée</strong> (Iop) — insensible à Effrayé et Terrorisé ; une taille au-dessus pour Empoigner, Bousculer, porter.</li>'
                    .'<li><strong>Retomber sur ses pattes</strong> (Ecaflip) — pas de dégâts de chute jusqu’à 6 m ; jamais À terre après une chute ou une poussée.</li>'
                    .'</ul>'
                    .'<h3>Étalons jouables (capacités de classe)</h3>'
                    .'<ul>'
                    .'<li><strong>Fureur</strong> (Iop, niveau 3) — 1 fois par repos court, 0 '.$pa.' : +2 dégâts de mêlée de classe tant que tu frappes.</li>'
                    .'<li><strong>Flèche guidée par le Wakfu</strong> (Crâ, niveau 3) — 1 fois par repos court, +1 '.$pa.' : la flèche ignore la ligne de vue.</li>'
                    .'<li><strong>Pièce truquée</strong> (Ecaflip, niveau 3) — 1 fois par repos court, tu choisis la face de la pièce.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-capability|Capacités]].</p>',
            ],
        ],
        'cms_extras' => [],
    ];
};
