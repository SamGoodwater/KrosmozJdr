<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Characteristic;
use App\Models\Scrapping\ScrappingEntityMapping;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour Admin\ScrappingMappingController.
 *
 * Vérifie : accès (guest → login, user → 403, admin → ok), index Inertia, store, update, destroy.
 */
class ScrappingMappingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (! Characteristic::where('key', 'level_creature')->exists()) {
            Characteristic::create([
                'key' => 'level_creature',
                'name' => 'Niveau',
                'type' => 'int',
                'sort_order' => 0,
                'group' => 'creature',
            ]);
        }
    }

    public function test_guest_redirected_to_login_on_index(): void
    {
        $response = $this->get(route('admin.scrapping-mappings.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_receives_403_on_index(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.scrapping-mappings.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.scrapping-mappings.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/scrapping-mappings/Index')
            ->has('source')
            ->has('entities')
            ->has('mappings')
            ->has('characteristicsForSelect')
        );
    }

    public function test_admin_index_accepts_mapping_key_query_param(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.scrapping-mappings.index', [
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'life',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/scrapping-mappings/Index')
            ->where('mappingKey', 'life')
        );
    }

    public function test_admin_index_exposes_mapping_key_even_without_existing_rule(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.scrapping-mappings.index', [
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'missing_key',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/scrapping-mappings/Index')
            ->where('mappingKey', 'missing_key')
        );
    }

    public function test_admin_can_store_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('admin.scrapping-mappings.store'), [
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'test_key',
            'from_path' => 'grades.0.level',
            'from_lang_aware' => false,
            'sort_order' => 0,
            'targets' => [
                ['target_model' => 'creatures', 'target_field' => 'level', 'sort_order' => 0],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Règle de mapping créée.'])
            ->assertJsonStructure(['mapping' => ['id', 'source', 'entity', 'mapping_key', 'from_path', 'targets']]);
        $this->assertDatabaseHas('scrapping_entity_mappings', [
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'test_key',
        ]);
    }

    public function test_admin_can_update_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $mapping = ScrappingEntityMapping::create([
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'old_key',
            'from_path' => 'grades.0.level',
            'from_lang_aware' => false,
            'sort_order' => 0,
        ]);
        $mapping->targets()->create([
            'target_model' => 'creatures',
            'target_field' => 'level',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin)->patchJson(route('admin.scrapping-mappings.update', $mapping->id), [
            'mapping_key' => 'updated_key',
            'from_path' => 'grades.0.lifePoints',
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
        $mapping->refresh();
        $this->assertSame('updated_key', $mapping->mapping_key);
        $this->assertSame('grades.0.lifePoints', $mapping->from_path);
    }

    public function test_admin_can_destroy_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $mapping = ScrappingEntityMapping::create([
            'source' => 'dofusdb',
            'entity' => 'monster',
            'mapping_key' => 'to_delete',
            'from_path' => 'id',
            'from_lang_aware' => false,
            'sort_order' => 0,
        ]);
        $id = $mapping->id;

        $response = $this->actingAs($admin)->deleteJson(route('admin.scrapping-mappings.destroy', $id));

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Règle de mapping supprimée.']);
        $this->assertDatabaseMissing('scrapping_entity_mappings', ['id' => $id]);
    }
}
