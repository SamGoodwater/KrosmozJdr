<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/auth/ForgotPassword')
            ->has('status')
        );
    }

    public function test_reset_password_link_can_be_requested_for_user_with_password(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHasNoErrors();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_link_not_sent_for_oauth_only_user(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => null,
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHasNoErrors();
        Notification::assertNotSentTo($user, ResetPassword::class);
        $response->assertSessionHas('status');
        $response->assertSessionHas('statusType', 'info');
    }

    public function test_reset_password_link_requires_valid_email(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $token = '';
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;
            return true;
        });

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors();
    }
} 