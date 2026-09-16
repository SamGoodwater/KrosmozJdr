<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Breed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class BreedsSyncImagesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_fills_local_paths_from_dofus_id(): void
    {
        $breed = Breed::factory()->create([
            'name' => 'Iop',
            'dofusdb_id' => '8',
            'image' => '/old.png',
            'icon' => null,
        ]);

        $code = Artisan::call('breeds:sync-images', ['--skip-pages' => true]);

        $this->assertSame(0, $code);
        $fresh = $breed->fresh();
        $this->assertSame('/storage/images/breeds/iop/full_m.png', $fresh->image_full_male);
        $this->assertSame('/storage/images/breeds/iop/full_m.png', $fresh->image);
        $this->assertSame('/storage/images/breeds/iop/symbol-bw.png', $fresh->symbol_bw);
        $this->assertSame('/storage/images/breeds/iop/symbol-bw.png', $fresh->icon);
        $this->assertSame('/storage/images/breeds/iop/logo_m.png', $fresh->logo_male);
    }
}
