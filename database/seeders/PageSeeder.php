<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Characteristic;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\Characteristics\CharacteristicDefinitionReader;
use App\Services\PageService;
use App\Support\Characteristics\CharacteristicDefinitionNaming;
use App\Support\Cms\KrefShortcodeReplacer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed les pages de contribution : page « Nous rejoindre », présentation
 * du groupe puis une sous-page par type d'entité (créature, objet, sort),
 * chacune avec une introduction puis des sections par caractéristique normée
 * (texte + charte interactive).
 */
class PageSeeder extends Seeder
{
    private const ESSENTIAL_GROUP = "L'Essentiel";

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
            'icon' => null,
            'created_by' => $creatorId,
        ]);

        // Section introduction de la page parente « Contribution » (aperçu + liens vers sous-pages).
        $this->ensureTextSection(
            $contributionPage,
            'contribution-intro',
            'Contribution au projet',
            '<h2>Contribution au projet</h2>'
            .'<p>Cette section regroupe les ressources pour participer à <strong>Krosmoz JDR</strong> : rejoindre la communauté, obtenir les accès d’édition, puis consulter les <strong>chartes de caractéristiques</strong> par type d’entité (créatures, objets, sorts).</p>'
            .'<p>Commence par <strong>Nous rejoindre</strong> pour Discord, GitHub et la procédure de demande de droits. Ensuite, ouvre les sous-pages pour vérifier l’équilibre des valeurs grâce aux tableaux interactifs.</p>',
            1,
            $creatorId
        );

        $nousRejoindrePage = $this->createOrRestorePage([
            'title' => 'Nous rejoindre',
            'slug' => 'nous-rejoindre',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => null,
            'parent_id' => $contributionPage->id,
            'icon' => null,
            'created_by' => $creatorId,
        ]);

        $this->ensureTextSection(
            $nousRejoindrePage,
            'nous-rejoindre-intro',
            'Introduction',
            $this->nousRejoindreIntroHtml(),
            0,
            $creatorId
        );

        $this->seedEssentialPages($creatorId);
        $this->seedLibrariesPages($creatorId);

        // Sous-pages par groupe d'entité (menu_order ≥ 10 pour rester après « Nous rejoindre »).
        $menuOrder = 10;
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

        PageService::clearMenuCache();
    }

    private function nousRejoindreIntroHtml(): string
    {
        return <<<'HTML'
<h2>Nous rejoindre</h2>
<p><strong>Krosmoz JDR</strong> est un jeu de rôle sur table inspiré du Krosmoz (Dofus, Wakfu). Ce site rassemble les règles, outils et chartes ; le développement du contenu et du code est collaboratif.</p>

<h3>Discord</h3>
<p>Rejoins le serveur pour échanger, suivre les annonces et demander l’accès au contenu éditorial.</p>
<p><strong>Droits de modification :</strong> pour créer ou modifier des pages et des entités (créatures, objets, sorts, etc.) dans l’interface, ouvre un fil sur Discord et indique clairement ton <strong>nom de compte</strong> sur cette plateforme — le même identifiant que ton compte utilisateur Krosmoz JDR — afin que l’équipe puisse t’attribuer les permissions adaptées.</p>
<p><a href="https://discord.com/invite/XVu4VWFskj" target="_blank" rel="noopener noreferrer">Rejoindre le Discord</a></p>

<h3>GitHub</h3>
<p>Le code source (Laravel, Vue), les issues et les propositions de correctifs ou de fonctionnalités.</p>
<p><a href="https://github.com/SamGoodwater/KrosmozJdr" target="_blank" rel="noopener noreferrer">Ouvrir le dépôt GitHub</a></p>

<h3>Plateforme collaborative</h3>
<p>Fichiers et discussions autour du projet (inscription requise).</p>
<p><a href="https://project.krosmoz-jdr.fr" target="_blank" rel="noopener noreferrer">Projet Krosmoz JDR (Nextcloud)</a></p>

<h3>Chartes de conception des entités Dofus</h3>
<p>Pages internes : normes de référence pour équilibrer les caractéristiques (monstres, classes jouables et PNJ sont regroupés sous « Créatures »).</p>
<ul>
<li><a href="/pages/contribution-creatures">Créatures</a> (monstres, classes jouables, PNJ)</li>
<li><a href="/pages/contribution-objets">Objets</a></li>
<li><a href="/pages/contribution-sorts">Sorts</a></li>
</ul>
HTML;
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
        ?int $creatorId,
        bool $enableRichReferences = false
    ): Section {
        $settings = ['align' => 'left', 'size' => 'md'];
        if ($enableRichReferences) {
            $settings['enableRichReferences'] = true;
        }

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => $settings,
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

    /**
     * Pages « L'Essentiel » : résumés joueur·euse et aide MJ (database/seeders/data/essential-pages.php).
     *
     * @return array<string, array{
     *   title: string,
     *   slug: string,
     *   icon: string|null,
     *   menu_order: int,
     *   intro_title: string,
     *   intro_html: string,
     *   sections: list<array{slug: string, title: string, html: string}>,
     *   include_reference_table?: bool
     * }>
     */
    private function essentialPagesConfig(): array
    {
        $path = database_path('seeders/data/essential-pages.php');
        if (! is_file($path)) {
            return [];
        }

        $pages = require $path;

        return is_array($pages) ? $pages : [];
    }

    private function seedEssentialPages(?int $creatorId): void
    {
        $krefReplacer = KrefShortcodeReplacer::forEssentialPages();

        foreach ($this->essentialPagesConfig() as $pageConfig) {
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
                $krefReplacer->replace($pageConfig['intro_html']),
                $order++,
                $creatorId,
                true
            );

            foreach ($pageConfig['sections'] as $section) {
                $this->ensureTextSection(
                    $page,
                    $pageConfig['slug'].'-'.$section['slug'],
                    $section['title'],
                    $krefReplacer->replace($section['html']),
                    $order++,
                    $creatorId,
                    true
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

            $this->removeOrphanEssentialSections($page, $pageConfig);
        }
    }

    /**
     * Supprime les sections Essentiel obsolètes (slug hors config courante).
     *
     * @param array{
     *   slug: string,
     *   sections: list<array{slug: string, title: string, html: string}>,
     *   include_reference_table?: bool
     * } $pageConfig
     */
    private function removeOrphanEssentialSections(Page $page, array $pageConfig): void
    {
        $expectedSlugs = [$pageConfig['slug'].'-intro'];
        foreach ($pageConfig['sections'] as $section) {
            $expectedSlugs[] = $pageConfig['slug'].'-'.$section['slug'];
        }
        if (($pageConfig['include_reference_table'] ?? false) === true) {
            $expectedSlugs[] = $pageConfig['slug'].'-reference-table';
        }

        Section::query()
            ->where('page_id', $page->id)
            ->whereNotIn('slug', $expectedSlugs)
            ->each(fn (Section $section) => $section->delete());
    }

    private function seedLibrariesPages(?int $creatorId): void
    {
        $libraries = config('nav_menu.bibliotheques', []);
        if (! is_array($libraries) || $libraries === []) {
            return;
        }

        foreach ($libraries as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = (string) ($item['label'] ?? '');
            $routeName = (string) ($item['route'] ?? '');
            $entityKey = (string) ($item['entity_key'] ?? '');
            if ($title === '' || $routeName === '') {
                continue;
            }

            $slug = 'bibliotheque-'.Str::slug($entityKey !== '' ? $entityKey : $title);
            $menuOrder = (int) ($item['order'] ?? 0);
            $menuItemCssClasses = is_string($item['menu_item_css_classes'] ?? null)
                ? $item['menu_item_css_classes']
                : ($entityKey !== '' ? 'color-'.$entityKey.'-500 box-shadow-glass' : null);

            $page = $this->createOrRestorePage([
                'title' => $title,
                'slug' => $slug,
                'in_menu' => true,
                'state' => Page::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
                'menu_order' => $menuOrder,
                'menu_group' => 'Bibliothèques',
                'parent_id' => null,
                'entity_key' => $entityKey !== '' ? $entityKey : null,
                'menu_item_css_classes' => $menuItemCssClasses,
                'icon' => null,
                'created_by' => $creatorId,
            ]);

            $this->ensureTextSection(
                $page,
                $slug.'-intro',
                'Introduction',
                '<h2>'.$title.'</h2><p>Cette page regroupe l\'accès au tableau principal de l\'entité <strong>'.$title.'</strong>.</p>',
                0,
                $creatorId,
                true
            );

            $this->ensureEntityTableSection(
                $page,
                $slug.'-tableau',
                'Tableau',
                $this->libraryEntityTableType($entityKey),
                1,
                $creatorId
            );
        }
    }

    private function libraryEntityTableType(string $entityKey): string
    {
        return match ($entityKey) {
            'breed' => 'breeds',
            'specialization' => 'specializations',
            'spell' => 'spells',
            'capability' => 'capabilities',
            'monster' => 'monsters',
            'item' => 'items',
            'panoply' => 'panoplies',
            'consumable' => 'consumables',
            'resource' => 'resources',
            'condition' => 'conditions',
            'creature-trait' => 'creature-traits',
            default => 'spells',
        };
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

    private function ensureEntityTableSection(
        Page $page,
        string $slug,
        string $title,
        string $entity,
        int $order,
        ?int $creatorId
    ): Section {
        $payload = [
            'entity' => $entity,
            'filters' => [],
            'limit' => 50,
            'columns' => [],
        ];
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        if (! $section) {
            $section = Section::withTrashed()
                ->where('page_id', $page->id)
                ->where('template', SectionType::ENTITY_TABLE->value)
                ->where('order', $order)
                ->first();
        }

        $attributes = [
            'page_id' => $page->id,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::ENTITY_TABLE->value,
            'type' => SectionType::ENTITY_TABLE->value,
            'settings' => $payload,
            'data' => $payload,
            'params' => $payload,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ];

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

        $firstUser = User::query()->orderBy('id', 'asc')->first();

        return $firstUser ? (int) $firstUser->id : null;
    }
}
