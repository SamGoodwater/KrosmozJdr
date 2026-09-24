<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Entity;

use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\Entity\EntityLeveledSectionsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Payload léger availableSections (id/title/slug) pour l’édition Inertia.
 */
final class EntityLeveledSectionsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_sections_for_select_exposes_only_id_title_slug(): void
    {
        $author = User::factory()->create();
        $page = Page::factory()->create(['created_by' => $author->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $author->id,
            'title' => 'Section légère',
            'slug' => 'section-legere',
            'data' => ['blocks' => [['type' => 'paragraph', 'text' => 'payload lourd']]],
            'settings' => ['foo' => 'bar'],
        ]);

        $rows = (new EntityLeveledSectionsService)->availableSectionsForSelect(null, 50);

        $this->assertNotEmpty($rows);
        $match = collect($rows)->firstWhere('id', $section->id);
        $this->assertNotNull($match);
        $this->assertSame(['id', 'title', 'slug'], array_keys($match));
        $this->assertSame('Section légère', $match['title']);
        $this->assertSame('section-legere', $match['slug']);
        $this->assertArrayNotHasKey('data', $match);
        $this->assertArrayNotHasKey('settings', $match);
        $this->assertArrayNotHasKey('can', $match);
    }
}
