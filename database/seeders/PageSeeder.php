<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Characteristic;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use Illuminate\Database\Seeder;

/**
 * Seed les pages de contribution : présentation du projet puis une sous-page
 * par groupe d'entité (créature, objet, sort), chacune avec une introduction
 * puis deux sections par caractéristique normée (texte + charte interactive).
 */
class PageSeeder extends Seeder
{
    private const ESSENTIAL_GROUP = "L'Essentiels";

    /** Groupes d'entités avec métadonnées pour les pages. */
    private const ENTITY_GROUPS = [
        'creature' => [
            'title' => 'Créatures',
            'slug' => 'contribution-creatures',
            'icon' => 'fa-solid fa-dragon',
            'intro' => '<h2>Chartes des caractéristiques — Créatures</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les caractéristiques des créatures '
                .'(monstres, classes jouables, PNJ). Chaque charte définit les valeurs attendues selon le <strong>niveau</strong> (1–20) '
                .'et la <strong>puissance</strong> (très faible → très forte).</p>'
                .'<p>Utilise ces chartes pour vérifier qu\'une créature est équilibrée par rapport aux références du jeu. '
                .'Les conditions de lecture permettent d\'ajuster la ligne de puissance ou le niveau en fonction d\'autres caractéristiques.</p>',
            'entity' => '*',
        ],
        'object' => [
            'title' => 'Objets',
            'slug' => 'contribution-objets',
            'icon' => 'fa-solid fa-shield-halved',
            'intro' => '<h2>Chartes des caractéristiques — Objets</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les bonus d\'équipement. '
                .'Les valeurs sont calibrées selon les <strong>règles 5.2.4</strong> (Équipements et panoplies) : '
                .'+1-2 aux niveaux 1-5, +2-3 aux niveaux 6-10, +3-4 aux niveaux 11-15, +4-5 aux niveaux 16-20.</p>'
                .'<p>Un objet dont les bonus dépassent significativement la ligne « neutre » de sa charte est potentiellement '
                .'déséquilibré. Les objets rares ou légendaires peuvent atteindre la ligne « fort » ou « très fort ».</p>',
            'entity' => '*',
        ],
        'spell' => [
            'title' => 'Sorts',
            'slug' => 'contribution-sorts',
            'icon' => 'fa-solid fa-wand-sparkles',
            'intro' => '<h2>Chartes des caractéristiques — Sorts</h2>'
                .'<p>Cette section présente les <strong>normes de référence</strong> pour les effets de sorts. '
                .'Les dégâts, soins et boucliers sont calibrés selon les <strong>règles 5.2.3</strong> (Sorts et aptitudes), '
                .'avec une progression de ~1d6 (niveau 1) à ~5d6+mod (niveau 20).</p>'
                .'<p>Les conditions de lecture prennent en compte le coût en PA et la zone d\'effet : '
                .'un sort coûteux (5+ PA) peut avoir des dégâts supérieurs, tandis qu\'un sort en zone (≥2 cases) '
                .'devrait avoir des dégâts réduits par cible.</p>',
            'entity' => '*',
        ],
    ];

