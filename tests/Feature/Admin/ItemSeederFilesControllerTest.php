<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Models\User;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ItemSeederFilesControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/items-seeder-admin-'.uniqid());
        $this->app->bind(
            ItemSeederFileRepository::class,
            fn (): ItemSeederFileRepository => new ItemSeederFileRepository($this->root)
        );
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_admin_without_super_admin_role_is_forbidden(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->withSession($this->passwordConfirmedSession())
            ->postJson(route('admin.content.ia-generation.items-seeder.export'))
            ->assertForbidden();
    }

    public function test_export_without_password_confirmation_is_rejected(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->postJson(route('admin.content.ia-generation.items-seeder.export'))
            ->assertStatus(423);
    }

    public function test_super_admin_exports_auto_items_then_replays_them(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $type = ItemType::query()->create([
            'name' => 'Cape',
            'dofusdb_type_id' => 17,
            'state' => ItemType::STATE_PLAYABLE,
            'read_level' => 0,
            'write_level' => 3,
            'show_in_catalog' => true,
            'allow_scrap' => false,
        ]);
        Item::factory()->create([
            'name' => 'Cape du Wa Wobot',
            'level' => '8',
            'state' => Item::STATE_AUTO,
            'rarity' => 2,
            'dofusdb_id' => '14492',
            'official_id' => null,
            'bonus' => json_encode(['strength' => 3], JSON_THROW_ON_ERROR),
            'item_type_id' => $type->id,
            'auto_update' => false,
            'created_by' => null,
        ]);

        $this->actingAs($superAdmin)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.content.ia-generation.items-seeder.export'))
            ->assertRedirect(route('admin.content.ia-generation.edit'))
            ->assertSessionHas('success');

        $this->assertCount(1, (new ItemSeederFileRepository($this->root))->paths());

        Item::query()->where('dofusdb_id', '14492')->forceDelete();

        $this->actingAs($superAdmin)
            ->withSession($this->passwordConfirmedSession())
            ->post(route('admin.content.ia-generation.items-seeder.import'))
            ->assertRedirect(route('admin.content.ia-generation.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('items', ['dofusdb_id' => '14492', 'rarity' => 2]);
    }

    public function test_page_exposes_items_seeder_state(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAsConfirmed($superAdmin)
            ->get(route('admin.content.ia-generation.edit'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('items_seeder.file_count', 0)
                ->where('items_seeder.auto_count', 0)
                ->where('items_seeder.allowed', true)
                ->where('items_seeder.relative_root', ItemSeederFileRepository::RELATIVE_ROOT));
    }
}
