<?php

namespace Tests\Unit\Scrapping\Core;

use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\Type\MonsterRace;
use App\Services\Scrapping\Core\Integration\IntegrationResult;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesSystemUser;
use Tests\TestCase;

/**
 * Tests unitaires pour IntegrationService (monster, spell, class, item, dry_run).
 */
class IntegrationServiceTest extends TestCase
{
    use CreatesSystemUser;
    use RefreshDatabase;

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
            'breeds' => [
                'dofusdb_id' => '1',
                'name' => 'Feca',
                'description' => 'Desc',
            ],
        ];

        $result = $this->service->integrate('class', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }

    public function test_integrate_class_creates_breed(): void
    {
        $this->createSystemUser();

        $convertedData = [
            'breeds' => [
                'dofusdb_id' => '2',
                'name' => 'Classe Integration Test',
                'description' => 'Desc',
            ],
        ];

        $result = $this->service->integrate('class', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('created', $result->getPrimaryAction());
        $breed = Breed::find($result->getPrimaryId());
        $this->assertNotNull($breed);
        $this->assertSame('Classe Integration Test', $breed->name);
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
            'breeds' => [
                'dofusdb_id' => '3',
                'name' => 'Breed Alias Test',
                'description' => 'Desc',
            ],
        ];

        $result = $this->service->integrate('breed', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }

    public function test_integrate_panoply_dry_run_returns_would_create(): void
    {
        $convertedData = [
            'panoplies' => [
                'dofusdb_id' => '42',
                'name' => 'Panoplie Test',
                'description' => 'Description',
                'bonus' => '[]',
                'item_dofusdb_ids' => [],
            ],
        ];

        $result = $this->service->integrate('panoply', $convertedData, ['dry_run' => true]);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('would_create', $result->getPrimaryAction());
    }

    public function test_integrate_panoply_creates_panoply(): void
    {
        $this->createSystemUser();

        $convertedData = [
            'panoplies' => [
                'dofusdb_id' => '100',
                'name' => 'Panoplie Integration Test',
                'description' => 'Description panoplie',
                'bonus' => '[{"effectId":1,"value":10}]',
                'item_dofusdb_ids' => [],
            ],
        ];

        $result = $this->service->integrate('panoply', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('created', $result->getPrimaryAction());
        $panoply = Panoply::find($result->getPrimaryId());
        $this->assertNotNull($panoply);
        $this->assertSame('Panoplie Integration Test', $panoply->name);
        $this->assertSame('100', $panoply->dofusdb_id);
        $this->assertSame('[{"effectId":1,"value":10}]', $panoply->bonus);
    }

    public function test_integrate_panoply_incomplete_returns_fail(): void
    {
        $result = $this->service->integrate('panoply', [], []);

        $this->assertFalse($result->isSuccess());
        $this->assertStringContainsString('panoplies', $result->getMessage());
    }

    public function test_integrate_panoply_skips_when_dofusdb_id_exists_without_force_update(): void
    {
        $this->createSystemUser();
        $existing = Panoply::factory()->create([
            'dofusdb_id' => '200',
            'name' => 'Panoplie existante',
        ]);

        $convertedData = [
            'panoplies' => [
                'dofusdb_id' => '200',
                'name' => 'Autre nom',
                'description' => 'Desc',
                'bonus' => '[]',
                'item_dofusdb_ids' => [],
            ],
        ];

        $result = $this->service->integrate('panoply', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('skipped', $result->getPrimaryAction());
        $existing->refresh();
        $this->assertSame('Panoplie existante', $existing->name);
    }

    public function test_integrate_panoply_syncs_items(): void
    {
        $this->createSystemUser();
        $item1 = Item::factory()->create(['dofusdb_id' => '501']);
        $item2 = Item::factory()->create(['dofusdb_id' => '502']);

        $convertedData = [
            'panoplies' => [
                'dofusdb_id' => '300',
                'name' => 'Panoplie avec items',
                'description' => 'Desc',
                'bonus' => '[]',
                'item_dofusdb_ids' => [501, 502],
            ],
        ];

        $result = $this->service->integrate('panoply', $convertedData, []);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('created', $result->getPrimaryAction());
        $panoply = Panoply::find($result->getPrimaryId());
        $this->assertNotNull($panoply);
        $syncedIds = $panoply->items()->pluck('items.id')->all();
        $this->assertCount(2, $syncedIds);
        $this->assertContains($item1->id, $syncedIds);
        $this->assertContains($item2->id, $syncedIds);
    }

    /**
     * Test : attachImageFromUrl retourne false quand l'URL est vide.
     */
    public function test_attach_image_from_url_returns_false_when_url_empty(): void
    {
        $this->createSystemUser();
        $resource = Resource::factory()->create();

        $this->assertFalse($this->service->attachImageFromUrl($resource, null, ['download_images' => true]));
        $this->assertFalse($this->service->attachImageFromUrl($resource, '', ['download_images' => true]));
        $this->assertFalse($this->service->attachImageFromUrl($resource, '   ', ['download_images' => true]));
    }

    /**
     * Test : attachImageFromUrl retourne false quand download_images est false.
     */
    public function test_attach_image_from_url_returns_false_when_download_images_disabled(): void
    {
        $this->createSystemUser();
        $resource = Resource::factory()->create();
        config(['scrapping.images.allowed_hosts' => ['api.dofusdb.fr']]);

        $this->assertFalse($this->service->attachImageFromUrl(
            $resource,
            'https://api.dofusdb.fr/img/items/1.png',
            ['download_images' => false]
        ));
    }

    /**
     * Test : attachImageFromUrl retourne false quand l'hôte n'est pas dans allowed_hosts.
     */
    public function test_attach_image_from_url_returns_false_when_host_not_allowed(): void
    {
        $this->createSystemUser();
        config(['scrapping.images.allowed_hosts' => ['api.dofusdb.fr']]);

        $resource = Resource::factory()->create();
        $otherUrl = 'https://other.example.com/img/1.png';

        $this->assertFalse($this->service->attachImageFromUrl($resource, $otherUrl, ['download_images' => true]));
        $this->assertCount(0, $resource->getMedia('images'));
    }
}
