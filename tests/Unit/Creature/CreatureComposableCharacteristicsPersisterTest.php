<?php

declare(strict_types=1);

namespace Tests\Unit\Creature;

use App\Models\Entity\Creature;
use App\Services\Creature\CreatureComposableCharacteristicsPersister;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreatureComposableCharacteristicsPersisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_persists_total_and_context_and_clears_empty_strings(): void
    {
        $creature = Creature::factory()->create([
            'ca' => '14',
            'ca_context' => null,
        ]);

        $persister = new CreatureComposableCharacteristicsPersister;
        $persister->apply($creature, [
            'ca' => '',
            'ca_context' => '2',
            'life' => '120',
        ]);

        $creature->refresh();
        $this->assertFalse($creature->hasExplicitTotal('ca'));
        $this->assertSame('2', $creature->ca_context);
        $this->assertSame('120', $creature->life);
    }

    public function test_extract_payload_keeps_only_composable_keys(): void
    {
        $persister = new CreatureComposableCharacteristicsPersister;
        $payload = $persister->extractPayload([
            'size' => 2,
            'ca' => '10',
            'ca_context' => '{[niveau] / 2}',
            'name' => 'Test',
        ]);

        $this->assertSame(['ca' => '10', 'ca_context' => '{[niveau] / 2}'], $payload);
    }
}
