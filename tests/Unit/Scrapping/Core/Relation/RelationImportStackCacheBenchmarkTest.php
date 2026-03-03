<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core\Relation;

use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Scrapping\Core\Relation\RelationImportStack;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Micro-benchmark de requêtes SQL pour valider le cache local de RelationImportStack.
 */
final class RelationImportStackCacheBenchmarkTest extends TestCase
{
    public function test_register_creature_dependents_reuses_lookup_cache_between_calls(): void
    {
        $creature = Creature::factory()->create();
        Spell::factory()->create(['dofusdb_id' => '9001']);
        Resource::factory()->create(['dofusdb_id' => '8001']);
        Item::factory()->create(['dofusdb_id' => '7001']);
        Consumable::factory()->create(['dofusdb_id' => '6001']);

        $rawData = [
            'spells' => [
                ['id' => 9001],
            ],
            'drops' => [
                ['itemId' => 8001, 'quantity' => 1],
                ['itemId' => 7001, 'quantity' => 1],
                ['itemId' => 6001, 'quantity' => 1],
            ],
        ];

        $stack = new RelationImportStack();

        DB::flushQueryLog();
        DB::enableQueryLog();
        $stack->registerCreatureRelationDependents($creature->id, $rawData, true);
        /** @var array<int, mixed> $firstQueryLog */
        $firstQueryLog = DB::getQueryLog();
        $firstQueryCount = count($firstQueryLog);

        DB::flushQueryLog();
        $stack->registerCreatureRelationDependents($creature->id, $rawData, true);
        /** @var array<int, mixed> $secondQueryLog */
        $secondQueryLog = DB::getQueryLog();
        $secondQueryCount = count($secondQueryLog);
        DB::disableQueryLog();

        fwrite(STDOUT, "RelationImportStack cache benchmark: first={$firstQueryCount}, second={$secondQueryCount}\n");

        $this->assertGreaterThan(
            $secondQueryCount,
            $firstQueryCount,
            "Le 2e passage devrait émettre moins de requêtes (first={$firstQueryCount}, second={$secondQueryCount})."
        );
    }
}
