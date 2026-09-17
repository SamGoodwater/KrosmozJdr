<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Effect;

use App\Models\Entity\Spell;
use App\Services\Effect\SpellNestedPreviewSerializer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpellNestedPreviewSerializerTest extends TestCase
{
    use RefreshDatabase;

    public function test_serialize_includes_chips_without_effects_tree(): void
    {
        $spell = Spell::factory()->create(['name' => 'Preview Spell']);
        $payload = app(SpellNestedPreviewSerializer::class)->serialize($spell);

        $this->assertSame($spell->id, $payload['id']);
        $this->assertSame('Preview Spell', $payload['name']);
        $this->assertArrayHasKey('effect_usages_chips', $payload);
        $this->assertIsArray($payload['effect_usages_chips']);
        $this->assertArrayHasKey('effect_usages_summary', $payload);
        $this->assertArrayNotHasKey('effects', $payload);
        $this->assertArrayHasKey('resolution_mode', $payload);
        $this->assertArrayHasKey('allows_reaction', $payload);
        $this->assertIsBool($payload['allows_reaction']);
    }

    public function test_decorate_sets_chip_attributes_on_model(): void
    {
        $spell = Spell::factory()->create();
        app(SpellNestedPreviewSerializer::class)->decorate($spell);

        $this->assertIsArray($spell->getAttribute('effect_usages_chips'));
        $this->assertIsString($spell->getAttribute('effect_usages_summary'));
    }
}
