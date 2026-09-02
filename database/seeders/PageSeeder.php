<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\PageService;
use App\Support\Cms\KrefShortcodeReplacer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seed les pages CMS publiques : Contribution (Nous rejoindre), L'Essentiel, Bibliothèques.
 * Les chartes MJ (créatures, objets, sorts) sont gérées par {@see CreationPagesSeeder}.
 */
class PageSeeder extends Seeder
{
    private const ESSENTIAL_GROUP = "L'Essentiel";

    public function run(): void
    {
        $creatorId = $this->resolveDefaultCreatorId();

        // Page parente : Contribution (informations + Nous rejoindre).
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

        $this->ensureTextSection(
            $contributionPage,
            'contribution-intro',
            'Contribution au projet',
            '<h2>Contribution au projet</h2>'
            .'<p>Cette section regroupe les ressources pour participer à <strong>Krosmoz JDR</strong> : rejoindre la communauté et obtenir les accès d’édition.</p>'
            .'<p>Commence par <strong>Nous rejoindre</strong> pour Discord, GitHub et la procédure de demande de droits.</p>',
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
        $this->seedJobsPage($creatorId);
        $this->seedResourcesPage($creatorId);

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

    /**
     * Page documentaire « Les métiers » (Bibliothèques).
     *
     * Contrairement aux autres pages du groupe, elle ne liste pas une entité de
     * base : elle explique le système et embarque le tableau vivant des runes.
     */
    private function seedJobsPage(?int $creatorId): void
    {
        $path = database_path('seeders/data/jobs-page.php');
        if (! is_file($path)) {
            return;
        }

        $config = require $path;
        if (! is_array($config) || ! isset($config['slug'], $config['sections'])) {
            return;
        }

        $page = $this->createOrRestorePage([
            'title' => (string) $config['title'],
            'slug' => (string) $config['slug'],
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => (int) $config['menu_order'],
            'menu_group' => 'Bibliothèques',
            'parent_id' => null,
            'icon' => $config['icon'] ?? null,
            'created_by' => $creatorId,
        ]);

        $krefReplacer = KrefShortcodeReplacer::forEssentialPages();
        $expectedSlugs = [];
        $order = 0;

        foreach ($config['sections'] as $section) {
            $slug = (string) $config['slug'].'-'.$section['slug'];
            $expectedSlugs[] = $slug;

            if (($section['template'] ?? 'text') === SectionType::FORGEMAGIE_RUNE_TABLE->value) {
                $this->ensureForgemagieRuneTableSection(
                    $page,
                    $slug,
                    (string) $section['title'],
                    is_array($section['settings'] ?? null) ? $section['settings'] : [],
                    $order++,
                    $creatorId
                );

                continue;
            }

            $this->ensureTextSection(
                $page,
                $slug,
                (string) $section['title'],
                $krefReplacer->replace((string) $section['html']),
                $order++,
                $creatorId,
                true
            );
        }

        Section::query()
            ->where('page_id', $page->id)
            ->whereNotIn('slug', $expectedSlugs)
            ->each(fn (Section $section) => $section->delete());
    }

    /**
     * Page « Ressources » (sous « Ressources et équilibrage ») : livre compilé, fiches, logo.
     */
    private function seedResourcesPage(?int $creatorId): void
    {
        $path = database_path('seeders/data/ressources-page.php');
        if (! is_file($path)) {
            return;
        }

        $config = require $path;
        if (! is_array($config) || ! isset($config['slug'], $config['sections'])) {
            return;
        }

        $parentSlug = (string) ($config['parent_slug'] ?? '');
        $parent = $parentSlug !== ''
            ? Page::query()->where('slug', $parentSlug)->first()
            : null;

        if ($parentSlug !== '' && $parent === null && $this->command) {
            $this->command->warn(
                "Page parente « {$parentSlug} » introuvable : « {$config['slug']} » reste à la racine du menu Règles jusqu’à l’import TOC."
            );
        }

        $page = $this->createOrRestorePage([
            'title' => (string) $config['title'],
            'slug' => (string) $config['slug'],
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => $parent !== null
                ? (int) $config['menu_order']
                : (int) ($config['fallback_menu_order'] ?? $config['menu_order']),
            'menu_group' => 'Règles',
            'parent_id' => $parent?->id,
            'icon' => $config['icon'] ?? null,
            'created_by' => $creatorId,
        ]);

        $krefReplacer = KrefShortcodeReplacer::forEssentialPages();
        $expectedSlugs = [];
        $order = 0;

        foreach ($config['sections'] as $section) {
            $slug = (string) $config['slug'].'-'.$section['slug'];
            $expectedSlugs[] = $slug;

            if (($section['template'] ?? 'text') === SectionType::DOWNLOAD_CATALOG->value) {
                $this->ensureDownloadCatalogSection(
                    $page,
                    $slug,
                    (string) $section['title'],
                    is_array($section['settings'] ?? null) ? $section['settings'] : [],
                    $order++,
                    $creatorId
                );

                continue;
            }

            $this->ensureTextSection(
                $page,
                $slug,
                (string) $section['title'],
                $krefReplacer->replace((string) $section['html']),
                $order++,
                $creatorId,
                true
            );
        }

        Section::query()
            ->where('page_id', $page->id)
            ->whereNotIn('slug', $expectedSlugs)
            ->each(fn (Section $section) => $section->delete());
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    private function ensureDownloadCatalogSection(
        Page $page,
        string $slug,
        string $title,
        array $settings,
        int $order,
        ?int $creatorId
    ): Section {
        $settings = array_merge(
            config('section_templates.download_catalog.settings', []),
            $settings
        );

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::DOWNLOAD_CATALOG->value,
            'type' => SectionType::DOWNLOAD_CATALOG->value,
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
     * @param  array<string, mixed>  $settings
     */
    private function ensureForgemagieRuneTableSection(
        Page $page,
        string $slug,
        string $title,
        array $settings,
        int $order,
        ?int $creatorId
    ): Section {
        $settings = array_merge(
            config('section_templates.forgemagie_rune_table.settings', []),
            $settings
        );

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::FORGEMAGIE_RUNE_TABLE->value,
            'type' => SectionType::FORGEMAGIE_RUNE_TABLE->value,
            'settings' => $settings,
            'data' => [],
            'params' => $settings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
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
