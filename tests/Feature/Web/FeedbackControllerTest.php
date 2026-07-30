<?php

declare(strict_types=1);

namespace Tests\Feature\Web;

use App\Http\Controllers\FeedbackController;
use App\Mail\FeedbackMail;
use App\Mail\FeedbackRecapMail;
use App\Models\FeedbackThread;
use App\Models\User;
use App\Notifications\FeedbackThreadNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * @see FeedbackController
 */
class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_submit_feedback_without_recap(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->post(route('feedback.store'), [
            'type' => 'bug',
            'message' => 'Un bug sur la page d’accueil',
            'email_recap' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(FeedbackMail::class);
        Mail::assertNotSent(FeedbackRecapMail::class);
        unset($admin);
    }

    public function test_authenticated_user_creates_thread_and_receives_recap_only_when_opted_in(): void
    {
        Mail::fake();
        Notification::fake();

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'email' => 'admin-feedback@example.test']);

        $user = User::factory()->create([
            'role' => User::ROLE_PLAYER,
            'email' => 'player-feedback@example.test',
        ]);

        $this->actingAs($user)->post(route('feedback.store'), [
            'type' => 'suggestion',
            'message' => 'Améliorer la recherche',
            'email_recap' => false,
        ])->assertRedirect();

        $this->assertDatabaseHas('feedback_threads', [
            'user_id' => $user->id,
            'type' => 'suggestion',
            'staff_unread_count' => 1,
        ]);
        $this->assertDatabaseHas('feedback_messages', [
            'author_id' => $user->id,
            'author_role' => 'user',
            'body' => 'Améliorer la recherche',
        ]);
        Mail::assertNotSent(FeedbackMail::class);
        Mail::assertNotSent(FeedbackRecapMail::class);
        Notification::assertSentTo($admin, FeedbackThreadNotification::class);

        Mail::fake();

        $this->actingAs($user)->post(route('feedback.store'), [
            'type' => 'suggestion',
            'message' => 'Autre suggestion',
            'email_recap' => true,
        ])->assertSessionHas('success');

        Mail::assertSent(FeedbackRecapMail::class, function (FeedbackRecapMail $mail) use ($user) {
            return $mail->hasTo($user->email)
                && str_contains($mail->feedbackMessage, 'Autre suggestion');
        });

        $this->assertSame(2, FeedbackThread::query()->where('user_id', $user->id)->count());
    }

    public function test_message_is_required(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->post(route('feedback.store'), [
            'type' => 'bug',
            'message' => '',
        ])->assertSessionHasErrors('message');
    }
}
