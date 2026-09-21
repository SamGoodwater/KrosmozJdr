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
 * Seed des 11 spécialisations jouables (gabarit 2.4.2.6).
 */
class SpecializationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_writes_all_eleven_playable_specs_on_the_erudit_gabarit(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->seed(SpecializationSeeder::class);

        $cases = [
            'Érudit' => ['Politicien', 'Mémoire des formules', 'Expertise en Wakfu'],
            'Milicien·ne' => ['Frères d’armes', 'Position d’autorité', 'Général'],
            'Dévot' => ['Refuge du pèlerin', 'Médecine de terrain', 'Haranguer les foules'],
            'Artiste' => ['Spectacle ambulant', 'Touche-à-tout', 'Manipulateur subtil'],
            'Explorateur·rice' => ['Jamais vraiment perdu', 'Rituel', 'Œil de l’éclaireur·euse'],
            'Voleur·euse' => ['Argot des voleurs', 'Furtivité suprême', 'Insaisissable'],
            'Artisan·e' => ['Réseau d’ateliers', 'Gestes économiques', 'Maître d’un geste'],
            'Négociant·e' => ['Remise de comptoir', 'Oreilles partout', 'Réputation de place'],
            'Sylvain·e' => ['Les bêtes te jaugent', 'Peau des saisons', 'Sang contre poison'],
            'Marin·e' => ['Estomac de mer', 'Yeux de vigie', 'Langue des quais'],
            'Courtisan·e' => ['Tenue correcte exigée', 'Mémoire des blasons', 'Nom qui ouvre'],
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
            $this->assertStringNotContainsString('emplacement libre', $html, $name);
            $this->assertStringNotContainsString('à calibrer', $html, $name);
            $this->assertStringNotContainsString('+2 points', $html, $name);
            $this->assertStringContainsString('Choix entre', $html, $name);
            $this->assertGreaterThanOrEqual(3, $spec->capabilities()->where('is_passive', true)->count(), $name);
        }

        $this->assertTrue(
            Page::query()->where('slug', 'import-specialization-marin-e')->where('state', Page::STATE_PLAYABLE)->exists()
        );
        $this->assertTrue(
            Page::query()->where('slug', 'import-specialization-courtisan-e')->where('state', Page::STATE_PLAYABLE)->exists()
        );
        $this->assertTrue(
            Page::query()->where('slug', 'import-specialization-artisan-e')->where('state', Page::STATE_PLAYABLE)->exists()
        );
    }

    public function test_seeder_overwrites_a_playable_specialization_from_authored_file(): void
    {
        User::factory()->create(['role' => User::ROLE_ADMIN]);
        Specialization::factory()->create([
            'name' => 'Artisan·e',
            'state' => Specialization::STATE_DRAFT,
            'short_description' => 'Version périmée',
        ]);

        $this->seed(SpecializationSeeder::class);

        $artisan = Specialization::query()->where('name', 'Artisan·e')->first();
        $this->assertNotNull($artisan);
        $this->assertNotSame('Version périmée', $artisan->short_description);
        $this->assertSame(Specialization::STATE_PLAYABLE, $artisan->state);
        $this->assertSame(1, Specialization::query()->where('name', 'Artisan·e')->count());
        $this->assertTrue(
            $artisan->sections()->where('state', Section::STATE_PLAYABLE)->exists()
        );
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
}
