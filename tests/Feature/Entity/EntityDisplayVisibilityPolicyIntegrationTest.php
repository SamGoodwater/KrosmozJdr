<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\ApplicationSetting;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\EntityDisplay\EntityDisplayVisibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Phase A — la matrice « Gérer l’affichage » restreint réellement la policy view.
 */
class EntityDisplayVisibilityPolicyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_restrictive_matrix_blocks_guest_view_on_playable_spell(): void
    {
        $visibilityService = app(EntityDisplayVisibilityService::class);

        ApplicationSetting::query()->updateOrCreate(
            ['key' => EntityDisplayVisibilityService::SETTINGS_KEY],
            ['value' => [
                'spells' => [
                    'playable' => User::ROLE_ADMIN,
                ],
            ]],
        );
        $visibilityService->forgetRulesCache();

        $spell = Spell::factory()->create([
            'name' => 'Sort matrice visibilité',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->assertFalse(Gate::forUser(null)->allows('view', $spell));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->assertTrue(Gate::forUser($admin)->allows('view', $spell));
    }

    public function test_restrictive_matrix_excludes_spell_from_global_search_for_guest(): void
    {
        $token = 'GlVisPolicyTok'.uniqid();
        $visibilityService = app(EntityDisplayVisibilityService::class);

        ApplicationSetting::query()->updateOrCreate(
            ['key' => EntityDisplayVisibilityService::SETTINGS_KEY],
            ['value' => [
                'spells' => [
                    'playable' => User::ROLE_ADMIN,
                ],
            ]],
        );
        $visibilityService->forgetRulesCache();

        Spell::factory()->create([
            'name' => 'Sort '.$token,
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->getJson('/api/global-search?'.http_build_query([
            'q' => $token,
            'types' => ['spells'],
        ]));

        $response->assertOk();
        $this->assertSame([], $response->json('results'));
    }

    public function test_auto_spell_is_hidden_from_player_and_visible_to_game_master(): void
    {
        $spell = Spell::factory()->create([
            'name' => 'Sort auto visibilité',
            'state' => Spell::STATE_AUTO,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->assertFalse(Gate::forUser($player)->allows('view', $spell));
        $this->assertTrue(Gate::forUser($gm)->allows('view', $spell));
    }
}
