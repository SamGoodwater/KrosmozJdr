<?php

declare(strict_types=1);

/**
 * Guide de création / conversion : capacités (passifs de classe et dons de spé).
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
                    .'<p>Une capacité, on la <em>lance</em> ; une aptitude, on l’<em>a</em>. Chaque palier de spé (niveaux 1, 3, 6, 9, 12, 15, 20) donne 1 capacité garantie plus un emplacement libre ; les aptitudes tombent automatiquement aux niveaux 3, 9 et 15.</p>'
                    .'<p>Les 19 classes ont chacune un <strong>passif</strong> dès le niveau 1 (gratuit) et un moteur parmi cinq : zone, invocation, état perso, marque, tempo. Le moteur reste simple au 1 et s’ouvre au 7. Conversion Dofus : on ne recrée pas le kit MMO ; on écrit un passif lisible en trois paliers (1 / 7 / 11).</p>'
                    .'<p>Pas de grille de normes dédiée : pour des dégâts ou un coût, compare aux '.$k['kSortsPage'].', en restant plus faible qu’un sort actif du même niveau (c’est « toujours là »).</p>',
            ],
            [
                'id' => 'points',
                'title' => 'Points importants',
                'html' => '<h3>À respecter</h3>'
                    .'<ol>'
                    .'<li><strong>Décide le mode :</strong> passive (toujours), contextuelle (situation), réactive (déclencheur).</li>'
                    .'<li>Si ça ressemble à un sort (dégâts, zone, contrôle), c’est un sort — ou alors baisse nettement et ajoute une condition rare.</li>'
                    .'<li><strong>Un moteur, pas cinq.</strong> Fureur = +1 (puis +1d4 au 7), pas un dé empilé sur Concentration.</li>'
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
                    .'<li><strong>Fureur</strong> (Iop) — si tu as tapé depuis ton dernier tour, +1 dégât de mêlée de classe (pas un dé). Au 7 : +1d4.</li>'
                    .'<li><strong>Mot</strong> (Eniripsa) — un soin laisse 1 '.$k['pv'].' temporaire (pas de cumul). Soins en d4.</li>'
                    .'<li><strong>Glyphes</strong> (Féca) — zones visibles, limite 2, alliés ignorent tes dégâts de glyphe.</li>'
                    .'<li><strong>Pièges</strong> (Sram) — zones cachées, limite 2, Perception pour les voir.</li>'
                    .'<li><strong>Œil</strong> (Crâ) — +1 '.$po.' sur les sorts de classe, portée mini 2 sur les flèches.</li>'
                    .'</ul>'
                    .'<p>Catalogue : [[kref:page:bibliotheque-capability|Capacités]].</p>',
            ],
        ],
        'cms_extras' => [],
    ];
};
