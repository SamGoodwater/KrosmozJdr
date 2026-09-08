<?php

namespace Tests\Feature\Pages;

use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\User;
use App\Services\BibliothequeEntityPageService;
use App\Services\PageService;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BibliothequeEntityPageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_creates_child_page_with_linked_entity_settings(): void
    {
        $this->seed(PageSeeder::class);

        $breed = Breed::factory()->create([
            'name' => 'Féca Test',
            'state' => Breed::STATE_PLAYABLE,
            'icon' => '/storage/images/entity/breeds/icon-test.webp',
        ]);

        $stats = app(BibliothequeEntityPageService::class)->syncAll(User::factory()->create()->id);

        $this->assertGreaterThanOrEqual(1, $stats['breeds']);

        $child = Page::query()->where('slug', 'classe-feca-test')->first();
        $this->assertNotNull($child);
        $parent = Page::query()->where('slug', BibliothequeEntityPageService::PARENT_SLUG_BREED)->first();
        $this->assertSame($parent?->id, $child->parent_id);
        $this->assertTrue($child->in_menu);
        $this->assertSame('breed', $child->settings['linked_entity']['type'] ?? null);
        $this->assertSame($breed->id, $child->settings['linked_entity']['id'] ?? null);
        $this->assertSame('/storage/images/entity/breeds/icon-test.webp', $child->icon);

        PageService::clearMenuCache();
    }

    public function test_sync_skips_draft_specializations_and_keeps_playable_in_menu(): void
    {
        $this->seed(PageSeeder::class);

        Specialization::factory()->create([
            'name' => 'Artisan Draft Test',
            'state' => Specialization::STATE_DRAFT,
        ]);
        $playable = Specialization::factory()->create([
            'name' => 'Milicien Playable Test',
            'state' => Specialization::STATE_PLAYABLE,
        ]);

        app(BibliothequeEntityPageService::class)->syncAll(User::factory()->create()->id);

        $this->assertNull(Page::query()->where('slug', 'specialisation-artisan-draft-test')->first());

        $playablePage = Page::query()->where('slug', 'specialisation-milicien-playable-test')->first();
        $this->assertNotNull($playablePage);
        $this->assertTrue($playablePage->in_menu);
        $this->assertSame($playable->id, $playablePage->settings['linked_entity']['id'] ?? null);

        PageService::clearMenuCache();
    }
}
