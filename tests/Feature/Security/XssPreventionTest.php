<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Page;
use App\Models\Section;
use App\Enums\SectionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de prévention XSS
 * 
 * Vérifie que le HTML dangereux est correctement sanitizé :
 * - <script> neutralisé
 * - Attributs onclick/onerror retirés
 * - HTML safe autorisé (<p>, <strong>, etc.)
 */
class XssPreventionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Section TEXT : les balises <script> sont neutralisées
     */
    public function test_section_text_sanitizes_script_tags(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'XSS Test Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => [
                'content' => '<p>Safe content</p><script>alert("XSS")</script>',
            ],
        ]);

        $response->assertStatus(302);

        $section = Section::where('title', 'XSS Test Section')->first();
        $this->assertNotNull($section);

        $content = $section->data['content'] ?? '';

        // Le <script> doit être retiré ou neutralisé
        $this->assertStringNotContainsString('<script>', $content);
        $this->assertStringNotContainsString('alert("XSS")', $content);

        // Le contenu safe doit être préservé
        $this->assertStringContainsString('Safe content', $content);
    }

    /**
     * Section TEXT : les attributs onclick sont retirés
     */
    public function test_section_text_sanitizes_onclick(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Onclick Test Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => [
                'content' => '<p onclick="alert(\'XSS\')">Click me</p>',
            ],
        ]);

        $response->assertStatus(302);

        $section = Section::where('title', 'Onclick Test Section')->first();
        $this->assertNotNull($section);

        $content = $section->data['content'] ?? '';

        // L'attribut onclick doit être retiré
        $this->assertStringNotContainsString('onclick', $content);
        $this->assertStringNotContainsString('alert', $content);

        // Le texte doit être préservé
        $this->assertStringContainsString('Click me', $content);
    }

    /**
     * Section TEXT : le HTML safe est autorisé (<p>, <strong>, etc.)
     */
    public function test_section_text_allows_safe_html(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $safeHtml = '<p>Paragraph with <strong>bold</strong> and <em>italic</em> text.</p><ul><li>Item 1</li><li>Item 2</li></ul>';

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Safe HTML Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => [
                'content' => $safeHtml,
            ],
        ]);

        $response->assertStatus(302);

        $section = Section::where('title', 'Safe HTML Section')->first();
        $this->assertNotNull($section);

        $content = $section->data['content'] ?? '';

        // Les balises safe doivent être préservées
        $this->assertStringContainsString('<p>', $content);
        $this->assertStringContainsString('<strong>', $content);
        $this->assertStringContainsString('<em>', $content);
        $this->assertStringContainsString('<ul>', $content);
        $this->assertStringContainsString('<li>', $content);

        // Le texte doit être préservé
        $this->assertStringContainsString('Paragraph with', $content);
        $this->assertStringContainsString('bold', $content);
        $this->assertStringContainsString('italic', $content);
    }

    /**
     * Section TEXT : les balises iframe sont retirées (sauf liste blanche)
     */
    public function test_section_text_sanitizes_iframe(): void
    {
        $admin = User::factory()->create(['role' => 4]);
        $page = Page::factory()->create(['created_by' => $admin->id]);

        $response = $this->actingAs($admin)->postJson(route('sections.store'), [
            'title' => 'Iframe Test Section',
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => [
                'content' => '<p>Content</p><iframe src="https://evil.com"></iframe>',
            ],
        ]);

        $response->assertStatus(302);

        $section = Section::where('title', 'Iframe Test Section')->first();
        $this->assertNotNull($section);

        $content = $section->data['content'] ?? '';

        // L'iframe doit être retiré (sauf si liste blanche dans config Purifier)
        // Selon la config, l'iframe peut être complètement retiré ou seulement l'attribut src
        // On vérifie que le contenu malveillant n'est pas présent
        $this->assertStringNotContainsString('evil.com', $content);

        // Le contenu safe doit être préservé
        $this->assertStringContainsString('Content', $content);
    }

    /**
     * Test d'UPDATE : la sanitization est aussi appliquée lors de la mise à jour
     */
    public function test_section_update_also_sanitizes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $admin->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Original content</p>'],
            'write_level' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->patchJson(route('sections.update', $section->id), [
            'data' => [
                'content' => '<p>Updated</p><script>alert("XSS")</script>',
            ],
        ]);

        $response->assertStatus(302); // Redirect après update

        $section->refresh();
        $content = $section->data['content'] ?? '';

        // Le <script> doit être retiré
        $this->assertStringNotContainsString('<script>', $content);
        $this->assertStringNotContainsString('alert', $content);

        // Le contenu safe doit être préservé
        $this->assertStringContainsString('Updated', $content);
    }
}

