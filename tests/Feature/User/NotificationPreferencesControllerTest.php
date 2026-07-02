<?php

namespace Tests\Feature\User;

use App\Http\Middleware\CheckRole;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour la mise à jour des préférences de notification.
 *
 * Vérifie que le UserController normalise correctement les préférences
 * et que l'utilisateur peut les modifier depuis les paramètres.
 *
 * @see UserController::update
 * @see docs/features/notifications/README.md
 */
class NotificationPreferencesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(CheckRole::class);
        $this->withoutMiddleware([PreventRequestForgery::class]);
    }

    public function test_user_can_disable_notifications_globally_from_settings(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
        ]);

        $response = $this->actingAs($user)
            ->patchJson(route('user.update').'?redirect=settings', [
                'name' => $user->name,
                'email' => $user->email,
                'notifications_enabled' => false,
            ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertFalse($user->notifications_enabled);
    }

    public function test_user_can_update_notification_preferences_from_settings(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => null,
        ]);

        $response = $this->actingAs($user)
            ->patchJson(route('user.update').'?redirect=settings', [
                'name' => $user->name,
                'email' => $user->email,
                'notification_preferences' => [
                    'entity_modified' => ['database'],
                    'profile_modified' => ['database', 'mail'],
                    'last_connection' => [],
                ],
            ]);

        // Le contrôleur redirige avec 302 et success
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertIsArray($user->notification_preferences);

        // Format normalisé : { channels: [...], frequency: '...' }
        $this->assertArrayHasKey('entity_modified', $user->notification_preferences);
        $this->assertEquals(['database'], $user->notification_preferences['entity_modified']['channels']);

        $this->assertEqualsCanonicalizing(
            ['database', 'mail'],
            $user->notification_preferences['profile_modified']['channels']
        );

        $this->assertEquals([], $user->notification_preferences['last_connection']['channels']);
    }

    public function test_controller_normalizes_legacy_array_format_to_channels_object(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->patch(route('user.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'notification_preferences' => [
                    'entity_modified' => ['mail'], // Format legacy : tableau direct
                ],
            ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertEquals(
            ['channels' => ['mail'], 'frequency' => 'instant'],
            $user->notification_preferences['entity_modified']
        );
    }

    public function test_controller_ignores_unknown_notification_types(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->patch(route('user.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'notification_preferences' => [
                    'entity_modified' => ['mail'],
                    'unknown_type' => ['database'], // Type non présent dans config
                ],
            ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertArrayHasKey('entity_modified', $user->notification_preferences);
        $this->assertArrayNotHasKey('unknown_type', $user->notification_preferences);
    }

    public function test_user_can_disable_all_channels_for_a_type(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'entity_modified' => ['channels' => ['database', 'mail'], 'frequency' => 'instant'],
            ],
        ]);

        $response = $this->actingAs($user)
            ->from(route('user.edit'))
            ->patch(route('user.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'notification_preferences' => [
                    'entity_modified' => [],
                ],
            ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertEquals([], $user->notification_preferences['entity_modified']['channels']);
    }

    public function test_user_can_update_notification_frequency_from_settings(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => null,
        ]);

        $response = $this->actingAs($user)
            ->patchJson(route('user.update').'?redirect=settings', [
                'name' => $user->name,
                'email' => $user->email,
                'notification_preferences' => [
                    'profile_modified' => [
                        'channels' => ['mail'],
                        'frequency' => 'daily',
                    ],
                ],
            ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals(['mail'], $user->notification_preferences['profile_modified']['channels']);
        $this->assertSame('daily', $user->notification_preferences['profile_modified']['frequency']);
        $this->assertFalse($user->getChannelsForNotificationType('profile_modified') === []);
        $this->assertTrue($user->wantsNotificationForType('profile_modified'));
    }

    public function test_mail_channel_blocked_when_notifications_disabled_globally(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => false,
            'notification_preferences' => [
                'profile_modified' => ['channels' => ['database', 'mail'], 'frequency' => 'instant'],
            ],
        ]);

        $this->assertSame([], $user->getChannelsForNotificationType('profile_modified'));
        $this->assertFalse($user->wantsNotificationForType('profile_modified'));
    }

    public function test_settings_page_includes_notification_types(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.settings'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/user/Settings')
            ->has('notificationTypes')
            ->has('notificationChannelsLabels')
            ->has('notificationFrequencies')
        );
    }

    public function test_super_admin_can_save_all_notification_types_from_settings(): void
    {
        $types = config('notifications.types', []);
        $prefs = [];
        foreach ($types as $typeKey => $cfg) {
            if (isset($cfg['roles']) && ! in_array('super_admin', $cfg['roles'], true)) {
                continue;
            }
            $prefs[$typeKey] = [
                'channels' => $cfg['channels_default'] ?? ['database'],
                'frequency' => $cfg['frequency_default'] ?? 'instant',
            ];
        }

        $user = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
            'notification_channels' => ['database', 'mail'],
            'notification_preferences' => null,
        ]);

        $response = $this->actingAs($user)
            ->withHeaders(['X-Inertia' => 'true', 'X-Requested-With' => 'XMLHttpRequest'])
            ->from(route('user.settings'))
            ->patch(route('user.update').'?redirect=settings', [
                'notifications_enabled' => true,
                'notification_preferences' => $prefs,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertArrayHasKey('new_account_registered', $user->notification_preferences);
    }

    public function test_user_can_save_notification_settings_without_profile_fields(): void
    {
        $types = config('notifications.types', []);
        $prefs = [];
        foreach (array_keys($types) as $typeKey) {
            $cfg = $types[$typeKey];
            if (isset($cfg['roles']) && ! in_array('user', $cfg['roles'], true)) {
                continue;
            }
            $prefs[$typeKey] = [
                'channels' => $cfg['channels_default'] ?? ['database'],
                'frequency' => $cfg['frequency_default'] ?? 'instant',
            ];
        }

        $user = User::factory()->create([
            'notification_channels' => ['database', 'mail'],
            'notification_preferences' => null,
        ]);

        $response = $this->actingAs($user)
            ->patchJson(route('user.update').'?redirect=settings', [
                'notifications_enabled' => true,
                'notification_preferences' => $prefs,
            ]);

        $response->assertRedirect(route('user.settings').'#notifications');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame(['database', 'mail'], $user->notification_channels);
        $this->assertArrayHasKey('profile_modified', $user->notification_preferences);
    }
}
