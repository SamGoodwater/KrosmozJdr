<?php

declare(strict_types=1);

namespace Tests\Feature\Entity;

use App\Models\Entity\Capability;
use App\Models\Entity\Condition;
use App\Models\Entity\Creature;
use App\Models\Entity\Specialization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fiche capacité : les liaisons nested respectent visibleToUser en lecture.
 */
class CapabilityNestedVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_hides_foreign_draft_links_from_guest(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        [$capability, $playable, $draft] = $this->playableCapabilityWithDraftLinks($author);

        $response = $this->get(route('entities.capabilities.show', $capability));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/capability/Show')
            ->where('capability.data.specializations', function ($rows) use ($playable, $draft) {
                $ids = collect($rows)->pluck('id')->all();

                return in_array($playable['specialization']->id, $ids, true)
                    && ! in_array($draft['specialization']->id, $ids, true);
            })
            ->where('capability.data.creatures', function ($rows) use ($playable, $draft) {
                $ids = collect($rows)->pluck('id')->all();

                return in_array($playable['creature']->id, $ids, true)
                    && ! in_array($draft['creature']->id, $ids, true);
            })
            ->where('capability.data.conditions', function ($rows) use ($playable, $draft) {
                $ids = collect($rows)->pluck('id')->all();

                return in_array($playable['condition']->id, $ids, true)
                    && ! in_array($draft['condition']->id, $ids, true);
            })
        );
    }

    public function test_edit_still_loads_draft_links_for_admin(): void
    {
        $author = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        [$capability, $playable, $draft] = $this->playableCapabilityWithDraftLinks($author);

        $response = $this->actingAs($admin)
            ->get(route('entities.capabilities.edit', $capability));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Pages/entity/capability/Edit')
            ->where('capability.data.specializations', function ($rows) use ($playable, $draft) {
                $ids = collect($rows)->pluck('id')->all();

                return in_array($playable['specialization']->id, $ids, true)
                    && in_array($draft['specialization']->id, $ids, true);
            })
        );
    }

    /**
     * @return array{0: Capability, 1: array{specialization: Specialization, creature: Creature, condition: Condition}, 2: array{specialization: Specialization, creature: Creature, condition: Condition}}
     */
    private function playableCapabilityWithDraftLinks(User $author): array
    {
        $playableSpecialization = Specialization::factory()->create([
            'name' => 'Voie publique',
            'state' => Specialization::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftSpecialization = Specialization::factory()->create([
            'name' => 'Voie secrète',
            'state' => Specialization::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $playableCreature = Creature::factory()->create([
            'name' => 'Créature publique',
            'state' => Creature::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftCreature = Creature::factory()->create([
            'name' => 'Créature brouillon',
            'state' => Creature::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $playableCondition = Condition::factory()->create([
            'name' => 'État public',
            'state' => Condition::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $draftCondition = Condition::factory()->create([
            'name' => 'État brouillon',
            'state' => Condition::STATE_DRAFT,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);

        $capability = Capability::factory()->create([
            'name' => 'Coup public',
            'state' => Capability::STATE_PLAYABLE,
            'read_level' => User::ROLE_GUEST,
            'write_level' => User::ROLE_GAME_MASTER,
            'created_by' => $author->id,
        ]);
        $capability->specializations()->attach([
            $playableSpecialization->id,
            $draftSpecialization->id,
        ]);
        $capability->creatures()->attach([
            $playableCreature->id,
            $draftCreature->id,
        ]);
        $capability->conditions()->attach([
            $playableCondition->id,
            $draftCondition->id,
        ]);

        return [
            $capability,
            [
                'specialization' => $playableSpecialization,
                'creature' => $playableCreature,
                'condition' => $playableCondition,
            ],
            [
                'specialization' => $draftSpecialization,
                'creature' => $draftCreature,
                'condition' => $draftCondition,
            ],
        ];
    }
}
