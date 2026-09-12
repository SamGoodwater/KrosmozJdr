<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class ItemsSeederRoundTripTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/items-seeder-'.uniqid());
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

    public function test_export_then_import_recreates_the_item(): void
    {
        $type = $this->capeType();
        $this->cape($type);

        $this->assertSame(0, Artisan::call('items:seeder-export'));
        $this->assertStringContainsString('1 fichier(s) écrit(s)', Artisan::output());

        Item::query()->where('dofusdb_id', '14492')->forceDelete();
        $this->assertSame(0, Item::query()->count());

        $this->assertSame(0, Artisan::call('items:seeder-import'));
        $this->assertStringContainsString('1 création(s)', Artisan::output());

        $restored = Item::query()->where('dofusdb_id', '14492')->first();
        $this->assertNotNull($restored);
        $this->assertSame('Cape du Wa Wobot', $restored->name);
        $this->assertSame(2, $restored->rarity);
        $this->assertSame(Item::STATE_PLAYABLE, $restored->state);
        $this->assertSame($type->id, $restored->item_type_id);
        $this->assertFalse((bool) $restored->auto_update);
        $this->assertSame(
            ['strength' => 3, 'initiative' => 2],
            json_decode((string) $restored->bonus, true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function test_import_is_idempotent(): void
    {
        $this->cape($this->capeType());
        Artisan::call('items:seeder-export');

        Artisan::call('items:seeder-import');
        Artisan::call('items:seeder-import');

        $this->assertSame(1, Item::query()->count());
        $this->assertStringContainsString('0 création(s), 1 mise(s) à jour', Artisan::output());
    }

    public function test_export_skips_items_without_stable_key(): void
    {
        $type = $this->capeType();
        Item::factory()->create([
            'name' => 'Cape sans clé',
            'level' => '8',
            'state' => Item::STATE_PLAYABLE,
            'dofusdb_id' => null,
            'official_id' => null,
            'item_type_id' => $type->id,
            'created_by' => null,
        ]);

        Artisan::call('items:seeder-export');

        $output = Artisan::output();
        $this->assertStringContainsString('ni dofusdb_id ni official_id', $output);
        $this->assertSame([], (new ItemSeederFileRepository($this->root))->paths());
    }

    public function test_import_skips_file_whose_item_type_is_unknown(): void
    {
        $repository = new ItemSeederFileRepository($this->root);
        $repository->write([
            '_schema_version' => '1',
            'key' => ['dofusdb_id' => '999', 'official_id' => null],
            'item' => ['name' => 'Cape orpheline', 'item_type_dofus_id' => 4242],
            'relations' => [],
        ], 'cape-orpheline-item.json');

        $this->assertSame(0, Artisan::call('items:seeder-import'));
        $this->assertStringContainsString('type inconnu en base', Artisan::output());
        $this->assertSame(0, Item::query()->count());
    }

    private function capeType(): ItemType
    {
        return ItemType::query()->create([
            'name' => 'Cape',
            'dofusdb_type_id' => 17,
            'state' => ItemType::STATE_PLAYABLE,
            'read_level' => 0,
            'write_level' => 3,
            'show_in_catalog' => true,
            'allow_scrap' => false,
        ]);
    }

    private function cape(ItemType $type): Item
    {
        return Item::factory()->create([
            'name' => 'Cape du Wa Wobot',
            'level' => '8',
            'state' => Item::STATE_PLAYABLE,
            'rarity' => 2,
            'dofusdb_id' => '14492',
            'official_id' => null,
            'effect' => json_encode(['strength' => 3, 'initiative' => 2], JSON_THROW_ON_ERROR),
            'bonus' => json_encode(['strength' => 3, 'initiative' => 2], JSON_THROW_ON_ERROR),
            'item_type_id' => $type->id,
            'auto_update' => false,
            'read_level' => 0,
            'write_level' => 3,
            'created_by' => null,
        ]);
    }
}
