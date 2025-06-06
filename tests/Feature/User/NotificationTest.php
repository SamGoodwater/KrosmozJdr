<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Services\NotificationService;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_update_triggers_notification()
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->actingAs($user)->patch('/user', [
            'name' => 'Updated Name',
        ]);
        // On vérifie que le service a bien été appelé (ici, on vérifie la notification Laravel, sinon mock le service)
        Notification::assertSentTo($user, \App\Notifications\ProfileModifiedNotification::class);
    }

    public function test_notification_respects_user_preferences()
    {
        Notification::fake();
        $user = User::factory()->create([
            'notifications_enabled' => false,
        ]);
        $this->actingAs($user)->patch('/user', [
            'name' => 'Updated Name',
        ]);
        Notification::assertNothingSent();
    }
}