    /**
     * Pages "résumé joueur/joueuse" : chaque entrée = 1 page, chaque item "sections" = 1 section texte.
     *
     * @var array<string, array{
     *   title: string,
     *   slug: string,
     *   icon: string,
     *   menu_order: int,
     *   intro_title: string,
     *   intro_html: string,
     *   sections: list<array{slug: string, title: string, html: string}>,
     *   include_reference_table?: bool
     * }>
     */
    private const ESSENTIAL_PAGES = [
        'bien-demarrer' => [
            'title' => 'Bien démarrer',
            'slug' => 'essentiels-bien-demarrer',
            'icon' => 'fa-solid fa-compass',
            'menu_order' => 10,
            'intro_title' => 'Vue d’ensemble',
            'intro_html' => '<p>Guide express pour lancer une partie rapidement.</p>'
                .'<p><strong>À retenir :</strong> les mécaniques clés tiennent sur une seule lecture.</p>'
                .'<p><strong>À faire :</strong> relire cette page avant chaque session.</p>'
                .'<p><strong>À éviter :</strong> ouvrir tout le règlement pour une simple vérification.</p>',
            'sections' => [
                ['slug' => 'concept', 'title' => 'Essentiel — Concept du jeu en 2 minutes', 'html' => '<ul><li>Jets de dés pour résoudre l’incertitude.</li><li>Ressources à gérer : PA, PM, Wakfu.</li><li>Alternance exploration / combat / progression.</li></ul>'],
                ['slug' => 'materiel', 'title' => 'Action — Ce qu’il faut pour jouer', 'html' => '<ul><li>Une fiche de personnage.</li><li>Des dés.</li><li>Ces pages résumées + la page Caractéristiques en support.</li></ul>'],
                ['slug' => 'boucle', 'title' => 'Essentiel — Boucle de jeu', 'html' => '<ul><li>Exploration et décisions.</li><li>Résolution des actions (tests, compétences, réactions).</li><li>Conflit/combat si nécessaire.</li><li>Récompenses et progression.</li></ul>'],
                ['slug' => 'lexique', 'title' => 'Vigilance — Lexique rapide', 'html' => '<ul><li><strong>PA</strong> : actions principales.</li><li><strong>PM</strong> : déplacement.</li><li><strong>CA</strong> : défense.</li><li><strong>Wakfu</strong> : réserve spéciale.</li><li><strong>Maîtrise/Expertise</strong> : bonus de compétence.</li></ul>'],
            ],
        ],
        'creation' => [
            'title' => 'Créer son personnage (rapide)',
            'slug' => 'essentiels-creation-personnage',
            'icon' => 'fa-solid fa-user-plus',
            'menu_order' => 20,
            'intro_title' => 'Checklist de création',
            'intro_html' => '<p>Créez un personnage jouable avec un parcours court et fiable.</p>'
                .'<p><strong>À retenir :</strong> stats -> classe -> spécialisation -> équipement.</p>'
                .'<p><strong>À faire :</strong> sécuriser un rôle clair dès le départ.</p>'
                .'<p><strong>À éviter :</strong> disperser les points sur trop de caractéristiques.</p>',
            'sections' => [
                ['slug' => 'etapes', 'title' => 'Action — Étapes de création', 'html' => '<ol><li>Répartir les caractéristiques.</li><li>Choisir classe et spécialisation.</li><li>Sélectionner aptitudes/capacités.</li><li>S’équiper.</li></ol>'],
                ['slug' => 'caracs', 'title' => 'Essentiel — Caractéristiques utiles', 'html' => '<ul><li>Priorisez 2-3 stats liées à votre rôle.</li><li>Évitez les profils trop dispersés en début de campagne.</li><li>Utilisez la page Caractéristiques pour vérifier les bornes.</li></ul>'],
                ['slug' => 'classe-spe', 'title' => 'Essentiel — Classe + spécialisation', 'html' => '<ul><li>La classe définit l’identité globale.</li><li>La spécialisation précise votre rôle (dégâts, contrôle, soutien, etc.).</li><li>Visez une synergie simple avant les optimisations avancées.</li></ul>'],
                ['slug' => 'equipement', 'title' => 'Vigilance — Équipement de départ', 'html' => '<ul><li>Choisissez du matériel cohérent avec votre rôle.</li><li>Privilégiez la fiabilité avant les gains marginaux.</li><li>Les prix sont indicatifs et peuvent varier en campagne.</li></ul>'],
            ],
        ],
        'actions-hors-combat' => [
            'title' => 'Actions en jeu (hors combat)',
            'slug' => 'essentiels-actions-hors-combat',
            'icon' => 'fa-solid fa-map',
            'menu_order' => 30,
            'intro_title' => 'Exploration et interactions',
            'intro_html' => '<p>Résumé des décisions les plus fréquentes hors affrontement direct.</p>'
                .'<p><strong>À retenir :</strong> annoncer clairement intention, action et timing.</p>'
                .'<p><strong>À faire :</strong> garder un rythme de table fluide.</p>'
                .'<p><strong>À éviter :</strong> multiplier les vérifications longues en partie.</p>',
            'sections' => [
                ['slug' => 'exploration', 'title' => 'Action — Exploration', 'html' => '<ul><li>Observer l’environnement.</li><li>Se déplacer intelligemment.</li><li>Identifier risques/opportunités (pièges, ressources, PNJ).</li></ul>'],
                ['slug' => 'temps', 'title' => 'Vigilance — Gestion du temps', 'html' => '<ul><li>Le temps influe sur repos, trajets et préparation.</li><li>Annoncez clairement les durées d’action au MJ.</li><li>Anticiper évite les pénalités de rythme.</li></ul>'],
                ['slug' => 'competences', 'title' => 'Essentiel — Tests de compétences', 'html' => '<ul><li>Lancer le dé.</li><li>Ajouter modificateurs + maîtrise/expertise.</li><li>Comparer à la difficulté.</li></ul>'],
                ['slug' => 'reactions', 'title' => 'Action — Réactions hors combat', 'html' => '<ul><li>Utiles pour répondre vite à un imprévu.</li><li>À déclarer clairement (intention + action).</li><li>Servez-vous-en pour protéger le groupe ou saisir une fenêtre tactique.</li></ul>'],
            ],
        ],
        'combat' => [
            'title' => 'Combat (résumé pratique)',
            'slug' => 'essentiels-combat',
            'icon' => 'fa-solid fa-shield',
            'menu_order' => 40,
            'intro_title' => 'Combat en une page',
            'intro_html' => '<p>Règles minimales pour mener un combat lisible et rapide.</p>'
                .'<p><strong>À retenir :</strong> position, initiative, ressources, états.</p>'
                .'<p><strong>À faire :</strong> annoncer les actions dans un ordre simple.</p>'
                .'<p><strong>À éviter :</strong> oublier les effets persistants entre les tours.</p>',
            'sections' => [
                ['slug' => 'mise-en-place', 'title' => 'Action — Mise en place', 'html' => '<ul><li>Positions initiales.</li><li>Initiative.</li><li>États actifs.</li><li>Objectif du combat.</li></ul>'],
                ['slug' => 'tour-actions', 'title' => 'Essentiel — Tour de jeu et actions', 'html' => '<ul><li>Choisir l’action prioritaire.</li><li>Optimiser le déplacement (PM).</li><li>Gérer les ressources restantes (PA/Wakfu).</li></ul>'],
                ['slug' => 'reactions', 'title' => 'Action — Système de réaction', 'html' => '<ul><li>Déclenchement hors tour.</li><li>Respect des conditions.</li><li>Très utile pour la défense et le contrôle.</li></ul>'],
                ['slug' => 'sante-etats', 'title' => 'Vigilance — Santé, dégâts, états', 'html' => '<ul><li>Suivre PV et boucliers.</li><li>Appliquer les états sans oubli.</li><li>Vérifier les effets qui persistent d’un tour à l’autre.</li></ul>'],
            ],
        ],
        'sorts-aptitudes' => [
            'title' => 'Sorts, aptitudes, capacités',
            'slug' => 'essentiels-sorts-aptitudes',
            'icon' => 'fa-solid fa-wand-sparkles',
            'menu_order' => 50,
            'intro_title' => 'Pouvoirs de personnage',
            'intro_html' => '<p>Résumé des mécaniques qui gouvernent vos pouvoirs actifs.</p>'
                .'<p><strong>À retenir :</strong> coût, portée, ligne de vue, conditions.</p>'
                .'<p><strong>À faire :</strong> garder le Wakfu pour les moments décisifs.</p>'
                .'<p><strong>À éviter :</strong> surconsommer les ressources tôt dans le combat.</p>',
            'sections' => [
                ['slug' => 'typologie', 'title' => 'Essentiel — Types de sorts', 'html' => '<ul><li>Dégâts</li><li>Soutien/soin</li><li>Contrôle</li><li>Mobilité</li><li>Invocation</li></ul>'],
                ['slug' => 'lancement', 'title' => 'Action — Lancement en pratique', 'html' => '<ul><li>Vérifier coût (PA).</li><li>Vérifier portée et ligne de vue.</li><li>Vérifier conditions/réactions éventuelles.</li></ul>'],
                ['slug' => 'wakfu', 'title' => 'Vigilance — Réserve de Wakfu', 'html' => '<ul><li>Ressource rare.</li><li>À garder pour les moments décisifs.</li><li>Coordonnez son usage avec le groupe.</li></ul>'],
                ['slug' => 'synergies', 'title' => 'Essentiel — Aptitudes et synergies', 'html' => '<ul><li>Associez vos capacités à vos sorts principaux.</li><li>Évitez les builds trop complexes en début de campagne.</li><li>Privilégiez la cohérence de rôle.</li></ul>'],
            ],
        ],
        'economie-progression' => [
            'title' => 'Économie, équipement, progression',
            'slug' => 'essentiels-economie-progression',
            'icon' => 'fa-solid fa-coins',
            'menu_order' => 60,
            'intro_title' => 'Progression utile',
            'intro_html' => '<p>L’essentiel pour progresser efficacement sans entrer dans l’optimisation lourde.</p>'
                .'<p><strong>À retenir :</strong> cohérence de build > bonus isolés.</p>'
                .'<p><strong>À faire :</strong> vérifier les bornes avant tout achat/forgemagie.</p>'
                .'<p><strong>À éviter :</strong> casser l’économie de campagne avec des dépenses excessives.</p>',
            'sections' => [
                ['slug' => 'rarete-loot', 'title' => 'Essentiel — Rareté, loot, récompenses', 'html' => '<ul><li>La rareté donne une bonne estimation de puissance.</li><li>Les récompenses doivent rester cohérentes avec le niveau du groupe.</li></ul>'],
                ['slug' => 'equip-panoplie', 'title' => 'Action — Équipement et panoplies', 'html' => '<ul><li>Visez la cohérence de build.</li><li>Ne cumulez pas des bonus hors bornes.</li><li>Les synergies valent souvent plus que les pics isolés.</li></ul>'],
                ['slug' => 'metiers-fm', 'title' => 'Vigilance — Métiers et forgemagie', 'html' => '<ul><li>La forgemagie ajuste un équipement.</li><li>Respecter les maxima évite les déséquilibres.</li><li>Utiliser la page Caractéristiques pour contrôler les bornes.</li></ul>'],
                ['slug' => 'conseils', 'title' => 'Vigilance — Conseils d’achat', 'html' => '<ul><li>Les prix sont indicatifs.</li><li>Prioriser l’impact en jeu avant le prestige.</li><li>Éviter les dépenses qui cassent l’économie de campagne.</li></ul>'],
            ],
        ],
        'caracteristiques' => [
            'title' => 'Caractéristiques',
            'slug' => 'caracteristiques',
            'icon' => 'fa-solid fa-table-columns',
            'menu_order' => 70,
            'intro_title' => 'Accès rapide',
            'intro_html' => '<p>Point d’entrée vers les bornes de conception : formules, min/max, valeurs par défaut, équipement et forgemagie.</p>'
                .'<p><strong>À retenir :</strong> ce tableau sert de référence rapide pour valider une valeur.</p>'
                .'<p><strong>À faire :</strong> croiser avec le contexte de campagne si besoin.</p>'
                .'<p><strong>À éviter :</strong> considérer les prix comme fixes (ils sont <strong>indicatifs</strong>).</p>',
            'sections' => [],
            'include_reference_table' => true,
        ],
    ];

