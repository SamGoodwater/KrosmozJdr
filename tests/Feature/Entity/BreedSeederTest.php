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
        $this->assertNotContains('Capacités', $titles);
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
            'name' => 'Classe hors catalogue',
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

    public function test_authored_class_sheet_replaces_capability_dump_and_refreshes(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        Capability::factory()->create([
            'name' => 'Fureur',
            'is_passive' => true,
            'created_by' => $admin->id,
        ]);

        $breed = Breed::factory()->create([
            'name' => 'Iop',
            'specificity' => 'Dump périmé',
            'created_by' => $admin->id,
        ]);

        $this->seed(BreedSeeder::class);

        $breed->refresh();
        $titles = $breed->sections()->pluck('title')->all();
        $this->assertSame(['Texte'], $titles);

        $html = (string) ($breed->sections()->first()?->data['content'] ?? '');
        $this->assertStringContainsString('Fureur', $html);
        $this->assertStringContainsString('tu charges', $html);
        $this->assertStringNotContainsString('Capacités disponibles', $html);
        $this->assertStringNotContainsString('Dump périmé', $html);

        $this->seed(BreedSeeder::class);
        $this->assertSame(['Texte'], $breed->fresh()->sections()->pluck('title')->all());
    }
}
