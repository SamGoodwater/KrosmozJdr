<?php

declare(strict_types=1);

namespace Tests\Feature\GenerativeAi;

use App\Enums\EntityState;
use App\Models\AiGenerationRun;
use App\Models\Entity\Monster;
use App\Models\User;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\GenerativeAiClient;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Accès IA : admin / super-admin + confirmation mot de passe (même fenêtre que la gestion admin).
 */
final class IaConvertAuthorizationTest extends TestCase
{
    public function test_guest_cannot_convert_or_read_status(): void
    {
        $monster = $this->sourceMonster();

        $this->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
            'action' => 'encounter',
        ])->assertUnauthorized();

        $this->getJson(route('api.ia.status'))->assertUnauthorized();
    }

    public function test_registered_user_cannot_convert(): void
    {
        $this->assertRoleForbiddenFromIa(User::ROLE_USER);
    }

    public function test_player_cannot_convert(): void
    {
        $this->assertRoleForbiddenFromIa(User::ROLE_PLAYER);
    }

    public function test_game_master_cannot_convert_even_with_password_session(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $monster = $this->sourceMonster();

        $this->actingAsConfirmed($gm)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertForbidden();

        $this->actingAsConfirmed($gm)
            ->getJson(route('api.ia.status'))
            ->assertForbidden();

        Http::assertSentCount(0);
    }

    public function test_admin_without_password_confirmation_receives_423(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = $this->sourceMonster();

        $this->actingAs($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertStatus(423);

        $this->actingAs($admin)
            ->getJson(route('api.ia.status'))
            ->assertStatus(423);

        $this->assertDatabaseCount('ai_generation_runs', 0);
        Http::assertSentCount(0);
    }

    public function test_admin_with_password_confirmation_can_convert(): void
    {
        $this->fakeAnthropicEncounter();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->seedPlayableEtalon();
        $monster = $this->sourceMonster();

        $this->actingAsConfirmed($admin)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', AiGenerationRun::STATUS_SUCCESS);

        $this->actingAsConfirmed($admin)
            ->getJson(route('api.ia.status'))
            ->assertOk()
            ->assertJsonPath('usage.has_api_key', true);
    }

    public function test_super_admin_with_password_confirmation_can_read_status(): void
    {
        $super = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->actingAsConfirmed($super)
            ->getJson(route('api.ia.status'))
            ->assertOk();
    }

    public function test_expired_password_confirmation_is_rejected(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $monster = $this->sourceMonster();
        $expired = time() - 10_000;

        $this->actingAs($admin)
            ->withSession([
                'auth.password_confirmed_at' => $expired,
                'auth.password_last_activity_at' => $expired,
            ])
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertStatus(423);

        Http::assertSentCount(0);
    }

    public function test_pipeline_rejects_null_user(): void
    {
        $this->expectException(HttpException::class);
        ConversionRequest::assertUserMayGenerate(null);
    }

    public function test_pipeline_rejects_player(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $this->expectException(HttpException::class);
        ConversionRequest::assertUserMayGenerate($player);
    }

    public function test_artisan_without_user_is_rejected(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $monster = $this->sourceMonster();

        $this->artisan('ia:convert', [
            'type' => 'encounter',
            '--id' => $monster->id,
        ])->assertFailed();

        $this->assertSame(EntityState::Raw->value, $monster->fresh()->state);
        Http::assertSentCount(0);
    }

    public function test_artisan_with_player_user_is_rejected(): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $monster = $this->sourceMonster();

        $this->artisan('ia:convert', [
            'type' => 'encounter',
            '--id' => $monster->id,
            '--user' => $player->id,
        ])->assertFailed();

        $this->assertSame(EntityState::Raw->value, $monster->fresh()->state);
        Http::assertSentCount(0);
    }

    private function assertRoleForbiddenFromIa(int $role): void
    {
        Http::fake();
        Http::preventStrayRequests();
        $user = User::factory()->create(['role' => $role]);
        $monster = $this->sourceMonster();

        $this->actingAsConfirmed($user)
            ->postJson(route('api.entities.ia-convert', ['entityType' => 'monsters', 'id' => $monster->id]), [
                'action' => 'encounter',
            ])
            ->assertForbidden();

        $this->actingAsConfirmed($user)
            ->getJson(route('api.ia.status'))
            ->assertForbidden();

        Http::assertSentCount(0);
    }

    private function fakeAnthropicEncounter(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);
        Http::preventStrayRequests();
        Http::fake([
            '*anthropic.com/v1/messages' => Http::response([
                'model' => 'claude-haiku-4-5',
                'content' => [[
                    'type' => 'tool_use',
                    'name' => GenerativeAiClient::TOOL_NAME,
                    'input' => [
                        'monster' => [],
                        'spells' => [
                            ['name' => 'Bec', 'effect' => '1d6 Air', 'pa' => '3', 'element' => 'air'],
                            ['name' => 'Picore', 'effect' => '1d4 Air', 'pa' => '2', 'element' => 'air'],
                        ],
                    ],
                ]],
                'usage' => [
                    'input_tokens' => 80,
                    'output_tokens' => 20,
                ],
            ], 200),
        ]);
    }

    private function seedPlayableEtalon(): Monster
    {
        return Monster::factory()->create([
            'official_id' => 'jdr:bestiary:piou-vert',
            'state' => EntityState::Playable->value,
            'auto_update' => false,
        ]);
    }

    private function sourceMonster(): Monster
    {
        $monster = Monster::factory()->create([
            'official_id' => 'dofus:auth-source',
            'state' => EntityState::Raw->value,
            'auto_update' => true,
        ]);
        $monster->creature?->update([
            'pa' => '6',
            'state' => EntityState::Raw->value,
        ]);

        return $monster->fresh(['creature']);
    }
}
