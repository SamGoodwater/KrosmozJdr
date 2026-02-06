<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Characteristic;
use App\Models\User;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsMinimalCharacteristics;
use Tests\TestCase;

/**
 * Tests Feature pour Admin\CharacteristicController.
 *
 * Prérequis : tables characteristics + characteristic_creature/object/spell (nouvelle structure).
 * RefreshDatabase exécute les migrations ; les seeders peuplent les caractéristiques.
 *
 * Vérifie que :
 * - Un invité est redirigé vers la connexion
 * - Un utilisateur (non admin) reçoit 403
 * - Un admin peut accéder à la liste, au détail, à la mise à jour et à l'API formula-preview
 * - Un super_admin a les mêmes accès
 */
class CharacteristicControllerTest extends TestCase
{
    use RefreshDatabase;
    use SeedsMinimalCharacteristics;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCharacteristics();
        $this->seedMinimalCharacteristicsIfEmpty();
        $this->app->make(CharacteristicGetterService::class)->clearCache();
    }

    private function seedCharacteristics(): void
    {
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);
        $this->seed(ObjectCharacteristicSeeder::class);
        $this->seed(SpellCharacteristicSeeder::class);
    }

    public function test_guest_redirected_to_login_on_index(): void
    {
        $response = $this->get(route('admin.characteristics.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_on_show(): void
    {
        $response = $this->get(route('admin.characteristics.show', 'life_creature'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_redirected_to_login_on_create(): void
    {
        $response = $this->get(route('admin.characteristics.create'));

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

        $response = $this->actingAs($user)->get(route('admin.characteristics.show', 'life_creature'));

        $response->assertForbidden();
    }

    public function test_user_receives_403_on_create(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->get(route('admin.characteristics.create'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_create(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.characteristics.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
            ->where('createMode', true)
            ->has('groups')
            ->has('entitiesByGroup')
        );
    }

    public function test_admin_can_store_characteristic(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $uniqueKey = 'test_store_pa_creature';

        $response = $this->actingAs($admin)
            ->post(route('admin.characteristics.store'), [
                'key' => $uniqueKey,
                'name' => 'Points d\'action (test)',
                'short_name' => 'PA',
                'type' => 'int',
                'group' => 'creature',
                'entities' => [
                    ['entity' => '*', 'min' => '0', 'max' => '20', 'db_column' => 'pa', 'conversion_formula' => null, 'sort_order' => 0],
                ],
            ]);

        $response->assertRedirect(route('admin.characteristics.show', $uniqueKey));
        $this->assertDatabaseHas('characteristics', ['key' => $uniqueKey, 'name' => 'Points d\'action (test)']);
        $this->assertDatabaseHas('characteristic_creature', ['entity' => '*']);
    }

    public function test_store_automatically_adds_group_suffix_to_key(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $keyWithoutSuffix = 'test_life_dice';

        $response = $this->actingAs($admin)
            ->post(route('admin.characteristics.store'), [
                'key' => $keyWithoutSuffix,
                'name' => 'Dé de vie (test)',
                'type' => 'int',
                'group' => 'creature',
                'entities' => [
                    ['entity' => '*', 'min' => '4', 'max' => '20', 'db_column' => 'life_dice', 'conversion_formula' => null, 'sort_order' => 0],
                ],
            ]);

        $expectedKey = 'test_life_dice_creature';
        $response->assertRedirect(route('admin.characteristics.show', $expectedKey));
        $this->assertDatabaseHas('characteristics', ['key' => $expectedKey, 'name' => 'Dé de vie (test)']);
        $this->assertDatabaseMissing('characteristics', ['key' => $keyWithoutSuffix]);
    }

    public function test_store_validation_fails_duplicate_key(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->post(route('admin.characteristics.store'), [
                'key' => 'life_creature',
                'name' => 'Vie',
                'type' => 'int',
                'group' => 'creature',
                'entities' => [],
            ]);

        $response->assertSessionHasErrors('key');
    }

    public function test_admin_can_access_index(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.characteristics.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
            ->where('selected', null)
        );
    }

    public function test_admin_can_access_show(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.characteristics.show', 'life_creature'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
            ->has('selected')
            ->where('selected.id', 'life_creature')
        );
    }

    public function test_admin_can_update_characteristic(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $char = Characteristic::where('key', 'life_creature')->first();
        $this->assertNotNull($char);

        $response = $this->actingAs($admin)
            ->patch(route('admin.characteristics.update', 'life_creature'), [
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

        $response->assertRedirect(route('admin.characteristics.show', 'life_creature'));
        $this->assertDatabaseHas('characteristics', [
            'key' => 'life_creature',
            'name' => 'Points de vie (modifié)',
        ]);
    }

    public function test_admin_can_call_formula_preview(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $url = route('admin.characteristics.formula-preview') . '?' . http_build_query([
            'characteristic_id' => 'life_creature',
            'entity' => 'monster',
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
            'characteristic_id' => 'life_creature',
            'entity' => 'monster',
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

    public function test_formula_preview_returns_empty_points_when_entity_invalid(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $url = route('admin.characteristics.formula-preview') . '?' . http_build_query([
            'characteristic_id' => 'life_creature',
            'entity' => 'invalid_entity',
        ]);
        $response = $this->actingAs($admin)->getJson($url);

        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('points', $data);
        $this->assertSame([], $data['points']);
    }

    public function test_super_admin_can_access_index(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->get(route('admin.characteristics.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/characteristics/Index')
            ->has('characteristicsByGroup')
        );
    }

    public function test_super_admin_can_update_characteristic(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $char = Characteristic::where('key', 'level_creature')->first();
        $this->assertNotNull($char);

        $response = $this->actingAs($superAdmin)
            ->patch(route('admin.characteristics.update', 'level_creature'), [
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

        $response->assertRedirect(route('admin.characteristics.show', 'level_creature'));
        $this->assertDatabaseHas('characteristics', [
            'key' => 'level_creature',
            'name' => 'Niveau (modifié)',
        ]);
    }
}
