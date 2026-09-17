<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @description Routes Phase B légal/changelog Markdown : exposition same-origin depuis le stockage public.
 *
 * @example GET `legal.cgu` renvoie le corps Markdown de `storage/app/public/legal/cgu.md`.
 */
class LegalAndChangelogMarkdownRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_legal_named_routes_serve_storage_markdown(): void
    {
        $response = $this->get(route('legal.cgu'));
        $response->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $this->assertStringContainsString('Conditions', $response->getContent() ?: '');
    }

    public function test_changelog_feed_composes_navigation_and_intro(): void
    {
        $response = $this->get(route('changelog.feed', ['version' => '1.3.2']));
        $response->assertOk()->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
        $body = $response->getContent() ?: '';
        $this->assertStringContainsString('Navigation des versions', $body);
        $this->assertStringContainsString('**1.3.2**', $body);
        $this->assertStringContainsString('/changelog/feed/1.3.1', $body);
    }

    public function test_unknown_changelog_semver_returns_404(): void
    {
        $this->get(route('changelog.feed', ['version' => '9.9.9']))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_access_privacy_hub(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.privacy.index'));
        $response->assertSuccessful();
        $response->assertInertia(fn ($page) => $page->component('Pages/user/Privacy'));
    }
}
