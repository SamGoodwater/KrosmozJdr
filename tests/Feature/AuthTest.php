<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function testUserRegistration()
    {
        $response = $this->withMiddleware()
            ->post(route('register.add'), [
                'name' => 'Test User',
                'email' => 'testuser@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function testUserUpdate()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->withMiddleware()
            ->patch(route('user.update', $user), [
                'name' => 'Updated User',
                'email' => 'updateduser@example.com',
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'email' => 'updateduser@example.com',
        ]);
    }

    public function testUserDeletion()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->withMiddleware()
            ->delete(route('user.delete', $user));

        $response->assertStatus(302);
        $this->assertSoftDeleted('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function testUserRestoration()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->delete();

        $response = $this->withMiddleware()
            ->post(route('user.restore', $user));

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function testUserForceDeletion()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->withMiddleware()
            ->delete(route('user.forcedDelete', $user));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function testUserLogin()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->withMiddleware()
            ->post(route('login.connect'), [
                'email' => 'testuser@example.com',
                'password' => 'password',
            ]);

        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }
}
