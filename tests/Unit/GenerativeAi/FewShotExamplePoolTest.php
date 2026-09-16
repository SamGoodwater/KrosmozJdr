<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Npc;
use App\Services\GenerativeAi\FewShotExamplePool;
use App\Services\GenerativeAi\NpcKitCatalog;
use RuntimeException;
use Tests\TestCase;

final class FewShotExamplePoolTest extends TestCase
{
    public function test_resolves_official_id_and_name_only_when_playable(): void
    {
        $playable = Item::factory()->create([
            'name' => 'Cape étalon',
            'official_id' => 'jdr:item:cape-etalon',
            'state' => Item::STATE_PLAYABLE,
        ]);
        Item::factory()->create([
            'name' => 'Cape brouillon',
            'official_id' => 'jdr:item:cape-draft',
            'state' => Item::STATE_DRAFT,
        ]);

        $pool = app(FewShotExamplePool::class);

        $this->assertSame(
            [(int) $playable->id],
            $pool->resolvePlayableIds('item', ['jdr:item:cape-etalon', 'Cape étalon'])
        );
        $this->assertSame([], $pool->resolvePlayableIds('item', ['jdr:item:cape-draft']));
        $this->assertSame([], $pool->resolvePlayableIds('item', [999999]));
    }

    public function test_resolves_playable_monster_by_official_id_not_draft(): void
    {
        $playable = Monster::factory()->create([
            'official_id' => 'jdr:bestiary:piou-vert',
            'state' => EntityState::Playable->value,
        ]);
        Monster::factory()->create([
            'official_id' => 'jdr:bestiary:draft-ignored',
            'state' => EntityState::Draft->value,
        ]);

        $ids = app(FewShotExamplePool::class)->resolvePlayableIds('monster', [
            'jdr:bestiary:piou-vert',
            'jdr:bestiary:draft-ignored',
        ]);

        $this->assertSame([(int) $playable->id], $ids);
    }

    public function test_require_ids_refuses_empty_configured_pool(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pool few-shot vide');

        app(FewShotExamplePool::class)->requireIds('npc', ['missing-official'], []);
    }

    public function test_require_ids_refuses_empty_monster_pool(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pool few-shot vide');

        app(FewShotExamplePool::class)->requireIds('monster', ['jdr:bestiary:missing'], []);
    }

    public function test_assemble_refuses_empty_npc_example_pool(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Pool few-shot vide');

        (new NpcKitCatalog)->assemble(4);
    }

    public function test_assemble_uses_incarnam_fallback_when_config_empty(): void
    {
        $npc = Npc::factory()->create([
            'official_id' => NpcKitCatalog::OFFICIAL_ID_PREFIX.'ganymede',
            'state' => Npc::STATE_PLAYABLE,
        ]);

        $payload = (new NpcKitCatalog)->assemble(4);

        $this->assertSame([(int) $npc->id], $payload['example_ids']);
    }
}
