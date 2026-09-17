<?php

namespace Tests\Feature\Admin;

use App\Models\Entity\Language;
use App\Models\User;
use Tests\TestCase;

class LanguageAdminTest extends TestCase
{
    public function test_guest_is_redirected_from_admin_languages_index(): void
    {
        $this->get(route('admin.languages.index'))
            ->assertRedirect();
    }

    public function test_non_admin_cannot_view_admin_languages_index(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($user)
            ->get(route('admin.languages.index'))
            ->assertForbidden();
    }

    public function test_game_master_cannot_view_admin_languages_index(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('admin.languages.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_admin_languages_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.languages.index'))
            ->assertOk();
    }

    public function test_admin_can_create_language_without_password_confirmation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('admin.languages.store'), [
                'name' => 'Commun (test)',
                'description' => 'Description test',
                'color' => '#aabbcc',
            ])
            ->assertRedirect(route('admin.languages.index'));

        $this->assertDatabaseHas('languages', [
            'name' => 'Commun (test)',
            'color' => '#aabbcc',
        ]);
    }

    public function test_admin_can_delete_language_without_password_confirmation(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $language = Language::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.languages.destroy', $language))
            ->assertRedirect(route('admin.languages.index'));

        $this->assertDatabaseMissing('languages', ['id' => $language->id]);
    }
}
