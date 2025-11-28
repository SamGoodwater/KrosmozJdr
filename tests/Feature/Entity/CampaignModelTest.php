<?php

namespace Tests\Feature\Entity;

use App\Models\User;
use App\Models\Entity\Campaign;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Creature;
use App\Models\Entity\Spell;
use App\Models\Entity\Panoply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests d'intégration pour le modèle Campaign
 * 
 * Vérifie que le modèle fonctionne correctement avec ses relations
 * 
 * @package Tests\Feature\Entity
 */
class CampaignModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de création d'une campagne via factory
     */
    public function test_campaign_factory_creates_valid_campaign(): void
    {
        $user = User::factory()->create();
        
        $campaign = Campaign::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($campaign);
        $this->assertNotNull($campaign->id);
        $this->assertNotNull($campaign->name);
        $this->assertEquals($user->id, $campaign->created_by);
    }

    /**
     * Test de la relation createdBy
     */
    public function test_campaign_has_created_by_relation(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'created_by' => $user->id,
        ]);

        $this->assertNotNull($campaign->createdBy);
        $this->assertEquals($user->id, $campaign->createdBy->id);
    }

    /**
     * Test de la relation items (many-to-many)
     */
    public function test_campaign_has_items_relation(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'created_by' => $user->id,
        ]);

        $item1 = Item::factory()->create([
            'created_by' => $user->id,
        ]);
        $item2 = Item::factory()->create([
            'created_by' => $user->id,
        ]);

        $campaign->items()->sync([$item1->id, $item2->id]);

        $campaign->refresh();
        $this->assertCount(2, $campaign->items);
        $this->assertTrue($campaign->items->contains($item1));
        $this->assertTrue($campaign->items->contains($item2));
    }

    /**
     * Test de la relation monsters (many-to-many)
     */
    public function test_campaign_has_monsters_relation(): void
    {
        $user = User::factory()->create();
        $creature = Creature::factory()->create([
            'created_by' => $user->id,
        ]);
        $campaign = Campaign::factory()->create([
            'created_by' => $user->id,
        ]);

        $monster1 = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);
        $monster2 = Monster::factory()->create([
            'creature_id' => $creature->id,
        ]);

        $campaign->monsters()->sync([$monster1->id, $monster2->id]);

        $campaign->refresh();
        $this->assertCount(2, $campaign->monsters);
    }

    /**
     * Test de la relation spells (many-to-many)
     */
    public function test_campaign_has_spells_relation(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'created_by' => $user->id,
        ]);

        $spell1 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);
        $spell2 = Spell::factory()->create([
            'created_by' => $user->id,
        ]);

        $campaign->spells()->sync([$spell1->id, $spell2->id]);

        $campaign->refresh();
        $this->assertCount(2, $campaign->spells);
    }

    /**
     * Test de la relation panoplies (many-to-many)
     */
    public function test_campaign_has_panoplies_relation(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'created_by' => $user->id,
        ]);

        $panoply1 = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);
        $panoply2 = Panoply::factory()->create([
            'created_by' => $user->id,
        ]);

        $campaign->panoplies()->sync([$panoply1->id, $panoply2->id]);

        $campaign->refresh();
        $this->assertCount(2, $campaign->panoplies);
    }
}

