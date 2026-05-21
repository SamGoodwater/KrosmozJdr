<?php

namespace Tests\Feature\Entity;

use App\Models\Entity\Breed;
use App\Models\Entity\Spell;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Tests HTTP / Inertia pour le CRUD web des classes (Breed).
 */
class BreedControllerTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_guest_can_view_playable_breed_when_read_level_public(): void
    {
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $this->get(route('entities.breeds.show', $breed))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Pages/entity/breed/Show')
                ->has('breed'));
    }

    public function test_guest_cannot_view_draft_breed(): void
    {
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->get(route('entities.breeds.show', $breed))
            ->assertForbidden();
    }

    public function test_creator_can_view_own_draft_breed(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_DRAFT,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('entities.breeds.show', $breed))
            ->assertOk();
    }

    public function test_game_master_can_view_foreign_draft_breed(): void
    {
        $creator = User::factory()->create();
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_DRAFT,
            'created_by' => $creator->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($gm)
            ->get(route('entities.breeds.show', $breed))
            ->assertOk();
    }

    public function test_player_cannot_view_foreign_draft_breed(): void
    {
        $creator = User::factory()->create();
        $other = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_DRAFT,
            'created_by' => $creator->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $this->actingAs($other)
            ->get(route('entities.breeds.show', $breed))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_edit(): void
    {
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $this->get(route('entities.breeds.edit', $breed))
            ->assertRedirect();
    }

    public function test_admin_can_create_breed_and_redirects_to_edit(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post(route('entities.breeds.store'), [
            'name' => 'Classe Test',
            'description' => 'Desc',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('breeds', [
            'name' => 'Classe Test',
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_update_breed(): void
    {
        $admin = $this->adminUser();
        $breed = Breed::factory()->create(['name' => 'Ancien']);

        $this->actingAs($admin)
            ->patch(route('entities.breeds.update', $breed), [
                'name' => 'Nouveau nom',
            ])
            ->assertRedirect(route('entities.breeds.show', $breed));

        $this->assertDatabaseHas('breeds', [
            'id' => $breed->id,
            'name' => 'Nouveau nom',
        ]);
    }

    public function test_admin_can_update_element_orientations(): void
    {
        $admin = $this->adminUser();
        $breed = Breed::factory()->create();

        $this->actingAs($admin)
            ->patch(route('entities.breeds.update', $breed), [
                'element_orientations' => [
                    'air' => 'tank',
                    'earth' => 'soin',
                    'fire' => null,
                    'water' => '',
                ],
            ])
            ->assertRedirect(route('entities.breeds.show', $breed));

        $this->assertDatabaseHas('breed_element_orientations', [
            'breed_id' => $breed->id,
            'element' => 'air',
            'orientation_key' => 'tank',
        ]);
        $this->assertDatabaseHas('breed_element_orientations', [
            'breed_id' => $breed->id,
            'element' => 'earth',
            'orientation_key' => 'soin',
        ]);
        $this->assertEquals(2, $breed->fresh()->elementOrientations()->count());
    }

    public function test_admin_can_soft_delete_breed(): void
    {
        $admin = $this->adminUser();
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);

        $this->actingAs($admin)
            ->delete(route('entities.breeds.delete', $breed))
            ->assertRedirect(route('entities.breeds.index'));

        $this->assertSoftDeleted('breeds', ['id' => $breed->id]);
    }

    public function test_admin_can_sync_spells_on_breed(): void
    {
        $admin = $this->adminUser();
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $s1 = Spell::factory()->create(['created_by' => $admin->id]);
        $s2 = Spell::factory()->create(['created_by' => $admin->id]);

        $this->actingAs($admin)
            ->patch(route('entities.breeds.updateSpells', $breed), [
                'spells' => [
                    $s1->id => [
                        'character_level' => 1,
                        'slot_index' => 1,
                        'choice_order' => 0,
                    ],
                    $s2->id => [
                        'character_level' => 1,
                        'slot_index' => 2,
                        'choice_order' => 0,
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(
            [$s1->id, $s2->id],
            $breed->fresh()->spells->pluck('id')->all()
        );
        $this->assertDatabaseHas('breed_spell', [
            'breed_id' => $breed->id,
            'spell_id' => $s1->id,
            'character_level' => 1,
            'slot_index' => 1,
            'choice_order' => 0,
        ]);
    }

    public function test_admin_can_sync_sections_on_breed(): void
    {
        $admin = $this->adminUser();
        $breed = Breed::factory()->create([
            'state' => Breed::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
        ]);
        $page = Page::factory()->create(['created_by' => $admin->id]);
        $sectionA = Section::factory()->create(['page_id' => $page->id, 'created_by' => $admin->id]);
        $sectionB = Section::factory()->create(['page_id' => $page->id, 'created_by' => $admin->id]);

        $this->actingAs($admin)
            ->patch(route('entities.breeds.updateSections', $breed), [
                'sections' => [
                    ['id' => $sectionA->id, 'level' => 2],
                    ['id' => $sectionB->id, 'level' => 1],
                ],
            ])
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(
            [$sectionA->id, $sectionB->id],
            $breed->fresh()->sections->pluck('id')->all(),
        );
        $this->assertDatabaseHas('section_breed', [
            'breed_id' => $breed->id,
            'section_id' => $sectionA->id,
            'level' => 2,
        ]);
        $this->assertDatabaseHas('section_breed', [
            'breed_id' => $breed->id,
            'section_id' => $sectionB->id,
            'level' => 1,
        ]);
    }
}
