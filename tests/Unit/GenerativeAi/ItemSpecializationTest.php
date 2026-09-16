<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Enums\EntityState;
use App\Models\Entity\Item;
use App\Services\GenerativeAi\ConversionRequest;
use App\Services\GenerativeAi\EntityGenerationProfile;
use App\Services\GenerativeAi\Specializations\ItemSpecialization;
use Tests\TestCase;

final class ItemSpecializationTest extends TestCase
{
    public function test_unique_item_without_dofusdb_id_has_identity_writable(): void
    {
        $item = Item::factory()->create([
            'dofusdb_id' => null,
            'state' => EntityState::Draft->value,
        ]);
        $spec = app(ItemSpecialization::class);
        $profile = $this->profile();
        $request = new ConversionRequest(action: 'item', entityType: 'item', entityId: (int) $item->id);

        $schema = $spec->jsonSchema($profile, $request);
        $this->assertContains('bonus', $schema['required']);
        $this->assertSame([], $spec->preflight($request, $profile));
    }

    public function test_sourced_item_preflight_blocks_frozen_sheet(): void
    {
        $item = Item::factory()->create([
            'dofusdb_id' => '8236',
            'state' => EntityState::Raw->value,
        ]);
        $spec = app(ItemSpecialization::class);
        $profile = $this->profile();
        $request = new ConversionRequest(action: 'item', entityType: 'item', entityId: (int) $item->id);

        $errors = $spec->preflight($request, $profile);
        $this->assertNotSame([], $errors);
        $this->assertStringContainsString('writable', $errors[0]);
    }

    /**
     * @param  list<string>  $writable
     */
    private function profile(array $writable = []): EntityGenerationProfile
    {
        return new EntityGenerationProfile(
            entity: 'item',
            hasDofusSource: true,
            frozenFields: '*',
            writableFields: $writable,
            frozenCharacteristics: '*',
            writableCharacteristics: [],
            exampleIds: [],
            extra: [],
        );
    }
}
