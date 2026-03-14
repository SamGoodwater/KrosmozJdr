<?php

namespace Tests\Unit\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitaires pour les préférences de notification du modèle User.
 *
 * @see User::getChannelsForNotificationType
 * @see User::wantsNotificationForType
 * @see User::getFrequencyForNotificationType
 * @see User::wantsNotification
 */
class NotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_channels_uses_config_default_when_no_preferences(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
            'notification_preferences' => null,
        ]);

        $channels = $user->getChannelsForNotificationType('entity_modified');
        $this->assertEquals(['database', 'mail'], $channels);
    }

    public function test_get_channels_returns_preference_with_channels_object_format(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
            'notification_preferences' => [
                'entity_modified' => ['channels' => ['mail'], 'frequency' => 'instant'],
            ],
        ]);

        $channels = $user->getChannelsForNotificationType('entity_modified');
        $this->assertEquals(['mail'], $channels);
    }

    public function test_get_channels_returns_preference_with_legacy_array_format(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
            'notification_preferences' => [
                'entity_modified' => ['database', 'mail'],
            ],
        ]);

        $channels = $user->getChannelsForNotificationType('entity_modified');
        $this->assertEqualsCanonicalizing(['database', 'mail'], $channels);
    }

    public function test_get_channels_returns_empty_when_preference_is_off(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
            'notification_preferences' => [
                'entity_modified' => ['channels' => [], 'frequency' => 'instant'],
            ],
        ]);

        $channels = $user->getChannelsForNotificationType('entity_modified');
        $this->assertEquals([], $channels);
    }

    public function test_get_channels_filters_invalid_channels(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
            'notification_preferences' => [
                'entity_modified' => ['channels' => ['database', 'mail', 'invalid', 'push'], 'frequency' => 'instant'],
            ],
        ]);

        $channels = $user->getChannelsForNotificationType('entity_modified');
        $this->assertEqualsCanonicalizing(['database', 'mail'], $channels);
    }

    public function test_get_channels_returns_empty_when_notifications_disabled(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => false,
            'notification_preferences' => null,
        ]);

        $channels = $user->getChannelsForNotificationType('entity_modified');
        $this->assertEquals([], $channels);
    }

    public function test_get_channels_uses_notification_channels_fallback_when_type_not_in_config(): void
    {
        $user = User::factory()->create([
            'notifications_enabled' => true,
            'notification_channels' => ['mail'],
            'notification_preferences' => [],
        ]);

        $channels = $user->getChannelsForNotificationType('unknown_type');
        $this->assertEquals(['mail'], $channels);
    }

    public function test_wants_notification_for_type_returns_true_when_channels_non_empty(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'entity_modified' => ['channels' => ['database'], 'frequency' => 'instant'],
            ],
        ]);

        $this->assertTrue($user->wantsNotificationForType('entity_modified'));
    }

    public function test_wants_notification_for_type_returns_false_when_channels_empty(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'entity_modified' => ['channels' => [], 'frequency' => 'instant'],
            ],
        ]);

        $this->assertFalse($user->wantsNotificationForType('entity_modified'));
    }

    public function test_get_frequency_returns_preference_value(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'entity_modified' => ['channels' => ['mail'], 'frequency' => 'daily'],
            ],
        ]);

        $this->assertEquals('daily', $user->getFrequencyForNotificationType('entity_modified'));
    }

    public function test_get_frequency_returns_config_default_when_not_set(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [],
        ]);

        $this->assertEquals('instant', $user->getFrequencyForNotificationType('entity_modified'));
    }

    public function test_get_frequency_returns_instant_for_weekly_config(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'new_account_registered' => ['channels' => ['mail'], 'frequency' => 'weekly'],
            ],
        ]);

        $this->assertEquals('weekly', $user->getFrequencyForNotificationType('new_account_registered'));
    }

    public function test_wants_notification_without_type_uses_notifications_enabled(): void
    {
        $user = User::factory()->create(['notifications_enabled' => true]);
        $this->assertTrue($user->wantsNotification());

        $userDisabled = User::factory()->create(['notifications_enabled' => false]);
        $this->assertFalse($userDisabled->wantsNotification());
    }

    public function test_wants_notification_with_type_delegates_to_wants_notification_for_type(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => ['entity_modified' => ['channels' => ['mail']]],
        ]);
        $this->assertTrue($user->wantsNotification('entity_modified'));

        $userNoPref = User::factory()->create(['notification_preferences' => []]);
        $this->assertTrue($userNoPref->wantsNotification('entity_modified')); // config default
    }
}
