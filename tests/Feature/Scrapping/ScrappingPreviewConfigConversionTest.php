<?php

namespace Tests\Feature\Scrapping;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ScrappingPreviewConfigConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_monster_uses_config_driven_conversion_shape(): void
    {
        Http::fake([
            'api.dofusdb.fr/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'grades' => [['level' => 5, 'lifePoints' => 100]],
                'size' => 'medium',
                'race' => 1,
                'img' => 'https://api.dofusdb.fr/img/monsters/31.png',
            ], 200),
        ]);

        $res = $this->getJson('/api/scrapping/preview/monster/31');
        $res->assertStatus(200)->assertJson(['success' => true]);

        $converted = $res->json('data.converted');
        $this->assertIsArray($converted);
        $this->assertArrayHasKey('creatures', $converted);
        $this->assertArrayHasKey('monsters', $converted);
        $this->assertEquals('Bouftou', $converted['creatures']['name'] ?? null);
    }

    public function test_preview_spell_uses_config_driven_conversion_and_keeps_name(): void
    {
        $spellData = [
            'id' => 201,
            'name' => ['fr' => 'Béco du Tofu'],
            'description' => ['fr' => 'Description'],
            'breedId' => 1,
            'img' => 'https://api.dofusdb.fr/img/spells/201.png',
        ];
        Http::fake([
            'api.dofusdb.fr/spells/201*' => Http::response($spellData, 200),
            'api.dofusdb.fr/spells*' => Http::response(['data' => [$spellData]], 200),
            'api.dofusdb.fr/spell-levels*' => Http::response(['data' => []], 200),
        ]);

        $res = $this->getJson('/api/scrapping/preview/spell/201');
        $res->assertStatus(200)->assertJson(['success' => true]);

        $converted = $res->json('data.converted');
        $this->assertIsArray($converted);
        $this->assertEquals('Béco du Tofu', $converted['spells']['name'] ?? $converted['name'] ?? null);
    }
}

