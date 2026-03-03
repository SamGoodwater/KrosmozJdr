<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\DofusdbEffectMapping;
use App\Models\SubEffect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour Admin\DofusdbEffectMappingController.
 *
 * Vérifie : accès (guest → login, user → 403, admin → ok), index Inertia, store, update, destroy.
 */
class DofusdbEffectMappingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (! SubEffect::where('slug', 'frapper')->exists()) {
            SubEffect::create([
                'slug' => 'frapper',
                'type_slug' => 'frapper',
                'template_text' => 'Dégâts [value] [characteristic].',
                'variables_allowed' => ['value', 'characteristic'],
                'param_schema' => ['action' => 'frapper', 'params' => []],
            ]);
        }
    }

    public function test_guest_redirected_to_login_on_index(): void
    {
        $response = $this->get(route('admin.dofusdb-effect-mappings.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_receives_403_on_index(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.dofusdb-effect-mappings.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dofusdb-effect-mappings.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/dofusdb-effect-mappings/Index')
            ->has('mappings')
            ->has('subEffectsForSelect')
            ->has('characteristicSourceOptions')
            ->has('characteristicsForSelect')
        );
    }

    public function test_admin_index_accepts_effect_id_query_param(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.dofusdb-effect-mappings.index', [
            'effect_id' => 96,
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/dofusdb-effect-mappings/Index')
            ->where('effectIdFilter', '96')
        );
    }

    public function test_admin_can_store_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->postJson(route('admin.dofusdb-effect-mappings.store'), [
            'dofusdb_effect_id' => 96,
            'sub_effect_slug' => 'frapper',
            'characteristic_source' => 'element',
            'characteristic_key' => null,
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Mapping créé.'])
            ->assertJsonStructure(['mapping' => ['id', 'dofusdb_effect_id', 'sub_effect_slug', 'characteristic_source']]);
        $this->assertDatabaseHas('dofusdb_effect_mappings', [
            'dofusdb_effect_id' => 96,
            'sub_effect_slug' => 'frapper',
            'characteristic_source' => 'element',
        ]);
    }

    public function test_admin_can_update_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $mapping = DofusdbEffectMapping::create([
            'dofusdb_effect_id' => 97,
            'sub_effect_slug' => 'frapper',
            'characteristic_source' => 'element',
            'characteristic_key' => null,
        ]);

        $response = $this->actingAs($admin)->patchJson(route('admin.dofusdb-effect-mappings.update', $mapping), [
            'sub_effect_slug' => 'frapper',
            'characteristic_source' => 'none',
            'characteristic_key' => null,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
        $mapping->refresh();
        $this->assertSame('none', $mapping->characteristic_source);
    }

    public function test_admin_can_destroy_mapping(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $mapping = DofusdbEffectMapping::create([
            'dofusdb_effect_id' => 98,
            'sub_effect_slug' => 'frapper',
            'characteristic_source' => 'element',
            'characteristic_key' => null,
        ]);
        $id = $mapping->id;

        $response = $this->actingAs($admin)->deleteJson(route('admin.dofusdb-effect-mappings.destroy', $mapping));

        $response->assertOk()
            ->assertJson(['success' => true, 'message' => 'Mapping supprimé.']);
        $this->assertDatabaseMissing('dofusdb_effect_mappings', ['id' => $id]);
    }
}
