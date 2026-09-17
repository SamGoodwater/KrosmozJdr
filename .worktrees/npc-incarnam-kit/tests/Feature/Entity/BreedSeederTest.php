<?php

namespace Tests\Feature\Entity;

use App\Models\Characteristic;
use App\Models\Entity\Breed;
use App\Models\Entity\Capability;
use App\Models\User;
use Database\Seeders\Entity\BreedSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @example php artisan test --filter=BreedSeederTest
 */
class BreedSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_rebuilds_sections_from_breed_columns_when_no_legacy_html(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Characteristic::create([
            'key' => 'life_dice_creature',
            'name' => 'Dé de vie',
            'type' => 'int',
            'sort_order' => 0,
            'group' => 'creature',
        ]);
        $capability = Capability::factory()->create(['created_by' => $admin->id]);

        $breed = Breed::factory()->create([
            'name' => 'Féca Test',
            'specificity' => 'Protection élémentaire',
            'life_dice' => '1d8',
            'evolution' => '<p>Progression par niveaux.</p>',
            'created_by' => $admin->id,
        ]);
        $breed->capabilities()->attach($capability->id);

        $this->seed(BreedSeeder::class);

        $breed->refresh();
        $this->assertGreaterThanOrEqual(3, $breed->sections()->count());
        $this->assertDatabaseHas('section_breed', ['breed_id' => $breed->id]);

        $titles = $breed->sections()->pluck('title')->all();
        $this->assertContains('Spécificité', $titles);
        $this->assertContains('Dé de vie', $titles);
        $this->assertContains('Capacités', $titles);
        $this->assertContains('Évolution', $titles);

        $lifeSection = $breed->sections()->where('title', 'Dé de vie')->first();
        $this->assertNotNull($lifeSection);
        $content = (string) ($lifeSection->data['content'] ?? '');
        $this->assertStringContainsString('kref--nav', $content);
        $this->assertStringContainsString('1d8', $content);
    }

    public function test_skips_breed_that_already_has_sections(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $breed = Breed::factory()->create([
            'specificity' => 'Avant seed',
            'created_by' => $admin->id,
        ]);

        $this->seed(BreedSeeder::class);
        $firstCount = $breed->fresh()->sections()->count();
        $this->assertGreaterThan(0, $firstCount);

        $breed->update(['specificity' => 'Après seed']);
        $this->seed(BreedSeeder::class);

        $this->assertSame($firstCount, $breed->fresh()->sections()->count());
    }
}
