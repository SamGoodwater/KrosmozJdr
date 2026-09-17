<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\NewUserCreatedNotification;
use App\Notifications\ProfileModifiedNotification;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Tests API du centre de notifications (filtres admin, métadonnées).
 */
class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([PreventRequestForgery::class]);
    }

    public function test_admin_can_filter_actionable_notifications(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $newUser = User::factory()->create();

        $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => NewUserCreatedNotification::class,
            'data' => [
                'config_type' => 'new_account_registered',
                'message' => 'Nouveau compte créé',
                'url' => '/users/'.$newUser->id,
            ],
        ]);
        $admin->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => ProfileModifiedNotification::class,
            'data' => [
                'config_type' => 'profile_modified',
                'message' => 'Profil modifié',
                'url' => '/users/'.$admin->id,
            ],
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('notifications.index', ['scope' => 'admin_action']));

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.category', 'admin_action');
        $response->assertJsonPath('data.0.requires_action', true);
        $response->assertJsonPath('data.0.action_label', 'Examiner le compte');
    }
}
