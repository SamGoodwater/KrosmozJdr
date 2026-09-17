<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeederResourcesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_nests_ressources_page_under_chapter_five_when_parent_exists(): void
    {
        $parent = Page::factory()->create([
            'title' => 'Ressources et équilibrage',
            'slug' => 'regles-5-ressources-et-equilibrage',
            'in_menu' => true,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'menu_group' => 'Règles',
            'menu_order' => 5,
            'parent_id' => null,
        ]);

        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'ressources-de-jeu')->first();
        $this->assertNotNull($page);
        $this->assertSame($parent->id, $page->parent_id);
        $this->assertSame('Règles', $page->menu_group);
        $this->assertSame(4, $page->menu_order);
        $this->assertTrue($page->in_menu);

        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'slug' => 'ressources-de-jeu-fichiers',
            'template' => SectionType::DOWNLOAD_CATALOG->value,
        ]);
    }

    public function test_keeps_ressources_page_at_rules_root_when_parent_is_missing(): void
    {
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'ressources-de-jeu')->first();
        $this->assertNotNull($page);
        $this->assertNull($page->parent_id);
        $this->assertSame('Règles', $page->menu_group);
        $this->assertSame(90, $page->menu_order);
    }
}
