<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunEntityPricesRecalculateJob;
use App\Models\ProjectConsoleJob;
use App\Models\User;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class EntityPricesRecalculateControllerTest extends TestCase
{
    public function test_admin_can_queue_item_price_recalc(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.content.entity-prices.run'), ['type' => 'items'])
            ->assertRedirect(route('admin.content.dashboard.index'))
            ->assertSessionHas('success');

        Bus::assertDispatched(RunEntityPricesRecalculateJob::class);
        $this->assertTrue(ProjectConsoleJob::hasActive(ProjectConsoleDomain::ENTITY_PRICES));
    }

    public function test_game_master_cannot_queue_price_recalc(): void
    {
        Bus::fake();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->post(route('admin.content.entity-prices.run'), ['type' => 'consumables'])
            ->assertForbidden();

        Bus::assertNothingDispatched();
    }
}
