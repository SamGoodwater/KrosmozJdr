<?php

namespace Tests\Feature\Requests;

use App\Enums\SectionType;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateSectionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_characteristic_norms_catalog_rejects_invalid_group_on_update(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $admin->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'settings' => [
                'group' => 'spell',
                'entity' => '*',
                'characteristic_keys' => [],
            ],
        ]);

        $response = $this->actingAs($admin)->patchJson(route('sections.update', $section->id), [
            'settings' => [
                'group' => 'invalid',
                'entity' => '*',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('settings.group');
    }

    public function test_characteristic_norms_catalog_accepts_valid_update_settings(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $page = Page::factory()->create(['created_by' => $admin->id]);
        $section = Section::factory()->create([
            'page_id' => $page->id,
            'created_by' => $admin->id,
            'template' => SectionType::CHARACTERISTIC_NORMS_CATALOG->value,
            'settings' => [
                'group' => 'spell',
                'entity' => '*',
                'characteristic_keys' => [],
            ],
        ]);

        $response = $this->actingAs($admin)->patch(route('sections.update', $section->id), [
            'settings' => [
                'group' => 'object',
                'entity' => '*',
                'characteristic_keys' => ['action_points_object'],
            ],
        ]);

        $response->assertRedirect();

        $section->refresh();
        $this->assertSame('object', $section->settings['group'] ?? null);
        $this->assertSame('*', $section->settings['entity'] ?? null);
        $this->assertSame(['action_points_object'], $section->settings['characteristic_keys'] ?? null);
    }
}
