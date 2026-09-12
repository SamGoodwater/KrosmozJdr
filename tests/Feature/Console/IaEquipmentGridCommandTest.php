<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Entity\Item;
use App\Models\Type\ItemType;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

final class IaEquipmentGridCommandTest extends TestCase
{
    public function test_report_is_read_only_and_lists_holes(): void
    {
        $type = $this->ringType();
        Item::factory()->create([
            'name' => 'Anneau du Bouftou',
            'level' => '8',
            'state' => Item::STATE_PLAYABLE,
            'dofusdb_id' => '441',
            'official_id' => null,
            'effect' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'bonus' => json_encode(['strength' => 2], JSON_THROW_ON_ERROR),
            'item_type_id' => $type->id,
            'auto_update' => false,
            'created_by' => null,
        ]);

        $code = Artisan::call('ia:equipment-grid', [
            '--slot' => 'ring',
            '--voie' => 'terre',
            '--level' => 8,
        ]);

        $this->assertSame(0, $code);
        $output = Artisan::output();
        $this->assertStringContainsString('Anneau du Bouftou', $output);
        $this->assertStringContainsString('Lecture seule', $output);
        $this->assertSame(1, Item::query()->count());
    }

    public function test_write_creates_draft_hole_and_is_idempotent(): void
    {
        $this->ringType();

        $code = Artisan::call('ia:equipment-grid', [
            '--write' => true,
            '--slot' => 'ring',
            '--voie' => 'feu',
            '--level' => 8,
        ]);

        $this->assertSame(0, $code);
        $item = Item::query()->where('official_id', 'ia-grid:ring:feu:8')->first();
        $this->assertNotNull($item);
        $this->assertSame(Item::STATE_DRAFT, $item->state);
        $this->assertNull($item->dofusdb_id);
        $this->assertFalse((bool) $item->auto_update);
        $this->assertStringContainsString('[Grille]', (string) $item->name);
        $bonus = json_decode((string) $item->bonus, true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(2, $bonus['intelligence'] ?? null);
        $this->assertNotNull($item->price_calculated);
        $this->assertSame((string) $item->price_calculated, $item->price);

        $before = Item::query()->count();
        Artisan::call('ia:equipment-grid', [
            '--write' => true,
            '--slot' => 'ring',
            '--voie' => 'feu',
            '--level' => 8,
        ]);
        $this->assertSame($before, Item::query()->count());
    }

    public function test_unknown_slot_fails(): void
    {
        $code = Artisan::call('ia:equipment-grid', ['--slot' => 'monture']);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('Slot inconnu', Artisan::output());
    }

    private function ringType(): ItemType
    {
        return ItemType::query()->create([
            'name' => 'Anneau',
            'dofusdb_type_id' => 9,
            'state' => ItemType::STATE_PLAYABLE,
            'read_level' => 0,
            'write_level' => 3,
            'show_in_catalog' => true,
            'allow_scrap' => false,
        ]);
    }
}
