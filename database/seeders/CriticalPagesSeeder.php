<?php

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder idempotent des pages critiques (accueil, CGU).
 */
class CriticalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCreatorId = $this->resolveDefaultCreatorId();

        $homePage = $this->createOrRestoreBySlug([
            'title' => 'Accueil',
            'slug' => 'accueil',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 0,
            'menu_group' => null,
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
        ], 'Page Accueil');

        $this->ensureTextSection(
            $homePage,
            'hero-accueil',
            'Bienvenue',
            '<p>Bienvenue sur Krosmoz JDR.</p>',
            1,
            $defaultCreatorId
        );

        $cguPage = $this->createOrRestoreBySlug([
            'title' => 'Conditions Générales d\'Utilisation',
            'slug' => 'cgu',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
            'menu_order' => 999,
            'menu_group' => 'Informations',
            'parent_id' => null,
            'created_by' => $defaultCreatorId,
        ], 'Page CGU');

        $this->ensureTextSection(
            $cguPage,
            'intro-cgu',
            'Conditions Générales d\'Utilisation',
            '<p>Rédigez ici vos CGU et publiez les modifications en changeant l\'état de la page.</p>',
            1,
            $defaultCreatorId
        );
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function createOrRestoreBySlug(array $attributes, string $label): Page
    {
        $slug = (string) $attributes['slug'];
        $page = Page::withTrashed()->where('slug', $slug)->first();

        if ($page) {
            if ($page->trashed()) {
                $page->restore();
            }

            $page->fill($attributes);
            $page->save();
            $this->command?->info("♻️ {$label} restaurée/mise à jour ({$slug})");

            return $page;
        }

        $page = Page::create($attributes);
        $this->command?->info("✅ {$label} créée ({$slug})");

        return $page;
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

    private function ensureTextSection(
        Page $page,
        string $slug,
        string $title,
        string $contentHtml,
        int $order,
        ?int $creatorId
    ): Section {
        $section = Section::withTrashed()
            ->where('page_id', $page->id)
            ->where('slug', $slug)
            ->first();

        $attributes = [
            'page_id' => $page->id,
            'title' => $title,
            'slug' => $slug,
            'order' => $order,
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'settings' => [
                'align' => 'left',
                'size' => 'md',
            ],
            'data' => [
                'content' => $contentHtml,
            ],
            'params' => [
                'content' => $contentHtml,
            ],
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
            $this->command?->info("♻️ Section {$slug} restaurée/mise à jour");
            return $section;
        }

        $section = Section::create($attributes);
        $this->command?->info("✅ Section {$slug} créée");

        return $section;
    }
}

