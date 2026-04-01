<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Suppression d’un degré isolé (admin.effects.destroy-degree).
 */
class EffectControllerDestroyDegreeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_redirected_to_login(): void
    {
        $effect = Effect::create(['name' => 'E', 'target_type' => Effect::TARGET_DIRECT]);
        EffectDegree::create(['effect_id' => $effect->id, 'degree' => 1]);
        $d2 = EffectDegree::create(['effect_id' => $effect->id, 'degree' => 2]);

        $this->delete(route('admin.effects.destroy-degree', [$effect->id, $d2->id]))
            ->assertRedirect(route('login'));
    }

    public function test_game_master_deletes_one_degree_when_at_least_two(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $effect = Effect::create(['name' => 'E', 'target_type' => Effect::TARGET_DIRECT]);
        EffectDegree::create(['effect_id' => $effect->id, 'degree' => 1]);
        $d2 = EffectDegree::create(['effect_id' => $effect->id, 'degree' => 2]);

        $response = $this->actingAs($user)->from(route('admin.effects.show', $effect))
            ->delete(route('admin.effects.destroy-degree', [$effect->id, $d2->id]));

        $response->assertRedirect(route('admin.effects.show', $effect));
        $this->assertDatabaseMissing('effect_degrees', ['id' => $d2->id]);
        $this->assertDatabaseCount('effect_degrees', 1);
    }

    public function test_cannot_delete_last_degree(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $effect = Effect::create(['name' => 'E', 'target_type' => Effect::TARGET_DIRECT]);
        $d1 = EffectDegree::create(['effect_id' => $effect->id, 'degree' => 1]);

        $response = $this->actingAs($user)->from(route('admin.effects.show', $effect))
            ->delete(route('admin.effects.destroy-degree', [$effect->id, $d1->id]));

        $response->assertRedirect(route('admin.effects.show', $effect));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('effect_degrees', ['id' => $d1->id]);
    }

    public function test_degree_must_belong_to_effect(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $e1 = Effect::create(['name' => 'A', 'target_type' => Effect::TARGET_DIRECT]);
        $e2 = Effect::create(['name' => 'B', 'target_type' => Effect::TARGET_DIRECT]);
        EffectDegree::create(['effect_id' => $e1->id, 'degree' => 1]);
        $d2 = EffectDegree::create(['effect_id' => $e2->id, 'degree' => 1]);

        $this->actingAs($user)->delete(route('admin.effects.destroy-degree', [$e1->id, $d2->id]))
            ->assertNotFound();
    }
}
