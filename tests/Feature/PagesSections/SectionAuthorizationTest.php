<?php

namespace Tests\Feature\PagesSections;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'autorisation (Policies) pour le système Pages/Sections.
 *
 * @example
 * php artisan test --filter=SectionAuthorizationTest
 */
class SectionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_authorized_user_cannot_create_section_on_restricted_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $payload = [
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Hello</p>'],
            'settings' => [],
        ];

        $this->actingAs($user)
            ->post(route('sections.store'), $payload)
            ->assertForbidden();
    }

    public function test_admin_can_create_section_on_page(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $page = Page::factory()->create([
            'created_by' => $admin->id,
            'state' => Page::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_ADMIN,
        ]);

        $payload = [
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
            'data' => ['content' => '<p>Hello</p>'],
            'settings' => [],
        ];

        $this->actingAs($admin)
            ->post(route('sections.store'), $payload)
            ->assertRedirect(route('pages.show', $page->slug));

        $this->assertDatabaseHas('sections', [
            'page_id' => $page->id,
            'template' => SectionType::TEXT->value,
        ]);
    }
}


