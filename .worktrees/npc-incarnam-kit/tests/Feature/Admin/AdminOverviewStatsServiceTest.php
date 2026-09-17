<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\User;
use App\Services\Admin\AdminOverviewStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOverviewStatsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_overview_includes_spell_states_and_cms_counts(): void
    {
        Spell::factory()->create(['state' => Spell::STATE_PLAYABLE]);
        Spell::factory()->create(['state' => Spell::STATE_DRAFT]);
        Page::factory()->create();

        $overview = app(AdminOverviewStatsService::class)->contentOverview();

        $spells = collect($overview['entities'])->firstWhere('key', 'spells');
        $this->assertNotNull($spells);
        $this->assertGreaterThanOrEqual(2, $spells['total']);
        $this->assertGreaterThanOrEqual(1, $overview['cms']['pages']);
    }

    public function test_admin_recap_groups_users_by_role(): void
    {
        User::factory()->create(['role' => User::ROLE_PLAYER]);
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $recap = app(AdminOverviewStatsService::class)->adminRecap();

        $this->assertGreaterThanOrEqual(2, $recap['totals']['users']);
        $this->assertCount(6, $recap['usersByRole']);
        $this->assertCount(12, $recap['userGrowth']);
    }
}
