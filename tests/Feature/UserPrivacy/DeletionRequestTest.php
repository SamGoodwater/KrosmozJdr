<?php

namespace Tests\Feature\UserPrivacy;

use App\Models\DataSubjectRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletion_request_requires_recent_password_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.privacy.delete.request'), [
            'current_password' => 'password',
        ]);

        $response->assertRedirect(route('password.confirm'));
    }

    public function test_deletion_request_fails_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->from(route('user.privacy.index'))
            ->post(route('user.privacy.delete.request'), [
                'current_password' => 'wrong-password',
            ]);

        $response->assertRedirect(route('user.privacy.index'));
        $response->assertSessionHasErrors('current_password');
    }

    public function test_deletion_request_is_created_when_payload_is_valid(): void
    {
        $user = User::factory()->create([
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->post(route('user.privacy.delete.request'), [
                'current_password' => 'password',
            ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('data_subject_requests', [
            'user_id' => $user->id,
            'type' => DataSubjectRequest::TYPE_ERASURE,
            'status' => DataSubjectRequest::STATUS_PENDING,
        ]);
    }
}

