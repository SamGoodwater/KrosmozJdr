<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Specialization;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Tests HTTP / Inertia pour le CRUD web des spécialisations.
 */
class SpecializationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_guest_can_view_specializations_index(): void
    {
        $this->get(route('entities.specializations.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/specialization/Index')
                ->has('specializations'));
    }

    public function test_guest_can_view_specialization_show(): void
    {
        $spec = Specialization::factory()->create([
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $this->get(route('entities.specializations.show', $spec))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/specialization/Show')
                ->has('specialization'));
    }

    public function test_guest_does_not_see_draft_spells_on_playable_specialization_show(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spec = Specialization::factory()->create([
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);

        $playableSpell = Spell::factory()->create([
            'name' => 'Sort public',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpell = Spell::factory()->create([
            'name' => 'Sort secret WIP',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $spec->spells()->attach($playableSpell->id, ['level' => 1]);
        $spec->spells()->attach($draftSpell->id, ['level' => 1]);

        $this->get(route('entities.specializations.show', $spec))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/specialization/Show')
                ->has('specialization.data.spells', 1)
                ->where('specialization.data.spells.0.name', 'Sort public'));

        $this->actingAs($author)
            ->get(route('entities.specializations.show', $spec))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/specialization/Show')
                ->has('specialization.data.spells', 2));
    }

    public function test_guest_cannot_access_edit(): void
    {
        $spec = Specialization::factory()->create();

        $this->get(route('entities.specializations.edit', $spec))
            ->assertRedirect(route('login'));
    }

    public function test_admin_create_redirects_to_index(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->get(route('entities.specializations.create'))
            ->assertRedirect(route('entities.specializations.index'));
    }

    public function test_admin_can_store_specialization_and_redirects_to_edit(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post(route('entities.specializations.store'), [
            'name' => 'Spécialisation test',
            'description' => 'Description courte',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('specializations', [
            'name' => 'Spécialisation test',
            'created_by' => $admin->id,
        ]);
        $spec = Specialization::query()->where('name', 'Spécialisation test')->first();
        $this->assertNotNull($spec);
        $response->assertRedirect(route('entities.specializations.edit', $spec));
    }

    public function test_admin_can_update_specialization(): void
    {
        $admin = $this->adminUser();
        $spec = Specialization::factory()->create(['name' => 'Ancien']);

        $this->actingAs($admin)
            ->patch(route('entities.specializations.update', $spec), [
                'name' => 'Nouveau nom',
            ])
            ->assertRedirect(route('entities.specializations.show', $spec));

        $this->assertDatabaseHas('specializations', [
            'id' => $spec->id,
            'name' => 'Nouveau nom',
        ]);
    }

    public function test_admin_can_sync_spells_on_specialization(): void
    {
        $admin = $this->adminUser();
        $spec = Specialization::factory()->create();
        $s1 = Spell::factory()->create(['created_by' => $admin->id]);
        $s2 = Spell::factory()->create(['created_by' => $admin->id]);

        $this->from(route('entities.specializations.edit', $spec))
            ->actingAs($admin)
            ->patch(route('entities.specializations.updateSpells', $spec), [
                'spells' => [
                    ['id' => $s1->id, 'level' => 1],
                    ['id' => $s2->id, 'level' => 2],
                ],
            ])
            ->assertRedirect(route('entities.specializations.edit', $spec));

        $this->assertEqualsCanonicalizing(
            [$s1->id, $s2->id],
            $spec->fresh()->spells->pluck('id')->all()
        );
        $this->assertDatabaseHas('specialization_spell', [
            'specialization_id' => $spec->id,
            'spell_id' => $s1->id,
            'level' => 1,
        ]);
        $this->assertDatabaseHas('specialization_spell', [
            'specialization_id' => $spec->id,
            'spell_id' => $s2->id,
            'level' => 2,
        ]);
    }

    public function test_admin_can_soft_delete_specialization(): void
    {
        $admin = $this->adminUser();
        $spec = Specialization::factory()->create();

        $this->actingAs($admin)
            ->delete(route('entities.specializations.delete', $spec))
            ->assertRedirect(route('entities.specializations.index'));

        $this->assertSoftDeleted('specializations', ['id' => $spec->id]);
    }

    public function test_game_master_cannot_sync_sections_on_specialization(): void
    {
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $spec = Specialization::factory()->create([
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $page = Page::factory()->create(['created_by' => $gm->id]);
        $section = Section::factory()->create(['page_id' => $page->id, 'created_by' => $gm->id]);

        $this->actingAs($gm)
            ->patch(route('entities.specializations.updateSections', $spec), [
                'sections' => [
                    ['id' => $section->id, 'level' => 1],
                ],
            ])
            ->assertForbidden();
    }

    public function test_admin_can_sync_sections_on_specialization(): void
    {
        $admin = $this->adminUser();
        $spec = Specialization::factory()->create([
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $page = Page::factory()->create(['created_by' => $admin->id]);
        $sectionA = Section::factory()->create(['page_id' => $page->id, 'created_by' => $admin->id]);
        $sectionB = Section::factory()->create(['page_id' => $page->id, 'created_by' => $admin->id]);

        $this->actingAs($admin)
            ->patch(route('entities.specializations.updateSections', $spec), [
                'sections' => [
                    ['id' => $sectionA->id, 'level' => 2],
                    ['id' => $sectionB->id, 'level' => 1],
                ],
            ])
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(
            [$sectionA->id, $sectionB->id],
            $spec->fresh()->sections->pluck('id')->all(),
        );
    }

    public function test_player_cannot_store_specialization(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);

        $this->actingAs($player)
            ->post(route('entities.specializations.store'), [
                'name' => 'Interdit',
            ])
            ->assertForbidden();
    }
}
