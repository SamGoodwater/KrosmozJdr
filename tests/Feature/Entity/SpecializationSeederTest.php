<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\Entity\SpecializationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Seed des spécialisations brouillon (Artisan·e, Négociant·e, Sylvain·e, Marin·e, Courtisan·e).
 */
class SpecializationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_missing_specializations_as_drafts_with_sections(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->seed(SpecializationSeeder::class);

        foreach (['Artisan·e', 'Négociant·e', 'Sylvain·e', 'Marin·e', 'Courtisan·e'] as $name) {
            $specialization = Specialization::query()->where('name', $name)->first();
            $this->assertNotNull($specialization, "Fiche manquante : {$name}");
            $this->assertSame(Specialization::STATE_DRAFT, $specialization->state);
            $this->assertNotSame('', (string) $specialization->description);
            $this->assertGreaterThan(1, $specialization->sections()->count());
            $this->assertTrue(
                $specialization->sections()->where('state', Section::STATE_DRAFT)->exists()
            );
        }

        $this->assertTrue(
            Page::query()->where('slug', 'import-specialization-marin-e')->where('state', Page::STATE_DRAFT)->exists()
        );
        $this->assertTrue(
            Page::query()->where('slug', 'import-specialization-courtisan-e')->where('state', Page::STATE_DRAFT)->exists()
        );
    }

    public function test_seeder_refreshes_a_specialization_still_in_draft(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);
        Specialization::factory()->create([
            'name' => 'Artisan·e',
            'state' => Specialization::STATE_DRAFT,
            'short_description' => 'Version périmée',
        ]);

        $this->seed(SpecializationSeeder::class);

        $this->assertNotSame(
            'Version périmée',
            Specialization::query()->where('name', 'Artisan·e')->value('short_description')
        );
        $this->assertSame(1, Specialization::query()->where('name', 'Artisan·e')->count());
    }

    public function test_seeder_does_not_overwrite_a_specialization_out_of_draft(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);
        Specialization::factory()->create([
            'name' => 'Artisan·e',
            'state' => Specialization::STATE_PLAYABLE,
            'short_description' => 'Ne pas écraser',
        ]);

        $this->seed(SpecializationSeeder::class);

        $this->assertSame(
            'Ne pas écraser',
            Specialization::query()->where('name', 'Artisan·e')->value('short_description')
        );
        $this->assertSame(1, Specialization::query()->where('name', 'Artisan·e')->count());
    }

    public function test_drafts_follow_the_seven_paliers(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->seed(SpecializationSeeder::class);

        foreach (['Artisan·e', 'Négociant·e', 'Sylvain·e', 'Marin·e', 'Courtisan·e'] as $name) {
            $specialization = Specialization::query()->where('name', $name)->firstOrFail();
            $paliers = $specialization->sections()->pluck('level')->unique()->sort()->values()->all();

            $this->assertSame([1, 3, 6, 9, 12, 15, 20], $paliers, "Paliers inattendus pour {$name}.");
        }
    }
}
