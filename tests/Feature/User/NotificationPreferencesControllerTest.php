<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour la mise à jour des préférences de notification.
 *
 * Vérifie que le UserController normalise correctement les préférences
 * et que l'utilisateur peut les modifier depuis les paramètres.
 *
 * @see UserController::update
 * @see docs/50-Fonctionnalités/Notifications/README.md
 */
class NotificationPreferencesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\CheckRole::class);
        $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    }

    public function test_user_can_update_notification_preferences_from_settings(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => null,
        ]);

        $response = $this->actingAs($user)
            ->patch(route('user.update') . '?redirect=settings', [
                'notification_preferences' => [
                    'entity_modified' => ['database'],
                    'profile_modified' => ['database', 'mail'],
                    'last_connection' => [],
                ],
            ]);

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
            ->patch(route('user.update'), [
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
            ->patch(route('user.update'), [
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
            ->patch(route('user.update'), [
                'notification_preferences' => [
                    'entity_modified' => [],
                ],
            ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertEquals([], $user->notification_preferences['entity_modified']['channels']);
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
}
