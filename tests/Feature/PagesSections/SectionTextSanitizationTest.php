<?php

namespace Tests\Feature\PagesSections;

use App\Enums\PageState;
use App\Enums\SectionType;
use App\Enums\Visibility;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests anti-XSS sur le contenu riche (v-html) des sections texte.
 *
 * @example
 * php artisan test --filter=SectionTextSanitizationTest
 */
class SectionTextSanitizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_text_content_is_sanitized_on_update(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => PageState::PUBLISHED->value,
            'is_visible' => Visibility::GUEST->value,
            'can_edit_role' => Visibility::ADMIN->value,
        ]);

        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Initial</p>'],
            'settings' => [],
            'state' => PageState::PUBLISHED->value,
        ]);

        $malicious = '<p>ok</p><script>alert(1)</script><img src="x" onerror="alert(2)" />';

        $this->actingAs($admin)
            ->patch(route('sections.update', ['section' => $section->id]), [
                'data' => ['content' => $malicious],
            ])
            ->assertRedirect(route('pages.show', $page->slug));

        $section->refresh();
        $content = (string) ($section->data['content'] ?? '');

        $this->assertStringContainsString('<p>ok</p>', $content);
        $this->assertStringNotContainsString('<script', strtolower($content));
        $this->assertStringNotContainsString('onerror', strtolower($content));
    }
}


