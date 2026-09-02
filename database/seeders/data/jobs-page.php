<?php

declare(strict_types=1);
use App\Support\Cms\KrefShortcodeReplacer;
use Database\Seeders\PageSeeder;

/**
 * Contenu de la page CMS « Les métiers » (groupe Bibliothèques).
 *
 * Page documentaire : elle explique le système de métiers et en donne la liste
 * illustrée. Elle n'a pas de tableau d'entités (il n'existe pas d'entité métier
 * en base), mais elle embarque le tableau vivant des runes de forgemagie.
 *
 * Aligné sur `private/game/rules/4-Le-monde-des-douze/4.3-les-metiers`.
 * Shortcodes {@code [[kref:…]]} convertis par {@see PageSeeder}
 * via {@see KrefShortcodeReplacer}.
 *
 * Les icônes vivent dans `storage/app/public/images/jobs/` et sont servies
 * depuis `/storage/images/jobs/`.
 *
 * @return array{
 *   title: string,
 *   slug: string,
 *   menu_order: int,
 *   icon: string|null,
 *   sections: list<array{slug: string, title: string, template: string, html?: string, settings?: array<string, mixed>}>
 * }
 */

/**
 * Construit la grille HTML d'une liste de métiers.
 *
 * @param  list<array{name: string, image: string, characteristic: string, description: string}>  $jobs
 */
$grid = static function (array $jobs): string {
    $cards = array_map(static function (array $job): string {
        return '<div class="flex items-start gap-3 rounded-box border border-base-content/20 bg-base-100/40 p-3">'
            .'<img src="/storage/images/jobs/'.$job['image'].'.webp" alt="'.$job['name'].'" width="50" height="50" loading="lazy" class="shrink-0" />'
            .'<div>'
            .'<p class="font-semibold">'.$job['name'].'</p>'
            .'<p class="text-sm opacity-80">'.$job['description'].'</p>'
            .'<p class="text-sm">Caractéristique : '.$job['characteristic'].'</p>'
            .'</div>'
            .'</div>';
    }, $jobs);

    return '<div class="grid grid-cols-1 gap-3 md:grid-cols-2">'.implode('', $cards).'</div>';
};

$agility = '[[kref:characteristic:agility_creature|Agilité]]';
$chance = '[[kref:characteristic:chance_creature|Chance]]';
$intelligence = '[[kref:characteristic:intelligence_creature|Intelligence]]';
$strength = '[[kref:characteristic:strength_creature|Force]]';

