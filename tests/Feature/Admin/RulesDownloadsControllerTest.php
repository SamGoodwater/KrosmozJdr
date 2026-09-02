<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\RunRulesCompileDownloadsJob;
use App\Models\ProjectConsoleJob;
use App\Models\User;
use App\Support\Project\ProjectConsoleDomain;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class RulesDownloadsControllerTest extends TestCase
{
    public function test_admin_can_queue_rules_compile(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.content.rules-downloads.run'))
            ->assertRedirect(route('admin.content.dashboard.index'))
            ->assertSessionHas('success');

        Bus::assertDispatched(RunRulesCompileDownloadsJob::class);
    }

    public function test_game_master_cannot_queue_rules_compile(): void
    {
        Bus::fake();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->post(route('admin.content.rules-downloads.run'))
            ->assertForbidden();

        Bus::assertNothingDispatched();
    }

    public function test_admin_content_dashboard_exposes_downloads_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.content.dashboard.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Content/Dashboard/Index')
                ->has('rulesDownloads.available')
                ->has('rulesDownloads.missing')
                ->where('consoleJob', null));
    }

    public function test_second_compile_is_rejected_while_busy(): void
    {
        Bus::fake();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.content.rules-downloads.run'))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post(route('admin.content.rules-downloads.run'))
            ->assertRedirect(route('admin.content.dashboard.index'))
            ->assertSessionHas('error');

        Bus::assertDispatchedTimes(RunRulesCompileDownloadsJob::class, 1);
        $this->assertTrue(ProjectConsoleJob::hasActive(ProjectConsoleDomain::RULES_DOWNLOADS));
    }
}
