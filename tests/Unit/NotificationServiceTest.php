<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\ProfileModifiedNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Tests unitaires pour NotificationService — respect des préférences utilisateur.
 *
 * Vérifie que les notifications ne sont pas envoyées lorsque l'utilisateur
 * a désactivé les canaux correspondants.
 *
 * @see App\Services\NotificationService
 * @see docs/50-Fonctionnalités/Notifications/README.md
 */
class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    public function test_notify_profile_modified_does_not_notify_when_channels_empty(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'profile_modified' => ['channels' => [], 'frequency' => 'instant'],
            ],
        ]);
        $modifier = User::factory()->create();

        NotificationService::notifyProfileModified($user, $modifier);

        Notification::assertNothingSent();
    }

    public function test_notify_profile_modified_sends_when_mail_channel_enabled(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'profile_modified' => ['channels' => ['mail'], 'frequency' => 'instant'],
            ],
        ]);
        $modifier = User::factory()->create();

        NotificationService::notifyProfileModified($user, $modifier);

        Notification::assertSentTo($user, ProfileModifiedNotification::class);
    }

    public function test_notify_profile_modified_sends_when_database_channel_enabled(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'profile_modified' => ['channels' => ['database'], 'frequency' => 'instant'],
            ],
        ]);
        $modifier = User::factory()->create();

        NotificationService::notifyProfileModified($user, $modifier);

        Notification::assertSentTo($user, ProfileModifiedNotification::class);
    }

    public function test_notify_new_user_created_does_not_notify_admin_with_empty_channels(): void
    {
        $newUser = User::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'notification_preferences' => [
                'new_account_registered' => ['channels' => [], 'frequency' => 'instant'],
            ],
        ]);

        NotificationService::notifyNewUserCreated($newUser);

        Notification::assertNotSentTo($admin, \App\Notifications\NewUserCreatedNotification::class);
    }

    public function test_notify_new_user_created_notifies_admin_with_mail_channel(): void
    {
        $newUser = User::factory()->create();
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'notification_preferences' => [
                'new_account_registered' => ['channels' => ['mail'], 'frequency' => 'instant'],
            ],
        ]);

        NotificationService::notifyNewUserCreated($newUser);

        Notification::assertSentTo($admin, \App\Notifications\NewUserCreatedNotification::class);
    }

    public function test_notify_last_connection_does_not_notify_when_channels_empty(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'last_connection' => ['channels' => [], 'frequency' => 'instant'],
            ],
        ]);

        NotificationService::notifyLastConnection($user);

        Notification::assertNothingSent();
    }

    public function test_notify_last_connection_sends_when_database_enabled(): void
    {
        $user = User::factory()->create([
            'notification_preferences' => [
                'last_connection' => ['channels' => ['database'], 'frequency' => 'instant'],
            ],
        ]);

        NotificationService::notifyLastConnection($user);

        Notification::assertSentTo($user, \App\Notifications\LastConnectionNotification::class);
    }

    public function test_truncate_and_sanitize_strips_tags_and_truncates(): void
    {
        $long = str_repeat('a', 150);
        $result = NotificationService::truncateAndSanitize($long);
        $this->assertLessThanOrEqual(123, strlen($result));
        $this->assertStringEndsWith('...', $result);

        $withTags = '<script>alert(1)</script>Hello';
        $this->assertEquals('Hello', NotificationService::truncateAndSanitize($withTags));
    }
}
