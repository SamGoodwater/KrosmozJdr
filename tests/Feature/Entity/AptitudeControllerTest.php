<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Aptitude;
use App\Models\Entity\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * CRUD web + table API + sync pivot spé pour Aptitude.
 */
class AptitudeControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_guest_can_view_aptitudes_index_and_show(): void
    {
        $aptitude = Aptitude::factory()->create([
            'state' => Aptitude::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'name' => 'Spectacle ambulant',
        ]);

        $this->get(route('entities.aptitudes.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/aptitude/Index')
                ->has('aptitudes'));

        $this->get(route('entities.aptitudes.show', $aptitude))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/aptitude/Show')
                ->where('aptitude.data.name', 'Spectacle ambulant'));
    }

    public function test_admin_can_store_and_update_aptitude(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->withHeaders(['X-Inertia' => 'true'])
            ->post(route('entities.aptitudes.store'), [
                'name' => 'Nouvelle aptitude',
                'effect' => 'Un bonus naturel',
                'reuse' => 'Après un repos court',
                'duration' => 'Tant qu’il y a une foule',
                'range' => 'À portée d’ouïe',
                'state' => Aptitude::STATE_DRAFT,
            ])
            ->assertRedirect();

        $aptitude = Aptitude::query()->where('name', 'Nouvelle aptitude')->first();
        $this->assertNotNull($aptitude);
        $this->assertSame('Après un repos court', $aptitude->reuse);

        $this->actingAs($admin)
            ->patch(route('entities.aptitudes.update', $aptitude), [
                'name' => 'Aptitude renommée',
                'reuse' => 'Après un repos long',
            ])
            ->assertRedirect();

        $this->assertSame('Aptitude renommée', $aptitude->fresh()->name);
        $this->assertSame('Après un repos long', $aptitude->fresh()->reuse);
    }

    public function test_table_api_returns_aptitude_rows(): void
    {
        $admin = $this->adminUser();
        Aptitude::factory()->create([
            'name' => 'Table aptitude',
            'state' => Aptitude::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $this->actingAs($admin)
            ->getJson(route('api.tables.aptitudes', ['format' => 'entities', 'limit' => 50]))
            ->assertOk()
            ->assertJsonPath('meta.entityType', 'aptitudes')
            ->assertJsonFragment(['name' => 'Table aptitude']);
    }

    public function test_specialization_can_sync_aptitudes_with_level(): void
    {
        $admin = $this->adminUser();
        $spec = Specialization::factory()->create([
            'state' => Specialization::STATE_PLAYABLE,
            'write_level' => User::ROLE_ADMIN,
            'created_by' => $admin->id,
        ]);
        $a = Aptitude::factory()->create(['name' => 'A1', 'state' => Aptitude::STATE_PLAYABLE]);
        $b = Aptitude::factory()->create(['name' => 'A2', 'state' => Aptitude::STATE_PLAYABLE]);

        $this->actingAs($admin)
            ->patch(route('entities.specializations.updateAptitudes', $spec), [
                'aptitudes' => [
                    ['id' => $a->id, 'level' => 3],
                    ['id' => $b->id, 'level' => 9],
                ],
            ])
            ->assertRedirect();

        $this->assertSame(2, $spec->aptitudes()->count());
        $this->assertSame(3, (int) $spec->aptitudes()->where('aptitudes.id', $a->id)->first()?->pivot->level);
        $this->assertSame(9, (int) $spec->aptitudes()->where('aptitudes.id', $b->id)->first()?->pivot->level);
    }
}
