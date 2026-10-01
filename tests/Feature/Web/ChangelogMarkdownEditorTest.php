<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * @description Le journal public se lit sans compte ; seul un admin réécrit les fichiers markdown.
 */
class ChangelogMarkdownEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_roadmap_is_public(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('changelog/roadmap.md', "## 1.4 · Prochaine version\n\nLes campagnes.\n");

        $this->getJson(route('changelog.roadmap'))
            ->assertOk()
            ->assertJsonPath('steps.0.version', '1.4')
            ->assertJsonPath('steps.0.state', 'next')
            ->assertJsonPath('steps.0.text', 'Les campagnes.');
    }

    public function test_player_cannot_rewrite_a_changelog_file(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($player)
            ->putJson(route('changelog.sources.update', ['name' => 'roadmap']), [
                'markdown' => "## 1.4 · Prochaine version\n\nNon.\n",
            ])
            ->assertForbidden();
    }

    public function test_admin_saves_the_roadmap_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $markdown = "## 1.4 · Prochaine version\n\nFiches de joueurs.\n";

        $this->actingAs($admin)
            ->putJson(route('changelog.sources.update', ['name' => 'roadmap']), [
                'markdown' => $markdown,
            ])
            ->assertOk()
            ->assertJsonPath('saved', true);

        $this->assertSame($markdown, Storage::disk('public')->get('changelog/roadmap.md'));
    }

    public function test_a_path_outside_the_changelog_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->putJson('/changelog/sources/'.rawurlencode('../legal/cgu'), [
                'markdown' => 'non',
            ])
            ->assertNotFound();
    }
}
