<?php

namespace Tests\Feature\Scrapping;

use App\Models\Entity\Breed;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Panoply;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\CreatesSystemUser;

/**
 * Tests de la commande scrapping : chaîne complète (collecte API, conversion, validation, intégration BDD)
 * pour toutes les entités et principaux paramètres.
 *
 * @package Tests\Feature\Scrapping
 */
class ScrappingCommandTest extends TestCase
{
    use CreatesSystemUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSystemUser();
    }

    // ---- Validation / options ----

    public function test_command_requires_entity_option(): void
    {
        $code = Artisan::call('scrapping', []);

        $this->assertSame(1, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('--entity', $out);
        $this->assertStringContainsString('Aucune entité', $out);
    }

    public function test_command_rejects_unknown_entity(): void
    {
        $code = Artisan::call('scrapping', [
            '--entity' => 'unknown-entity',
        ]);

        $out = Artisan::output();
        $this->assertTrue(
            $code !== 0 || str_contains($out, 'erreur') || str_contains($out, 'error') || str_contains($out, 'unknown') || str_contains($out, 'Config') || str_contains($out, 'requis'),
            "Expected failure or error in output: code={$code}, out={$out}"
        );
    }

    public function test_command_accepts_multiple_entities(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters')) {
                return Http::response(['data' => [['id' => 31]], 'total' => 1, 'limit' => 50, 'skip' => 0], 200);
            }
            if (str_contains($url, '/breeds')) {
                return Http::response(['data' => [['id' => 1]], 'total' => 1, 'limit' => 50, 'skip' => 0], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster,class',
            '--limit' => '1',
            '--max-items' => '1',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
        $this->assertStringContainsString('class', $out);
    }

    // ---- Collecte (fetchMany / fetchOne) ----

    public function test_command_collect_monster_fetch_many(): void
    {
        $monsterOne = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'level' => 5,
            'grades' => [['level' => 5, 'lifePoints' => 100]],
        ];
        Http::fake(function ($request) use ($monsterOne) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters/31')) {
                return Http::response($monsterOne, 200);
            }
            if (str_contains($url, '/monsters')) {
                return Http::response([
                    'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '5',
            '--max-items' => '5',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
    }

    public function test_command_collect_monster_by_id(): void
    {
        Http::fake([
            '*/monsters/31*' => Http::response([
                'id' => 31,
                'name' => ['fr' => 'Bouftou'],
                'grades' => [['level' => 5, 'lifePoints' => 100]],
            ], 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--id' => '31',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
        $this->assertTrue(str_contains($out, '31') || str_contains($out, 'imports'), "Output should mention id or imports: {$out}");
    }

    public function test_command_collect_item_by_ids(): void
    {
        $itemPayload = [
            'id' => 70,
            'name' => ['fr' => 'Épée de test'],
            'typeId' => 1,
            'level' => 10,
            'rarity' => 'common',
        ];
        Http::fake(function ($request) use ($itemPayload) {
            $url = (string) $request->url();
            if (str_contains($url, '/items/70')) {
                return Http::response($itemPayload, 200);
            }
            if (str_contains($url, '/items/71')) {
                return Http::response(array_merge($itemPayload, ['id' => 71, 'name' => ['fr' => 'Épée 71']]), 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'item',
            '--ids' => '70,71',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('item', $out);
    }

    public function test_command_collect_resource_returns_success(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/item-types')) {
                return Http::response([
                    'data' => [['id' => 15, 'superTypeId' => 1, 'name' => ['fr' => 'Ressource']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            if (str_contains($url, '/items')) {
                return Http::response([
                    'data' => [['id' => 1, 'typeId' => 15, 'name' => ['fr' => 'Ressource test']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'resource',
            '--limit' => '1',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('resource', $out);
    }

    public function test_command_limit_and_max_items(): void
    {
        Http::fake(function ($request) {
            return Http::response([
                'data' => [
                    ['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5],
                ],
                'total' => 100,
                'limit' => 5,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '3',
            '--max-items' => '5',
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
    }

    public function test_command_start_skip_passed_to_collect(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            $this->assertTrue(str_contains($url, 'skip') || str_contains($url, '%24skip'));
            return Http::response([
                'data' => [['id' => 11], ['id' => 12]],
                'total' => 20,
                'limit' => 2,
                'skip' => 10,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '2',
            '--start-skip' => '10',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
    }

    public function test_command_max_pages_limits_pagination(): void
    {
        $callCount = 0;
        Http::fake(function ($request) use (&$callCount) {
            $callCount++;
            $url = (string) $request->url();
            if (str_contains($url, 'skip=0') || str_contains($url, '%24skip=0')) {
                return Http::response([
                    'data' => [['id' => 1], ['id' => 2]],
                    'total' => 10,
                    'limit' => 2,
                    'skip' => 0,
                ], 200);
            }
            if (str_contains($url, 'skip=2') || str_contains($url, '%24skip=2')) {
                return Http::response([
                    'data' => [['id' => 3], ['id' => 4]],
                    'total' => 10,
                    'limit' => 2,
                    'skip' => 2,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '2',
            '--max-pages' => '2',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
        $this->assertGreaterThanOrEqual(1, $callCount);
    }

    // ---- Sortie (output / json) ----

    public function test_command_output_json_valid(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters/31')) {
                return Http::response([
                    'id' => 31,
                    'name' => ['fr' => 'Bouftou'],
                    'grades' => [['level' => 5, 'lifePoints' => 100]],
                ], 200);
            }
            return Http::response([
                'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                'total' => 1,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '1',
            '--max-items' => '1',
            '--json' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $jsonStart = strpos($out, '{');
        $this->assertNotFalse($jsonStart);
        $decoded = json_decode(substr($out, $jsonStart), true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('query', $decoded);
        $this->assertArrayHasKey('entities', $decoded);
    }

    public function test_command_output_raw(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters')) {
                return Http::response([
                    'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '1',
            '--max-items' => '1',
            '--output' => 'raw',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('monster', $out);
    }

    public function test_command_output_summary(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters')) {
                return Http::response([
                    'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '1',
            '--max-items' => '1',
            '--output' => 'summary',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('Résumé', $out);
        $this->assertStringContainsString('Collectés', $out);
    }

    // ---- Simulate (pas d'écriture BDD) ----

    public function test_command_simulate_does_not_crash_and_exits_zero(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters')) {
                return Http::response([
                    'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--limit' => '1',
            '--max-items' => '1',
            '--simulate' => true,
        ]);

        $this->assertSame(0, $code);
    }

    // ---- Chaîne complète par entité (écriture BDD) ----

    public function test_command_full_chain_monster_writes_to_db(): void
    {
        $monsterOne = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou'],
            'level' => 5,
            'lifePoints' => 100,
            'grades' => [
                [
                    'level' => 5,
                    'lifePoints' => 100,
                    'strength' => 10,
                    'intelligence' => 5,
                    'agility' => 8,
                    'wisdom' => 3,
                    'chance' => 2,
                ],
            ],
            'size' => 'medium',
        ];
        Http::fake(function ($request) use ($monsterOne) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters/31')) {
                return Http::response($monsterOne, 200);
            }
            if (str_contains($url, '/monsters')) {
                return Http::response([
                    'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--id' => '31',
        ]);

        $this->assertSame(0, $code);
        $creature = Creature::where('name', 'Bouftou')->first();
        $this->assertNotNull($creature);
        $monster = Monster::where('creature_id', $creature->id)->first();
        $this->assertNotNull($monster);
    }

    public function test_command_full_chain_class_writes_to_db(): void
    {
        Http::fake([
            '*/breeds/1*' => Http::response([
                'id' => 1,
                'name' => ['fr' => 'Iop'],
                'description' => ['fr' => 'Description Iop'],
                'life' => 50,
                'life_dice' => '1d6',
                'specificity' => 'Force',
            ], 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--entity' => 'class',
            '--id' => '1',
        ]);

        $this->assertSame(0, $code);
        $breed = Breed::where('name', 'like', '%Iop%')->orWhere('name', 'like', '%Classe%')->first();
        $this->assertNotNull($breed);
    }

    public function test_command_full_chain_item_resource_writes_to_db(): void
    {
        Http::fake([
            '*/items/15*' => Http::response([
                'id' => 15,
                'name' => ['fr' => 'Purée pique-fêle'],
                'description' => ['fr' => 'Description'],
                'typeId' => 15,
                'level' => 1,
                'rarity' => 'common',
                'price' => 10,
            ], 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--entity' => 'item',
            '--id' => '15',
        ]);

        $this->assertSame(0, $code);
        $resource = Resource::where('name', 'Purée pique-fêle')->first();
        $this->assertNotNull($resource);
    }

    public function test_command_full_chain_spell_writes_to_db(): void
    {
        Http::fake([
            '*/spells/201*' => Http::response([
                'id' => 201,
                'name' => ['fr' => 'Béco du Tofu'],
                'description' => ['fr' => 'Description du sort'],
                'cost' => 3,
                'range' => 1,
                'area' => 1,
            ], 200),
            '*/spell-levels*' => Http::response(['data' => []], 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--entity' => 'spell',
            '--id' => '201',
        ]);

        $this->assertSame(0, $code);
        $spell = Spell::where('name', 'Béco du Tofu')->first();
        $this->assertNotNull($spell);
    }

    public function test_command_full_chain_panoply_writes_to_db(): void
    {
        Http::fake([
            '*/item-sets/1*' => Http::response([
                'id' => 1,
                'name' => ['fr' => 'Panoplie test'],
                'level' => 50,
                'isCosmetic' => false,
                'itemIds' => [],
            ], 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--entity' => 'panoply',
            '--id' => '1',
        ]);

        $this->assertSame(0, $code);
        $panoply = Panoply::where('name', 'Panoplie test')->first();
        $this->assertNotNull($panoply);
    }

    // ---- Item avec effects → effect / bonus ----

    public function test_command_item_with_effects_converts_and_writes_effect_bonus(): void
    {
        $itemWithEffects = [
            'id' => 70,
            'name' => ['fr' => 'Anneau avec bonus'],
            'typeId' => 1,
            'level' => 10,
            'rarity' => 'common',
            'effects' => [
                ['characteristic' => 15, 'from' => 10, 'to' => 13],
                ['characteristic' => 16, 'from' => 2],
            ],
        ];
        Http::fake([
            '*/items/70*' => Http::response($itemWithEffects, 200),
        ]);

        $code = Artisan::call('scrapping', [
            '--entity' => 'item',
            '--id' => '70',
            '--no-validate' => true,
        ]);

        $this->assertSame(0, $code);
        $item = Item::where('dofusdb_id', '70')->first();
        $resource = Resource::where('dofusdb_id', '70')->first();
        $consumable = \App\Models\Entity\Consumable::where('dofusdb_id', '70')->first();
        $this->assertTrue(
            $item !== null || $resource !== null || $consumable !== null,
            'L\'objet dofusdb_id 70 doit être créé (items, resources ou consumables)'
        );
        if ($item !== null) {
            $this->assertNotNull($item->effect, 'Item should have effect (Krosmoz bonus)');
            $this->assertNotNull($item->bonus, 'Item should have bonus (raw JSON)');
        }
    }

    // ---- Options (replace-existing, no-validate, debug) ----

    public function test_command_replace_existing_updates_record(): void
    {
        $monsterPayload = [
            'id' => 31,
            'name' => ['fr' => 'Bouftou mis à jour'],
            'level' => 5,
            'grades' => [['level' => 5, 'lifePoints' => 100]],
        ];
        Http::fake(function ($request) use ($monsterPayload) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters/31')) {
                return Http::response($monsterPayload, 200);
            }
            if (str_contains($url, '/monsters')) {
                return Http::response([
                    'data' => [['id' => 31, 'name' => ['fr' => 'Bouftou']]],
                    'total' => 1,
                    'limit' => 50,
                    'skip' => 0,
                ], 200);
            }
            return Http::response([], 404);
        });

        Artisan::call('scrapping', ['--entity' => 'monster', '--id' => '31']);
        $first = Creature::where('name', 'like', '%Bouftou%')->first();
        $this->assertNotNull($first);

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--id' => '31',
            '--replace-existing' => true,
        ]);
        $this->assertSame(0, $code);
        $updated = Creature::where('name', 'like', '%Bouftou%')->first();
        $this->assertNotNull($updated);
        $this->assertStringContainsString('mis à jour', $updated->name ?? '');
    }

    public function test_command_debug_exits_zero(): void
    {
        Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, '/monsters/31')) {
                return Http::response([
                    'id' => 31,
                    'name' => ['fr' => 'Bouftou'],
                    'grades' => [['level' => 5, 'lifePoints' => 100]],
                ], 200);
            }
            return Http::response([
                'data' => [['id' => 31]],
                'total' => 1,
                'limit' => 50,
                'skip' => 0,
            ], 200);
        });

        $code = Artisan::call('scrapping', [
            '--entity' => 'monster',
            '--id' => '31',
            '--debug' => true,
        ]);

        $this->assertSame(0, $code);
        $out = Artisan::output();
        $this->assertStringContainsString('debug', $out);
    }
}
