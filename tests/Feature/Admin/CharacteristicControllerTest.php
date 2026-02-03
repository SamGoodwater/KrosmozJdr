<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\EntityCharacteristic;
use App\Models\User;
use Database\Seeders\EntityCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests Feature pour Admin\CharacteristicController.
 *
 * Prérequis : table entity_characteristics (migrations 2026_02_03_150000_*).
 * RefreshDatabase exécute les migrations ; en environnement de test MySQL, s’assurer que les migrations sont à jour.
 *
 * Vérifie que :
 * - Un invité est redirigé vers la connexion
 * - Un utilisateur (non admin) reçoit 403
 * - Un admin peut accéder à la liste, au détail, à la mise à jour et à l’API formula-preview
 * - Un super_admin a les mêmes accès
 */
class CharacteristicControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedEntityCharacteristics();
        app(\App\Services\Characteristic\CharacteristicService::class)->clearCache();
    }

    private function seedEntityCharacteristics(): void
    {
        $path = base_path('database/seeders/data/entity_characteristics.php');
        if (is_file($path)) {
            (new EntityCharacteristicSeeder)->run();
            return;
        }
        EntityCharacteristic::insert([
            ['entity' => 'class', 'characteristic_key' => 'life', 'name' => 'Points de vie', 'short_name' => 'PV', 'type' => 'int', 'min' => 1, 'max' => 500, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['entity' => 'monster', 'characteristic_key' => 'life', 'name' => 'Points de vie', 'short_name' => 'PV', 'type' => 'int', 'min' => 1, 'max' => 10000, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['entity' => 'class', 'characteristic_key' => 'level', 'name' => 'Niveau', 'short_name' => 'Niv', 'type' => 'int', 'min' => 1, 'max' => 200, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_guest_redirected_to_login_on_index(): void
    {
        $response = $this->get(route('admin.characteristics.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_on_show(): void
    {
        $response = $this->get(route('admin.characteristics.show', 'life'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_receives_403_on_index(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.characteristics.index'));

        $response->assertForbidden();
    }

    public function test_user_receives_403_on_show(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.characteristics.show', 'life'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.characteristics.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristics')
            ->where('selected', null)
        );
    }

    public function test_admin_can_access_show(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.characteristics.show', 'life'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristics')
            ->has('selected')
            ->where('selected.id', 'life')
        );
    }

    public function test_admin_can_update_characteristic(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = EntityCharacteristic::where('characteristic_key', 'life')->first();
        $this->assertNotNull($char);

        $response = $this->actingAs($admin)
            ->patch(route('admin.characteristics.update', 'life'), [
                'name' => 'Points de vie (modifié)',
                'short_name' => $char->short_name,
                'description' => $char->descriptions,
                'icon' => $char->icon,
                'color' => $char->color,
                'type' => 'int',
                'unit' => $char->unit,
                'sort_order' => $char->sort_order,
                'entities' => [],
            ]);

        $response->assertRedirect(route('admin.characteristics.show', 'life'));
        $this->assertDatabaseHas('entity_characteristics', [
            'characteristic_key' => 'life',
            'name' => 'Points de vie (modifié)',
        ]);
    }

    public function test_admin_can_call_formula_preview(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $url = route('admin.characteristics.formula-preview') . '?' . http_build_query([
            'characteristic_id' => 'life',
            'entity' => 'class',
            'variable' => 'level',
        ]);
        $response = $this->actingAs($admin)->getJson($url);

        $response->assertOk();
        $response->assertJsonStructure(['points']);
        $data = $response->json();
        $this->assertIsArray($data['points']);
    }

    public function test_formula_preview_accepts_optional_formula_param(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->getJson(route('admin.characteristics.formula-preview') . '?' . http_build_query([
            'characteristic_id' => 'life',
            'entity' => 'class',
            'variable' => 'level',
            'formula' => '[level] * 2',
        ]));

        $response->assertOk();
        $data = $response->json();
        $this->assertIsArray($data['points']);
        $this->assertGreaterThan(0, count($data['points']));
        $first = $data['points'][0];
        $this->assertArrayHasKey('x', $first);
        $this->assertArrayHasKey('y', $first);
        $this->assertEquals(2, $data['points'][0]['y']); // x=1 (min) -> [level]*2 = 2 (int ou float en JSON)
    }

    public function test_formula_preview_returns_422_when_params_invalid(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $url = route('admin.characteristics.formula-preview') . '?' . http_build_query([
            'characteristic_id' => 'life',
            'entity' => 'invalid_entity',
        ]);
        $response = $this->actingAs($admin)->getJson($url);

        $response->assertStatus(422);
    }

    public function test_super_admin_can_access_index(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->get(route('admin.characteristics.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristics')
        );
    }

    public function test_super_admin_can_update_characteristic(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $char = EntityCharacteristic::where('characteristic_key', 'level')->first();
        $this->assertNotNull($char);

        $response = $this->actingAs($superAdmin)
            ->patch(route('admin.characteristics.update', 'level'), [
                'name' => 'Niveau (modifié)',
                'short_name' => $char->short_name,
                'description' => $char->descriptions,
                'icon' => $char->icon,
                'color' => $char->color,
                'type' => 'int',
                'unit' => $char->unit,
                'sort_order' => $char->sort_order,
                'entities' => [],
            ]);

        $response->assertRedirect(route('admin.characteristics.show', 'level'));
        $this->assertDatabaseHas('entity_characteristics', [
            'characteristic_key' => 'level',
            'name' => 'Niveau (modifié)',
        ]);
    }
}
