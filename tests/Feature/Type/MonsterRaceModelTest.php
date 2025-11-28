<?php

namespace Tests\Feature\Type;

use App\Models\User;
use App\Models\Type\MonsterRace;
use App\Models\Entity\Monster;
use App\Models\Entity\Creature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle MonsterRace
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Type
 */
class MonsterRaceModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une race de monstre via factory
     */
    public function test_monster_race_factory_creates_valid_monster_race(): void
    {
        $user = User::factory()->create();
        
        $monsterRace = MonsterRace::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($monsterRace);
        $this->assertNotNull($monsterRace->id);
        $this->assertNotNull($monsterRace->name);
        $this->assertEquals($user->id, $monsterRace->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_monster_race_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $monsterRace = MonsterRace::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($monsterRace->createdBy);
        $this->assertEquals($user->id, $monsterRace->createdBy->id);
    }

    /**
     * Test de la relation monsters (hasMany)
     */
    public function test_monster_race_has_monsters_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $monsterRace = MonsterRace::factory()->create([
            'created_by' => $user->id,
        ]);

        $monster1 = Monster::factory()->create([
            'creature_id' => $creature->id,
            'monster_race_id' => $monsterRace->id,
        ]);
        $monster2 = Monster::factory()->create([
            'creature_id' => $creature->id,
            'monster_race_id' => $monsterRace->id,
        ]);

        $monsterRace->refresh();
        $this->assertCount(2, $monsterRace->monsters);
        $this->assertTrue($monsterRace->monsters->contains($monster1));
        $this->assertTrue($monsterRace->monsters->contains($monster2));
    }

    /**
     * Test de la relation superRace (belongsTo)
     */
    public function test_monster_race_has_super_race_relation(): void
    {
        $user = User::factory()->create();
        $superRace = MonsterRace::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $subRace = MonsterRace::factory()->create([
            'created_by' => $user->id,
            'id_super_race' => $superRace->id,
        ]);

        $this->assertNotNull($subRace->superRace);
        $this->assertEquals($superRace->id, $subRace->superRace->id);
    }

    /**
     * Test de la relation subRaces (hasMany)
     */
    public function test_monster_race_has_sub_races_relation(): void
    {
        $user = User::factory()->create();
        $superRace = MonsterRace::factory()->create([
            'created_by' => $user->id,
        ]);
        
        $subRace1 = MonsterRace::factory()->create([
            'created_by' => $user->id,
            'id_super_race' => $superRace->id,
        ]);
        $subRace2 = MonsterRace::factory()->create([
            'created_by' => $user->id,
            'id_super_race' => $superRace->id,
        ]);

        $superRace->refresh();
        $this->assertCount(2, $superRace->subRaces);
        $this->assertTrue($superRace->subRaces->contains($subRace1));
        $this->assertTrue($superRace->subRaces->contains($subRace2));
    }
}

