<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Entity\Item;
use App\Models\Entity\Panoply;
use App\Services\Seeder\Panoply\PanoplySeederFileRepository;
use Database\Seeders\Entity\PanoplySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

final class PanoplySeederImportTest extends TestCase
{
    use RefreshDatabase;

    private string $root;

    protected function setUp(): void
    {
        parent::setUp();

        $this->root = storage_path('framework/testing/panoplies-seeder-'.uniqid());
        $this->app->bind(
            PanoplySeederFileRepository::class,
            fn (): PanoplySeederFileRepository => new PanoplySeederFileRepository($this->root)
        );
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->root);

        parent::tearDown();
    }

    public function test_seeder_creates_panoply_and_links_items(): void
    {
        $item = Item::factory()->create([
            'dofusdb_id' => '2416',
            'name' => 'Marteau du Bouftou',
            'created_by' => null,
        ]);

        $repository = new PanoplySeederFileRepository($this->root);
        $repository->write([
            '_schema_version' => '1',
            'key' => ['dofusdb_id' => '1'],
            'panoply' => [
                'name' => 'Panoplie du Bouftou',
                'bonus' => ['8' => ['action_points' => 1]],
                'state' => Panoply::STATE_PLAYABLE,
                'read_level' => 0,
                'write_level' => 3,
            ],
            'relations' => ['item_dofusdb_ids' => ['2416']],
        ], 'panoplie-du-bouftou-panoply.json');

        $this->seed(PanoplySeeder::class);

        $panoply = Panoply::query()->where('dofusdb_id', '1')->first();
        $this->assertNotNull($panoply);
        $this->assertSame(Panoply::STATE_PLAYABLE, $panoply->state);
        $this->assertSame(['8' => ['action_points' => 1]], json_decode((string) $panoply->bonus, true));
        $this->assertTrue($panoply->items->contains('id', $item->id));
    }
}
