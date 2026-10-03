<?php

declare(strict_types=1);

namespace Tests\Feature\Seeders;

use App\Models\Characteristic;
use App\Models\CharacteristicCreature;
use App\Support\Seeder\SeedMode;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Les seeders de caractéristiques ne réécrivent plus la base sans --overwrite.
 */
final class SeedModeCharacteristicPreservationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        SeedMode::clearForce();
        parent::tearDown();
    }

    public function test_characteristic_seeder_preserves_admin_edits_without_overwrite(): void
    {
        SeedMode::forceOverwrite(false);
        $this->seed(CharacteristicSeeder::class);

        $char = Characteristic::query()->where('key', 'strength_creature')->first();
        $this->assertNotNull($char);

        $char->update([
            'helper' => 'Helper modifié en admin',
            'status' => Characteristic::STATUS_VALIDEE,
        ]);

        $this->seed(CharacteristicSeeder::class);

        $char->refresh();
        $this->assertSame('Helper modifié en admin', $char->helper);
        $this->assertSame(Characteristic::STATUS_VALIDEE, $char->status);
    }

    public function test_characteristic_seeder_overwrites_when_forced(): void
    {
        SeedMode::forceOverwrite(false);
        $this->seed(CharacteristicSeeder::class);

        $char = Characteristic::query()->where('key', 'strength_creature')->first();
        $this->assertNotNull($char);
        $originalHelper = $char->helper;

        $char->update(['helper' => 'Helper temporaire']);

        SeedMode::forceOverwrite(true);
        $this->seed(CharacteristicSeeder::class);

        $char->refresh();
        $this->assertSame($originalHelper, $char->helper);
    }

    public function test_creature_pivot_preserves_min_max_without_overwrite(): void
    {
        SeedMode::forceOverwrite(false);
        $this->seed(CharacteristicSeeder::class);
        $this->seed(CreatureCharacteristicSeeder::class);

        $char = Characteristic::query()->where('key', 'strength_creature')->first();
        $this->assertNotNull($char);

        $pivot = CharacteristicCreature::query()
            ->where('characteristic_id', $char->id)
            ->where('entity', '*')
            ->first();
        $this->assertNotNull($pivot);

        $pivot->update(['min' => -99, 'max' => 99]);

        $this->seed(CreatureCharacteristicSeeder::class);

        $pivot->refresh();
        $this->assertSame(-99, (int) $pivot->min);
        $this->assertSame(99, (int) $pivot->max);
    }
}
