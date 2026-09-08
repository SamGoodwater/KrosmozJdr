<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Services\PageService;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeederNpcLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_npc_library_page_with_entity_table(): void
    {
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'bibliotheque-npc')->first();
        $this->assertNotNull($page);
        $this->assertSame('PNJ', $page->title);
        $this->assertTrue($page->in_menu);
        $this->assertSame('Bibliothèques', $page->menu_group);
        $this->assertSame('npc', $page->entity_key);
        $this->assertSame(5, $page->menu_order);

        $section = Section::query()
            ->where('page_id', $page->id)
            ->where('slug', 'bibliotheque-npc-tableau')
            ->first();
        $this->assertNotNull($section);
        $this->assertSame(SectionType::ENTITY_TABLE, $section->template);
        $this->assertSame('npcs', $section->settings['entity'] ?? $section->data['entity'] ?? null);
    }

    public function test_menu_includes_npc_library_entry(): void
    {
        $this->seed(PageSeeder::class);
        PageService::clearMenuCache();

        $response = $this->getJson(route('pages.menu'));
        $response->assertOk();

        $libraries = collect($response->json('menu'))->firstWhere('id', 'bibliotheques');
        $this->assertIsArray($libraries);
        $npc = collect($libraries['children'] ?? [])->firstWhere('title', 'PNJ');
        $this->assertNotNull($npc);
        $this->assertSame('/pages/bibliotheque-npc', $npc['url']);
    }
}
