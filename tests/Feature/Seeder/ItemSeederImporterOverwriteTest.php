<?php

declare(strict_types=1);

namespace Tests\Feature\Seeder;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use App\Services\Seeder\Item\ItemSeederFileRepository;
use App\Services\Seeder\Item\ItemSeederImporter;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class ItemSeederImporterOverwriteTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/items-import-ow-'.uniqid());
        File::ensureDirectoryExists($this->root);
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

    public function test_import_without_overwrite_keeps_existing_item(): void
    {
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
            'name' => 'Cape en base',
            'dofusdb_id' => '90001',
            'official_id' => null,
            'item_type_id' => $type->id,
            'level' => '5',
            'state' => Item::STATE_PLAYABLE,
            'bonus' => json_encode(['strength' => 1], JSON_THROW_ON_ERROR),
            'effect' => json_encode(['strength' => 1], JSON_THROW_ON_ERROR),
            'created_by' => null,
        ]);

        File::put($this->root.'/cape-test-item.json', json_encode([
            '_schema_version' => '1',
            'key' => ['dofusdb_id' => '90001', 'official_id' => null],
            'item' => [
                'name' => 'Cape depuis JSON',
                'level' => '10',
                'item_type_dofus_id' => 17,
                'state' => Item::STATE_AUTO,
                'rarity' => 1,
                'bonus' => ['strength' => 9],
                'effect' => ['strength' => 9],
                'read_level' => 0,
                'write_level' => 3,
                'auto_update' => false,
            ],
            'relations' => [
                'panoply_dofusdb_ids' => [],
                'resources' => [],
            ],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));

        $result = app(ItemSeederImporter::class)->import(overwrite: false);

        $this->assertSame([], $result['created']);
        $this->assertSame([], $result['updated']);
        $this->assertNotEmpty($result['skipped']);

        $item = Item::query()->where('dofusdb_id', '90001')->first();
        $this->assertNotNull($item);
        $this->assertSame('Cape en base', $item->name);
        $this->assertSame(Item::STATE_PLAYABLE, $item->state);
    }
}