return [
    'title' => 'Les métiers',
    'slug' => 'les-metiers',
    'menu_order' => 11,
    'icon' => null,
    'sections' => [
        [
            'slug' => 'intro',
            'title' => 'Artisanat, récolte et forgemagie',
            'template' => 'text',
            'html' => '<p>Les métiers permettent à ton personnage de <strong>récolter</strong> des ressources dans la nature, de <strong>fabriquer</strong> des objets à partir de ces ressources, et de les <strong>améliorer</strong> en y incrustant des runes.</p>'
                .'<p>Il existe <strong>16 métiers</strong> : 9 d’artisanat, 6 de récolte et 1 de forgemagie. Ton personnage peut en apprendre <strong>6 au maximum</strong>, répartis comme tu veux — six récoltes, quatre artisanats et deux autres, peu importe : aucune catégorie n’a de quota réservé.</p>'
                .'<p>Cette page résume le système et donne la liste complète. Le détail des tests, des temps de fabrication et des risques est dans le chapitre [[kref:page:regles-4-3-les-metiers|Les métiers]] du livre de règles.</p>',
        ],
        [
            'slug' => 'niveaux',
            'title' => 'Les cinq niveaux',
            'template' => 'text',
            'html' => '<p>Chaque métier appris a un niveau de <strong>1 à 5</strong>. Ce niveau ne mesure pas ta puissance : il dit <strong>jusqu’où tu peux monter</strong> en rareté d’objet, ou en niveau de ressource.</p>'
                .'<table>'
                .'<thead><tr><th>Niveau</th><th>Artisanat et Forgemagie</th><th>Récolte</th></tr></thead>'
                .'<tbody>'
                .'<tr><td><strong>1</strong></td><td>Objets Communs</td><td>Ressources de niveau 1 à 4</td></tr>'
                .'<tr><td><strong>2</strong></td><td>Objets Peu communs</td><td>Ressources de niveau 5 à 8</td></tr>'
                .'<tr><td><strong>3</strong></td><td>Objets Rares</td><td>Ressources de niveau 9 à 12</td></tr>'
                .'<tr><td><strong>4</strong></td><td>Objets Très rares</td><td>Ressources de niveau 13 à 16</td></tr>'
                .'<tr><td><strong>5</strong></td><td>Objets Légendaires</td><td>Ressources de niveau 17 à 20</td></tr>'
                .'</tbody>'
                .'</table>'
                .'<p>Un niveau donne accès à tout ce qui se trouve en dessous : un Forgeron de niveau 4 fabrique du Très rare, mais aussi du Rare, du Peu commun et du Commun.</p>'
                .'<h3>Deux conditions pour fabriquer</h3>'
                .'<p>Pour fabriquer ou forgemager un objet, il faut remplir <strong>les deux</strong> :</p>'
                .'<ol>'
                .'<li>Ton <strong>niveau de métier</strong> couvre la <strong>rareté</strong> de l’objet.</li>'
                .'<li>Ton <strong>personnage</strong> a au moins le <strong>niveau</strong> de l’objet.</li>'
                .'</ol>'
                .'<p>Exemple : une épée Rare de niveau 12 demande Forgeron 3 <em>et</em> un personnage de niveau 12. La récolte, elle, ne demande que le niveau de métier.</p>'
                .'<p>Les objets de rareté <strong>Unique</strong> ne se fabriquent ni ne se forgemagent : ce sont des pièces de quête ou des créations du MJ.</p>',
        ],
        [
            'slug' => 'artisanat',
            'title' => 'Les métiers d’artisanat',
            'template' => 'text',
            'html' => '<p>Ils transforment les ressources en objets. Il t’en faut l’accès à un <strong>atelier équipé</strong>, la <strong>recette</strong>, et un jet réussi contre un DD égal au niveau de l’objet + 5. Compte <strong>deux heures par niveau d’objet</strong> à l’établi.</p>'
                .$grid([
                    ['name' => 'Bijoutier', 'image' => 'bijoutier', 'characteristic' => $agility, 'description' => 'Amulettes et anneaux.'],
                    ['name' => 'Cordonnier', 'image' => 'cordonnier', 'characteristic' => $intelligence, 'description' => 'Bottes et ceintures.'],
                    ['name' => 'Tailleur', 'image' => 'tailleur', 'characteristic' => $agility, 'description' => 'Capes et chapeaux.'],
                    ['name' => 'Sculpteur', 'image' => 'sculpteur', 'characteristic' => $chance, 'description' => 'Baguettes, bâtons et arcs — tout ce qui se travaille dans le bois.'],
                    ['name' => 'Forgeron', 'image' => 'forgeron', 'characteristic' => $strength, 'description' => 'Épées, haches, marteaux, dagues, lances et boucliers.'],
                    ['name' => 'Bricoleur', 'image' => 'bricoleur', 'characteristic' => $intelligence, 'description' => 'Outils, pièges et objets divers.'],
                    ['name' => 'Boulanger', 'image' => 'boulanger', 'characteristic' => $intelligence, 'description' => 'Pains, pâtisseries et consommables céréaliers.'],
                    ['name' => 'Boucher', 'image' => 'boucher', 'characteristic' => $strength, 'description' => 'Viandes préparées et consommables carnés.'],
                    ['name' => 'Poissonnier', 'image' => 'poissonnier', 'characteristic' => $chance, 'description' => 'Poissons préparés et consommables marins.'],
                ])
                .'<p>Le Forgeron couvre les armes de mêlée <em>et</em> les boucliers : il n’y a pas de métier séparé pour les boucliers.</p>',
        ],
        [
            'slug' => 'recolte',
            'title' => 'Les métiers de récolte',
            'template' => 'text',
            'html' => '<p>Ils ramassent la matière première : céréales, bois, minerais, gibier, poissons, herbes. Le jet se compare au niveau de la ressource + 3, et ton niveau de métier détermine la bande de ressources que tu peux atteindre.</p>'
                .$grid([
                    ['name' => 'Paysan', 'image' => 'paysan', 'characteristic' => $intelligence, 'description' => 'Céréales, légumes, fruits et herbes culinaires.'],
                    ['name' => 'Bûcheron', 'image' => 'bucheron', 'characteristic' => $strength, 'description' => 'Bois, résine et écorce.'],
                    ['name' => 'Mineur', 'image' => 'mineur', 'characteristic' => $strength, 'description' => 'Minerais, pierres précieuses et cristaux.'],
                    ['name' => 'Chasseur', 'image' => 'chasseur', 'characteristic' => $agility, 'description' => 'Viande, peaux, cornes et griffes.'],
                    ['name' => 'Pêcheur', 'image' => 'pecheur', 'characteristic' => $agility, 'description' => 'Poissons, coquillages, algues et perles.'],
                    ['name' => 'Alchimiste', 'image' => 'alchimiste', 'characteristic' => $chance, 'description' => 'Herbes, plantes magiques et essences. Aussi appelé Herboriste — et c’est le seul métier de récolte qui transforme aussi ce qu’il ramasse, en potions.'],
                ]),
        ],
        [
            'slug' => 'forgemagie',
            'title' => 'La forgemagie',
            'template' => 'text',
            'html' => $grid([
                ['name' => 'Forgemage', 'image' => 'forgemage', 'characteristic' => $intelligence, 'description' => 'Améliore un équipement en y incrustant une rune. Un seul métier couvre toutes les runes.'],
            ])
                .'<p>La forgemagie est un <strong>métier unique</strong>. Il n’y a pas de spécialisation par type de rune, et il compte dans ta limite de 6 métiers comme n’importe quel autre.</p>'
                .'<h3>Les trois règles à ne pas oublier</h3>'
                .'<ul>'
                .'<li><strong>Une seule rune par objet</strong> : un équipement forgemagé ne porte qu’un seul bonus.</li>'
                .'<li><strong>Pas de cumul</strong> : une créature ne peut pas porter deux équipements forgemagés avec le même bonus.</li>'
                .'<li><strong>Pas de dépassement</strong> : la forgemagie ne fait jamais franchir le plafond d’une caractéristique.</li>'
                .'</ul>'
                .'<p>Le jet se compare au niveau de l’équipement + 6, et l’opération prend <strong>une heure par niveau d’équipement</strong> dans un atelier de forgemagie. En cas d’échec, la rune et le temps sont perdus, et l’équipement peut être endommagé.</p>',
        ],
        [
            'slug' => 'runes',
            'title' => 'Le prix des runes',
            'template' => 'forgemagie_rune_table',
            'settings' => [
                'sort_by' => 'rune_price',
                'sort_dir' => 'desc',
                'show_base_price' => true,
            ],
        ],
    ],
];
