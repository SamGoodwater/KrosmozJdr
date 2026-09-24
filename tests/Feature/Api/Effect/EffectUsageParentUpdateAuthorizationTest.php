<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Effect;

use App\Models\Effect;
use App\Models\EffectDegree;
use App\Models\EffectUsage;
use App\Models\Entity\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Écriture effect_usages : exige `update` sur la fiche parente (SEC-02).
 */
final class EffectUsageParentUpdateAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_gm_cannot_store_usage_on_item_they_cannot_update(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $admin->id,
        ]);
        $degree = $this->makeDegree('usage-idor');

        $this->actingAs($gm)
            ->postJson('/api/effects/usages', [
                'entity_type' => 'item',
                'entity_id' => $item->id,
                'effect_degree_id' => $degree->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('effect_usages', [
            'entity_id' => $item->id,
            'effect_degree_id' => $degree->id,
        ]);
    }

    public function test_gm_can_store_usage_on_item_they_can_update(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $item = Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $gm->id,
        ]);
        $degree = $this->makeDegree('usage-ok');

        $this->actingAs($gm)
            ->postJson('/api/effects/usages', [
                'entity_type' => 'item',
                'entity_id' => $item->id,
                'effect_degree_id' => $degree->id,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('effect_usages', [
            'entity_type' => Item::class,
            'entity_id' => $item->id,
            'effect_degree_id' => $degree->id,
        ]);
    }

    public function test_gm_cannot_destroy_usage_on_item_they_cannot_update(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'state' => Item::STATE_DRAFT,
            'read_level' => User::ROLE_GAME_MASTER,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $admin->id,
        ]);
        $usage = EffectUsage::query()->create([
            'entity_type' => Item::class,
            'entity_id' => $item->id,
            'effect_degree_id' => $this->makeDegree('usage-del')->id,
        ]);

        $this->actingAs($gm)
            ->deleteJson('/api/effects/usages/'.$usage->id)
            ->assertForbidden();

        $this->assertDatabaseHas('effect_usages', ['id' => $usage->id]);
    }

    private function makeDegree(string $slug): EffectDegree
    {
        $effect = Effect::query()->create([
            'name' => $slug,
            'slug' => $slug,
            'description' => 'Effet de test '.$slug,
            'target_type' => Effect::TARGET_DIRECT,
        ]);

        return EffectDegree::query()->create([
            'effect_id' => $effect->id,
            'degree' => 1,
            'required_creature_level' => 1,
            'area' => '0',
            'slug' => $slug.'-d1',
        ]);
    }
}
