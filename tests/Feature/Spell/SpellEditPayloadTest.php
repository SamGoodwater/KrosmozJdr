<?php

declare(strict_types=1);

namespace Tests\Feature\Spell;

use App\Models\Entity\Breed;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Models\Entity\Spell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class SpellEditPayloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_payload_exposes_every_spell_holder_kind(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $spell = Spell::factory()->create([
            'created_by' => $admin->id,
            'write_level' => User::ROLE_GAME_MASTER,
        ]);
        $monsterCreatureId = $this->createCreature('Monstre porteur', $admin->id);
        $npcCreatureId = $this->createCreature('PNJ porteur', $admin->id);
        $monster = Monster::factory()->create(['creature_id' => $monsterCreatureId]);
        $npc = Npc::factory()->create(['creature_id' => $npcCreatureId]);
        $breed = Breed::factory()->create();

        $spell->creatures()->attach([
            $monster->creature_id,
            $npc->creature_id,
        ]);
        $spell->breeds()->attach($breed->id);

        $response = $this->actingAs($admin)->getJson(
            route('entities.spells.edit-payload', $spell)
        );

        $response->assertOk()
            ->assertJsonPath('spellHolders.monsters.0.id', $monster->id)
            ->assertJsonPath('spellHolders.npcs.0.id', $npc->id)
            ->assertJsonPath('spellHolders.breeds.0.id', $breed->id)
            ->assertJsonStructure([
                'spellHolders' => [
                    'monsters' => [['id', 'name', 'href', 'level', 'stats', 'accessible_degree', 'monster']],
                    'npcs' => [['id', 'name', 'href', 'stats']],
                    'breeds' => [['id', 'name', 'href']],
                ],
            ]);
    }

    private function createCreature(string $name, int $creatorId): int
    {
        return DB::table('creatures')->insertGetId([
            'name' => $name,
            'res_fixe_neutre' => '0',
            'res_fixe_terre' => '0',
            'res_fixe_feu' => '0',
            'res_fixe_air' => '0',
            'res_fixe_eau' => '0',
            'created_by' => $creatorId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
