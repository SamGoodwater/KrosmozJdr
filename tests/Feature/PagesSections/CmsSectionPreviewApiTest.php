<?php

namespace Tests\Feature\PagesSections;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * API d’aperçu des sections pour les références riches (survol type Wikipédia).
 *
 * @example
 * php artisan test --filter=CmsSectionPreviewApiTest
 */
class CmsSectionPreviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_snippet_returns_sanitized_html_for_authorized_guest(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $malicious = '<p>Extrait</p><script>alert(1)</script>';
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => $malicious],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $res = $this->getJson(route('api.cms.sections.preview-snippet', ['section' => $section->id]));

        $res->assertOk()
            ->assertJsonPath('canView', true)
            ->assertJsonPath('title', $section->title);

        $html = (string) $res->json('html');
        $this->assertStringContainsString('<p>Extrait</p>', $html);
        $this->assertStringNotContainsString('<script', strtolower($html));
    }

    public function test_preview_snippet_forbidden_when_section_not_readable_by_guest(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_ADMIN,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Secret</p>'],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_ADMIN,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $this->getJson(route('api.cms.sections.preview-snippet', ['section' => $section->id]))
            ->assertForbidden();
    }

    public function test_preview_snippet_allowed_for_authenticated_user_with_access(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_PLAYER,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Contenu joueur</p>'],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_PLAYER,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $res = $this->actingAs($player)
            ->getJson(route('api.cms.sections.preview-snippet', ['section' => $section->id]));

        $res->assertOk()
            ->assertJsonPath('canView', true);

        $this->assertStringContainsString('Contenu joueur', (string) $res->json('html'));
    }

    public function test_preview_snippet_by_page_and_section_slug_returns_excerpt_for_text_section(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'slug' => 'regles-test-kref-scroll',
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'slug' => 'regle-9-9-9-apercu',
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Extrait slug</p>'],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $res = $this->getJson(route('api.cms.sections.preview-snippet-query', [
            'page_slug' => 'regles-test-kref-scroll',
            'section_slug' => 'regle-9-9-9-apercu',
        ]));

        $res->assertOk()
            ->assertJsonPath('canView', true)
            ->assertJsonMissingPath('textPreviewOnly');

        $this->assertStringContainsString('Extrait slug', (string) $res->json('html'));
    }

    public function test_preview_snippet_truncates_after_ten_blocks(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $paragraphs = '';
        for ($i = 1; $i <= 15; $i++) {
            $paragraphs .= '<p>Paragraphe '.$i.'</p>';
        }

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => $paragraphs],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $html = (string) $this->getJson(route('api.cms.sections.preview-snippet', ['section' => $section->id]))
            ->assertOk()
            ->json('html');

        $this->assertStringContainsString('Paragraphe 1', $html);
        $this->assertStringContainsString('Paragraphe 10', $html);
        $this->assertStringNotContainsString('Paragraphe 11', $html);
        $this->assertStringContainsString('…', $html);
    }

    public function test_preview_snippet_truncates_list_items_in_order(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $items = '';
        for ($i = 1; $i <= 12; $i++) {
            $items .= '<li>Item '.$i.'</li>';
        }
        $content = '<ul>'.$items.'</ul>';

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => $content],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $html = (string) $this->getJson(route('api.cms.sections.preview-snippet', ['section' => $section->id]))
            ->assertOk()
            ->json('html');

        $this->assertStringContainsString('Item 1', $html);
        $this->assertStringContainsString('Item 10', $html);
        $this->assertStringNotContainsString('Item 11', $html);
        $this->assertStringContainsString('…', $html);
    }

    public function test_preview_snippet_by_slug_returns_empty_html_for_non_text_template(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'slug' => 'regles-test-kref-scroll-2',
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'slug' => 'regle-8-8-8-image',
            'created_by' => $admin->id,
            'template' => SectionType::IMAGE->value,
            'data' => ['content' => '<p>X</p>'],
            'settings' => [],
            'state' => Section::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $res = $this->getJson(route('api.cms.sections.preview-snippet-query', [
            'page_slug' => 'regles-test-kref-scroll-2',
            'section_slug' => 'regle-8-8-8-image',
        ]));

        $res->assertOk()
            ->assertJsonPath('textPreviewOnly', true)
            ->assertJsonPath('html', '');
    }
}
