<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Seeder;

use App\Models\LoadingTip;
use App\Support\Seeder\SeedMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Mode seed création seule vs overwrite.
 */
final class SeedModeTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        SeedMode::clearForce();
        parent::tearDown();
    }

    public function test_upsert_preserves_existing_row_without_overwrite(): void
    {
        SeedMode::forceOverwrite(false);

        $tip = SeedMode::upsert(
            LoadingTip::class,
            ['body' => 'Astuce test seed mode'],
            ['url' => null, 'featured' => true, 'is_active' => true, 'duration_seconds' => 5]
        );
        $tip->update(['featured' => false, 'duration_seconds' => 12]);

        SeedMode::upsert(
            LoadingTip::class,
            ['body' => 'Astuce test seed mode'],
            ['url' => null, 'featured' => true, 'is_active' => true, 'duration_seconds' => 5]
        );

        $tip->refresh();
        $this->assertFalse($tip->featured);
        $this->assertSame(12, $tip->duration_seconds);
    }

    public function test_upsert_overwrites_when_forced(): void
    {
        SeedMode::forceOverwrite(false);
        SeedMode::upsert(
            LoadingTip::class,
            ['body' => 'Astuce overwrite'],
            ['url' => null, 'featured' => false, 'is_active' => true, 'duration_seconds' => 8]
        );

        SeedMode::forceOverwrite(true);
        SeedMode::upsert(
            LoadingTip::class,
            ['body' => 'Astuce overwrite'],
            ['url' => 'https://example.test', 'featured' => true, 'is_active' => true, 'duration_seconds' => 3]
        );

        $tip = LoadingTip::query()->where('body', 'Astuce overwrite')->first();
        $this->assertNotNull($tip);
        $this->assertTrue($tip->featured);
        $this->assertSame(3, $tip->duration_seconds);
        $this->assertSame('https://example.test', $tip->url);
    }
}
