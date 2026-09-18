<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Support\Cms\RulesImportSlugHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesImportRulesTocPlacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_chapter_five_goes_to_gm_menu_and_downloads_stay_in_rules(): void
    {
        User::factory()->create([
            'email' => User::SYSTEM_USER_EMAIL,
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $chapterFive = Page::factory()->create([
            'title' => 'Ressources et équilibrage',
            'slug' => 'regles-5-ressources-et-equilibrage',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'menu_group' => 'Règles',
            'menu_order' => 5,
            'parent_id' => null,
        ]);
        Page::factory()->create([
            'title' => 'Ressources',
            'slug' => 'ressources-de-jeu',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'menu_group' => 'Règles',
            'menu_order' => 4,
            'parent_id' => $chapterFive->id,
        ]);

        $dir = sys_get_temp_dir().'/krosmoz-toc-'.uniqid('', true);
        mkdir($dir, 0775, true);
        file_put_contents($dir.'/TABLE_DES_MATIERES.md', <<<'MD'
## 1. Introduction

### 1.1 Présentation du jeu

- **1.1.1** Concept général

## 5. Ressources et équilibrage

### 5.2 Principes d'équilibrage

- **5.2.3** Sorts et aptitudes
MD);

        try {
            $this->artisan('pages:import-rules-toc', ['path' => $dir.'/TABLE_DES_MATIERES.md'])
                ->assertSuccessful();

            $intro = Page::query()->where('slug', 'regles-1-introduction')->first();
            $this->assertNotNull($intro);
            $this->assertSame('Règles', $intro->menu_group);
            $this->assertSame(User::ROLE_GUEST, $intro->read_level);

            $chapterFive->refresh();
            $this->assertSame('Pour les MJ', $chapterFive->menu_group);
            $this->assertSame(User::ROLE_GAME_MASTER, $chapterFive->read_level);
            $this->assertNull($chapterFive->parent_id);

            $balance = Page::query()->where(
                'slug',
                RulesImportSlugHelper::buildPageSlug('5.2', "Principes d'équilibrage")
            )->first();
            $this->assertNotNull($balance);
            $this->assertSame('Pour les MJ', $balance->menu_group);
            $this->assertSame(User::ROLE_GAME_MASTER, $balance->read_level);
            $this->assertSame($chapterFive->id, $balance->parent_id);

            $section = Section::query()->where(
                'slug',
                RulesImportSlugHelper::buildSectionSlug('5.2.3', 'Sorts et aptitudes')
            )->first();
            $this->assertNotNull($section);
            $this->assertSame(User::ROLE_GAME_MASTER, $section->read_level);

            $downloads = Page::query()->where('slug', 'ressources-de-jeu')->first();
            $this->assertNotNull($downloads);
            $this->assertNull($downloads->parent_id);
            $this->assertSame('Règles', $downloads->menu_group);
            $this->assertSame(User::ROLE_GUEST, $downloads->read_level);
            $this->assertSame(90, $downloads->menu_order);
        } finally {
            @unlink($dir.'/TABLE_DES_MATIERES.md');
            @rmdir($dir);
        }
    }

    public function test_retired_chapter_six_pages_leave_the_menu(): void
    {
        User::factory()->create([
            'email' => User::SYSTEM_USER_EMAIL,
            'role' => User::ROLE_SUPER_ADMIN,
        ]);
        Page::factory()->create([
            'title' => 'Annexes',
            'slug' => 'regles-6-annexes',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'menu_group' => 'Règles',
            'menu_order' => 6,
            'parent_id' => null,
        ]);

        $dir = sys_get_temp_dir().'/krosmoz-toc-'.uniqid('', true);
        mkdir($dir, 0775, true);
        file_put_contents($dir.'/TABLE_DES_MATIERES.md', "## 1. Introduction\n\n### 1.1 Présentation du jeu\n\n- **1.1.1** Concept général\n");

        try {
            $this->artisan('pages:import-rules-toc', ['path' => $dir.'/TABLE_DES_MATIERES.md'])
                ->assertSuccessful();

            $annex = Page::query()->where('slug', 'regles-6-annexes')->first();
            $this->assertNotNull($annex);
            $this->assertFalse((bool) $annex->in_menu);
            $this->assertNull($annex->menu_group);
            $this->assertSame(Page::STATE_ARCHIVED, $annex->state);
        } finally {
            @unlink($dir.'/TABLE_DES_MATIERES.md');
            @rmdir($dir);
        }
    }
}
