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

/**
 * Atelier MJ « Création » : hub Pour les MJ et une page d’aide par type d’entité.
 *
 * Contenu : `database/seeders/data/creation-pages.php`.
 */
class CreationPagesSeeder extends Seeder
{
    /**
     * Anciennes pages (chartes groupées, doublons) à archiver.
     *
     * @var list<string>
     */
    private const DEPRECATED_SLUGS = [
        'contribution-creatures',
        'contribution-objets',
        'contribution-sorts',
        'creation-creatures',
        'creation-objets',
        'creation-equipement',
    ];

    public function run(): void
    {
        $creatorId = $this->resolveDefaultCreatorId();
        $config = $this->loadConfig();
        $kref = new KrefShortcodeReplacer;

        $parent = $this->createOrRestorePage([
            'title' => 'Création',
            'slug' => 'creation',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 850,
            'menu_group' => 'Pour les MJ',
            'parent_id' => null,
            'icon' => 'fa-solid fa-hat-wizard',
            'created_by' => $creatorId,
        ]);

        $hubExpected = ['creation-intro'];
        $this->ensureTextSection(
            $parent,
            'creation-intro',
            'Introduction',
            $kref->replace((string) ($config['hub_intro'] ?? '')),
            0,
            $creatorId
        );
        $this->removeOrphanSections($parent, $hubExpected);

        $this->archiveDeprecatedPages($parent);

        $pages = $config['pages'] ?? [];
        if (! is_array($pages)) {
            $pages = [];
        }

        foreach ($pages as $meta) {
            if (! is_array($meta)) {
                continue;
            }
            $this->seedEntityGuidePage($parent, $meta, $kref, $creatorId);
        }

        PageService::clearMenuCache();
        $this->command?->info('📐 Pages Création : hub MJ + '.count($pages).' guides d’entité.');
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function seedEntityGuidePage(
        Page $parent,
        array $meta,
        KrefShortcodeReplacer $kref,
        ?int $creatorId
    ): void {
        $slug = (string) ($meta['slug'] ?? '');
        $title = (string) ($meta['title'] ?? '');
        if ($slug === '' || $title === '') {
            return;
        }

        $page = $this->createOrRestorePage([
            'title' => $title,
            'slug' => $slug,
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => (int) ($meta['menu_order'] ?? 0),
            'menu_group' => null,
            'parent_id' => $parent->id,
            'icon' => is_string($meta['icon'] ?? null) ? $meta['icon'] : null,
            'created_by' => $creatorId,
        ]);

        $sections = $meta['sections'] ?? [];
        if (! is_array($sections)) {
            $sections = [];
        }

        $expectedSlugs = [];
        $order = 0;
        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }
            $sectionSlug = (string) ($section['slug'] ?? '');
            if ($sectionSlug === '') {
                continue;
            }
            $expectedSlugs[] = $sectionSlug;
            $this->ensureConfiguredSection($page, $section, $order++, $kref, $creatorId);
        }

        $this->removeOrphanSections($page, $expectedSlugs);
        $this->command?->info("📄 Page {$slug} : ".count($expectedSlugs).' section(s).');
    }

    /**
     * @param  array<string, mixed>  $section
     */
    private function ensureConfiguredSection(
        Page $page,
        array $section,
        int $order,
        KrefShortcodeReplacer $kref,
        ?int $creatorId
    ): void {
        $slug = (string) $section['slug'];
        $title = (string) ($section['title'] ?? $slug);
        $template = (string) ($section['template'] ?? SectionType::TEXT->value);

        if ($template === SectionType::CHARACTERISTIC_NORMS_CATALOG->value) {
            $this->ensureCatalogSection(
                $page,
                $slug,
                $title,
                (string) ($section['group'] ?? 'creature'),
                (string) ($section['entity'] ?? '*'),
                $order,
                $creatorId
            );

            return;
        }

        if ($template === SectionType::EQUIPMENT_BONUS_TABLE->value) {
            $this->ensureEquipmentBonusTableSection($page, $slug, $title, $order, $creatorId);

            return;
        }

        $html = is_string($section['html'] ?? null) ? $section['html'] : '';
        $this->ensureTextSection($page, $slug, $title, $kref->replace($html), $order, $creatorId);
    }

    /**
     * @param  list<string>  $expectedSlugs
     */
    private function removeOrphanSections(Page $page, array $expectedSlugs): void
    {
        Section::query()
            ->where('page_id', $page->id)
            ->whereNotIn('slug', $expectedSlugs === [] ? [''] : $expectedSlugs)
            ->each(fn (Section $section) => $section->delete());
    }

    private function archiveDeprecatedPages(Page $parent): void
    {
        foreach (self::DEPRECATED_SLUGS as $slug) {
            /** @var Page|null $page */
            $page = Page::withTrashed()->where('slug', $slug)->first();
            if (! $page instanceof Page) {
                continue;
            }
            if ($page->parent_id !== null && (int) $page->parent_id !== (int) $parent->id) {
                $page->parent_id = $parent->id;
                $page->save();
            }
            if (! $page->trashed()) {
                Page::destroy($page->id);
                $this->command?->info("🗑️ Ancienne page « {$slug} » archivée.");
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function loadConfig(): array
    {
        $path = database_path('seeders/data/creation-pages.php');
        if (! is_file($path)) {
            return [];
        }

        $config = require $path;

        return is_array($config) ? $config : [];
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
            $this->command?->info("♻️ Page {$slug} restaurée/mise à jour");

            return $page;
        }

        $page = Page::create($attributes);
        $this->command?->info("✅ Page {$slug} créée");

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
        $settings = [
            'align' => 'left',
            'size' => 'md',
            'enableRichReferences' => true,
        ];

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => $settings,
            'data' => ['content' => $contentHtml],
            'params' => ['content' => $contentHtml],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureCatalogSection(
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
            'characteristic_keys' => [],
        ];

        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'type' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'settings' => $settings,
            'data' => [],
            'params' => $settings,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
    }

    private function ensureEquipmentBonusTableSection(
        Page $page,
        string $slug,
        string $title,
        int $order,
        ?int $creatorId
    ): Section {
        return $this->ensureSection($page, $slug, [
            'title' => $title,
            'order' => $order,
            'template' => SectionType::EQUIPMENT_BONUS_TABLE->value,
            'type' => SectionType::EQUIPMENT_BONUS_TABLE->value,
            'settings' => [],
            'data' => [],
            'params' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $creatorId,
        ]);
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