    public function run(): void
    {
        $creatorId = $this->resolveDefaultCreatorId();
        $characteristicNames = $this->loadCharacteristicNames();

        // Page parente : Contribution (informations + sous-pages chartes).
        $contributionPage = $this->createOrRestorePage([
            'title' => 'Contribution',
            'slug' => 'contribution',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 900,
            'menu_group' => 'Informations',
            'parent_id' => null,
            'icon' => 'fa-solid fa-handshake-angle',
            'created_by' => $creatorId,
        ]);

        // Section introduction de contribution.
        $this->ensureTextSection(
            $contributionPage,
            'contribution-intro',
            'Contribuer au projet',
            '<h2>Bienvenue, contributeur !</h2>'
            .'<p><strong>Krosmoz JDR</strong> est un jeu de rôle sur table inspiré de l\'univers du Krosmoz (Dofus, Wakfu). '
            .'Ce projet est le fruit d\'un travail collaboratif qui vise à adapter les mécaniques du jeu vidéo '
            .'en un système de JDR complet et équilibré, jouable avec des dés et une feuille de personnage.</p>'
            .'<h3>Les piliers du projet</h3>'
            .'<ul>'
            .'<li><strong>Fidélité à l\'univers</strong> : respecter le lore et l\'ambiance du Krosmoz tout en l\'adaptant au format JDR.</li>'
            .'<li><strong>Équilibre</strong> : chaque classe, sort et objet doit être viable. Les chartes de caractéristiques (présentées dans les sous-pages) servent de référence.</li>'
            .'<li><strong>Accessibilité</strong> : le système doit rester simple à prendre en main, même pour les novices du JDR.</li>'
            .'<li><strong>Modularité</strong> : le MJ peut activer ou désactiver des modules (crafting, PvP, exploration) selon sa campagne.</li>'
            .'</ul>'
            .'<h3>Comment contribuer ?</h3>'
            .'<ul>'
            .'<li><strong>Équilibrage</strong> : utilise les chartes ci-dessous pour vérifier que les valeurs d\'une créature, d\'un objet ou d\'un sort sont cohérentes.</li>'
            .'<li><strong>Contenu</strong> : propose de nouvelles classes, sorts, monstres ou objets en respectant les normes établies.</li>'
            .'<li><strong>Relecture</strong> : signale les incohérences, fautes ou déséquilibres que tu repères.</li>'
            .'<li><strong>Playtest</strong> : joue des sessions et remonte tes retours d\'expérience.</li>'
            .'</ul>'
            .'<p>Les sous-pages suivantes détaillent les <strong>chartes de caractéristiques</strong> pour chaque type d\'entité. '
            .'Chaque charte est un tableau interactif montrant les valeurs de référence par niveau et par puissance.</p>',
            1,
            $creatorId
        );

        $this->seedEssentialPages($creatorId);

        // Sous-pages par groupe d'entité
        $menuOrder = 0;
        foreach (self::ENTITY_GROUPS as $group => $meta) {
            $normsKeys = $this->characteristicKeysWithNormsFromDefinitions($group);
            if ($normsKeys === []) {
                continue;
            }

            $subPage = $this->createOrRestorePage([
                'title' => $meta['title'],
                'slug' => $meta['slug'],
                'in_menu' => true,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'menu_order' => $menuOrder++,
                'menu_group' => null,
                'parent_id' => $contributionPage->id,
                'icon' => $meta['icon'],
                'created_by' => $creatorId,
            ]);

            // Section intro du groupe
            $this->ensureTextSection(
                $subPage,
                $meta['slug'].'-intro',
                'Introduction',
                $meta['intro'],
                0,
                $creatorId
            );

            // Sections par caractéristique normée : charte interactive
            $order = 1;
            foreach ($normsKeys as $charKey) {
                $charName = $characteristicNames[$charKey] ?? $charKey;

                // Section charte interactive
                $this->ensureCharacteristicNormsSection(
                    $subPage,
                    $meta['slug'].'-norms-'.str_replace('_', '-', $charKey),
                    $charName,
                    $charKey,
                    $group,
                    $meta['entity'],
                    $order++,
                    $creatorId
                );
            }

            if ($this->command) {
                $this->command->info("📄 Page {$meta['slug']} : {$order} sections (intro + ".count($normsKeys).' chartes).');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createOrRestorePage(array $attributes): Page
    {
        $slug = (string) $attributes['slug'];
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page) {
            if ($page->trashed()) {
                $page->restore();
            }
            $page->fill($attributes);
            $page->save();
            if ($this->command) {
                $this->command->info("♻️ Page {$slug} restaurée/mise à jour");
            }

            return $page;
        }

        $page = Page::create($attributes);
        if ($this->command) {
            $this->command->info("✅ Page {$slug} créée");
        }

        return $page;
    }

    private function ensureTextSection(
        Page $page,
        string $slug,
        string $title,
        string $contentHtml,
        int $order,
        ?int $creatorId
    ): Section {
        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => ['align' => 'left', 'size' => 'md'],
            'data' => ['content' => $contentHtml],
            'params' => ['content' => $contentHtml],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureCharacteristicNormsSection(
        Page $page,
        string $slug,
        string $title,
        string $characteristicKey,
        string $group,
        string $entity,
        int $order,
        ?int $creatorId
    ): Section {
        $settings = [
            'characteristic_key' => $characteristicKey,
            'group' => $group,
            'entity' => $entity,
        ];

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::CHARACTERISTIC_NORMS->value,
            'type' => SectionType::CHARACTERISTIC_NORMS->value,
            'settings' => $settings,
            'data' => [],
            'params' => $settings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureCharacteristicReferenceTableSection(
        Page $page,
        string $slug,
        string $title,
        string $group,
        string $entity,
        int $order,
        ?int $creatorId
    ): Section {
        $settings = [
            'group' => $group,
            'entity' => $entity,
            'search' => '',
            'sort_by' => 'name',
            'sort_dir' => 'asc',
            'status_filter' => 'all',
            'show_prices' => true,
            'show_only_with_equipment' => false,
        ];

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::CHARACTERISTIC_REFERENCE_TABLE->value,
            'type' => SectionType::CHARACTERISTIC_REFERENCE_TABLE->value,
            'settings' => $settings,
            'data' => [],
            'params' => $settings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function seedEssentialPages(?int $creatorId): void
    {
        foreach (self::ESSENTIAL_PAGES as $pageConfig) {
            $page = $this->createOrRestorePage([
                'title' => $pageConfig['title'],
                'slug' => $pageConfig['slug'],
                'in_menu' => true,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'menu_order' => $pageConfig['menu_order'],
                'menu_group' => self::ESSENTIAL_GROUP,
                'parent_id' => null,
                'icon' => $pageConfig['icon'],
                'created_by' => $creatorId,
            ]);

            $order = 0;
            $this->ensureTextSection(
                $page,
                $pageConfig['slug'].'-intro',
                $pageConfig['intro_title'],
                $pageConfig['intro_html'],
                $order++,
                $creatorId
            );

            foreach ($pageConfig['sections'] as $section) {
                $this->ensureTextSection(
                    $page,
                    $pageConfig['slug'].'-'.$section['slug'],
                    $section['title'],
                    $section['html'],
                    $order++,
                    $creatorId
                );
            }

            if (($pageConfig['include_reference_table'] ?? false) === true) {
                $this->ensureCharacteristicReferenceTableSection(
                    $page,
                    $pageConfig['slug'].'-reference-table',
                    'Tableau de référence',
                    'all',
                    '*',
                    $order++,
                    $creatorId
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function ensureSection(Page $page, string $slug, array $attributes): Section
    {
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        $attributes = array_merge(['page_id' => $page->id, 'slug' => $slug], $attributes);

        if ($section) {
            if ($section->trashed()) {
                $section->restore();
            }
            $section->fill($attributes);
            $section->save();

            return $section;
        }

        return Section::create($attributes);
    }

    /**
     * @return array<string, string>
     */
    private function loadCharacteristicNames(): array
    {
        // Essayer depuis la BDD d'abord
        try {
            $names = Characteristic::pluck('name', 'key')->toArray();
            if (count($names) > 0) {
                return $names;
            }
        } catch (\Throwable) {
            // Table non disponible, fallback sur le fichier
        }

        return $this->loadCharacteristicNamesFromDefinitionFiles();
    }

    /**
     * Libellés depuis les définitions JSON (fallback si la table `characteristics` est vide).
     *
     * @return array<string, string>
     */
    private function loadCharacteristicNamesFromDefinitionFiles(): array
    {
        $names = [];
        foreach (CharacteristicDefinitionReader::allDefinitionAbsolutePaths() as $path) {
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable) {
                continue;
            }
            $c = $def['characteristic'] ?? [];
            if (! is_array($c)) {
                continue;
            }
            $key = $c['key'] ?? '';
            if (! is_string($key) || $key === '') {
                continue;
            }
            $names[$key] = is_string($c['name'] ?? null) && $c['name'] !== '' ? $c['name'] : $key;
        }

        return $names;
    }

    /**
     * Clés ayant au moins une norme dans les définitions JSON du groupe.
     *
     * @return list<string>
     */
    private function characteristicKeysWithNormsFromDefinitions(string $group): array
    {
        $dir = base_path(CharacteristicDefinitionNaming::RELATIVE_ROOT.'/'.$group);
        if (! is_dir($dir)) {
            return [];
        }
        $found = [];
        foreach (glob($dir.DIRECTORY_SEPARATOR.'*-definition.json') ?: [] as $path) {
            if (! is_file($path)) {
                continue;
            }
            try {
                $def = CharacteristicDefinitionReader::load($path);
            } catch (\Throwable) {
                continue;
            }
            $key = $def['characteristic']['key'] ?? '';
            if (! is_string($key) || $key === '') {
                continue;
            }
            foreach ($def['entities'] as $payload) {
                if (! is_array($payload)) {
                    continue;
                }
                $hasNorms = ! empty($payload['norms_grid'])
                    || ! empty($payload['norms_conditions'])
                    || (isset($payload['norms_description']) && is_string($payload['norms_description']) && $payload['norms_description'] !== '');
                if ($hasNorms) {
                    $found[$key] = true;
                    break;
                }
            }
        }
        $list = array_keys($found);
        sort($list);

        return $list;
    }

    private function resolveDefaultCreatorId(): ?int
    {
        $systemUser = User::query()->where('email', User::SYSTEM_USER_EMAIL)->first();
        if ($systemUser) {
            return (int) $systemUser->id;
        }

        $superAdmin = User::query()->where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->first();
        if ($superAdmin) {
            return (int) $superAdmin->id;
        }

        $firstUser = User::query()->orderBy('id')->first();

        return $firstUser ? (int) $firstUser->id : null;
    }
}
