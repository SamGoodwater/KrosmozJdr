<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\User;
use App\Services\Entity\EntityUpdateDiffService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Apply / restore d’instantané : pas d’IDOR entre comptes.
 */
class EntityUpdateDiffControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_other_admin_cannot_apply_or_restore_foreign_snapshot(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $other = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'created_by' => $owner->id,
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
        ]);

        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $item->update(['name' => 'Cape nouvelle', 'description' => 'après']);
        $diff = $svc->remember($owner, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());
        $snapshotId = $diff['snapshot_id'];

        $this->actingAs($other)
            ->postJson("/api/entities/items/{$item->id}/update-diff/apply", [
                'snapshot_id' => $snapshotId,
                'restore_keys' => ['name'],
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->actingAs($other)
            ->postJson("/api/entities/items/{$item->id}/update-diff/restore", [
                'snapshot_id' => $snapshotId,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $item->refresh();
        $this->assertSame('Cape nouvelle', $item->name);
        $this->assertSame('après', $item->description);

        $this->actingAs($owner)
            ->postJson("/api/entities/items/{$item->id}/update-diff/restore", [
                'snapshot_id' => $snapshotId,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame('avant', $item->description);
    }
}
