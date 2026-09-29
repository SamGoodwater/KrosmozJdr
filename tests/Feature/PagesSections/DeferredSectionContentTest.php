<?php

declare(strict_types=1);

namespace Tests\Feature\PagesSections;

use App\Enums\SectionType;
use App\Http\Middleware\CheckRole;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Services\SectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * HTML différé sur pages.show : ne doit pas écraser le contenu en base.
 *
 * @example php artisan test --filter=DeferredSectionContentTest
 */
class DeferredSectionContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
    }

    public function test_page_show_defers_html_after_the_first_three_sections(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = $this->makePage($admin);
        $sections = $this->makeTextSections($page, $admin, 4);

        $this->actingAs($admin)
            ->get(route('pages.show', $page->slug))
            ->assertOk()
            ->assertInertia(function (Assert $inertia) use ($sections): void {
                $inertia->component('Pages/page/Show')->has('page');
                $rows = $this->inertiaSections($inertia);
                $this->assertCount(4, $rows);

                foreach ([0, 1, 2] as $index) {
                    $this->assertFalse((bool) ($rows[$index]['content_deferred'] ?? false));
                    $this->assertSame(
                        '<p>HTML '.$sections[$index]->order.'</p>',
                        $rows[$index]['data']['content'] ?? null
                    );
                }

                $deferred = $rows[3];
                $this->assertTrue((bool) ($deferred['content_deferred'] ?? false));
                $this->assertTrue((bool) ($deferred['data']['content_deferred'] ?? false));
                $this->assertNull($deferred['data']['content'] ?? null);
            });

        $this->actingAs($admin)
            ->getJson(route('api.cms.sections.content', $sections[3]))
            ->assertOk()
            ->assertJsonPath('data.content', '<p>HTML 4</p>');
    }

    public function test_deferred_placeholder_patch_does_not_wipe_stored_html(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = $this->makePage($admin);
        $section = $this->makeTextSections($page, $admin, 1)[0];
        $original = '<p>Chapitre à conserver</p>';
        $section->update(['data' => ['content' => $original]]);

        $this->actingAs($admin)
            ->from(route('pages.show', $page->slug))
            ->patch(route('sections.update', $section), [
                'data' => [
                    'content' => null,
                    'content_deferred' => true,
                ],
            ])
            ->assertRedirect(route('pages.show', $page->slug));

        $section->refresh();
        $this->assertSame($original, $section->data['content'] ?? null);
        $this->assertArrayNotHasKey('content_deferred', $section->data ?? []);
    }

    public function test_empty_tiptap_html_with_deferred_flag_does_not_wipe_stored_html(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = $this->makePage($admin);
        $section = $this->makeTextSections($page, $admin, 1)[0];
        $original = '<p>Texte réel</p>';
        $section->update(['data' => ['content' => $original]]);

        SectionService::update($section, [
            'data' => [
                'content' => '<p></p>',
                'content_deferred' => true,
            ],
        ], $admin);

        $section->refresh();
        $this->assertSame($original, $section->data['content'] ?? null);
        $this->assertArrayNotHasKey('content_deferred', $section->data ?? []);
    }

    public function test_intentional_empty_content_without_deferred_flag_still_clears(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = $this->makePage($admin);
        $section = $this->makeTextSections($page, $admin, 1)[0];
        $section->update(['data' => ['content' => '<p>À vider</p>']]);

        $this->actingAs($admin)
            ->from(route('pages.show', $page->slug))
            ->patch(route('sections.update', $section), [
                'data' => ['content' => ''],
            ])
            ->assertRedirect(route('pages.show', $page->slug));

        $section->refresh();
        $this->assertSame('', $section->data['content'] ?? null);
    }

    public function test_real_content_update_still_persists(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = $this->makePage($admin);
        $section = $this->makeTextSections($page, $admin, 1)[0];

        $this->actingAs($admin)
            ->from(route('pages.show', $page->slug))
            ->patch(route('sections.update', $section), [
                'data' => ['content' => '<p>Nouvelle version</p>'],
            ])
            ->assertRedirect(route('pages.show', $page->slug));

        $section->refresh();
        $this->assertStringContainsString('Nouvelle version', (string) ($section->data['content'] ?? ''));
    }

    private function makePage(User $admin): Page
    {
        return Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * @return list<Section>
     */
    private function makeTextSections(Page $page, User $admin, int $count): array
    {
        $sections = [];
        for ($i = 1; $i <= $count; $i++) {
            $sections[] = Section::factory()->create([
                'page_id' => $page->id,
                'created_by' => $admin->id,
                'template' => SectionType::TEXT->value,
                'order' => $i,
                'title' => 'Section '.$i,
                'data' => ['content' => '<p>HTML '.$i.'</p>'],
                'settings' => ['align' => 'left', 'size' => 'md'],
                'state' => Section::STATE_PLAYABLE,
                'read_level' => User::ROLE_GUEST,
                'write_level' => User::ROLE_ADMIN,
            ]);
        }

        return $sections;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function inertiaSections(Assert $inertia): array
    {
        $props = $inertia->toArray();
        $page = $props['props']['page'] ?? $props['page'] ?? [];
        if (isset($page['data']['sections']) && is_array($page['data']['sections'])) {
            return array_values($page['data']['sections']);
        }
        if (isset($page['sections']) && is_array($page['sections'])) {
            return array_values($page['sections']);
        }

        $this->fail('Impossible de lire page.sections dans le payload Inertia.');
    }
}
