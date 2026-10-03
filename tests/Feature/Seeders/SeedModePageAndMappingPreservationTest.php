<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\Section;
use App\Models\User;
use App\Support\Seeder\SeedMode;
use Database\Seeders\CriticalPagesSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\ScrappingEntityMappingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pages CMS et mappings scrapping : conservation hors overwrite.
 */
final class SeedModePageAndMappingPreservationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        SeedMode::clearForce();
        parent::tearDown();
    }

    public function test_page_seeder_keeps_hand_added_section_without_overwrite(): void
    {
        SeedMode::forceOverwrite(false);
        $this->seed(CriticalPagesSeeder::class);
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'accueil')->first()
            ?? Page::query()->where('slug', 'home')->first();
        if ($page === null) {
            $page = Page::query()->where('slug', 'contribution')->first();
        }
        $this->assertNotNull($page);

        $hand = Section::factory()->create([
            'page_id' => $page->id,
            'slug' => 'section-ajoutee-a-la-main',
            'title' => 'Ajout manuel',
            'template' => SectionType::TEXT->value,
            'type' => SectionType::TEXT->value,
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $this->seed(PageSeeder::class);
        $this->seed(CriticalPagesSeeder::class);

        $this->assertDatabaseHas('sections', [
            'id' => $hand->id,
            'slug' => 'section-ajoutee-a-la-main',
            'deleted_at' => null,
        ]);
    }

    public function test_scrapping_mapping_seeder_skips_when_rows_exist_without_overwrite(): void
    {
        SeedMode::forceOverwrite(true);
        $this->seed(ScrappingEntityMappingSeeder::class);
        $count = ScrappingEntityMapping::query()->count();
        $this->assertGreaterThan(0, $count);

        ScrappingEntityMapping::query()->limit(1)->update(['from_path' => 'admin.edited.path']);

        SeedMode::forceOverwrite(false);
        $this->seed(ScrappingEntityMappingSeeder::class);

        $this->assertSame($count, ScrappingEntityMapping::query()->count());
        $this->assertDatabaseHas('scrapping_entity_mappings', [
            'from_path' => 'admin.edited.path',
        ]);
    }
}
