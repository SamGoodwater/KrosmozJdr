<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\User;
use App\Services\Entity\EntityUpdateDiffService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

final class EntityUpdateDiffServiceTest extends TestCase
{
    public function test_remember_highlights_changed_fields_and_restore_reverts(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
        ]);
        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);

        $item->update([
            'name' => 'Cape nouvelle',
            'description' => 'après',
        ]);

        $diff = $svc->remember($user, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());
        $this->assertGreaterThan(0, $diff['changed_count']);
        $nameRow = collect($diff['fields'])->firstWhere('key', 'name');
        $this->assertNotNull($nameRow);
        $this->assertTrue($nameRow['changed']);
        $this->assertSame('Cape initiale', $nameRow['before']);
        $this->assertSame('Cape nouvelle', $nameRow['after']);

        $svc->restore($user, 'items', (int) $item->id, $diff['snapshot_id']);
        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame('avant', $item->description);
    }

    public function test_apply_restores_only_selected_keys(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Cape initiale',
            'description' => 'avant',
            'state' => EntityState::Draft->value,
        ]);
        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);

        $item->update([
            'name' => 'Cape nouvelle',
            'description' => 'après',
        ]);

        $diff = $svc->remember($user, 'items', (int) $item->id, 'dofusdb', $before, $item->fresh());
        $svc->apply($user, 'items', (int) $item->id, $diff['snapshot_id'], ['name']);

        $item->refresh();
        $this->assertSame('Cape initiale', $item->name);
        $this->assertSame('après', $item->description);
    }

    public function test_apply_without_keys_keeps_new_values(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Cape initiale',
            'description' => 'avant',
        ]);
        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $item->update(['name' => 'Cape nouvelle', 'description' => 'après']);
        $diff = $svc->remember($user, 'items', (int) $item->id, 'ia', $before, $item->fresh());

        $svc->apply($user, 'items', (int) $item->id, $diff['snapshot_id'], []);

        $item->refresh();
        $this->assertSame('Cape nouvelle', $item->name);
        $this->assertSame('après', $item->description);
    }

    public function test_apply_state_key_restores_playable_after_auto_conversion(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create([
            'name' => 'Cape or',
            'description' => 'texte jouable',
            'state' => EntityState::Playable->value,
        ]);
        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);

        $item->update([
            'name' => 'Cape auto',
            'state' => EntityState::Auto->value,
        ]);

        $diff = $svc->remember($user, 'items', (int) $item->id, 'ia', $before, $item->fresh());
        $svc->apply($user, 'items', (int) $item->id, $diff['snapshot_id'], ['state']);

        $item->refresh();
        $this->assertSame(EntityState::Playable->value, $item->state);
        $this->assertSame('Cape auto', $item->name);
        $this->assertSame('texte jouable', $item->description);
    }

    public function test_apply_rejects_snapshot_bound_to_another_entity(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $first = Item::factory()->create(['name' => 'Cape A', 'state' => EntityState::Draft->value]);
        $second = Item::factory()->create(['name' => 'Cape B', 'state' => EntityState::Draft->value]);
        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($first);
        $first->update(['name' => 'Cape A2']);
        $diff = $svc->remember($user, 'items', (int) $first->id, 'dofusdb', $before, $first->fresh());

        try {
            $svc->apply($user, 'items', (int) $second->id, $diff['snapshot_id'], ['name']);
            $this->fail('Expected snapshot mismatch.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $second->refresh();
        $this->assertSame('Cape B', $second->name);
    }

    public function test_identical_snapshots_report_zero_changes(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $item = Item::factory()->create(['name' => 'Figé']);
        $svc = app(EntityUpdateDiffService::class);
        $before = $svc->capture($item);
        $diff = $svc->remember($user, 'items', (int) $item->id, 'ia', $before, $item->fresh());
        $this->assertSame(0, $diff['changed_count']);
    }
}
