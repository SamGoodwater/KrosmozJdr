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

    public function test_seeder_writes_erudit_as_playable_model_sheet(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->seed(SpecializationSeeder::class);

        $erudit = Specialization::query()->where('name', 'Érudit')->first();
        $this->assertNotNull($erudit);
        $this->assertSame(Specialization::STATE_PLAYABLE, $erudit->state);
        $paliers = $erudit->sections()->pluck('level')->unique()->sort()->values()->all();
        $this->assertSame([1, 3, 6, 9, 12, 15, 20], $paliers);

        $html = $erudit->sections()->get()->pluck('data')->map(
            static fn (mixed $data): string => is_array($data) ? (string) ($data['content'] ?? '') : ''
        )->implode("\n");
        $this->assertStringContainsString('Politicien', $html);
        $this->assertStringContainsString('Façonneur de sorts', $html);
        $this->assertStringContainsString('Expertise en Wakfu', $html);
        $this->assertStringContainsString('Identification', $html);
        $this->assertStringNotContainsString('Brouillon', $html);
        $this->assertStringNotContainsString('5e-drs.fr', $html);
    }

    public function test_seeder_writes_all_six_playable_specs_on_the_erudit_gabarit(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->seed(SpecializationSeeder::class);

        $cases = [
            'Érudit' => ['Politicien', 'Façonneur de sorts', 'Expertise en Wakfu'],
            'Milicien·ne' => ['Frères d’armes', 'Position d’autorité', 'Général'],
            'Dévot' => ['Refuge du pèlerin', 'Médecine de terrain', 'Haranguer les foules'],
            'Artiste' => ['Spectacle ambulant', 'Touche-à-tout', 'Manipulateur subtil'],
            'Explorateur·rice' => ['Jamais vraiment perdu', 'Rituel', 'Œil de l’éclaireur·euse'],
            'Voleur·euse' => ['Argot des voleurs', 'Furtivité suprême', 'Insaisissable'],
        ];

        foreach ($cases as $name => $aptitudes) {
            $spec = Specialization::query()->where('name', $name)->first();
            $this->assertNotNull($spec, $name);
            $this->assertSame(Specialization::STATE_PLAYABLE, $spec->state, $name);
            $paliers = $spec->sections()->pluck('level')->unique()->sort()->values()->all();
            $this->assertSame([1, 3, 6, 9, 12, 15, 20], $paliers, $name);

            $html = $spec->sections()->get()->pluck('data')->map(
                static fn (mixed $data): string => is_array($data) ? (string) ($data['content'] ?? '') : ''
            )->implode("\n");
            foreach ($aptitudes as $aptitude) {
                $this->assertStringContainsString($aptitude, $html, $name);
            }
            $this->assertStringNotContainsString('Brouillon', $html, $name);
            $this->assertStringNotContainsString('5e-drs.fr', $html, $name);
            $this->assertGreaterThanOrEqual(3, $spec->capabilities()->where('is_passive', true)->count(), $name);
        }
    }
}
