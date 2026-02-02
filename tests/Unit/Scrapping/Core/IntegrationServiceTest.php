<?php

namespace Tests\Unit\Scrapping\Core;

use App\Models\Entity\Classe;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Spell;
use App\Models\Type\MonsterRace;
use App\Services\Scrapping\Core\Integration\IntegrationResult;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use Tests\CreatesSystemUser;
use Tests\TestCase;

/**
 * Tests unitaires pour IntegrationService (monster, spell, class, item, dry_run).
 */
class IntegrationServiceTest extends TestCase
{
    use CreatesSystemUser;

    private IntegrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new IntegrationService();
    }

    public function test_integrate_unknown_entity_returns_fail(): void
    {
        $result = $this->service->integrate('unknown-entity', [], []);

        $this->assertInstanceOf(IntegrationResult::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('non supporté', $result->getMessage());
    }

    public function test_integrate_monster_incomplete_data_returns_fail(): void
    {
        $convertedData = [
            'creatures' => [],
            'monsters' => ['dofusdb_id' => '31'],
        ];

        $result = $this->service->integrate('monster', $convertedData, []);

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('incomplètes', $result->getMessage());
    }

    public function test_integrate_monster_missing_creatures_or_monsters_returns_fail(): void
    {
        $result1 = $this->service->integrate('monster', ['monsters' => ['dofusdb_id' => '31']], []);
        $result2 = $this->service->integrate('monster', ['creatures' => ['name' => 'Bouftou']], []);

        $this->assertFalse($result1->isSuccess());
        $this->assertFalse($result2->isSuccess());
    }

    public function test_integrate_monster_dry_run_returns_would_create(): void
    {
        $convertedData = [
            'creatures' => [
                'name' => 'Bouftou Test',
                'level' => '1',
                'life' => '10',
            ],
            'monsters' => [
                'dofusdb_id' => '99999',
                'size' => 'medium',
                'monster_race_id' => null,
            ],
        ];

        $result = $this->service->integrate('monster', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getCreatureAction());
        $this->assertSame('would_create', $result->getMonsterAction());
        $this->assertStringContainsString('Simulation', $result->getMessage());
    }

    public function test_integrate_monster_creates_creature_and_monster(): void
    {
        $this->createSystemUser();
        $race = MonsterRace::factory()->create(['name' => 'Test Race']);

        $convertedData = [
            'creatures' => [
                'name' => 'Bouftou Integration Test',
                'level' => '1',
                'life' => '10',
            ],
            'monsters' => [
                'dofusdb_id' => '88888',
                'size' => 'medium',
                'monster_race_id' => $race->id,
            ],
        ];

        $result = $this->service->integrate('monster', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('created', $result->getCreatureAction());
        $this->assertSame('created', $result->getMonsterAction());
        $creatureId = $result->getCreatureId();
        $monsterId = $result->getMonsterId();
        $this->assertNotNull($creatureId);
        $this->assertNotNull($monsterId);

        $creature = Creature::find($creatureId);
        $monster = Monster::find($monsterId);
        $this->assertNotNull($creature);
        $this->assertNotNull($monster);
        $this->assertSame('Bouftou Integration Test', $creature->name);
        $this->assertSame($creature->id, $monster->creature_id);
        $this->assertSame('88888', $monster->dofusdb_id);
    }

    public function test_integrate_monster_skips_when_dofusdb_id_exists_without_force_update(): void
    {
        $this->createSystemUser();
        $race = MonsterRace::factory()->create();
        $creature = Creature::factory()->create(['name' => 'Existing Creature']);
        $monster = Monster::factory()->create([
            'creature_id' => $creature->id,
            'dofusdb_id' => '77777',
            'monster_race_id' => $race->id,
        ]);

        $convertedData = [
            'creatures' => ['name' => 'Other Name', 'level' => '1', 'life' => '5'],
            'monsters' => ['dofusdb_id' => '77777', 'size' => 'medium', 'monster_race_id' => $race->id],
        ];

        $result = $this->service->integrate('monster', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('skipped', $result->getCreatureAction());
        $this->assertSame('skipped', $result->getMonsterAction());
        $creature->refresh();
        $this->assertSame('Existing Creature', $creature->name);
    }

    public function test_integrate_spell_dry_run_returns_would_create(): void
    {
        $convertedData = [
            'spells' => [
                'dofusdb_id' => '12345',
                'name' => 'Évaporation Test',
                'description' => 'Desc',
                'pa' => '4',
                'po' => '1',
                'level' => '1',
            ],
        ];

        $result = $this->service->integrate('spell', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }

    public function test_integrate_spell_creates_spell(): void
    {
        $this->createSystemUser();

        $convertedData = [
            'spells' => [
                'dofusdb_id' => '54321',
                'name' => 'Sort Integration Test',
                'description' => 'Description',
                'pa' => '4',
                'po' => '1',
                'level' => '1',
            ],
        ];

        $result = $this->service->integrate('spell', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('created', $result->getPrimaryAction());
        $spell = Spell::find($result->getPrimaryId());
        $this->assertNotNull($spell);
        $this->assertSame('Sort Integration Test', $spell->name);
        $this->assertSame('54321', $spell->dofusdb_id);
    }

    public function test_integrate_spell_incomplete_returns_fail(): void
    {
        $result = $this->service->integrate('spell', [], []);

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('spells', $result->getMessage());
    }

    public function test_integrate_class_dry_run_returns_would_create(): void
    {
        $convertedData = [
            'classes' => [
                'dofusdb_id' => '1',
                'name' => 'Feca',
                'description' => 'Desc',
            ],
        ];

        $result = $this->service->integrate('class', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }

    public function test_integrate_class_creates_classe(): void
    {
        $this->createSystemUser();

        $convertedData = [
            'classes' => [
                'dofusdb_id' => '2',
                'name' => 'Classe Integration Test',
                'description' => 'Desc',
            ],
        ];

        $result = $this->service->integrate('class', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('created', $result->getPrimaryAction());
        $class = Classe::find($result->getPrimaryId());
        $this->assertNotNull($class);
        $this->assertSame('Classe Integration Test', $class->name);
    }

    public function test_integrate_item_dry_run_returns_would_create(): void
    {
        $convertedData = [
            'items' => [
                'type_id' => 15,
                'dofusdb_id' => '100',
                'name' => 'Ressource Test',
                'description' => 'Desc',
                'level' => '1',
            ],
        ];

        $result = $this->service->integrate('item', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }

    public function test_integrate_item_incomplete_returns_fail(): void
    {
        $result = $this->service->integrate('item', [], []);

        $this->assertFalse($result->isSuccess());
    }

    public function test_integrate_breed_dispatches_to_class(): void
    {
        $convertedData = [
            'classes' => [
                'dofusdb_id' => '3',
                'name' => 'Breed Alias Test',
                'description' => 'Desc',
            ],
        ];

        $result = $this->service->integrate('breed', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }
}
