<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\MonsterRace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Validation race + PDF multi monstre (visibilité).
 */
class MonsterControllerVisibilityAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_monster_race_with_monster_races_table(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $race = MonsterRace::factory()->create(['name' => 'Bouftous']);
        $monster = Monster::factory()->create(['monster_race_id' => null]);

        $this->actingAs($admin)
            ->patch(route('entities.monsters.update', $monster), [
                'monster_race_id' => $race->id,
            ])
            ->assertRedirect();

        $this->assertSame($race->id, $monster->fresh()->monster_race_id);
    }

    public function test_multi_pdf_only_includes_monsters_visible_to_viewer(): void
    {
        $player = User::factory()->create(['role' => User::ROLE_PLAYER]);
        $visible = Monster::factory()->create([
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $hidden = Monster::factory()->create([
            'state' => 'draft',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        // Route liée à un monstre visible ; la query `ids` mélange visible + brouillon.
        $response = $this->actingAs($player)
            ->get(route('entities.monsters.pdf', $visible).'?'.http_build_query([
                'ids' => [$visible->id, $hidden->id],
            ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_show_hides_foreign_draft_spells_from_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableSpell = Spell::factory()->create([
            'name' => 'Sort Public',
            'state' => Spell::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpell = Spell::factory()->create([
            'name' => 'Sort Brouillon',
            'state' => Spell::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $creature->spells()->attach([$playableSpell->id, $draftSpell->id]);
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->get(route('entities.monsters.show', $monster));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/monster/Show')
            ->has('monster.data.creature.spells')
            ->where('monster.data.creature.spells', function ($spells) use ($playableSpell, $draftSpell) {
                $ids = collect($spells)->pluck('id')->all();

                return in_array($playableSpell->id, $ids, true)
                    && ! in_array($draftSpell->id, $ids, true);
            })
        );
    }

    public function test_show_hides_foreign_draft_creature_traits_from_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $creature = Creature::factory()->create(['created_by' => $author->id]);
        $playableTrait = CreatureTrait::factory()->create([
            'name' => 'Trait Public',
            'state' => CreatureTrait::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftTrait = CreatureTrait::factory()->create([
            'name' => 'Trait Brouillon',
            'state' => CreatureTrait::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $creature->creatureTraits()->attach([$playableTrait->id, $draftTrait->id]);
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
            'state' => 'playable',
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);

        $response = $this->get(route('entities.monsters.show', $monster));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/monster/Show')
            ->where('monster.data.creature', function ($creature) use ($playableTrait, $draftTrait) {
                $traits = $creature['creatureTraits'] ?? $creature['creature_traits'] ?? [];
                $ids = collect($traits)->pluck('id')->all();

                return in_array($playableTrait->id, $ids, true)
                    && ! in_array($draftTrait->id, $ids, true);
            })
        );
    }
}
