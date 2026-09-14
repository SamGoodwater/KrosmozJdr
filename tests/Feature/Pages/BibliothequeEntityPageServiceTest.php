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

        $parentFresh = $parent->fresh();
        $this->assertTrue($parentFresh?->settings['menu_collapsible'] ?? false);

        PageService::clearMenuCache();
    }

    public function test_sync_keeps_draft_pages_in_menu_with_gm_read_level(): void
    {
        $this->seed(PageSeeder::class);

        $draft = Specialization::factory()->create([
            'name' => 'Artisan Draft Test',
            'state' => Specialization::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $playable = Specialization::factory()->create([
            'name' => 'Milicien Playable Test',
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        app(BibliothequeEntityPageService::class)->syncAll(User::factory()->create()->id);

        $draftPage = Page::query()->where('slug', 'specialisation-artisan-draft-test')->first();
        $this->assertNotNull($draftPage);
        $this->assertTrue($draftPage->in_menu);
        $this->assertSame(User::ROLE_GAME_MASTER, (int) $draftPage->read_level);
        $this->assertSame($draft->id, $draftPage->settings['linked_entity']['id'] ?? null);

        $playablePage = Page::query()->where('slug', 'specialisation-milicien-playable-test')->first();
        $this->assertNotNull($playablePage);
        $this->assertTrue($playablePage->in_menu);
        $this->assertSame(User::ROLE_GUEST, (int) $playablePage->read_level);
        $this->assertSame($playable->id, $playablePage->settings['linked_entity']['id'] ?? null);

        PageService::clearMenuCache();
    }

    public function test_sync_removes_archived_entities_from_menu(): void
    {
        $this->seed(PageSeeder::class);

        $breed = Breed::factory()->create([
            'name' => 'Archived Menu Breed',
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $service = app(BibliothequeEntityPageService::class);
        $service->syncAll(User::factory()->create()->id);

        $child = Page::query()->where('slug', 'classe-archived-menu-breed')->first();
        $this->assertNotNull($child);
        $this->assertTrue($child->in_menu);

        $breed->update(['state' => Breed::STATE_ARCHIVED]);
        $service->syncAll($breed->created_by);

        $child->refresh();
        $this->assertFalse($child->in_menu);

        PageService::clearMenuCache();
    }

    public function test_menu_shows_draft_class_pages_to_gm_not_guest(): void
    {
        $this->seed(PageSeeder::class);

        Breed::factory()->create([
            'name' => 'Iop Draft Menu',
            'state' => Breed::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        Breed::factory()->create([
            'name' => 'Feca Playable Menu',
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        app(BibliothequeEntityPageService::class)->syncAll(User::factory()->create()->id);
        PageService::clearMenuCache();

        $guestClasses = $this->menuItemByTitles($this->getJson(route('pages.menu'))->assertOk()->json('menu'), 'bibliotheques', 'Classes');
        $this->assertNotNull($guestClasses);
        $this->assertNull(collect($guestClasses['children'] ?? [])->firstWhere('title', 'Iop Draft Menu'));
        $this->assertNotNull(collect($guestClasses['children'] ?? [])->firstWhere('title', 'Feca Playable Menu'));

        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $gmClasses = $this->menuItemByTitles(
            $this->actingAs($gm)->getJson(route('pages.menu'))->assertOk()->json('menu'),
            'bibliotheques',
            'Classes'
        );
        $this->assertNotNull($gmClasses);
        $this->assertTrue($gmClasses['menu_collapsible'] ?? false);
        $this->assertNotNull(collect($gmClasses['children'] ?? [])->firstWhere('title', 'Iop Draft Menu'));
        $this->assertNotNull(collect($gmClasses['children'] ?? [])->firstWhere('title', 'Feca Playable Menu'));

        PageService::clearMenuCache();
    }

    /**
     * @param  array<int, array<string, mixed>>  $menu
     * @return array<string, mixed>|null
     */
    private function menuItemByTitles(array $menu, string $groupId, string $title): ?array
    {
        $group = collect($menu)->firstWhere('id', $groupId);
        if (! is_array($group)) {
            return null;
        }

        $item = collect($group['children'] ?? [])->firstWhere('title', $title);

        return is_array($item) ? $item : null;
    }
}
