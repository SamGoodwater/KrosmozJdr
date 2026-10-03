<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\LoadingTip;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LoadingTipAdminTest extends TestCase
{
    public function test_guest_is_redirected_from_admin_loading_tips_index(): void
    {
        $this->get(route('admin.loading-tips.index'))
            ->assertRedirect();
    }

    public function test_non_admin_cannot_view_admin_loading_tips_index(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('admin.loading-tips.index'))
            ->assertForbidden();
    }

    public function test_game_master_cannot_view_admin_loading_tips_index(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.loading-tips.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_admin_loading_tips_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        LoadingTip::factory()->create(['body' => 'Astuce visible admin']);

        $this->actingAsConfirmed($admin)
            ->get(route('admin.loading-tips.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/loading-tips/Index')
                ->has('tips')
                ->where('tips.0.body', 'Astuce visible admin'));
    }

    public function test_admin_can_create_loading_tip(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->post(route('admin.loading-tips.store'), [
                'body' => 'Rejoins-nous sur Discord.',
                'url' => 'https://discord.gg/XVu4VWFskj',
                'featured' => true,
                'is_active' => true,
                'duration_seconds' => 12,
            ])
            ->assertRedirect(route('admin.loading-tips.index'));

        $this->assertDatabaseHas('loading_tips', [
            'body' => 'Rejoins-nous sur Discord.',
            'url' => 'https://discord.gg/XVu4VWFskj',
            'featured' => 1,
            'is_active' => 1,
            'duration_seconds' => 12,
        ]);
    }

    public function test_admin_cannot_create_loading_tip_with_invalid_duration(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->from(route('admin.loading-tips.index'))
            ->post(route('admin.loading-tips.store'), [
                'body' => 'Durée trop courte',
                'featured' => false,
                'is_active' => true,
                'duration_seconds' => 1,
            ])
            ->assertRedirect(route('admin.loading-tips.index'))
            ->assertSessionHasErrors('duration_seconds');
    }

    public function test_admin_cannot_create_loading_tip_with_invalid_url(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAsConfirmed($admin)
            ->from(route('admin.loading-tips.index'))
            ->post(route('admin.loading-tips.store'), [
                'body' => 'Lien invalide',
                'url' => 'javascript:alert(1)',
                'featured' => false,
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.loading-tips.index'))
            ->assertSessionHasErrors('url');

        $this->assertDatabaseMissing('loading_tips', [
            'body' => 'Lien invalide',
        ]);
    }

    public function test_admin_can_update_and_delete_loading_tip(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $tip = LoadingTip::factory()->create([
            'body' => 'Ancienne phrase',
            'featured' => false,
        ]);

        $this->actingAsConfirmed($admin)
            ->patch(route('admin.loading-tips.update', $tip), [
                'body' => 'Phrase mise à jour',
                'url' => null,
                'featured' => true,
                'is_active' => false,
                'duration_seconds' => 10,
            ])
            ->assertRedirect(route('admin.loading-tips.index'));

        $this->assertDatabaseHas('loading_tips', [
            'id' => $tip->id,
            'body' => 'Phrase mise à jour',
            'featured' => 1,
            'is_active' => 0,
            'duration_seconds' => 10,
        ]);

        $this->actingAsConfirmed($admin)
            ->delete(route('admin.loading-tips.destroy', $tip))
            ->assertRedirect(route('admin.loading-tips.index'));

        $this->assertDatabaseMissing('loading_tips', ['id' => $tip->id]);
    }

    public function test_shared_loading_tips_only_include_active_tips(): void
    {
        LoadingTip::query()->delete();

        LoadingTip::factory()->create([
            'body' => 'Active tip',
            'url' => 'https://github.com/SamGoodwater/KrosmozJdr',
            'featured' => true,
            'is_active' => true,
            'duration_seconds' => 15,
        ]);
        LoadingTip::factory()->inactive()->create([
            'body' => 'Inactive tip',
            'featured' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('loadingTips', 1)
                ->where('loadingTips.0.body', 'Active tip')
                ->where('loadingTips.0.url', 'https://github.com/SamGoodwater/KrosmozJdr')
                ->where('loadingTips.0.featured', true)
                ->where('loadingTips.0.duration_seconds', 15)
                ->missing('loadingTips.0.id')
                ->missing('loadingTips.0.is_active'));
    }
}
